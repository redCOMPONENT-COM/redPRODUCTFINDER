<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Products model
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.model' );

/**
 * Associations Model
 */
class RedproductfinderModelAssociations extends JModel {
	/** @var integer Total entries */
	protected $_total = null;

	/** @var integer pagination limit starter */
	protected $_limitstart = null;

	/** @var integer pagination limit */
	protected $_limit = null;

	/**
	 * Show all tags that have been created
	 */
	function getAssociations() {
		$db = JFactory::getDBO();

		/* Get all the fields based on the limits */
		$query = "SELECT a.*, p.product_name
			FROM #__redproductfinder_associations a, #__redshop_product p
			WHERE a.product_id = p.product_id
			ORDER BY a.ordering";
		$db->setQuery($query, $this->_limitstart, $this->_limit);
		$products = $db->loadObjectList();
		return $products;
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
			. "\n FROM #__redproductfinder_associations";
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
         if ($state == 1) {
         $mainframe->enqueueMessage(JText::_('ASSOCIATIONS_HAVE_BEEN_PUBLISHED'));
         $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
         }
         else{
         $mainframe->enqueueMessage(JText::_('ASSOCIATIONS_HAVE_BEEN_UNPUBLISHED'));
          $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
         }
      }
      else {
         if ($state == 1) {
         $mainframe->enqueueMessage(JText::_('ASSOCIATIONS_COULD_NOT_BE_PUBLISHED'));
          $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
         }
         else {
         $mainframe->enqueueMessage(JText::_('ASSOCIATIONS_COULD_NOT_BE_UNPUBLISHED'));
          $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
         }
      }
   }

   /**
    * Retrieve an association to edit
    */
   function getAssociation() {
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
         $row->published = 1;
      }
      return $row;
   }

   /**
    * Retrieve a list of products from Redshop
    */
   public function getProducts() {
	   $db = JFactory::getDBO();

	   $q = "SELECT product_id, CONCAT(product_number, '::', product_name) AS full_product_name
	   		FROM #__redshop_product
			ORDER BY product_name";
      $db->setQuery($q);
	  return $db->loadObjectList();
   }

   /**
    * Save an association
    */
	function getSaveAssociations() {
     global $mainframe;
     $mainframe = JFactory::getApplication();
     $row = $this->getTable();

	 /* Get the posted data */
	 $post = JRequest::get('post');

      if (!$row->bind($post)) {
         $mainframe->enqueueMessage(JText::_('THERE_WAS_A_PROBLEM_BINDING_THE_ASSOCIATION_DATA'), 'error');
         return false;
      }

      /* pre-save checks */
      if (!$row->check()) {
         $mainframe->enqueueMessage(JText::_('THERE_WAS_A_PROBLEM_CHECKING_THE_ASSOCIATION_DATA'), 'error');
         return false;
      }

      /* save the changes */
      if (!$row->store()) {
         $mainframe->enqueueMessage(JText::_('THERE_WAS_A_PROBLEM_STORING_THE_ASSOCIATION_DATA'), 'error');
         return false;
      }
	  else {
		 $db = JFactory::getDBO();
		  /* Delete all tag type relations */
		  $q = "DELETE FROM #__redproductfinder_association_tag
		  		WHERE association_id = ".$row->id;
		  $db->setQuery($q);
		  $db->query();
		  /* Store the tag type relations */
		  $tags = JRequest::getVar('tag_id');
		  $qs = JRequest::getVar('qs_id');

		  if(is_array($tags))
		  {
			  foreach ($tags as $key => $tag) {
				  /* Split tag to type ID and tag ID */
				  list($type_id, $tag_id) = explode('.', $tag);
				  if (empty($qs[$type_id.'.'.$tag_id])) $qs_id = 0;
				  else $qs_id = $qs[$type_id.'.'.$tag_id];


				$q = "INSERT IGNORE INTO #__redproductfinder_association_tag
				  		VALUES (".$row->id.",".$tag_id.",".$type_id.",'".$qs_id."')";
				$db->setQuery($q);
				$db->query();
			  }
		  }
	  }
      $row->checkin();
	  $row->reorder();

      $mainframe->enqueueMessage(JText::_('THE_ASSOCIATION_HAS_BEEN_SAVED'));
       $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
	  $id = JRequest::setVar('cid', $row->id);
      return $this->getAssociation();
   }

   /**
    * Delete a product
    */
   function getRemoveAssociation() {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $database =& JFactory::getDBO();
      $cid = JRequest::getVar('cid');
      JArrayHelper::toInteger( $cid );

      if (!is_array( $cid ) || count( $cid ) < 1) {
         $mainframe->enqueueMessage(JText::_('NO_ASSOCIATION_FOUND_TO_DELETE'));
         return false;
      }
      if (count($cid)) {
         $cids = 'id=' . implode( ' OR id=', $cid );
         $query = "DELETE FROM #__redproductfinder_associations"
         . "\n  WHERE ( $cids )";
         $database->setQuery( $query );
         if (!$database->query()) {
            $mainframe->enqueueMessage(JText::_('A_PROBLEM_OCCURED_WHEN_DELETING_THE_ASSOCIATION'));
             $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
         }
         else {
            if (count($cid) > 1){
            $mainframe->enqueueMessage(JText::_('ASSOCIATIONS_HAVE_BEEN_DELETED'));
             $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
            }
            else{
            $mainframe->enqueueMessage(JText::_('ASSOCIATION_HAS_BEEN_DELETED'));
             $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
            }

            /* Now remove the type associations */
            $cids = 'association_id=' . implode( ' OR association_id=', $cid );
			$query = "DELETE FROM #__redproductfinder_association_tag"
			 . "\n  WHERE ( $cids )";
			$database->setQuery( $query );
			$database->query();
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
		 $mainframe->Redirect('index.php?option=com_redproductfinder&task=associations&controller=associations');
	}

	/**
	 * Get the list of selected types for this tag
	 */
	public function getAssociationTags() {
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid', false);
		if (!$id) return array();
		else {
			$q = "SELECT tag_id
				FROM #__redproductfinder_association_tag
				WHERE association_id = ".$id[0];
			$db->setQuery($q);
			return $db->loadResultArray();
		}
	}

	/**
	 * Get the list of selected type names for this tag
	 */
	public function getAssociationTagNames() {
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');

		$q = "SELECT association_id, CONCAT(y.type_name, ':', g.tag_name) AS tag_name
			FROM #__redproductfinder_association_tag a
			LEFT JOIN #__redproductfinder_tags g
			ON a.tag_id = g.id
			LEFT JOIN #__redproductfinder_types y
			ON a.type_id = y.id";
		$db->setQuery($q);
		$list = $db->loadObjectList();
		$sortlist = array();
		if (count($list) > 0) {
			foreach ($list as $key => $tag) {
				$sortlist[$tag->association_id][] = $tag->tag_name;
			}
		}
		return $sortlist;

	}

	/**
	 * Get a multi-select list with types and tags
	 */
	public function getTypeTagList() {
		$db = JFactory::getDBO();
		/* 1. Get all types */
		$q = "SELECT id, type_name FROM #__redproductfinder_types where type_select!='Productfinder datepicker' ORDER by ordering";
		$db->setQuery($q);
		$types = $db->loadAssocList('id');

		/* 2. Go through each type and get the tags */
		foreach ($types as $id => $type) {
			$q = "SELECT t.id, tag_name
				FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
				WHERE j.tag_id = t.id
				AND j.type_id = ".$id."
				ORDER BY t.ordering";
			$db->setQuery($q);
			$types[$id]['tags'] = $db->loadAssocList('id');
		}
		return $types;
	}
/*
	 *  save dependent tags
	 */
	public function savedependent(){
		$request = JRequest::get('REQUEST');
		$db = JFactory::getDBO();

		$args[] = "product_id='".$request['product_id']."'";
		$args[] = "tag_id='".$request['tag_id']."'";
		$args[] = "type_id='".$request['type_id']."'";

		$where = implode(" AND ",$args);

		$query = "SELECT count(dependent_tags) FROM #__redproductfinder_dependent_tag WHERE ".$where;
		$db->setQuery($query);

		$dependent_tags = $db->loadResult();


		if(!$dependent_tags)
		{
			$args[] = "dependent_tags='".$request['dependent_tags']."'";
			$set = implode(" , ",$args);
			$ins_query = "INSERT INTO #__redproductfinder_dependent_tag SET ".$set;
		}
		else
		{
			$set = "dependent_tags='".$request['dependent_tags']."'";
			$ins_query = "UPDATE #__redproductfinder_dependent_tag SET ".$set ." WHERE ".$where;
		}
		$db->setQuery($ins_query);

		if($db->query())
			return JText::_('Depedent Tag added Successfully !');
		else
			return JText::_('Error occur while adding Depedent Tag !');
	}
	/*
	 * get dependent tags
	 */
	function getDependenttag($product_id=0,$type_id=0,$tag_id=0)
	{
		$db = JFactory::getDBO();
		$where = " product_id='".$product_id."'";
		$where .= " AND type_id='".$type_id."'";
		$where .= " AND tag_id='".$tag_id."'";
		$query = "SELECT dependent_tags FROM #__redproductfinder_dependent_tag WHERE ".$where;
		$db->setQuery($query);
		$rs = $db->loadResult();

		return explode(",",$rs);
	}
	/**
	 * Get the list of selected types for this type id
	 */
	public function getAssociationTypes($tag) {
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid', false);
		if (!$id) return array();
		else {
			$q = "SELECT type_id 
				FROM #__redproductfinder_association_tag
				WHERE association_id = ".$id[0]." and tag_id=".$tag."";
			
			$db->setQuery($q);
			return $db->loadObject();
		}
	}
	
	function getFormDetail($id) {
		$db = JFactory::getDBO();
		if (!$id) return array();
		else {
			$query = "SELECT * 
				FROM #__redproductfinder_forms
				WHERE id = ".$id."";
			$db->setQuery($query);
			$list = $db->loadObjectlist();
			return $list;
		}
	}
}
?>
