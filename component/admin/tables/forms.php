<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 */
class TableForms extends JTable {
	/** @var int Primary key */
	var $id = null;
	/** @var string The IP address or range to block */
	var $formname = null;
	/** @var string Whether or not the entry is published */
	var $published = 0;
	/** @var string Whether or not the competition name is shown */
	var $showname = null;
	/** @var string CSS classname to allow individual styling */
	var $classname = null;
	/** @var string Whether or not the entry is dependency */
	var $dependency = 0;
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct('#__redproductfinder_forms', 'id', $db );
	}
}
?>