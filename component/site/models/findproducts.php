<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('findproducts', JPATH_SITE . '/components/com_redproductfinder/helpers');

/**
 * Findproducts Model.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Model
 * @since       2.0
 */
class RedproductfinderModelFindproducts extends RModelList
{
	protected $limitField = 'limit';

	protected $limitstartField = 'auto';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');
		$param = JComponentHelper::getParams('com_redproductfinder');
		$input = $app->input;

		// Load state from the request.
		$redform = $input->post->get('redform', array(), 'filter');

		if ($redform)
		{
			$pk = $redform;
		}
		else
		{
			$decode = $input->post->get('jsondata', "", "filter");
			$pk = json_decode($decode, true);
		}

		if (isset($pk['cid']))
		{
			$this->setState('catid', $pk['cid']);
		}
		else
		{
			$cid = $input->get("cid", 0, "int");
			$this->setState('catid', $cid);
		}

		$this->setState('redform.data', $pk);

		$orderBy = $app->input->getString('order_by', '');

		$this->setState('order_by', $orderBy);

		$params = $app->getParams();

		$this->setState('params', $params);

		$templateId = $param->get('prod_template');
		$templateDesc = RedproductfinderFindProducts::getTemplate($templateId);

		$this->setState('templateDesc', $templateDesc);

		$limit = $input->get("limit", null);

		if ($limit == null)
		{
			if ($pk['cid'] == null)
			{
				$cid = $input->get("cid", 0, "int");

				if ($cid !== 0)
				{
					$cat = RedshopHelperCategory::getCategoryById($cid);
					$limit = $cat->products_per_page;
				}
				else
				{
					$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
					$limit = $value;
				}
			}
			else
			{
				$cat = RedshopHelperCategory::getCategoryById($pk['cid']);

				if ($cat)
				{
					$limit = $cat->products_per_page;
				}
				else
				{
					$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
					$limit = $value;
				}
			}
		}
		else
		{
			$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			$limit = $value;
		}

		// If limit = 0, set limit by configuration, from redshop, see redshop to get more detail
		if (!$limit)
		{
			$limit = MAXCATEGORY;
		}

		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Set session
	 *
	 * @return array
	 */
	public function addFilterStateData()
	{
		$input = JFactory::getApplication()->input;
		$act = $input->getString("act");
		$tempType = $input->getInt("tempType");
		$tempTag = $input->getInt("tempTag");

		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		if ($tempTag)
		{
			if (!$saveFilter)
			{
				$saveFilter = array();
			}

			if (!$saveFilter)
			{
				$saveFilter[$tempType] = array();
				$saveFilter[$tempType][$tempTag] = array("typeid" => $tempType, "tagid" => $tempTag);
			}
			else
			{
				$saveFilter[$tempType][$tempTag] = array("typeid" => $tempType, "tagid" => $tempTag);
			}

			$session->set("saveFilter", $saveFilter);
		}

		if ($act == 'delete')
		{
			unset($saveFilter[$tempType][$tempTag]);

			if ($saveFilter[$tempType] == null)
			{
				unset($saveFilter[$tempType]);
			}

			$session->set("saveFilter", $saveFilter);
		}

		if ($act == 'clear')
		{
			$session->clear('saveFilter');
		}
	}

	/**
	 * Get List from product
	 *
	 * @return array
	 */
	function getListQuery()
	{
		// Add filter data for filter state
		$this->addFilterStateData();

		$param = JComponentHelper::getParams('com_redproductfinder');

		$searchBy = $param->get("search_relation");

		switch ($searchBy)
		{
			case "or":
				return $this->getListQueryByOr($param);
			break;
			default:
				return $this->getListQueryByAnd($param);
			break;
		}
	}

