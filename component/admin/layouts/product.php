<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$products = $displayData["products"];
$selected = $displayData["selected"];
$product_id = $displayData["product_id"];
$producthelper = $displayData["producthelper"];

echo JHtml::_('redshopselect.search', $producthelper->getProductById($product_id), 'product_id',
	array(
		'select2.options' => array('multiple' => 'false', 'placeholder' => JText::_('COM_REDPRODUCTFINDER_MODELS_FORMS_ASSOCIATION_PRODUCT_ID_LABEL')),
		'option.key' => 'product_id',
		'option.text' => 'product_name',
		'select2.ajaxOptions' => array('product_id:' . $product_id)
	)
);
