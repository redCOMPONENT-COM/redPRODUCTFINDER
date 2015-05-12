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

/**
 * redPRODUCTFINDER Controller
 */
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

	   	$html = $layout->render(
	   		array(
	   		)
	   	);

	   	echo $html;
	}
}

?>