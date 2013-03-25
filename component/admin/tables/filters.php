<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Tags table
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

/**
 */
class TableFilters extends JTable {
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
	/** @var string Tag type 1 */
	var $filter_name = null;
	/** @var varchar of the free text */
	var $type_select = null;
	/** @var varchar of the free text */
	var $tag_id = null;
	/** @var varchar of the free text */
	var $select_name = null;
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct('#__redproductfinder_filters', 'id', $db );
	}
}
?>