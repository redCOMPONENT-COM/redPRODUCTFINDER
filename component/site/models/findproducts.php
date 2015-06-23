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

		$order_by = $app->input->getString('order_by', '');

		$this->setState('order_by', $order_by);

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
				$limit = $cat->products_per_page;
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
	 * Get List from product
	 *
	 * @return array
	 */
	function getListQuery()
	{
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

	public function getListQueryByOr($param)
	{
		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');
		
		$search_by_comp = $param->get('search_by');
		
		$module = JModuleHelper::getModule('mod_redproductforms');
		$headLineParams = new JRegistry($module->params);
		$search_by_module = $headLineParams->get('search_by');
		
		$order_by = $this->getState('order_by');
		
		if ($order_by == 'pc.ordering ASC' || $order_by == 'c.ordering ASC')
		{
			$order_by = 'p.product_id DESC';
		}
		
		$attribute = $pk["properties"];
		
		if ($attribute != 0)
		{
			$properties = implode("','", $attribute);
		}
		
		$view = $this->getState("redform.view");
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if ($search_by_comp == 1)
		{
			$query->select("p.product_id")
			->from($db->qn("#__redshop_product") . " AS p")
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON " . "p.product_id = cat.product_id")
			->join("LEFT", $db->qn("#__redshop_product_attribute", "pa") . " ON " . "pa.product_id = p.product_id")
			->join("LEFT", $db->qn("#__redshop_product_attribute_property", "pp") . " ON " . "pp.attribute_id = pa.attribute_id")
			->join("LEFT", $db->qn("#__redshop_product_subattribute_color", "ps") . " ON " . "ps.subattribute_id = pp.property_id")
			->where("p.published=1")
			->group($db->qn("p.product_id"));
		}
		
		elseif ($search_by_comp == 0)
		{
			$query->select("a.product_id")
			->from($db->qn("#__redproductfinder_associations") . " AS a")
			->join("LEFT", $db->qn("#__redproductfinder_association_tag") . " AS at ON a.id = at.association_id")
			->join("LEFT", $db->qn("#__redproductfinder_types") . " AS tp ON tp.id = at.type_id")
			->join("LEFT", $db->qn("#__redproductfinder_tags") . " AS tg ON tg.id = at.tag_id")
			->join("INNER", $db->qn("#__redproductfinder_tag_type") . " AS tt ON tt.tag_id = tg.id and tt.type_id = tp.id")
			->join("LEFT", $db->qn("#__redshop_product") . " AS p ON a.product_id = p.product_id")
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON " . "p.product_id = cat.product_id")
			->where("a.published=1")
			->group($db->qn("a.product_id"));
		}
		
		if ($attribute)
		{
			$query->where("(" . $db->qn("pp.property_name") . " IN ('" . $properties . "') OR ps.subattribute_color_name IN ('" . $properties . "'))");
		}
		
		// Condition min max
		$filter = $pk["filterprice"];
		$min = $filter['min'];
		$max = $filter['max'];
		
		if ($filter)
		{
			$priceNormal = $db->qn("p.product_price") . " BETWEEN $min AND $max";
			$priceDiscount = $db->qn("p.discount_price") . " BETWEEN $min AND $max";
			$saleTime = $db->qn('p.discount_stratdate') . ' AND ' . $db->qn('p.discount_enddate');
			$query->where('IF(' . $query->qn('product_on_sale') . ' = 1 && UNIX_TIMESTAMP() BETWEEN ' . $saleTime . ', ' . $priceDiscount . ', ' . $priceNormal . ')');
		}
		
		// Filter by cid
		$cid = $this->getState("catid");
		$manufacturer_id = $pk["manufacturer_id"];
		
		unset($pk["filterprice"]);
		unset($pk["template_id"]);
		unset($pk["manufacturer_id"]);
		unset($pk["cid"]);
		
		// Add tag id
		$keyTags = array();
		
		foreach ( $pk as $k => $value )
		{
			if (!isset($value["tags"]))
			{
				continue;
			}
		
			foreach ( $value["tags"] as $k_t => $tag )
			{
				$keyTags[] = $tag;
			}
		}
		
		if (count($keyTags) != 0)
		{
			// Add type id
			$keyTypes = array_keys($pk);
		
			if ($keyTypes)
			{
				$keyTypeString = implode(",", $keyTypes);
				$query->where($db->qn("at.type_id") . " IN (" . $keyTypeString . ")");
			}
		
			// Remove duplicate tag id
			$keyTags = array_unique($keyTags);
		
			// Add tag id
			$keyTagString = implode(",", $keyTags);
			$query->where($db->qn("at.tag_id") . " IN (" . $keyTagString . ")");
		}
		else
		{
			if (!$filter)
			{
				$query = $db->getQuery(true);
				$query->select("p.product_id")
				->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON " . "p.product_id = cat.product_id")
				->where("p.published=1")
				->group($db->qn("p.product_id"));
			}
		}
		
		if ($cid)
		{
			$query->where($db->qn("cat.category_id") . "=" . $db->q($cid));
		}
		
		if ($manufacturer_id)
		{
			$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturer_id));
		}
		
		if ($order_by)
		{
			$query->order($db->escape($order_by));
		}
		
		return $query;
	}
	
	public function getListQueryByAnd($param)
	{
		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');
		
		$db = JFactory::getDbo();
		$search_by_comp = $param->get('search_by');
		$module = JModuleHelper::getModule('mod_redproductforms');
		$headLineParams = new JRegistry($module->params);
		$search_by_module = $headLineParams->get('search_by');
		$order_by = $this->getState('order_by');
		
		if ($order_by == 'pc.ordering ASC' || $order_by == 'c.ordering ASC')
		{
			$order_by = 'p.product_id DESC';
		}

		// Condition min max price
		$filter = $pk["filterprice"];
		$min = $filter['min'];
		$max = $filter['max'];
		
		$cid = $this->getState("catid");
		$manufacturer_id = $pk["manufacturer_id"];
		
		// Remove some field
		unset($pk["filterprice"]);
		unset($pk["template_id"]);
		unset($pk["manufacturer_id"]);
		unset($pk["cid"]);
		
		// Get how many type
		$types = array_keys($pk);
		
		// Create arrays variable
		$tables = array();
		
		$increase = 0;
		
		// Begin sub query
		foreach($types as $k => $type)
		{
			if (!isset($pk[$type]["tags"])) continue;
			
			foreach ($pk[$type]["tags"] as $i => $tag)
			{
				$tables[$increase]["alias"] = "tbl" . $increase;
				
				// Begin query
				$tables[$increase]["select"] = " ( ";
				$tables[$increase]["select"] .= "
					SELECT ac.product_id, ac_t.type_id `type`, ac_t.tag_id `tag`
					FROM #__redproductfinder_associations  as ac";
				
				$tables[$increase]["select"] .= "
					LEFT JOIN #__redproductfinder_association_tag as ac_t
					ON ac.id = ac_t.association_id";
				
				$tables[$increase]["select"] .= "
					WHERE ac_t.type_id = " . $type . " 
					AND ac_t.tag_id = " . $tag;
				
				$tables[$increase]["select"] .=" GROUP BY `ac`.`product_id` ";
				
				$tables[$increase]["select"] .= "\n ) ";
				
				$tables[$increase]["select"] .= " AS " . $tables[$increase]["alias"];
				// End query
				
				$increase++;
			}
		}
		
		// Main query
		$query = $db->getQuery(true);

		$query->select("p.product_id");
		$query->from("#__redshop_product as p");
		$query->join("LEFT", "#__redshop_product_category_xref as cat ON p.product_id = cat.product_id");
		
		// If has subTable then begin query by subtable
		if (count($tables) > 0)
		{
			// Create subtable
			$subTable = " ( ";
				
			// Begin merger
			if (count($tables) > 1)
			{
				$subTable .= " SELECT " . $tables[0]["alias"] . ".product_id" . "\n";
				$subTable .= " FROM  ";
					
				$subTable .= $tables[0]["select"];
					
				for($i = 1; $i < count($tables); $i++)
				{
					$subTable .= " INNER JOIN ";
					$tables[$i]["select"] .= " ON " . $tables[$i - 1]["alias"] . ".product_id = " . $tables[$i]["alias"] . ".product_id ";
					$subTable .= $tables[$i]["select"];
				}
			}
			else
			{
				$subTable .= " SELECT " . $tables[0]["alias"] . ".product_id" . "\n";
				$subTable .= " FROM  ";
					
				$subTable .= $tables[0]["select"];
			}
				
			$subTable .= " ) ";
			$subTable .= " AS `table`";
			// End subtable intersect
			
			$query->join("INNER", $subTable . " ON `p`.`product_id` = `table`.`product_id` ");
		}
		
		$query->where("p.published=1");
		$query->group("p.product_id");
		
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
		
		if ($manufacturer_id)
		{
			$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturer_id));
		}
		
		if ($order_by)
		{
			$query->order($db->escape($order_by));
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
		$this->_pagination = new JPagination($this->getTotal(), $limitstart, $endlimit);

		return $this->_pagination;
	}

	/**
	 * Get total.
	 *
	 * @return total
	 */
	public function getTotal()
	{
		$query        = $this->getListQuery();
		$this->_total = $this->_getListCount($query);

		return $this->_total;
	}
}
