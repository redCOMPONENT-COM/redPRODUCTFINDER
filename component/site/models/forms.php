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

class RedproductfinderModelForms extends RModel
{
	protected $data = array();

	protected $_results = array();

	protected $_item = null;

	protected function populateState()
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

	public function getItem($pk = null)
	{
		$user	= JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('form.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select("f.id as formid,t.*, t.id as typeid, tg.*, tg.id as tagid");

		$query->from($db->qn("#__redproductfinder_forms") . " AS f");
		$query->join("INNER", $db->qn("#__redproductfinder_types") . " AS t ON t.form_id = f.id");
		$query->join("INNER", $db->qn("#__redproductfinder_tag_type") . " AS tt ON tt.type_id = t.id");
		$query->join("LEFT", $db->qn("#__redproductfinder_tags") . " AS tg ON tg.id = tt.tag_id");
		$query->where($db->qn("f.id") . "=" . $pk);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}
}
?>
