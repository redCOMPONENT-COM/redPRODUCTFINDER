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
	public function onFilterByPrice($data, $filter)
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

		// Intersect
		$intersect = array_intersect($productFromTag, $productFromPrice);

		return $intersect;
	}
}
