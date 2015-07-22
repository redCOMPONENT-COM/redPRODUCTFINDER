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
JLoader::load('RedshopHelperAdminStockroom');
JLoader::load('RedshopHelperAdminConfiguration');
JLoader::load('RedshopHelperUser');
JLoader::load('RedshopHelperProduct');
JLoader::load('RedshopHelperHelper');

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

	/**
	 * This method get name of tag
	 *
	 * @param   int  $id  tag id
	 *
	 * @return array
	 */
	public static function getTagName($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("tag_name");
		$query->from("#__redproductfinder_tags");
		$query->where("id = " . $db->q($id));

		$db->setQuery($query);

		// Get tag name
		return $db->loadResult();
	}

	/**
	 * This method get name of type
	 *
	 * @param   int  $id  type id
	 *
	 * @return array
	 */
	public static function getTypeName($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("type_name");
		$query->from("#__redproductfinder_types");
		$query->where("id = " . $db->q($id));

		$db->setQuery($query);

		// Get type name
		return $db->loadResult();
	}
}
