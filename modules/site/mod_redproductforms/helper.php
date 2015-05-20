<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_archive
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_articles_archive
 *
 * @package     Joomla.Site
 * @subpackage  mod_articles_archive
 * @since       1.5
 */

// Add helper of site
JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');

require_once JPATH_SITE . '/components/com_redproductfinder/models/forms.php';
require_once(JPATH_SITE . '/components/com_redshop/helpers/helper.php');

class ModRedproductForms
{
	public static function getList(&$params)
	{
		$id = $params->get("form_id");

		$modelForms = new RedproductfinderModelForms();

		$data = $modelForms->getItem($id);
		$data = redproductfinderForms::filterForm($data);

		return $data;
	}

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
		}

		$db->setQuery($query);

		$pids = $db->loadAssocList("product_id");

		// Get only productid key
		$pids = array_keys($pids);
		$range = self::getRange($pids);

		return $range;
	}

	public static function getRange($pids)
	{
		$max = 0;
		$min = 0;
		$producthelper = new producthelper();
		$allProductPrices = array();

		// Get product price
		foreach($pids as $k => $id)
		{
			$productprices = $producthelper->getProductNetPrice($id);
			$allProductPrices[] = $productprices['product_price'];
		}

		$max = $min = $allProductPrices[0];

		// Loop to check max min
		foreach($allProductPrices as $k => $value)
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
}
