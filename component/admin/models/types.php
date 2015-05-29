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
class RedproductfinderModelTypes extends RModelList {
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	var $_context = null;

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
		parent::populateState('t.type_name', 'asc');
	}

	function _construct()
	{
		parent::__construct();
	}

	/**
	 * Show all types that have been created
	 */
	function getTypes() {
		$db = JFactory::getDBO();

		$form_id =  JRequest::getVar('formfilter',0);
		$filterForm = ($form_id >0) ? " WHERE t.form_id = '".$form_id."' ":"";
		/* Get all the fields based on the limits */
		$query = "SELECT t.*, f.formname AS form_name FROM #__redproductfinder_types t
				LEFT JOIN #__redproductfinder_forms f
				ON t.form_id = f.id $filterForm
				ORDER BY ordering";
		$db->setQuery($query, $this->_limitstart, $this->_limit);
		return $db->loadObjectList();
	}

	function getTypesList()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/* Get all the fields based on the limits */
		$query->select('t.id, t.type_name')
		->from($db->qn('#__redproductfinder_types') . 'as t')
		->where($db->qn('published') . ' = 1');

		$db->setQuery($query);

		return $db->loadAssocList();
	}

   /**
    * Retrieve a type to edit
    */
   function getType() {
      $row = $this->getTable();
      $my = JFactory::getUser();
      $id = JRequest::getVar('cid');

      /* load the row from the db table */
      $row->load($id[0]);
      if ($id[0]) {
         // do stuff for existing records
         $result = $row->checkout( $my->id );
      } else {
         // do stuff for new records
         $row->published    = 1;
      }
      return $row;
   }

	/**
	 * Load a list of forms
	 */
	public function getFormList() {
		$db = JFactory::getDBO();
		$q = "SELECT id, formname FROM #__redproductfinder_forms";
		$db->setQuery($q);
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

		$query->select("t.*, f.formname AS form_name")
		->from($db->qn("#__redproductfinder_types") . " t")
		->join("LEFT", $db->qn("#__redproductfinder_forms") . " f ON t.form_id = f.id")
		->order($db->qn("t") . "." . $db->qn("ordering"));

		if ($state == "-2")
		{
			$query->where($db->qn("t") . "." . $db->qn("published") . "=" . $db->qn("-2"));
		}
		else
		{
			$query->where($db->qn("t") . "." . $db->qn("published") . "!=" . $db->q("-2"));
		}

		// Filter by search in formname.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(t.type_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 't.type_name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
?>