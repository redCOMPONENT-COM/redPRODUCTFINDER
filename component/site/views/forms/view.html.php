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

/**
 */
class RedproductfinderViewForms extends RViewSite
{
	function display($tpl = null)
	{
		$app        = JFactory::getApplication();
		$user       = JFactory::getUser();
		$dispatcher = JEventDispatcher::getInstance();

		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->user  = $user;

		parent::display($tpl);
	}
}
?>