<?php
/**
 * @package     RedITEM
 * @subpackage  Plugin.Reditem
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Plugin of Log
 *
 * @package  RedITEM.Plugin
 *
 * @since    2.1.4
 */
require_once(JPATH_SITE . '/components/com_redshop/helpers/helper.php');

class PlgRedProductfinderFindProductPrice extends JPlugin
{
	/**
	 * event "onAfterItemRated"
	 *
	 * @param   int     $itemId  Id of item
	 * @param   float   $value   Rate value
	 * @param   object  $rater   JUser object of rater
	 *
	 * @return  bool         True on success. False otherwise.
	 */

	public function onFilterByPrice($data, $filter, $hasKeyTag)
	{
		$db = JFactory::getDbo();
		$producthelper = new producthelper();

		$min = $filter["min"];
		$max = $filter["max"];

		$query = $db->getQuery(true);
		$query->select("product_id")
			->from("#__redshop_product");

		$db->setQuery($query);

		// Filter by min max
		$results = $db->loadAssocList("product_id");

		$allProductOnRange = array();
		$allProductPrices = array();

		// Get Net price and check min max price
		foreach($results as $k => $product)
		{
			$productprices = $producthelper->getProductNetPrice($k);

			$allProductPrices[$k] = $productprices;

			if ($productprices["product_price"] >= $min && $productprices["product_price"] <= $max)
			{
				$allProductOnRange[] = $k;
			}
		}

		$productFromTag = array();
		$productFromPrice = array();

		// Filter and get from data
		foreach ($data as $k => $value)
		{
			$productFromTag[] = $value["product_id"];
		}

		// Filter and get from Range price
		foreach ($allProductOnRange as $k => $value)
		{
			$productFromPrice[] = $value;
		}

		if ($hasKeyTag == true)
		{
			// Intersect
			$intersect = array_intersect($productFromTag, $productFromPrice);

			return $intersect;
		}
		else
		{
			return $productFromPrice;
		}
	}

	public function onFilterByCategory($data, $cid, $manufacturer_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (intval($cid) === 0)
		{
			return $data[0];
		}
		else
		{
			$products = $data[0];

			// Query product from xref
			$query->select($db->qn("cat.product_id"))
				->from($db->qn("#__redshop_product", "p"))
				->join("LEFT", $db->qn("#__redshop_product_category_xref", "cat") . " ON " . "p.product_id=cat.product_id")
				->where($db->qn("cat.category_id") . "=" . $db->q($cid));

			// Filter by manufacture
			if (intval($manufacturer_id) !== 0)
			{
				$query->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturer_id));
			}

			$db->setQuery($query);

			$results = $db->loadAssocList("product_id");

			// Get only keys value
			$results = array_keys($results);

			// Intersect
			$intersects = array_intersect($products, $results);

			return $intersects;
		}
	}
}
