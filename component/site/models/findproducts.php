<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER model
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class RedproductfinderModelFindproducts extends RModel
{
	protected $data = array();

	protected $_results = array();

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->post->get('redform', array(), 'filter');

		$this->setState('redform.data', $pk);

		$params = $app->getParams();

		$this->setState('params', $params);
	}

	public function getItem($pk = null)
	{
		$user	= JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : $this->getState('redform.data');

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("DISTINCT a.product_id")
			->from($db->qn("#__redproductfinder_associations") . " AS a")
			->join("LEFT", $db->qn("#__redproductfinder_association_tag") . " AS at ON a.id = at.association_id")
			->where("a.published=1");

		// Delete filter price here
		$filter = $pk["filterprice"];

		unset($pk["filterprice"]);

		// Add tag id
		$keyTags = array();

		foreach ( $pk as $k => $value )
		{
			if (!isset($value["tags"])) continue;
			if (!isset($value["template_id"])) continue;
			if (!isset($value["cid"])) continue;

			foreach ( $value["tags"] as $k_t => $tag )
			{
				$keyTags[] = $tag;
			}
		}

		if ($keyTags)
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
			$data = $dispatcher->trigger('onFilterByPrice',array($data, $filter));

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
?>
