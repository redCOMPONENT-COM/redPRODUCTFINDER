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

	function getPagination()
	{
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
    * Publish or Unpublish testimonials
    */
   function getPublish()
   {
      global $mainframe;
	  $mainframe = JFactory::getApplication();
      $cids = JRequest::getVar('cid');
      $task = JRequest::getCmd('task');
      $state = ($task == 'publish') ? 1 : 0;
      $user = &JFactory::getUser();
      $row = $this->getTable();

      if ($row->Publish($cids, $state, $user->id))
      {
         if ($state == 1)
         {
	         $mainframe->enqueueMessage(JText::_('Forms have been published'));
	         $mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
         }
         else
         {
	         $mainframe->enqueueMessage(JText::_('Forms have been unpublished'));
	         $mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
         }
      }
      else
      {
         if ($state == 1)
         {
	         $mainframe->enqueueMessage(JText::_('Forms could not be published'));
	         $mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
	         }else{
	         $mainframe->enqueueMessage(JText::_('Forms could not be unpublished'));
	         $mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
         }
      }
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
    * Save a competition
    */
   function getSaveForm()
   {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $row = $this->getTable();
	  $post = JRequest::get('post', 4);

	  /* Get the posted data */
      if (!$row->bind($post))
      {
         $mainframe->enqueueMessage(JText::_('There was a problem binding the form data'), 'error');
         return false;
      }

      /* pre-save checks */
      if (!$row->check())
      {
         $mainframe->enqueueMessage(JText::_('There was a problem checking the form data'), 'error');
         return false;
      }

      /* save the changes */
      if (!$row->store())
      {
         $mainframe->enqueueMessage(JText::_('There was a problem storing the form data'), 'error');
         return false;
      }

      $row->checkin();
      $mainframe->enqueueMessage(JText::_('The form has been saved'));

      return $row;
   }

   /**
    * Delete a form
    */
   function getRemoveForm()
   {
      global $mainframe;
      $mainframe = JFactory::getApplication();
      $database = JFactory::getDBO();
      $cid = JRequest::getVar('cid');
      JArrayHelper::toInteger( $cid );

      if (!is_array( $cid ) || count( $cid ) < 1)
      {
         $mainframe->enqueueMessage(JText::_('No form found to delete'));
         return false;
      }

      if (count($cid))
      {
         $cids = 'id=' . implode( ' OR id=', $cid );
         $query = "DELETE FROM #__redproductfinder_forms"
         . "\n  WHERE ( $cids )";
         $database->setQuery( $query );
         if (!$database->query())
         {
            $mainframe->enqueueMessage(JText::_('A problem occured when deleting the form'));
         }
         else
         {
            if (count($cid) > 1)
            {
            	$mainframe->enqueueMessage(JText::_('Forms have been deleted'));
            }
            else
            {
            	$mainframe->enqueueMessage(JText::_('Form has been deleted'));
            }

			/* Get the field ids */
			$cids = 'form_id=' . implode( ' OR form_id=', $cid );
			$q = "SELECT id FROM #__redproductfinder_fields
				WHERE ( $cids )";
			$database->setQuery($q);
			$fieldids = $database->loadResultArray();

			/* See if there is any data */

			if (count($fieldids) > 0) {
				/* Now delete the fields */
				$cids = 'form_id=' . implode( ' OR form_id=', $cid );
				$q = "DELETE FROM #__redproductfinder_fields
					WHERE ( $cids )";
				$database->setQuery($q);
				if (!$database->query()) {
					$mainframe->enqueueMessage(JText::_('A problem occured when deleting the form fields'));
				}
				else {
					$mainframe->enqueueMessage(JText::_('Form fields have been deleted'));

					/* Delete the values */
					$cids = 'field_id=' . implode( ' OR field_id=', $fieldids );
					$q = "DELETE FROM #__redproductfinder_values
						WHERE ( $cids )";
					$database->setQuery($q);
					if (!$database->query()) {
						$mainframe->enqueueMessage(JText::_('A problem occured when deleting the field values'));

		 				 $mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');

					}
					else {
						$mainframe->enqueueMessage(JText::_('Field values have been deleted'));
						$mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
					}
				}
			}
			else $mainframe->enqueueMessage(JText::_('No fields found'));
			$mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
         }
      }
   }

   /**
    * Clone a form and all its related tags/types/associations
    */
    public function getCloneForm() {
    	$db = JFactory::getDBO();
    	$cids = JRequest::getVar('cid');
    	$existing_assoc_ids = array();
    	$mainframe=JFactory::getApplication();
    	/* Go through all forms */
    	foreach ($cids AS $key => $cid) {
    		/* Clone the form */
    		$form = $this->getTable();
    		$form->load($cid);
    		$form->id = null;
    		$form->store();
    		$form_id_new = $form->id;

    		/* Clone the types */
    		$q = "SELECT id FROM #__redproductfinder_types WHERE form_id = ".$cid;
    		$db->setQuery($q);
    		$type_ids = $db->loadResultArray();
    		$type = $this->getTable('types');
    		foreach ($type_ids as $typekey => $type_id) {
    			$type = $this->getTable('types');
				$type->load($type_id);
				$type->id = null;
				$type->form_id = $form_id_new;
				$type->store();

				/* Clone the tags */
				$q = "SELECT tag_id FROM #__redproductfinder_tag_type WHERE type_id = ".$type_id;
				$db->setQuery($q);
				$tag_ids = $db->loadResultArray();
				foreach ($tag_ids as $tagkey => $tag_id) {
					$tag = $this->getTable('tags');
					$tag->load($tag_id);
					$tag->id = null;
					$tag->store();

					$q = "INSERT INTO #__redproductfinder_tag_type VALUES (".$tag->id.", ".$type_id.")";
					$db->setQuery($q);
					$db->query();

					/* Clone the associations */
					$q = "SELECT association_id
						FROM #__redproductfinder_association_tag
						WHERE tag_id = ".$tag_id."
						AND type_id = ".$type_id;
					$db->setQuery($q);
					$assoc_ids = $db->loadResultArray();
					foreach ($assoc_ids as $assockey => $assoc_id) {
						if (!array_key_exists($assoc_id, $existing_assoc_ids)) {
							$assoc = $this->getTable('associations');
							$assoc->load($assoc_id);
							$assoc->id = null;
							$assoc->store();
							$existing_assoc_ids[$assoc_id] = $assoc->id;
							$insert_id = $assoc->id;
						}
						else {
							$insert_id = $existing_assoc_ids[$assoc_id];
						}

						$q = "INSERT INTO #__redproductfinder_association_tag VALUES (".$insert_id.", ".$tag_id.", ".$type_id.")";
						$db->setQuery($q);
						$db->query();

						/* Clean up */
						unset($assoc);
					}

					/* Clean up */
					unset($tag);
				}

				/* Clean up */
				unset($type);
			}

			/* Clean up */
    		unset($form);
    		$mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
    	}
    }

    /**
     * Imports all attributes from Redshop and creates associations
     */
    public function getImportAttributes() {
    	$db = JFactory::getDBO();
    	$cids = JRequest::getVar('cid');
    	$form_id = $cids[0];
    	$mainframe = Jfactory::getApplication('site');

    	/* Go through the whole product list, 100 at a time */

   		$q = "SELECT rdp.product_id as rdp_product_id,rdpa.*
   				FROM `#__redshop_product` as rdp
   				left join #__redshop_product_attribute as rdpa on rdpa.product_id = rdp.product_id ";
   		$db->setQuery($q);
   		$attributes = $db->loadObjectList();

   		/* Store all the data */
   		foreach ($attributes as $key => $attribute) {

   			if ($attribute->product_id != "" || $attribute->product_id != NULL){
    			/* Store the association */
				$assoc = $this->getTable('associations');
				$assoc->published = 1;
				$assoc->checked_out = 0;
				$assoc->checked_out_time = '0000-00-00 00:00:00';
				$assoc->ordering = 1;
				$assoc->product_id = $attribute->product_id;
				$assoc->store();

				/* Types */
				$types = $this->getTable('types');
				$types->form_id = $form_id;
				$types->type_select = 'generic';
				$types->published = 1;
				$types->checked_out = 0;
				$types->checked_out_time = '0000-00-00 00:00:00';
				$types->ordering = 1;
				$types->type_name = $attribute->attribute_name;
				$types->store();

				/* get the attributes property */
				$q = "SELECT *
						FROM `#__redshop_product_attribute_property`
						WHERE `attribute_id` = '".$attribute->attribute_id."'";
   				$db->setQuery($q);
   				$properties = $db->loadObjectList();

				/* Tags */
				foreach ($properties as $tag_index => $tag) {

					$tags = $this->getTable('tags');
					$tags->published = 1;
					$tags->checked_out = 0;
					$tags->checked_out_time = '0000-00-00 00:00:00';
					$tags->ordering = 1;
					$tags->tag_name = $tag->property_name;
					$tags->store();

					/* Store the relation with the type */
					$q = "INSERT INTO #__redproductfinder_tag_type
						VALUES (".$tags->id.", ".$types->id.")";
					$db->setQuery($q);
					$db->query();

					/* Store the relation with the association */
					$q = "INSERT INTO #__redproductfinder_association_tag
						VALUES (".$assoc->id.", ".$tags->id.", ".$types->id.")";
					$db->setQuery($q);
					$db->query();
				}
   			}
   		}
    	$mainframe->enqueueMessage(JText::_('PROCESSED_X_PRODUCTS'));
    	$mainframe->Redirect('index.php?option=com_redproductfinder&task=forms&controller=forms');
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
    	->from($db->qn("#__redproductfinder_forms"));

    	if ($state == "-2")
    	{
    		$query->where($db->qn("published") . "=" . $db->qn("-2"));
    	}
    	else
    	{
    		$query->where($db->qn("published") . "!=" . $db->q("-2"));
    	}

    	return $query;
    }
}
?>
