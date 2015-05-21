<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Add helper of site
JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');
JLoader::import('helper', JPATH_SITE . '/components/com_redshop/helpers');

require_once JPATH_SITE . '/components/com_redproductfinder/models/forms.php';

/**
 * Show form helper
 *
 * @since  2.0
 */
class ModRedproductForms
{
	/**
	 * Get all tag name from model component
	 *
	 * @param   object  &$params  This is an object from module params
	 *
	 * @return array
	 */
	public static function getList(&$params)
	{
		$id = $params->get("form_id");

		$modelForms = new RedproductfinderModelForms;

		$data = $modelForms->getItem($id);
		$data = RedproductfinderForms::filterForm($data);

		return $data;
	}

	/**
	 * This function will get range price of product from min to max
	 *
	 * @param   number  $cid              Default value is 0
	 * @param   number  $manufacturer_id  Default value is 0
	 *
	 * @return  array
	 */
	public static function getRangeMaxMin($cid = 0, $manufacturer_id = 0)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$min = 0;
		$max = 0;

		$query = $db->getQuery(true);

		if (intval($cid) != 0)
		{
			$query->select($db->qn("cat.product_id"))
				->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON p.product_id = cat.product_id")
				->where($db->qn("cat.category_id") . " = " . $db->q($cid));

			// Filter by manufacture
			if (intval($manufacturer_id) !== 0)
			{
				$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturer_id));
			}
		}
		else
		{
			$query->select($db->qn("product_id"))
				->from($db->qn("#__redshop_product", "p"));

			// Filter by manufacture
			if (intval($manufacturer_id) !== 0)
			{
				$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturer_id));
			}
		}

		$db->setQuery($query);

		$pids = $db->loadAssocList("product_id");

		// Get only productid key
		$pids = array_keys($pids);
		$range = self::getRange($pids);

		return $range;
	}

	/**
	 * This function will help get max and min value on list product price
	 *
	 * @param   array  $pids  default value is array
	 *
	 * @return array
	 */
	public static function getRange($pids = array())
	{
		$max = 0;
		$min = 0;
		$producthelper = new producthelper;
		$allProductPrices = array();

		// Get product price
		foreach ($pids as $k => $id)
		{
			$productprices = $producthelper->getProductNetPrice($id);
			$allProductPrices[] = $productprices['product_price'];
		}

		// Get first value to make sure it won't zero value
		$max = $min = $allProductPrices[0];

		// Loop to check max min
		foreach ($allProductPrices as $k => $value)
		{
			// Check max
			if ($value >= $max)
			{
				$max = $value;
			}

			// Check min
			if ($value <= $min)
			{
				$min = $value;
			}
		}

		$arrays = array(
			"max" => $max + 100,
			"min" => $min
		);

		return $arrays;
	}

	static function getProductTotal($type_id, $tag_id, $catid = 0, $mid = 0)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select($db->qn("a.product_id"))
			->from($db->qn("#__redproductfinder_association_tag", "ta"))
			->join("LEFT", $db->qn("#__redproductfinder_associations", "a") . " ON " . "a.id = ta.association_id")
			->join("LEFT", $db->qn("#__redshop_product", "p") . " ON " . "p.product_id = a.product_id")
			->join("INNER", $db->qn("#__redshop_product_category_xref", "c") . " ON " . "a.product_id = c.product_id")
			->join("LEFT", $db->qn("#__redproductfinder_association_tag", "t1") . " ON " . "t1.association_id=ta.association_id")
			->where("ta.type_id = " . $db->q($type_id))
			->where("a.published = 1")
			->where("p.product_id IS NOT NULL")
			->where("((t1.tag_id = " . $db->q($tag_id) . "))")
			->where("p.published = '1'")
			->group("ta.association_id");

		if($mid > 0)
		{
			$query->where("p.manufacturer_id = '" . $mid . "'");
		}

		if($catid > 0)
		{
			$query->where("c.category_id = '" . $catid . "'");
		}

		$db->setQuery($query);
		$db->query();

		return $db->getNumRows();
	}
}
