<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Findproducts Model.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class RedproductfinderModelFindproducts extends RModel
{
	protected $data = array();

	protected $_results = array();

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
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->post->get('redform', array(), 'filter');

		$this->setState('redform.data', $pk);

		$params = $app->getParams();

		$this->setState('params', $params);
	}

	/**
	 * Get Item from category view
	 *
	 * @param   array  $pk  default value is null
	 *
	 * @return array
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');
		$view = $this->getState("redform.view");
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("DISTINCT a.product_id")
			->from($db->qn("#__redproductfinder_associations") . " AS a")
			->join("LEFT", $db->qn("#__redproductfinder_association_tag") . " AS at ON a.id = at.association_id")
			->where("a.published=1");

		// Delete filter price here
		$filter = $pk["filterprice"];

		// Filter by cid
		$cid = $pk["cid"];
		$manufacturer_id = $pk["manufacturer_id"];

		unset($pk["filterprice"]);
		unset($pk["template_id"]);
		unset($pk["manufacturer_id"]);
		unset($pk["cid"]);

		// Add tag id
		$keyTags = array();

		foreach ( $pk as $k => $value )
		{
			if (!isset($value["tags"]))
			{
				continue;
			}

			foreach ( $value["tags"] as $k_t => $tag )
			{
				$keyTags[] = $tag;
			}
		}

		if (count($keyTags) != 0)
		{
			// Add type id
			$keyTypes = array_keys($pk);

			if ($keyTypes)
			{
				$keyTypeString = implode(",", $keyTypes);
				$query->where($db->qn("at.type_id") . " IN (" . $keyTypeString . ")");
			}

			// Remove duplicate tag id
			$keyTags = array_unique($keyTags);

			// Add tag id
			$keyTagString = implode(",", $keyTags);
			$query->where($db->qn("at.tag_id") . " IN (" . $keyTagString . ")");
		}

		$db->setQuery($query);

		$data = $db->loadAssocList();

		$dispatcher	= RFactory::getDispatcher();
		$loaded = JPluginHelper::importPlugin('redproductfinder');

		if ($loaded)
		{
			if (count($keyTags) != 0)
			{
				$data = $dispatcher->trigger('onFilterByPrice', array($data, $filter, true));
			}
			else
			{
				$data = $dispatcher->trigger('onFilterByPrice', array($data, $filter, false));
			}

			switch ($view)
			{
				case "category":

					// Filter by category
					if (intval($cid) !== 0)
					{
						// Query and get all product id
						$data = $dispatcher->trigger('onFilterByCategory', array($data, $cid, $manufacturer_id));
					}
					break;
				case "manufacturers":
						// Query and get all product id
						$data = $dispatcher->trigger('onFilterByManufacturers', array($data, $manufacturer_id));
					break;
			}

			return $data[0];
		}
		else
		{
			$temp = array();

			foreach ($data as $k => $value)
			{
				$temp[] = $value["product_id"];
			}

			return $temp;
		}
	}
}
