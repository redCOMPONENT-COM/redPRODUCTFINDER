<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('redshop.library');
JLoader::load('RedshopHelperHelper');
JLoader::import('helper', JPATH_SITE . '/modules/mod_redproductfilter');

$db = JFactory::getDBO();

$url = JRequest::getURI();

$input = JFactory::getApplication()->input;

$getItemid = new redhelper;

$templateId = $params->get("template_id");
$Itemid = $getItemid->getItemid();
$view = JFactory::getApplication()->input->get("view");
$option = $input->get("option");
$module_class_sfx = $params->get("moduleclass_sfx");

$cid = 0;
$manufacturer_id = 0;

switch ($option)
{
	case "com_redproductfinder":
			switch ($view)
			{
				case "findproducts":
					$cid = $app->input->get("cid", 0, "INT");
					$manufacturer_id = $app->input->get("manufacturer_id", 0, "INT");
				break;
			}
		break;
	case "com_redshop":
			switch ($view)
			{
				case "category":
					$cid = $app->input->get("cid", 0, "INT");
					$manufacturer_id = $app->input->get("manufacturer_id", 0, "INT");
					break;
				case "manufacturers":
					$params = $app->getParams('com_redshop');
					$manufacturer_id = $params->get("manufacturerid");
					break;
			}
		break;
}

$types = ModRedproductFilter::getType();
$tags = ModRedproductFilter::getTagsDetail();

$act = $input->getString("act");
$tempType = $input->getInt("tempType");
$tempTag = $input->getInt("tempTag");

$session = JFactory::getSession();
$saveFilter = $session->get('saveFilter');

require JModuleHelper::getLayoutPath('mod_redproductfilter');
