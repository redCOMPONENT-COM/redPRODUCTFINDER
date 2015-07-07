<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
JLoader::import('redproductfilter', JPATH_SITE . '/components/com_redproductfinder/helpers');

/**
 * Findproducts Model.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Model
 * @since       2.0
 */
class RedproductfinderModelRedproductfilter extends RModelList
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

		// Add filter data for filter state
		$this->addFilterStateData();

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
		$templateDesc = RedproductfinderFilter::getTemplate($templateId);

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
		$db = JFactory::getDbo();
		$session = JFactory::getSession();
		$saveFilter = $session->get('saveFilter');

		if ($saveFilter)
		{
			foreach ($saveFilter as $type_id => $value)
			{
				$typeId[] = $type_id;

				foreach ($value as $tag_id => $value1)
				{
					$tagId[] = $tag_id;
				}
			}
		}

		$orderBy = $this->getState('order_by');

		if ($orderBy == 'pc.ordering ASC' || $orderBy == 'c.ordering ASC')
		{
			$orderBy = 'p.product_id DESC';
		}

		if ($saveFilter)
		{
			$query = $db->getQuery(true);
			$query->select("p.product_id")
				->from($db->qn("#__redproductfinder_associations", "a"))
				->join("LEFT", $db->qn("#__redproductfinder_association_tag", "at") . " ON a.id = at.association_id")
				->join("LEFT", $db->qn("#__redproductfinder_types", "tp") . " ON tp.id = at.type_id")
				->join("LEFT", $db->qn("#__redproductfinder_tags", "tg") . " ON tg.id = at.tag_id")
				->join("INNER", $db->qn("#__redproductfinder_tag_type", "tt") . " ON tt.tag_id = tg.id and tt.type_id = tp.id")
				->join("LEFT", $db->qn("#__redshop_product", "p") . " ON a.product_id = p.product_id")
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->where("p.published = 1")
				->group($db->qn("p.product_id"));

			$keyTypeString = implode(",", $typeId);
			$keyTagString = implode(",", $tagId);

			$query->where($db->qn("at.type_id") . " IN (" . $keyTypeString . ")")
				->where($db->qn("at.tag_id") . " IN (" . $keyTagString . ")");
		}
		else
		{
			$query = $db->getQuery(true);
			$query->select("p.product_id")
				->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->join("LEFT", $db->qn("#__redshop_product_attribute", "pa") . " ON pa.product_id = p.product_id")
				->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp") . " ON pp.attribute_id = pa.attribute_id")
				->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps") . " ON ps.subattribute_id = pp.property_id")
				->where("p.published = 1")
				->group($db->qn("p.product_id"));
		}

		if ($orderBy)
		{
			$query->order($db->escape($orderBy));
		}

echo $query->dump();

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
