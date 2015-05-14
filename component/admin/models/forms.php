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
class RedproductfinderModelForms extends RModelList
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
		parent::populateState('a.formname', 'asc');
	}

	/**
	 * Method to get the total number of testimonial items for the category
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = "SELECT *"
			. "\n FROM #__redproductfinder_forms";
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
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

    	$query->select("*")
    	->from($db->qn("#__redproductfinder_forms")  . " as a");

    	// Filter by published state
    	$published = $this->getState('filter.published');

    	if ($state == "-2")
    	{
    		$query->where($db->qn("a.published") . "=" . $db->qn("-2"));
    	}
    	else
    	{
    		$query->where($db->qn("a.published") . "!=" . $db->q("-2"));
    	}

    	// Filter by search in formname.
    	$search = $this->getState('filter.search');

    	if (!empty($search))
    	{
    			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
    			$query->where('(a.formname LIKE ' . $search . ')');
    	}

    	// Add the list ordering clause.
    	$orderCol = $this->state->get('list.ordering', 'a.formname');
    	$orderDirn = $this->state->get('list.direction', 'asc');

    	if ($orderCol == 'a.ordering')
    	{
    		$orderCol = 'c.title ' . $orderDirn . ', a.ordering';
    	}

    	$query->order($db->escape($orderCol . ' ' . $orderDirn));

    	return $query;
    }
}
?>
