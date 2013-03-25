<?php
/** 
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved. 
 * @license can be read in this package of software in the file license.txt or 
 * read on http://redcomponent.com/license.txt  
 * Developed by email@recomponent.com - redCOMPONENT.com 
 *
 * redPRODUCTFINDER view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

/**
 * ProductSearch View
 */
class RedproductfinderViewRedproductfinder extends JView {
	/**
	 * Productsearch view display method
	 * @return void
	 **/
	function display($tpl = null) {
		
		/* Get the total number of tags */
		$stats = $this->get('Totals');
		
		$this->assignRef('stats', $stats);
		
		/* Get the toolbar */
		$this->toolbar();
		
		/* Display the page */
		parent::display($tpl);
	}
	
	function toolbar() {
		JToolBarHelper::title(JText::_('REDPRODUCTFINDER'), 'redproductfinder_redproductfinder');
		JToolBarHelper::preferences('com_redproductfinder', '300');
	}
}
?>