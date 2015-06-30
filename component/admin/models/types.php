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
class RedproductfinderModelTypes extends RModelList
{
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	protected $filter_fields = array('ordering', 't.ordering');

	var $_context = null;

	/** @var integer pagination limit starter */
	protected $filterFormName = 'filter_types';

	/** @var integer pagination limit */
	protected $limitField = 'types_limit';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  [description]
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

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
	protected function populateState($ordering = "t.ordering", $direction = "asc")
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		else
		{
			$this->context = $this->context;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Get all types formfilter
	 *
	 * @return Object
	 */
	public function getTypes()
	{
		$db = JFactory::getDBO();

		$form_id = JRequest::getVar('formfilter', 0);
		$filterForm = ($form_id > 0) ? " WHERE t.form_id = '" . $form_id . "' ":"";

		/* Get all the fields based on the limits */
		$query = "SELECT t.*, f.formname AS form_name FROM #__redproductfinder_types t
					LEFT JOIN #__redproductfinder_forms f
					ON t.form_id = f.id $filterForm
					ORDER BY ordering";

		$db->setQuery($query, $this->_limitstart, $this->_limit);

		return $db->loadObjectList();
	}

	/**
	 * Function get type list
	 *
	 * @return Object
	 */
	public function getTypesList()
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
	 *
	 * @return object
	 */
	public function getType()
	{
		$row = $this->getTable();
		$my = JFactory::getUser();
		$id = JRequest::getVar('cid');

		// Load the row from the db table
		$row->load($id[0]);

		if ($id[0])
		{
			// Do stuff for existing records
			$result = $row->checkout($my->id);
		}
		else
		{
			// Do stuff for new records
			$row->published    = 1;
		}

		return $row;
	}

	/**
	 * This method will get all form list from database
	 *
	 * @return object
	 */
	public function getFormList()
	{
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
		->join("LEFT", $db->qn("#__redproductfinder_forms") . " f ON t.form_id = f.id");

		// Filter by search in formname.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(t.type_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 't.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
