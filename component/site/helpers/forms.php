<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

JLoader::import('form', JPATH_SITE . '/components/com_redproductfinder/models/');

/**
 * Redproductfinder Component Form Helper
 *
 * @since  3.0
 */
class RedproductfinderForms
{
	/**
	 * This method will filter type and tag to array
	 *
	 * @param   array  $data  value is array type
	 *
	 * @return array
	 */
	static function filterForm($data)
	{
		$types = array();
		$forms = array();

		$model = JModelLegacy::getInstance("forms", "RedproductfinderModel");

		foreach ($data as $key => $record)
		{
			// Get Type data
			$types[] = $record->typeid;
		}

		// Get unique types
		$types = array_unique($types);

		// Find tag and add them to form
		foreach ($data as $key => $record)
		{
			foreach ($types as $k => $r)
			{
				if (!isset($forms[$r]))
				{
					$forms[$r] = array(
						"typeid"	=> $r
					);
				}

				if ($r === $record->typeid)
				{
					$forms[$r]["typename"] = $record->type_name;
					$forms[$r]["typeselect"] = $record->type_select;
					$forms[$r]["tags"][] = array(
						"tagid" 	=> $record->tagid,
						"tagname" 	=> $record->tag_name,
						"ordering"	=> $record->ordering,
						"aliases"	=> $record->aliases
					);

					unset($data[$key]);
				}
			}
		}

		// Remove duplicate types value
		return $forms;
	}

	/**
	 * This method will get form from model
	 *
	 * @return void
	 */
	public static function getModelForm()
	{
		return JModelLegacy::getInstance('forms', 'RedproducfinderModel');
	}
}
