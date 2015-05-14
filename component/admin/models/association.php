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
class RedproductfinderModelAssociation extends RModelAdmin
{
	/**
	 * This method will check this item can be deleted or no
	 *
	 * @param   object  $record  Value can be empty or object
	 *
	 * @return void
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			// Remove related table here
			$id = $record->id;

			$db = JFactory::getDbo();

			$query = $db->getQuery(true);

			// Delete all custom keys
			$conditions = array(
				$db->quoteName('association_id') . ' = ' . $id
			);

			$query->delete($db->quoteName('#__redproductfinder_association_tag'));
			$query->where($conditions);

			$db->setQuery($query);

			$result = $db->execute();

			if ($result === true)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		// Check against the category.
		if (!empty($record->catid))
		{
			$user = JFactory::getUser();

			return $user->authorise('core.edit.state', 'com_contact.category.' . (int) $record->catid);
		}
		// Default to component settings if category not known.
		else
		{
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Association', $prefix = 'RedproductfinderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_redproductfinder/models/fields');

		// Get the form.
		$form = $this->loadForm('com_redproductfinder.association', 'association', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_redproductfinder.edit.association.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since    3.0
	 */
	public function save($data)
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDbo();

		$post = $input->post->get("jform", null, null);

		// Edit
		if (parent::save($data))
		{
			if ($post["id"] != 0)
			{
				// Save tag type into table tag_type
				if (count($post["tag_id"]) > 0)
				{
					// Delete tag
					$r = $this->deleteTag_Type($post, $post["id"]);

					// Insert tag type
					$a = $this->insertTag_Type($post, $post["id"]);
				}
			}
			else
			{
				$idTag			= $db->insertid();

				// Save tag type into table tag_type
				if (count($post["tag_id"]) > 0)
				{
					$a = $this->insertTag_Type($post, $idTag);
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * This function will insert data record to map database between tag and type
	 *
	 * @param   array   $data   Default value is array()
	 * @param   string  $idTag  Default value is null
	 *
	 * @return boolean
	 */
	protected function insertTag_Type($data = array(), $idTag = "")
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->insert($db->quoteName('#__redproductfinder_association_tag'))
		->columns($db->quoteName(array('association_id', 'tag_id', 'type_id')));

		foreach ($data["tag_id"] as $key => $value)
		{
			$arr = explode(".", $value);

			$values = $db->q($idTag) . ',' . $db->q($arr[1]) . ',' . $db->q($arr[0]);
			$query->values($values);
		}

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	 * This method will delete record tag and type in table tag_type with idTag
	 *
	 * @param   object  $data   This data can be use on future
	 * @param   string  $idTag  This is idtag that we can know what record will be deleted
	 *
	 * @return boolean
	 */
	protected function deleteTag_Type($data, $idTag)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Delete all custom keys for user 1001.
		$conditions = array(
			$db->quoteName('association_id') . ' = ' . $idTag
		);

		$query->delete($db->quoteName('#__redproductfinder_association_tag'));
		$query->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return $result;
	}
}
