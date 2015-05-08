<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

JLoader::register('ContactHelper', JPATH_ADMINISTRATOR . '/components/com_contact/helpers/contact.php');

/**
 * Item Model for a Contact.
 *
 * @since  1.6
 */
class RedproductfinderModelTag extends JModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}

			$user = JFactory::getUser();

			return $user->authorise('core.delete', 'com_contact.category.' . (int) $record->catid);
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
		return parent::canEditState($record);
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
	public function getTable($type = 'Tag', $prefix = 'RedproductfinderTable', $config = array())
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
		$form = $this->loadForm('com_redproductfinder.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_redproductfinder.edit.tag.data', array());

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
				if (count($post["type_id"]) > 0)
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
				if (count($post["type_id"]) > 0)
				{
					$a = $this->insertTag_Type($post, $idTag);
				}
			}

			return true;
		}
		return false;
	}

	protected function insertTag_Type($data, $idTag)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->insert($db->quoteName('#__redproductfinder_tag_type'))
			->columns($db->quoteName(array('tag_id', 'type_id')));

		foreach ($data["type_id"] as $key => $value)
		{
			$values = $db->quote($idTag) . ',' . $db->quote($value);
			$query->values($values);
		}

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	protected function deleteTag_Type($data, $idTag)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// delete all custom keys for user 1001.
		$conditions = array(
			$db->quoteName('tag_id') . ' = ' . $idTag
		);

		$query->delete($db->quoteName('#__redproductfinder_tag_type'));
		$query->where($conditions);

		$db->setQuery($query);

		$result = $db->execute();

		return $result;
	}
}
