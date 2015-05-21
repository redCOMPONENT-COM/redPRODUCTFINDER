<?php
/**
 * @package    RedPRODUCTFINDER.Plugin
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_SITE . '/components/com_redshop/helpers');

/**
 * Plugin find product price
 *
 * @package  RedPRODUCTFINDER.Plugin
 *
 * @since    2.0
 */
class PlgRedProductfinderFindProductPrice extends JPlugin
{
	/**
	 * This method will filter data list with min or max value of data
	 *
	 * @param   array    $data       Default data is array
	 * @param   array    $filter     Add data array to filter
	 * @param   boolean  $hasKeyTag  Default data is false boolean
	 *
	 * @return array
	 */
	public function onFilterByPrice($data = array(), $filter = array(), $hasKeyTag = false)
	{
		$db = JFactory::getDbo();
		$producthelper = new producthelper;

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
		foreach ($results as $k => $product)
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

	/**
	 * This method will filter product by category id and manufacturer id
	 *
	 * @param   array   $data             value shall be array
	 * @param   number  $cid              default value is 0
	 * @param   number  $manufacturer_id  default value is 0
	 *
	 * @return unknown
	 */
	public function onFilterByCategory($data = array(), $cid = 0, $manufacturer_id = 0)
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

			$results = $db->loadColumn();

			// Intersect
			$intersects = array_intersect($products, $results);

			return $intersects;
		}
	}

	/**
	 * This method only filter product by manufacturer id
	 *
	 * @param   array   $data             default value is array
	 * @param   number  $manufacturer_id  default value is number
	 *
	 * @return array
	 */
	public function onFilterByManufacturers($data = array(), $manufacturer_id = 0)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if (intval($manufacturer_id) == 0)
		{
			return $data[0];
		}
		else
		{
			$products = $data[0];

			// Query product from xref
			$query->select($db->qn("p.product_id"))
				->from($db->qn("#__redshop_product", "p"))
				->where($db->qn("p.manufacturer_id") . "=" . $db->q($manufacturer_id));

			$db->setQuery($query);

			$results = $db->loadColumn();

			// Intersect
			$intersects = array_intersect($products, $results);

			return $intersects;
		}
	}
}
