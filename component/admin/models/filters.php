<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 *
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Association controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 *
 * @since    2.0
 */
class RedproductfinderModelFilters extends RModelList
{
   /**
    * Retrieve a Filter to edit
    */
    function getFilter($id)
    {
    	$db = JFactory::getDBO();
    	$query = $db->getQuery(true);

    	$query->select("f.*")
    	->from("#__redproductfinder_filters f")
    	->where($db->qn("id") . " = " . $db->q($id));

    	$db->setQuery($query);

    	return $db->loadAssoc();
   }

	/**
	 * Get the list of selected type names for this tag
	 */
	public function getTags($type_id) {
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');
		$q = "SELECT tag_id, tag_name
			FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
			WHERE j.tag_id = t.id AND j.type_id=".$type_id;
		$db->setQuery($q);

		$list = $db->loadObjectList();
		return $list;
	}

	/**
	 * Show all tag
	 */
	public function getTypes() {
		$db = JFactory::getDBO();

		/* Get all the fields based on the limits */
		$query = "SELECT id,type_name FROM #__redproductfinder_types
				ORDER BY ordering";

		$db->setQuery($query);
		return $db->loadObjectlist();
	}
	/**
	 * Show tag name
	 */
	public function getTagname($tag_ids = '')
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if (!empty($tag_ids))
		{
			$query->select("tag_name")
			->from("#__redproductfinder_tags");

			$tagsArray = explode(",", $tag_ids);
			$tags = array();

			foreach ($tagsArray as $k => $value)
			{
				$arr = explode(".", $value);
				$tags[] = $arr[1];
			}

			$tag_ids = implode(",", $tags);

			// Add where query
			$query->where("id IN (" . $tag_ids . ")");

			$db->setQuery($query);

			$result = $db->loadAssocList();
		}
		else
		{
			$result = array();
		}

		$return = array();

		$str	= "";

		if (count($result) > 0)
		{
			foreach ($result as $k => $r)
			{
				$return[] = $r["tag_name"];
			}

			$str = implode(",", $return);
		}

		return $str;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Get filter state - do it later
		$state = "1";

		$query->select("f.*")
		->from($db->qn("#__redproductfinder_filters") . " f")
		->group($db->qn("f") . "." . $db->qn("ordering"))
		->group($db->qn("f") . "." . $db->qn("id"));

		if ($state == "-2")
		{
			$query->where($db->qn("f") . "." . $db->qn("published") . "=" . $db->qn("-2"));
		}
		else
		{
			$query->where($db->qn("f") . "." . $db->qn("published") . "!=" . $db->q("-2"));
		}

		return $query;
	}
}
?>