<?php
error_reporting(0);
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Frontend file
 */

/**
 */
/* No direct access */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');

// Getting the redshop configuration
if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.cfg.php')){
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_redshop'.DS.'helpers'.DS.'redshop.cfg.php');
	}

/* Require the base controller */
require_once (JPATH_COMPONENT.DS.'controller.php');
JHTML::Stylesheet('redproductfinder.css', 'components/com_redproductfinder/assets/css/');
/* Require specific controller if requested */
if($controller = JRequest::getCmd('controller', 'redproductfinder')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

/* Create the controller */
$classname	= 'RedproductfinderController'.ucfirst($controller);
$controller = new $classname( );

/* Perform the Request task */
$controller->execute(JRequest::getCmd('task', 'redproductfinder'));

/* Redirect if set by the controller */
$controller->redirect();
?>
