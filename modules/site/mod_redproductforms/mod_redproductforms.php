<?php
// no direct access

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/helper.php';

$lists = ModRedproductForms::getList($params);

require(JModuleHelper::getLayoutPath('mod_redproductforms'));



