<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class modRedproductfinder_ajax
{
	static function getParams()
	{
		require_once JPATH_SITE . '/components/com_redshop/helpers/helper.php';

		$params_options = array();

		$mainframe = JFactory::getApplication();
		$params = $mainframe->getParams('com_redshop');
		$app = JFactory::getApplication();

		$params_options['current_limit_product'] = array(
			$app->getUserState('finder.texpricemin', 0),
			$app->getUserState('finder.texpricemax', 1000)
		);

		return $params_options;
	}
}
