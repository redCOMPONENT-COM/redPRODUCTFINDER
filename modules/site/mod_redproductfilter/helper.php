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

JLoader::import('redshop.library');
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
class ModRedproductFilter
{
	/**
	 * Method get tags.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function getTagsDetail()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select("tg.tag_name, tg.id as tag_id, t.type_name, t.id as type_id")
			->from($db->qn("#__redproductfinder_tags", "tg"))
			->join("INNER", $db->qn("#__redproductfinder_tag_type", "tt") . " ON tt.tag_id = tg.id")
			->join("LEFT", $db->qn("#__redproductfinder_types", "t") . " ON t.id = tt.type_id");

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * Method get type.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function getType()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("t.*")
			->from($db->qn("#__redproductfinder_types", "t"))
			->where($db->qn("t.published") . " = " . $db->q(1))
			->order($db->qn("ordering"));

		$db->setQuery($query);

		$data = $db->loadObjectList();

		return $data;
	}
}
