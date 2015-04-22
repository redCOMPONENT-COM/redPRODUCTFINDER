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
class RedproductfinderTableTag extends JTable
{
	/** @var int Primary key */
	var $id = null;

	/** @var string Whether or not a tag is published */
	var $published = null;

	var $publish_up = null;

	var $publish_down = null;

	/** @var string Whether or not a tag is checked out */
	var $checked_out = null;

	/** @var string When a tag is checked out */
	var $checked_out_time = null;

	/** @var integer The order of the tag */
	var $ordering = 0;

	/** @var integer The order of the tag */
	var $type_id = 0;

	/** @var string Tag type 1 */
	var $tag_name = null;

	/** @var varchar of the free text */
	var $aliases = null;

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct('#__redproductfinder_tags', 'id', $db );
	}
}
?>