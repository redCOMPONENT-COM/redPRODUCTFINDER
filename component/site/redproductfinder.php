<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Frontend file
 */

/**
 */
/* No direct access */
defined('_JEXEC') or die;

$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('COM_REDITEM_REDCORE_INIT_FAILED'), 404);
}

// Bootstraps redCORE
RBootstrap::bootstrap();

$app = JFactory::getApplication();
$input = JFactory::getApplication()->input;

JLoader::import('joomla.html.parameter');

$option = $input->getCmd('option');
$view   = $input->getCmd('view');

// Loading helper
JLoader::import('joomla.html.pagination');

$controller = JControllerLegacy::getInstance('Redproductfinder');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
