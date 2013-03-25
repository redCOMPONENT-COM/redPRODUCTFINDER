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
class RedproductfinderModelTags extends JModel {
	/** @var integer Total entries */
	protected $_total = null;
	
	/** @var integer pagination limit starter */
	protected $_limitstart = null;
	
	/** @var integer pagination limit */
	protected $_limit = null;
	   
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
			. "\n FROM #__redproductfinder_tags";
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
    * Publish or Unpublish tags
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
         $mainframe->enqueueMessage(JText::_('Tags have been published'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
         }
         else{
         $mainframe->enqueueMessage(JText::_('Tags have been unpublished'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
         }
      }
      else {
         if ($state == 1){
         $mainframe->enqueueMessage(JText::_('Tags could not be published'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
         }
         else{
         $mainframe->enqueueMessage(JText::_('Tags could not be unpublished'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
         }
      }
   }
   
   /**
    * Retrieve a tag to edit
    */
   function getTag() {
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
    * Save a tag
    */
   function getSaveTag() {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $row = $this->getTable();
	 
	 /* Get the posted data */
	 $post = JRequest::get('post');
	 $row->load($post['id']);
	 if (empty($row->ordering)) $post['ordering'] = $row->getNextOrder();
      if (!$row->bind($post)) {
         $mainframe->enqueueMessage(JText::_('There was a problem binding the tag data'), 'error');
         return false;
      }
	  
      /* pre-save checks */
      if (!$row->check()) {
         $mainframe->enqueueMessage(JText::_('There was a problem checking the tag data'), 'error');
         return false;
      }
    
      /* save the changes */
      if (!$row->store()) {
         $mainframe->enqueueMessage(JText::_('There was a problem storing the tag data'), 'error');
         return false;
      }
	  else {
		  $db = JFactory::getDBO();
		  /* Delete all tag type relations */
		  $q = "DELETE FROM #__redproductfinder_tag_type
		  		WHERE tag_id = ".$row->id;
		  $db->setQuery($q);
		  $db->query();
		  /* Store the tag type relations */
		  $types = JRequest::getVar('type_id');	 
		  if(count($types)>0)
		  {
			  foreach ($types as $key => $type) {
			  	
				$q = "INSERT IGNORE INTO #__redproductfinder_tag_type
				  		VALUES (".$row->id.",".$type.")";
				$db->setQuery($q);
				
				if ($db->query()){
					$q = "UPDATE `#__redproductfinder_association_tag` SET `type_id` = ".$type." WHERE `tag_id` = ".$row->id;
					$db->setQuery($q);
					$db->query();			
				}
				
				
			  }
		  }	  
	  }
	  
      $row->checkin();
      $mainframe->enqueueMessage(JText::_('The tag has been saved'));
	 
      return $row;
   }
   
   /**
    * Delete a tag
    */
   function getRemoveTag() {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $database =& JFactory::getDBO();
      $cid = JRequest::getVar('cid');
      JArrayHelper::toInteger( $cid );
	  
      if (!is_array( $cid ) || count( $cid ) < 1) {
         $mainframe->enqueueMessage(JText::_('No tag found to delete'));
         return false;
      }
      if (count($cid)) {
         $cids = 'id=' . implode( ' OR id=', $cid );
         $query = "DELETE FROM #__redproductfinder_tags"
         . "\n  WHERE ( $cids )";
         $database->setQuery( $query );
         if (!$database->query()) {
            $mainframe->enqueueMessage(JText::_('A problem occured when deleting the tag'));
         }
         else {
            if (count($cid) > 1) {
            $mainframe->enqueueMessage(JText::_('Tags have been deleted'));
            $mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
            }
            else {
            $mainframe->enqueueMessage(JText::_('Tag has been deleted'));
            $mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
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
		$mainframe->Redirect('index.php?option=com_redproductfinder&task=tags&controller=tags');
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
	 * Get all Quality Score values
	 */
	public function getQualityScores() {
		$db = JFactory::getDBO();
		$query = "SELECT CONCAT(association_id, '.', type_id,'.',tag_id) AS qs_id, quality_score 
				FROM #__redproductfinder_association_tag";
		$db->setQuery($query);
		
		return $db->loadAssocList('qs_id');
	}
}
?>