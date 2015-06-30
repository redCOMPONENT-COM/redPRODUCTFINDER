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
class RedproductfinderModelTags extends RModelList
{
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	/** @var integer pagination limit starter */
	protected $filterFormName = 'filter_tags';

	/** @var integer pagination limit */
	protected $limitField = 'tags_limit';

	/**
	 * Constructor
	 *
	 * @param   array  $config  [description]
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$filterFields = array(
					'ordering', 't.ordering'
			);

			$config['filter_fields'] = $filterFields;
		}

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
	 * Show all tags that have been created
	 *
	 * @return Objects
	 */
	function getTags()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$filtertype = JRequest::getInt('filtertype', false);

		/* Get all the fields based on the limits */
		$query->select("t.*");
		$query->from($db->qn("#__redproductfinder_tags", "t"));
		$query->join("LEFT", $db->qn("#__redproductfinder_tag_type", "y") . " ON t.id = y.tag_id");
		$query->where("y.type_id = " . $filtertype);
		$query->group("t.id");
		$query->order("t.ordering");

		$db->setQuery($query, $this->_limitstart, $this->_limit);

		return $db->loadObjectList();
	}

	/**
	 * Retrieve a tag to edit
	 *
	 * @return object
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
	 * Get the list of selected types for this tag
	 *
	 * @param   int  $id  id of tag type element
	 *
	 * @return array
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
	 *
	 * @return array
	 */
	public function getTagTypeNames()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$id = JRequest::getVar('cid');

		$query->select("tag_id, type_name")
			->from($db->qn("#__redproductfinder_tag_type", "j"))
			->from($db->qn("#__redproductfinder_types", "t"))
			->where("j.type_id = t.id");

		$db->setQuery($query);

		$list = $db->loadObjectList();

		$sortlist = array();

		foreach ($list as $key => $type)
		{
			$sortlist[$type->tag_id][] = $type->type_name;
		}

		return $sortlist;
	}

	/**
	 * Show all types
	 *
	 * @return object
	 */
	public function getTypes()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		/* Get all the fields based on the limits */
		$query->select("id, type_name")
			->from("#__redproductfinder_types")
			->order("type_name");

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

		/*
		 * @todo: we will continue make filter by state on next version
		 */
		$state = "1";

		$query->select("t.*")
			->from($db->qn("#__redproductfinder_tags", "t"));

		// Filter by search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(t.tag_name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 't.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
