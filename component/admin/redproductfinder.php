<?php
error_reporting(0);
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 *
 * redPRODUCTFINDER component
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/* Load the necessary stylesheet */
$document = JFactory::getDocument();
$document->addStyleSheet( JURI::root().'administrator/components/com_redproductfinder/helpers/redproductfinder.css' );
$document->addScript( JURI::root().'administrator/components/com_redproductfinder/helpers/redproductfinder.js' );

require_once(JPATH_ROOT.DS.'administrator/components'.DS.'com_redproductfinder'.DS.'helpers'.DS.'redproductfinder.php');
RedproductfinderHelper::addSubmenu('');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');
$controller = JRequest::getCmd('controller', 'redproductfinder');

//set the controller page
if(!file_exists(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.$controller.'.php')){
	$controller='redproductfinder';
	JRequest::setVar('controller','redproductfinder' );
}

// Require specific controller if requested
if($controller) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
}

// Create the controller
$classname	= 'RedproductfinderController'.$controller;
$controller = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar('task', 'redproductfinder'));

// Redirect if set by the controller
$controller->redirect();

?>
