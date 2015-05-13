<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

require_once JPATH_SITE . "/administrator/components/com_redshop/helpers/redshop.cfg.php";
JLoader::import('redshop.library');
JLoader::load('RedshopHelperUser');
JLoader::import('product', JPATH_SITE . '/libraries/redshop/helper');

/**
 */
class RedproductfinderViewFindProducts extends RViewSite
{
	function display($tpl = null)
	{
		$app        = JFactory::getApplication();
		$input      = JFactory::getApplication()->input;
		$user       = JFactory::getUser();
		$dispatcher	= RFactory::getDispatcher();

		$this->item  		= $this->get('Item');
		$this->state 		= $this->get('State');
		$this->Itemid 		= $input->getInt('Itemid', null);
		$this->option 		= $input->getString('option', 'com_redshop');
		$this->dispatcher	= $dispatcher;

		$products = array();

		// Get all product from here
		foreach ( $this->item as $k => $item )
		{
			$products[] = RedshopHelperProduct::getProductById($item);
		}

		$this->products = $products;

		parent::display($tpl);
	}
}
?>