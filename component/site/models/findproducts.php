<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

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

		$this->setState('catid', $pk['cid']);

		$this->setState('redform.data', $pk);

		$order_by = $app->input->getString('order_by', '');

		$this->setState('order_by', $order_by);

		$params = $app->getParams();

		$this->setState('params', $params);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('t.*')
			->from($db->qn('#__redshop_template', 't'))
			->where('t.template_section = ' . $db->q('redproductfinder'))
			->where('t.published = 1');

		$templateDesc = null;

		if ($template = $db->setQuery($query)->loadObject())
		{
			$redTemplate = new Redtemplate;
			$templateDesc = $redTemplate->readtemplateFile($template->template_section, $template->template_name);
		}

		$this->setState('templateDesc', $templateDesc);

		$limit = $input->get("limit", null);


			$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			$limit = $value;


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
		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');
		$param = JComponentHelper::getParams('com_redproductfinder');
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
			$query->where($db->qn("p.product_price") . " BETWEEN $min AND $max");
		}

		// Filter by cid
		$cid = $pk["cid"];
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
			$query = $db->getQuery(true);
			$query->select("p.product_id")
			->from($db->qn("#__redshop_product", "p"))
			->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON " . "p.product_id = cat.product_id")
			->where("p.published=1")
			->group($db->qn("p.product_id"));
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
	 * get pagination.
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
	 * get total.
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
