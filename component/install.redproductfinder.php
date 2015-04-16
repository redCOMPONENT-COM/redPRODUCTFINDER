<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Installation file
 */

/* ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

function com_install() {
	$db = JFactory::getDBO();

	/* Get the current columns */
	$q = "SHOW COLUMNS FROM #__redproductfinder_forms ";
	$db->setQuery($q);
	$cols = $db->loadObjectList('Field');

	/* Check if we have the dependency column */
	if (!array_key_exists('dependency', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_forms  ADD `dependency` TINYINT(1) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}

	/* Get the current columns */
	$q = "SHOW COLUMNS FROM #__redproductfinder_types";
	$db->setQuery($q);
	$cols = $db->loadObjectList('Field');

	/* Check if we have the form_id column */
	if (!array_key_exists('form_id', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_types ADD COLUMN ".$db->nameQuote('form_id')." INT(11) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}

	/* Check if we have the picker column */
	if (!array_key_exists('picker', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_types ADD COLUMN ".$db->nameQuote('picker')." TINYINT(1) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}
	/* Check if we have the extrafield column */
	if (!array_key_exists('extrafield', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_types ADD COLUMN ".$db->nameQuote('extrafield')." INT(11) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}

	/* Check if we have the aliases column */
	if (array_key_exists('aliases', $cols)) {
		$q = "ALTER TABLE `#__redproductfinder_types` DROP `aliases` ";
		$db->setQuery($q);
		$db->query();
	}

	/* Get the current columns */
	$q = "SHOW COLUMNS FROM #__redproductfinder_association_tag";
	$db->setQuery($q);
	$cols = $db->loadObjectList('Field');

	/* Check if we have the form_id column */
	if (!array_key_exists('quality_score', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_association_tag ADD COLUMN ".$db->nameQuote('quality_score')." int(10) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}else {
		$q = "ALTER IGNORE TABLE #__redproductfinder_association_tag CHANGE `quality_score` `quality_score` int(10) NOT NULL ";
		$db->setQuery($q);
		$db->query();
	}


	/* Get the current columns */
	$q = "SHOW COLUMNS FROM #__redproductfinder_associations";
	$db->setQuery($q);
	$cols = $db->loadObjectList('Field');

	/* Check if we have the aliases column */
	if (!array_key_exists('aliases', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_associations ADD COLUMN ".$db->nameQuote('aliases')." varchar(255) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}

	/* Get the current columns */
	$q = "SHOW COLUMNS FROM #__redproductfinder_tags";
	$db->setQuery($q);
	$cols = $db->loadObjectList('Field');

	/* Check if we have the aliases column */
	if (!array_key_exists('aliases', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_tags ADD COLUMN ".$db->nameQuote('aliases')." varchar(255) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}

	$q = "SHOW COLUMNS FROM #__redproductfinder_filters";
	$db->setQuery($q);
	$cols = $db->loadObjectList('Field');

	/* Check if we have the form_id column */
	if (!array_key_exists('select_name', $cols)) {
		$q = "ALTER IGNORE TABLE #__redproductfinder_filters ADD `select_name` VARCHAR( 255 ) NOT NULL";
		$db->setQuery($q);
		$db->query();
	}


	 /* Install the sh404SEF router files */
	 jimport('joomla.filesystem.file');
	 jimport('joomla.filesystem.folder');
	 $sh404sef = JPATH_SITE.DS.'components'.DS.'com_sh404sef'.DS.'sef_ext';
	 $sh404sefadmin = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_sh404sef';
	 $redadmin = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_redproductfinder'.DS.'extras';
	 /* Check if sh404SEF is installed */
	 if (JFolder::exists($sh404sef)) {
		/* Copy the plugin */
		if(!JFile::copy($redadmin.DS.'sh404sef'.DS.'com_redproductfinder.php', $sh404sef.DS.'com_redproductfinder.php')) {
			echo JText::_('<b>Failed</b> to copy sh404SEF plugin file<br />');
		}
		if(!JFile::copy($redadmin.DS.'sh404sef'.DS.'language'.DS.'com_redproductfinder.php', $sh404sefadmin.DS.'language'.DS.'plugins'.DS.'com_redproductfinder.php')) {
			echo JText::_('<b>Failed</b> to copy sh404SEF plugin language file<br />');
		}
	 }

	 /* Install plugin */
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');
	/* 1. XML file */
	if(!JFile::copy(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_redproductfinder'.DS.'plugins'.DS.'redproductfinder.xm', JPATH_SITE.DS.'plugins'.DS.'content'.DS.'redproductfinder.xml')){
		echo JText::_('<b>Failed</b> to copy plugin xml file<br />');
	}
	/* 2. PHP file */
	if(!JFile::copy(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_redproductfinder'.DS.'plugins'.DS.'redproductfinder.php', JPATH_SITE.DS.'plugins'.DS.'content'.DS.'redproductfinder.php')){
		echo JText::_('<b>Failed</b> to copy plugin php file<br />');
	}

	/* 3. Language files */
	$langfiles = JFolder::files(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_redproductfinder'.DS.'plugins'.DS.'language');
	$basefolder = JPATH_SITE.DS.'language';
	foreach ($langfiles as $key => $langfile) {
		$lang = substr($langfile, 0, 5);
		if (JFolder::exists('language'.DS.$lang)) {
			if(!JFile::copy(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_redproductfinder'.DS.'plugins'.DS.'language'.DS.$langfile, $basefolder.DS.$lang.DS.$langfile)) {
				echo JText::_('<b>Failed</b> to copy language file: '.$langfile.'<br />');
			}
		}
	}

	/* Check if plugin is already installed */
	$q = "SELECT extension_id FROM #__extensions WHERE folder='content' AND element='redproductfinder' limit 0,1";
	$db->setQuery($q);
	$plugin_id = $db->loadResult();

	/* Store the plugin settings */
	$plugin = JTable::getInstance( 'extension' );
	if($plugin_id)
		$plugin->extension_id = $plugin_id;
	$plugin->name = 'Content - redPRODUCTFINDER';
	$plugin->element = 'redproductfinder';
	$plugin->folder = 'content';
	$plugin->type = 'plugin';
	$plugin->ordering = 1;
	$plugin->enabled = 1;

	if (!$plugin->store()) {
		echo JText::_('Plugin install failed:') .$plugin->getError().'<br />';
	}
}
?>
