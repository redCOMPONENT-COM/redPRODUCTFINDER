<?php
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Un-installation file
 */

/* ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

function com_uninstall(){
	/* Remove sh404SEF plugin */
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	$sh404sef = JPATH_SITE.DS.'components'.DS.'com_sh404sef'.DS.'sef_ext';
	if (JFolder::exists($sh404sef)) {
		JFile::delete(JPATH_SITE.DS.'components'.DS.'com_sh404sef'.DS.'sef_ext'.DS.'com_redproductfinder.php');
		JFile::delete(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_sh404sef'.DS.'language'.DS.'plugins'.DS.'com_redproductfinder.php');
	}
	
	/* Remove the plugin */
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	$database = JFactory::getDBO();
	JFile::delete(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'redproductfinder.xml');
	JFile::delete(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'redproductfinder.php');
	$langfiles = JFolder::files(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_redproductfinder'.DS.'plugins'.DS.'language');
	$basefolder = JPATH_SITE.DS.'language';
	foreach ($langfiles as $key => $langfile) {
		$lang = substr($langfile, 0, 5);
		if (JFolder::exists('language'.DS.$lang)) {
			JFile::delete($basefolder.DS.$lang.DS.$langfile);
		}
	}
	
	$query = "DELETE FROM #__extensions WHERE folder = 'content' AND element = 'redproductfinder'";
	$database->setQuery($query);
	$database->query();
}
?>
