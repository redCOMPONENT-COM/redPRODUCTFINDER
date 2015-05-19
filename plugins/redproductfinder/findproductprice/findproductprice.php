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

		$min = $filter["min"];
		$max = $filter["max"];

		$query = $db->getQuery(true);
		$query->select("product_id")
			->from("#__redshop_product")
			->where($db->qn("product_price") . " >= " . $min)
			->where($db->qn("product_price") . " <= " . $max);

		$db->setQuery($query);

		$results = $db->loadAssocList();

		$productFromTag = array();
		$productFromPrice = array();

		foreach ($data as $k => $value)
		{
			$productFromTag[] = $value["product_id"];
		}

		foreach ($results as $k => $value)
		{
			$productFromPrice[] = $value["product_id"];
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

	public function onFilterByCategory($data, $cid)
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
			$query->select($db->qn("product_id"))
				->from($db->qn("#__redshop_product_category_xref", "cat"))
				->where($db->qn("category_id") . "=" . $db->q($cid));

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
