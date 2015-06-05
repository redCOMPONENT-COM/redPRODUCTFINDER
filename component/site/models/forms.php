<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Forms Model.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Model
 * @since       2.0
 */
class RedproductfinderModelForms extends RModel
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');

		$this->setState('form.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
	}

	/**
	 * This method will get all item data
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getItem($pk = null)
	{
		$user	= JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('form.id');

		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("f.id as formid,t.*, t.id as typeid,tg.*, tg.id as tagid");

		$query->from($db->qn("#__redproductfinder_forms") . " AS f");
		$query->join("INNER", $db->qn("#__redproductfinder_types") . " AS t ON t.form_id = f.id");
		$query->join("INNER", $db->qn("#__redproductfinder_tag_type") . " AS tt ON tt.type_id = t.id");
		$query->join("LEFT", $db->qn("#__redproductfinder_tags") . " AS tg ON tg.id = tt.tag_id");
		$query->where($db->qn("f.id") . "=" . $pk);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute name
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttribute($pk = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pa.attribute_name, pa.attribute_id")
		->from($db->qn("#__redshop_product_attribute") . " AS pa")
		->where($db->qn("pa.attribute_id") . "IN (SELECT attribute_id FROM #__redshop_product_attribute_property)")
		->group($db->qn("pa.attribute_name"));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get all attribute property name
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getAttributeProperty($pk = null)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pp.property_name, pp.attribute_id, pp.property_id")
		->from($db->qn("#__redshop_product_attribute_property") . " AS pp")
		->group($db->qn("pp.property_name"));

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * This method will get attribute name
	 *
	 * @param   array  $id  attribute id
	 *
	 * @return array
	 */
	public function getAttributeName($id)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("pa.attribute_name")
		->from($db->qn("#__redshop_product_attribute") . " AS pa")
		->where($db->qn("pa.attribute_id") . " = " . $id);

		$db->setQuery($query);
		$data = $db->loadRow();

		return $data;
	}
}
