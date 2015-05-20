<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . "/administrator/components/com_redshop/helpers/redshop.cfg.php";
JLoader::import('redshop.library');
JLoader::load('RedshopHelperUser');
JLoader::import('product', JPATH_SITE . '/libraries/redshop/helper');

class RedproductfinderControllerFindproducts extends JControllerForm
{
	function display($cachable = false, $urlparams = array())
	{
		parent::display($cachable, $urlparams);
	}

	function find()
	{
	   	$app		= JFactory::getApplication();
	   	$document 	= JFactory::getDocument();
	   	$input		= $app->input;

	   	$model = JModelLegacy::getInstance("FindProducts", "RedproductfinderModel");

	   	$layout = new JLayoutFile('result', JPATH_COMPONENT . '/layouts');

	   	$post = $input->post->get('redform', array(), 'filter');

	   	$model->setState("redform.data", $post);

	   	$list = $model->getItem();

	   	// Get all product from here
	   	foreach ( $list as $k => $value )
	   	{
	   		$products[] = $value;
	   	}

	   	if (count($products) != 0)
	   	{
	   		// Get layout HTML
	   		$html = $layout->render(
	   			array(
	   				"products" => $products,
	   				"post"	   => $post,
	   				"template_id" => $post["template_id"]
	   			)
	   		);

	   		echo $html;
	   		die;
	   	}
	   	else
	   	{
	   		echo "false";
	   		die;
	   	}
	}
}

?>