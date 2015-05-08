<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redFORM model
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.model' );

/**
 * redFORM Model
 */
class RedproductfinderModelForms extends JModelList
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
	 * Show all orders for which an invitation to fill in
	 * a testimonal has been sent
	 */
	function getForms()
	{
		/* Get all the orders based on the limits */
		$query = "SELECT *
				FROM #__redproductfinder_forms";
		return $this->_getList($query, $this->_limitstart, $this->_limit);
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
    * Retrieve a competition to edit
    */
   function getForm()
   {
      $row = $this->getTable();
      $my = JFactory::getUser();
      $id = JRequest::getVar('cid', false);

	  if (!$id)
	  {
	  	$id = array(JRequest::getVar('form_id'));
	  }

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

    	echo $query->dump();

    	return $query;
    }
}
?>
