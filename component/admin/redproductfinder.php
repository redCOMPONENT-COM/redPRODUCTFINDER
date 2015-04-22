<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_redproductfinder
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_redproductfinder'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('redproductfinder');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
