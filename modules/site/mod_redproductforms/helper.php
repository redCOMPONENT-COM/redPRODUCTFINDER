<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_archive
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_articles_archive
 *
 * @package     Joomla.Site
 * @subpackage  mod_articles_archive
 * @since       1.5
 */

// Add helper of site
JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');

require_once JPATH_SITE . '/components/com_redproductfinder/models/forms.php';
class ModRedproductForms
{
	public static function getList(&$params)
	{
		$id = $params->get("form_id");

		$modelForms = new RedproductfinderModelForms();

		$data = $modelForms->getItem($id);
		$data = redproductfinderForms::filterForm($data);

		return $data;
	}
}
