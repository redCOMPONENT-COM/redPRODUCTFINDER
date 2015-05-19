<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/helper.php';

$lists = ModRedproductForms::getList($params);

$range = ModRedproductForms::getRangeMaxMin();
$template_id = $params->get("template_id");
$cid = JFactory::getApplication()->input->get("cid", 0, "INT");
$formid = $params->get("form_id");
$module_class_sfx = $params->get("moduleclass_sfx");

require(JModuleHelper::getLayoutPath('mod_redproductforms'));



