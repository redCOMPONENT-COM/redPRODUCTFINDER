<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/helper.php';

$lists = ModRedproductForms::getList($params);

$range = ModRedproductForms::getRangeMaxMin();
$template_id = $params->get("template_id");

require(JModuleHelper::getLayoutPath('mod_redproductforms'));



