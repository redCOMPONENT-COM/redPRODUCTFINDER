<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

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
	   		$products[] = RedshopHelperProduct::getProductById($value);
	   	}

	   	if ($products === null)
	   	{
	   		$json = json_encode($products);
	   		echo $json;
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