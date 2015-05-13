<?php
defined('_JEXEC') or die('Restricted access');

JLoader::import('form', JPATH_COMPONENT . '/models');

class redproductfinderForms
{
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
		$types = array_unique ($types);

		// Find tag and add them to form
		foreach ($data as $key => $record)
		{
			foreach($types as $k => $r)
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

	public static function getModelForm()
	{
		return JModelLegacy::getInstance( 'forms', 'RedproducfinderModel' );
	}
}