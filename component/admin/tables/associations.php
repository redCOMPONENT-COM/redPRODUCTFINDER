<?php
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 *
 * Products table
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 */
class TableAssociations extends JTable {
	/** @var int Primary key */
	var $id = null;
	/** @var string Whether or not a product is published */
	var $published = null;
	/** @var string Whether or not a product is checked out */
	var $checked_out = null;
	/** @var string When a product is checked out */
	var $checked_out_time = null;
	/** @var integer The order of the product */
	var $ordering = 0;
	/** @var integer The ID of the Redshop product */
	var $product_id = 0;
	/** @var varchar of the free text */
	var $aliases = null;
	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct('#__redproductfinder_associations', 'id', $db );
	}
}
?>