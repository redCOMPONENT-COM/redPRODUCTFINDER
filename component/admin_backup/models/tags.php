<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Tags model
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.model' );

/**
 * Tags Model
 */
class RedproductfinderModelTags extends JModelList
{
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('t.tag_name', 'asc');
	}

	/**
	 * Show all tags that have been created
	 */
	function getTags() {
		$db = JFactory::getDBO();
		$filtertype = JRequest::getInt('filtertype', false);

		/* Get all the fields based on the limits */
		$query = "SELECT t.* FROM #__redproductfinder_tags t
				LEFT JOIN #__redproductfinder_tag_type y
				ON t.id = y.tag_id ";
		if ($filtertype) $query .= "WHERE y.type_id = ".$filtertype." ";
		$query .= " GROUP BY t.id
					ORDER BY t.ordering";
		$db->setQuery($query, $this->_limitstart, $this->_limit);
		return $db->loadObjectList();
	}

   /**
    * Retrieve a tag to edit
    */
   function getTag()
   {
      $row = $this->getTable();
      $my = JFactory::getUser();
      $id = JRequest::getVar('cid');

      /* load the row from the db table */
      $row->load($id[0]);

      if ($id[0])
      {
         // do stuff for existing records
         $result = $row->checkout( $my->id );
      }
      else
      {
         // do stuff for new records
         $row->published    = 1;
      }

      return $row;
   }

	/**
	 * Get the list of selected types for this tag
	 */
	public function getTagTypes($id)
	{
		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->select($db->qn("type_id"))
		->from($db->qn("#__redproductfinder_tag_type"))
		->where($db->qn("tag_id") . " = " . $db->q($id));

		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Get the list of selected type names for this tag
	 */
	public function getTagTypeNames() {
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');
		$q = "SELECT tag_id, type_name
			FROM #__redproductfinder_tag_type j, #__redproductfinder_types t
			WHERE j.type_id = t.id;";
		$db->setQuery($q);
		$list = $db->loadObjectList();
		$sortlist = array();
		foreach ($list as $key => $type) {
			$sortlist[$type->tag_id][] = $type->type_name;
		}
		return $sortlist;
	}

	/**
	 * Show all types
	 */
	public function getTypes() {
		$db = JFactory::getDBO();

		/* Get all the fields based on the limits */
		$query = "SELECT id, type_name FROM #__redproductfinder_types
				ORDER BY type_name";
		$db->setQuery($query, $this->_limitstart, $this->_limit);
		return $db->loadObjectList();
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

		$query->select("t.*")
		->from($db->qn("#__redproductfinder_tags") . " t")
		//->join("LEFT", $db->qn("#__redproductfinder_tag_type") . " y ON t.id = y.tag_id")
		->order($db->qn("t") . "." . $db->qn("id"));

		if ($state == "-2")
		{
			$query->where($db->qn("t") . "." . $db->qn("published") . "=" . $db->qn("-2"));
		}
		else
		{
			$query->where($db->qn("t") . "." . $db->qn("published") . "!=" . $db->q("-2"));
		}

		// Filter by search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(t.tag_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 't.tag_name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		echo $query->dump();

		return $query;
	}
}
?>