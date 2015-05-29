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
	 * Show all Filter that have been created
	 */
	function getFilters()
	{
		$db = JFactory::getDBO();
		//$filtertype = JRequest::getInt('filtertype', false);

		/* Get all the fields based on the limits */
		$query = "SELECT f.* FROM #__redproductfinder_filters f";
		//if ($filtertype) $query .= "WHERE y.type_id = ".$filtertype." ";
		$query .= " GROUP BY f.id
					ORDER BY f.ordering";

		$db->setQuery($query, $this->_limitstart, $this->_limit);
		return $db->loadObjectList();
	}

	function getPagination()
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		/* Lets load the pagination if it doesn't already exist */
		if (empty($this->_pagination)) {
		jimport('joomla.html.pagination');
		$this->_limit      = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$this->_limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$this->_limitstart = ($this->_limit != 0 ? (floor($this->_limitstart / $this->_limit) * $this->_limit) : 0);

			$this->_pagination = new JPagination( $this->getTotal(), $this->_limitstart, $this->_limit );
				//$mainframe->Redirect('index.php');
		}

		return $this->_pagination;
	}

	/**
	 * Method to get the total number of testimonial items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = "SELECT COUNT(*) AS total"
			. "\n FROM #__redproductfinder_filters";
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

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
	 * Get the list of selected types for this tag
	 */
	public function getTagTypes() {
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');
		$q = "SELECT type_id
			FROM #__redproductfinder_tag_type
			WHERE tag_id = ".$id[0];
		$db->setQuery($q);
		return $db->loadResultArray();
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