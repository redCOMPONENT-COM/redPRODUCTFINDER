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
	 * This method will get filter item on each id
	 *
	 * @param   int  $id  id filter item
	 *
	 * @return Array
	 */
	public function getFilter($id)
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
	 *
	 * @param   int  $typeId  typeid for item
	 *
	 * @return Objects
	 */
	public function getTags($typeId)
	{
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');
		$q = "SELECT tag_id, tag_name
			FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
			WHERE j.tag_id = t.id AND j.type_id=" . $typeId;
		$db->setQuery($q);

		$list = $db->loadObjectList();

		return $list;
	}

	/**
	 * Show all tag
	 *
	 * @return Objects
	 */
	public function getTypes()
	{
		$db = JFactory::getDBO();

		/* Get all the fields based on the limits */
		$query = "SELECT id,type_name FROM #__redproductfinder_types
				ORDER BY ordering";

		$db->setQuery($query);

		return $db->loadObjectlist();
	}

	/**
	 * Show tag name
	 *
	 * @param   string  $tagIds  list string id of tags
	 *
	 * @return string
	 */
	public function getTagname($tagIds = '')
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if (!empty($tagIds))
		{
			$query->select("tag_name")
			->from("#__redproductfinder_tags");

			$tagsArray = explode(",", $tagIds);
			$tags = array();

			foreach ($tagsArray as $k => $value)
			{
				$arr = explode(".", $value);
				$tags[] = $arr[1];
			}

			$tagIds = implode(",", $tags);

			// Add where query
			$query->where("id IN (" . $tagIds . ")");

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
		->group($db->qn("f.ordering"))
		->group($db->qn("f.id"));

		if ($state == "-2")
		{
			$query->where($db->qn("f.published") . "=" . $db->q("-2"));
		}
		else
		{
			$query->where($db->qn("f.published") . "!=" . $db->q("-2"));
		}

		return $query;
	}
}
