<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Types model
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.model' );

/**
 * Types Model
 */
class RedproductfinderModelTypes extends JModelList {
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	var $_context = null;

	function _construct()
	{
		parent::__construct();

		$this->_context='formfilter';

		$formfilter = $mainframe->getUserStateFromRequest( $this->_context.'formfilter',  'formfilter', 0 );

		$this->setState('formfilter', $formfilter);
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
			. "\n FROM #__redproductfinder_types";
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
    * Publish or Unpublish types
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
         if ($state == 1) {
         $mainframe->enqueueMessage(JText::_('Types have been published'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
         }
         else{
         $mainframe->enqueueMessage(JText::_('Types have been unpublished'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
         }

      }
      else {
         if ($state == 1){
         $mainframe->enqueueMessage(JText::_('Types could not be published'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
         }
         else{
         $mainframe->enqueueMessage(JText::_('Types could not be unpublished'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
         }
      }
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
    * Save a type
    */
	function getSaveType() {
     global $mainframe;
     $mainframe = JFactory::getApplication();
     $row = $this->getTable();

	 /* Get the posted data */
	 $post = JRequest::get('post');
	 if (isset($post['id'])) $row->load($post['id']);
	 if (empty($row->ordering)) $post['ordering'] = $row->getNextOrder();
     if (!$row->bind($post)) {
         $mainframe->enqueueMessage(JText::_('There was a problem binding the type data'), 'error');
         return false;
      }

      /* pre-save checks */
      if (!$row->check()) {
         $mainframe->enqueueMessage(JText::_('There was a problem checking the type data'), 'error');
         return false;
      }

      /* save the changes */
      if (!$row->store()) {
         $mainframe->enqueueMessage(JText::_('There was a problem storing the type data'), 'error');
         return false;
      }

      $row->checkin();
      $mainframe->enqueueMessage(JText::_('The type has been saved'));

      return $row;
   }

   /**
    * Delete a type
    */
   function getRemoveType() {
      $database =& JFactory::getDBO();
      $cid = JRequest::getVar('cid');
      JArrayHelper::toInteger( $cid );
	  $mainframe = Jfactory::getApplication('site');

      if (!is_array( $cid ) || count( $cid ) < 1) {
         $mainframe->enqueueMessage(JText::_('No type found to delete'));
         return false;
      }
      if (count($cid)) {
         $cids = 'id=' . implode( ' OR id=', $cid );
         $query = "DELETE FROM #__redproductfinder_types"
         . "\n  WHERE ( $cids )";
         $database->setQuery( $query );
         if (!$database->query()) {
            $mainframe->enqueueMessage(JText::_('A problem occured when deleting the type'));
         }
         else {
            if (count($cid) > 1) {
            $mainframe->enqueueMessage(JText::_('Types have been deleted'));
            $mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
            }
            else{
            $mainframe->enqueueMessage(JText::_('Type has been deleted'));
            $mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
            }

            /* Delete the relationship with the tag */
            $typecids = 'type_id=' . implode( ' OR type_id=', $cid );
            $query = "DELETE FROM #__redproductfinder_tag_type"
				. "\n  WHERE ( $typecids )";
			$database->setQuery( $query );
			$database->query();

			$query = "SELECT * FROM #__redproductfinder_tags WHERE id NOT IN (
				SELECT id FROM #__redproductfinder_tags a
				RIGHT JOIN #__redproductfinder_tag_type b
				ON a.id = b.tag_id
				)";
			$database->setQuery($query);
			$missing = $database->loadResultArray();
			if (count($missing) > 0) {
				$tagcids = 'id=' . implode( ' OR id=', $missing );
				$query = "DELETE FROM #__redproductfinder_tags"
					. "\n  WHERE ( $tagcids )";
				$database->setQuery( $query );
				$database->query();
			}
         }
      }
   }

   /**
    * Reorder types
	*/
	public function getSaveOrder() {
		$db = JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$order = JRequest::getVar('order');
		$total = count($cid);
		$row = $this->getTable();
		$mainframe=JFactory::getApplication();
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
		$mainframe->Redirect('index.php?option=com_redproductfinder&task=types&controller=types');
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
}
?>