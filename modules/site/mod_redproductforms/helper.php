<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Add helper of site
require_once JPATH_SITE . '/administrator/components/com_redshop/helpers/redshop.cfg.php';
require_once JPATH_SITE . '/components/com_redproductfinder/models/forms.php';

JLoader::import('redshop.library');
JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');
JLoader::import('product', JPATH_SITE . '/components/com_redshop/helpers');
JLoader::import('helper', JPATH_SITE . '/components/com_redshop/helpers');
JLoader::import('user', JPATH_SITE . '/components/com_redshop/helpers');

JLoader::load('RedshopHelperAdminStockroom');
JLoader::load('RedshopHelperAdminConfiguration');
JLoader::load('RedshopHelperUser');
JLoader::load('RedshopHelperProduct');


// Define some variable that make show warning error
$Redconfiguration = new Redconfiguration;
$Redconfiguration->defineDynamicVars();

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
		$param = JComponentHelper::getParams('com_redproductfinder');

		if (!empty($pids))
		{
			// Get product price
			$productPrices = $producthelper->getProductsNetPrice($pids);

			if (!empty($productPrices))
			{
				$min = $max = $productPrices[0]['product_price'];

				foreach ($productPrices as $productPrice)
				{
					$max = ($productPrice['product_price'] > $max) ? $productPrice['product_price'] : $max;
					$min = ($productPrice['product_price'] < $min) ? $productPrice['product_price'] : $min;
				}
			}
			else
			{
				$min = $param->get('filter_price_min_value');
				$max = $param->get('filter_price_max_value');
			}
		}

		$arrays = array(
			"max" => $max,
			"min" => $min
		);

		return $arrays;
	}

	/**
	 * This method will get total product that current tag had
	 *
	 * @param   number  $type_id  default value is 0
	 * @param   number  $tag_id   default value is 0
	 * @param   number  $catid    default value is 0
	 * @param   number  $mid      default value is 0
	 *
	 * @return array
	 */
	static function getProductTotal($type_id = 0, $tag_id = 0, $catid = 0, $mid = 0)
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

		if ($mid > 0)
		{
			$query->where("p.manufacturer_id = '" . $mid . "'");
		}

		if ($catid > 0)
		{
			$query->where("c.category_id = '" . $catid . "'");
		}

		$db->setQuery($query);
		$db->query();

		return $db->getNumRows();
	}
}
