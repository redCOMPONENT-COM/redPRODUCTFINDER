<?php
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 *
 * Types table
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 */
class TableTypes extends JTable {
	/** @var int Primary key */
	var $id = null;
	/** @var string Whether or not a tag is published */
	var $published = null;
	/** @var string Whether or not a tag is checked out */
	var $checked_out = null;
	/** @var string When a tag is checked out */
	var $checked_out_time = null;
	/** @var integer The order of the tag */
	var $ordering = 0;
	/** @var string Type name */
	var $type_name = null;
	/** @var string Type selection box */
	var $type_select = null;
	/** @var string Tooltip to show with type */
	var $tooltip = null;
	/** @var int ID of the form the type belongs to */
	var $form_id = null;
	var $picker = null;
	var $extrafield = null;
	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct('#__redproductfinder_types', 'id', $db );
	}
}
?>
