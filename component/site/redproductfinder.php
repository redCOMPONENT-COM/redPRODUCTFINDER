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

$controller = JControllerLegacy::getInstance('Redproductfinder');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
