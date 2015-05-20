<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('helper', JPATH_SITE . '/modules/mod_redproductforms');

$lists = ModRedproductForms::getList($params);
$template_id = $params->get("template_id");
$cid = JFactory::getApplication()->input->get("cid", 0, "INT");
$manufacturer_id = JFactory::getApplication()->input->get("manufacturer_id", 0, "INT");
$range = ModRedproductForms::getRangeMaxMin($cid, $manufacturer_id);
$formid = $params->get("form_id");
$module_class_sfx = $params->get("moduleclass_sfx");

require(JModuleHelper::getLayoutPath('mod_redproductforms'));



