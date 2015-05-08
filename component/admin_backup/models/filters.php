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
class RedproductfinderModelFilters extends JModelList {
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	/**
	 * Show all Filter that have been created
	 */
	function getFilters() {
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

	function getPagination() {
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
    * Publish or Unpublish Filters
    */
   function getPublish() {
      global $mainframe;
	  $mainframe = JFactory::getApplication();
      $cids = JRequest::getVar('cid');
      $task = JRequest::getCmd('task');
      $state = ($task == 'publish') ? 1 : 0;
      $user = JFactory::getUser();
      $row = $this->getTable();

      if ($row->Publish($cids, $state, $user->id)) {
         if ($state == 1){
         $mainframe->enqueueMessage(JText::_('Filters have been published'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
         }
         else{
         $mainframe->enqueueMessage(JText::_('Filters have been unpublished'));
          $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
         }
      }
      else {
         if ($state == 1) {
         $mainframe->enqueueMessage(JText::_('Filters could not be published'));
          $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
         }
         else {
         $mainframe->enqueueMessage(JText::_('Filters could not be unpublished'));
          $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
         }
      }
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
    * Save a Filter
    */
   function SaveFilter() {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $db = JFactory::getDBO();
      $row = $this->getTable();

	 /* Get the posted data */
	 $post = JRequest::get('post');
	 if(!strstr($post['filter_name'],'rp_'))
	 	$post['filter_name'] = 'rp_'.$post['filter_name'];

	 $query = "SELECT id FROM #__redproductfinder_filters WHERE id!=".$post['id']." AND filter_name='".$post['filter_name']."'";
	 $db->setQuery($query);

	 if($db->loadResult())
	 {
		 $mainframe->enqueueMessage(JText::_('Filter name already exits'),'error');
         return false;
	 }
	 else
	 {
	 	if(isset($post['tag_id']))
		 $post['tag_id'] = implode(",",$post['tag_id']);
		 $row->load($post['id']);
		 if (empty($row->ordering)) $post['ordering'] = $row->getNextOrder();
	      if (!$row->bind($post)) {
	       	 $mainframe->enqueueMessage(JText::_('There was a problem binding the filter data'),'error');
	         return false;
	      }

	      /* pre-save checks */
	      if (!$row->check()) {
	         $mainframe->enqueueMessage(JText::_('There was a problem checking the filter data'),'error');
	         return false;
	      }

	      /* save the changes */
	      if (!$row->store()) {
	         $mainframe->enqueueMessage(JText::_('There was a problem storing the tag data'),'error');
	         return false;
	      }
	      $row->checkin();
      	  $mainframe->enqueueMessage(JText::_('The Filter has been saved'),'message');
      	  return $row;
	 }
   }

   /**
    * Delete a Filter
    */
   function getRemoveFilter() {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $database =& JFactory::getDBO();
      $cid = JRequest::getVar('cid');
      JArrayHelper::toInteger( $cid );

      if (!is_array( $cid ) || count( $cid ) < 1) {
         $mainframe->enqueueMessage(JText::_('No filter found to delete'));
         return false;
      }
      if (count($cid)) {
         $cids = 'id=' . implode( ' OR id=', $cid );
         $query = "DELETE FROM #__redproductfinder_filters"
         . "\n  WHERE ( $cids )";
         $database->setQuery( $query );
         if (!$database->query()) {
            $mainframe->enqueueMessage(JText::_('A problem occured when deleting the filter'));
         }
         else {
            if (count($cid) > 1){
            $mainframe->enqueueMessage(JText::_('Filters have been deleted'));
             $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
            }
            else {
            $mainframe->enqueueMessage(JText::_('Filter has been deleted'));
             $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
            }
         }
      }
   }

   /**
    * Reorder tags
	*/
	function getSaveOrder() {
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$order = JRequest::getVar('order');
		$total = count($cid);
		$row = $this->getTable();

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		// update ordering values
		for ($i = 0; $i < $total; $i++) {
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}
		 $mainframe->Redirect('index.php?option=com_redproductfinder&task=filters&controller=filters');
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