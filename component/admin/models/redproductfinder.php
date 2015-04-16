<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER model
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.application.component.model' );

/**
 * redPRODUCTFINDER Model
 */
class RedproductfinderModelRedproductfinder extends JModelList
{
	/**
	 * Load the totals
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
?>