	/**
	 * Get List from product search by OR
	 *
	 * @param   int  $param  search relation id
	 *
	 * @return array
	 */
	public function getListQueryByOr($param)
	{
		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');

		// Session filter
		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		$searchByComp = $param->get('search_by');

		// Filter by cid
		$cid = $this->getState("catid");

		// Filter by manufacturer_id
		$manufacturerId = $pk["manufacturer_id"];

		// Filter by filterprice
		if (isset($pk["filterprice"]))
		{
			$filter = $pk["filterprice"];
		}

		$orderBy = $this->getState('order_by');

		if ($orderBy == 'pc.ordering ASC' || $orderBy == 'c.ordering ASC')
		{
			$orderBy = 'p.product_id DESC';
		}

		$attribute = "";

		if (isset($pk["attribute"]))
		{
			$attribute = $pk["attribute"];
		}

		$view = $this->getState("redform.view");
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (isset($saveFilter))
		{
			// Main query
			$query = $db->getQuery(true);

			$query->select("p.product_id");
			$query->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			$i = 0;
			$j = 0;

			// Begin join query
			foreach ($saveFilter as $type_id => $value)
			{
				$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id');

				foreach ($value as $tag_id => $type_tag)
				{
					$tagId[$j][] = $tag_id;
				}

				foreach ($tagId as $k => $tag)
				{
					if ($k == $i)
					{
						$tagString = implode(',', $tag);
						$arrQuery[] = 'ac_t' . $j . '.tag_id IN (' . $tagString . ")";
						$tagString = implode(' OR ', $arrQuery);
					}
				}

				$i++;
				$j++;
			}

			$query->where($tagString);
		}
		elseif ($searchByComp == 1)
		{
			$query->select("p.product_id")
			->from($db->qn("#__redshop_product", "p"))
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
			->join("LEFT", $db->qn("#__redshop_product_attribute", "pa") . " ON pa.product_id = p.product_id")
			->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp") . " ON pp.attribute_id = pa.attribute_id")
			->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps") . " ON ps.subattribute_id = pp.property_id");

			if (isset($pk['attribute']))
			{
				foreach ($attribute as $k => $value)
				{
					$att[] = $k;
					$pro[] = $value;
				}

				foreach ($pro as $k_p => $v_p)
				{
					if (isset($v_p["subproperty"]))
					{
						foreach ($v_p["subproperty"] as $k_sp => $v_sp)
						{
							foreach ($v_sp as $sp)
							{
								$subName[] = $sp;
							}
						}

						$subString = implode("','", $subName);
						$subQuery = "OR ps.subattribute_color_name IN ('" . $subString . "')";
					}

					unset($v_p["subproperty"]);

					foreach ($v_p as $k_vp => $v_vp)
					{
						$proName[] = $v_vp;
					}
				}

				$attString = implode("','", $att);
				$proString = implode("','", $proName);

				$query->where("( pp.property_name IN ('" . $proString . "') " . $subQuery . ")");
			}
		}
		elseif ($searchByComp == 0)
		{
			$query->select("a.product_id")
			->from($db->qn("#__redproductfinder_associations", "a"))
			->join("LEFT", $db->qn("#__redproductfinder_association_tag", "at") . " ON a.id = at.association_id")
			->join("LEFT", $db->qn("#__redshop_product", "p") . " ON a.product_id = p.product_id")
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id");

			// Remove some field
			unset($pk["filterprice"]);
			unset($pk["template_id"]);
			unset($pk["manufacturer_id"]);
			unset($pk["cid"]);

			// Main query
			$query = $db->getQuery(true);

			$query->select("p.product_id");
			$query->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			// Create arrays variable
			$types = array();
			$count = count($pk);
			$j = 0;
			$i = 0;

			if ($pk != null)
			{
				// Get how many type
				$types = array_keys($pk);

				foreach ($types as $k => $type)
				{
					if (isset($pk[$type]['tags']))
					{
						$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id');

						$typeString = implode(',', $pk[$type]["tags"]);

						if (isset($pk[$type]["tags"]))
						{
							$arrQuery[] = 'ac_t' . $j . '.tag_id IN (' . $typeString . ")";
							$tagString = implode(' OR ', $arrQuery);
						}

						$j++;
						$i++;
					}
				}

				$query->where($tagString);
				$query->group($db->qn("p.product_id"));
			}
			else
			{
				if (!isset($filter))
				{
					$query = $db->getQuery(true);
					$query->select("p.product_id")
					->from($db->qn("#__redshop_product", "p"))
					->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id");
				}
			}
		}

		$query->where("p.published = 1")
			->where("p.expired = 0")
			->where("p.product_parent_id = 0")
			->group($db->qn("p.product_id"));

		if (isset($filter))
		{
			// Condition min max
			$min = $filter['min'];
			$max = $filter['max'];

			$priceNormal = $db->qn("p.product_price") . " BETWEEN $min AND $max";
			$priceDiscount = $db->qn("p.discount_price") . " BETWEEN $min AND $max";
			$saleTime = $db->qn('p.discount_stratdate') . ' AND ' . $db->qn('p.discount_enddate');
			$query->where('IF(' . $query->qn('product_on_sale') . ' = 1 && UNIX_TIMESTAMP() BETWEEN ' . $saleTime . ', ' . $priceDiscount . ', ' . $priceNormal . ')');
		}

		if ($cid)
		{
			$query->where($db->qn("cat.category_id") . "=" . $db->q($cid));
		}

		if ($manufacturerId)
		{
			$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturerId));
		}

		if ($orderBy)
		{
			$query->order($db->escape($orderBy));
		}

		return $query;
	}

	/**
	 * Get List from product search by AND
	 *
	 * @param   int  $param  search relation id
	 *
	 * @return array
	 */
	public function getListQueryByAnd($param)
	{
		// Session filter
		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');

		$db = JFactory::getDbo();

		$searchByComp = $param->get('search_by');

		$orderBy = $this->getState('order_by');

		if ($orderBy == 'pc.ordering ASC' || $orderBy == 'c.ordering ASC')
		{
			$orderBy = 'p.product_id DESC';
		}

		// Condition min max price
		$filter = array();

		if (isset($pk["filterprice"]))
		{
			// Filter by filterprice
			$filter = $pk["filterprice"];
			$min = $filter['min'];
			$max = $filter['max'];
		}

		$cid = $this->getState("catid");
		$manufacturerId = $pk["manufacturer_id"];

		if (isset($saveFilter))
		{
			// Main query
			$query = $db->getQuery(true);

			$query->select("p.product_id");
			$query->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			$i = 0;
			$j = 0;

			// Begin join query
			foreach ($saveFilter as $type_id => $value)
			{
				$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id');

				foreach ($value as $tag_id => $type_tag)
				{
					$tagId[$j][] = $tag_id;
				}

				foreach ($tagId as $k => $tag)
				{
					if ($k == $i)
					{
						$tagString = implode(',', $tag);
						$query->where('ac_t' . $j . '.tag_id IN (' . $tagString . ")");
					}
				}

				$i++;
				$j++;
			}
		}
		elseif ($searchByComp == 1)
		{
			// Main query
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select("p.product_id");
			$query->from($db->qn("#__redshop_product", "p"));
			$query->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id");

			// Create arrays variable
			$tables = array();
			$increaseJoin = 0;
			$increaseWhere = 0;
			$atts = array();

			if (isset($pk["attribute"]))
			{
				$attribute = $pk["attribute"];

				// Begin sub query
				foreach ($attribute as $att => $pros)
				{
					$isAtt = $att;

					if (isset($pros["subproperty"]))
					{
						foreach ($pros["subproperty"] as $k_s => $s_n)
						{
							$subName[$k_s] = $s_n;
						}
					}

				unset($pros["subproperty"]);

					// Begin query from
					if (empty($pros))
					{
						foreach ($subName as $k => $sub)
						{
							foreach ($sub as $s)
							{
								$query->join("LEFT", $db->qn("#__redshop_product_attribute", "pa" . $increaseJoin) . " ON p.product_id = pa" . $increaseJoin . ".product_id")
									->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp" . $increaseJoin) . " ON pp" . $increaseJoin . ".attribute_id = pa" . $increaseJoin . ".attribute_id")
									->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps" . $increaseJoin) . " ON pp" . $increaseJoin . ".property_id = ps" . $increaseJoin . ".subattribute_id");

								$increaseJoin++;
							}
						}
					}
					else
					{
						foreach ($pros as $i => $pro)
						{
							if (isset($subName))
							{
								$query->join("LEFT", $db->qn("#__redshop_product_attribute", "pa" . $increaseJoin) . " ON p.product_id = pa" . $increaseJoin . ".product_id")
									->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp" . $increaseJoin) . " ON pp" . $increaseJoin . ".attribute_id = pa" . $increaseJoin . ".attribute_id");

								$increaseJoin++;

								foreach ($subName as $k => $sub)
								{
									foreach ($sub as $s)
									{
										if ($k == $pro)
										{
											$query->join("LEFT", $db->qn("#__redshop_product_attribute", "pa" . $increaseJoin) . " ON p.product_id = pa" . $increaseJoin . ".product_id")
												->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp" . $increaseJoin) . " ON pp" . $increaseJoin . ".attribute_id = pa" . $increaseJoin . ".attribute_id")
												->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps" . $increaseJoin) . " ON pp" . $increaseJoin . ".property_id = ps" . $increaseJoin . ".subattribute_id");

											$increaseJoin++;
										}
									}
								}
							}
							else
							{
								$query->join("LEFT", $db->qn("#__redshop_product_attribute", "pa" . $increaseJoin) . " ON p.product_id = pa" . $increaseJoin . ".product_id")
									->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp" . $increaseJoin) . " ON pp" . $increaseJoin . ".attribute_id = pa" . $increaseJoin . ".attribute_id")
									->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps" . $increaseJoin) . " ON pp" . $increaseJoin . ".property_id = ps" . $increaseJoin . ".subattribute_id");

								$increaseJoin++;
							}
						}
					}

					// Begin query where
					if (empty($pros))
					{
						foreach ($subName as $k => $sub)
						{
							foreach ($sub as $s)
							{
								$query->where('pa' . $increaseWhere . '.attribute_name = ' . $db->q($att))
									->where('pp' . $increaseWhere . '.property_name = ' . $db->q($k))
									->where('ps' . $increaseWhere . '.subattribute_color_name = ' . $db->q($s));

								$increaseWhere++;
							}
						}
					}
					else
					{
						foreach ($pros as $i => $pro)
						{
							if (isset($subName))
							{
								$query->where('pa' . $increaseWhere . '.attribute_name = ' . $db->q($att))
									->where('pp' . $increaseWhere . '.property_name = ' . $db->q($pro));

								$increaseWhere++;

								foreach ($subName as $k => $sub)
								{
									foreach ($sub as $s)
									{
										if ($k == $pro)
										{
											$query->where('pa' . $increaseWhere . '.attribute_name = ' . $db->q($att))
												->where('pp' . $increaseWhere . '.property_name = ' . $db->q($pro))
												->where('ps' . $increaseWhere . '.subattribute_color_name = ' . $db->q($s));

											$increaseWhere++;
										}
									}
								}
							}
							else
							{
								$query->where('pa' . $increaseWhere . '.attribute_name = ' . $db->q($att))
									->where('pp' . $increaseWhere . '.property_name = ' . $db->q($pro))
									->where('ps' . $increaseWhere . '.subattribute_color_name = ' . $db->q($s));

								$increaseWhere++;
							}
						}
					}
				}
			}
		}
		elseif ($searchByComp == 0)
		{
			// Remove some field
			unset($pk["filterprice"]);
			unset($pk["template_id"]);
			unset($pk["manufacturer_id"]);
			unset($pk["cid"]);

			// Main query
			$query = $db->getQuery(true);

			$query->select("p.product_id");
			$query->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->join("LEFT", $db->qn("#__redproductfinder_associations", "ac") . " ON p.product_id = ac.product_id");

			// Create arrays variable
			$types = array();
			$count = count($pk);
			$j = 0;
			$i = 0;

			if ($pk != null)
			{
				// Get how many type
				$types = array_keys($pk);

				foreach ($types as $k => $type)
				{
					if (isset($pk[$type]['tags']))
					{
						$query->join("LEFT", $db->qn('#__redproductfinder_association_tag', 'ac_t' . $i) . ' ON ac.id = ac_t' . $i . '.association_id');

						$typeString = implode(',', $pk[$type]["tags"]);

						if (isset($pk[$type]["tags"]))
						{
							$query->where('ac_t' . $j . '.tag_id IN (' . $typeString . ")");
						}

						$j++;
						$i++;
					}
				}
			}
		}

		$query->where("p.published = 1")
			->where("p.expired = 0")
			->where("p.product_parent_id = 0")
			->group("p.product_id");

		if ($filter)
		{
			$priceNormal = $db->qn("p.product_price") . " BETWEEN $min AND $max";
			$priceDiscount = $db->qn("p.discount_price") . " BETWEEN $min AND $max";
			$saleTime = $db->qn('p.discount_stratdate') . ' AND ' . $db->qn('p.discount_enddate');
			$query->where('IF(' . $query->qn('product_on_sale') . ' = 1 && UNIX_TIMESTAMP() BETWEEN ' . $saleTime . ', ' . $priceDiscount . ', ' . $priceNormal . ')');
		}

		if ($cid)
		{
			$query->where($db->qn("cat.category_id") . "=" . $db->q($cid));
		}

		if ($manufacturerId)
		{
			$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturerId));
		}

		if ($orderBy)
		{
			$query->order($db->escape($orderBy));
		}

		return $query;
	}

	/**
	 * Get Item from category view
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getItem($pk = null)
	{
		$query = $this->getListQuery();
		$db = JFactory::getDbo();
		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$templateDesc = $this->getState('templateDesc');

		if ($templateDesc)
		{
			if (strstr($templateDesc, "{pagination}"))
			{
				$db->setQuery($query, $start, $limit);
			}
			else
			{
				$db->setQuery($query);
			}
		}
		else
		{
			$db->setQuery($query);
		}

		$data = $db->loadAssocList();

		$temp = array();

		foreach ($data as $k => $value)
		{
			$temp[] = $value["product_id"];
		}

		return $temp;
	}

	/**
	 * Get pagination.
	 *
	 * @return pagination
	 */
	public function getPagination()
	{
		$endlimit          = $this->getState('list.limit');
		$limitstart        = $this->getState('list.start');
		$this->pagination = new JPagination($this->getTotal(), $limitstart, $endlimit);

		return $this->pagination;
	}

	/**
	 * Get total.
	 *
	 * @return total
	 */
	public function getTotal()
	{
		$query        = $this->getListQuery();
		$this->total = $this->_getListCount($query);

		return $this->total;
	}
}
