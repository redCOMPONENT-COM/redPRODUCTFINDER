<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once( dirname(__FILE__).DS.'helper.php' );

$form	           = $params->get('form');
$type	           = $params->get('type');
$buttonname	      = $params->get('submit', 'Search');
//$buttonname	      = $params->get('buttonname', 'Search');
$moduleclass_sfx     = $params->get('moduleclass_sfx');
$show_searchcriteria = $params->get('show_searchcriteria');
$show_productprice   = $params->get('show_productprice');
$show_type           = $params->get('show_type');
$pretext             = $params->get('pretext');
$pretext_link        = $params->get('pretext_link');
$pretext_url         = $params->get('pretext_url');
$itemid         = $params->get('itemid');
//Get fields to filter
$types = modRedproductfinderHelper::getTypes($type, $form);
JHTML::StyleSheet('style.css', 'modules/mod_redproductfinder/css/',false);
require(JModuleHelper::getLayoutPath('mod_redproductfinder'));
?>
