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
class RedproductfinderModelRedproductfinder extends RModelList
{
	/**
	 * Get total information of redproductfinder
	 *
	 * @return void
	 */
	public function getTotals()
	{
		$db = JFactory::getDBO();
		$totals = array();
		/* Type totals */
		$q = "SELECT COUNT(id) AS total FROM #__redproductfinder_types;";
		$db->setQuery($q);
		$totals['types']['total'] = $db->loadResult();
		$q = "SELECT COUNT(id) AS total FROM #__redproductfinder_tags;";
		$db->setQuery($q);
		$totals['tags']['total'] = $db->loadResult();
		/* Product totals */
		$q = "SELECT COUNT(id) AS total FROM #__redproductfinder_associations";
		$db->setQuery($q);
		$totals['associations']['total'] = $db->loadResult();

		return $totals;
	}
}
