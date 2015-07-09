<?php
/**
 * @package     RedITEM
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

// Load redCORE library
$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('COM_REDITEM_REDCORE_INIT_FAILED'), 404);
}

include_once $redcoreLoader;

RBootstrap::bootstrap();

require_once JPATH_SITE . '/components/com_redproductfinder/models/forms.php';
require_once JPATH_SITE . '/components/com_redproductfinder/helpers/forms.php';

/**
 * Plugins RedPRODUCTFINDER Form
 *
 * @since  2.0
 */
class PlgContentRedproductfinder_Form extends JPlugin
{
	protected $autoloadLanguage = true;

	/**
	 * Method run on Content Prepare trigger
	 *
	 * @param   string  $context  Context
	 * @param   array   &$row     Data
	 * @param   array   &$params  Plugins parameters
	 * @param   int     $page     Page number
	 *
	 * @return  boolean
	 */
	public function onContentPrepare($context, &$row, &$params, $page=0)
	{
		// Regex to find categorypage references
		$regex = "#{redproductfinder}(.*?){/redproductfinder}#s";

		$matches = array();

		preg_match($regex, $row->text, $matches);

		if ($matches)
		{
			// Get id form from content
			$id = $matches[1];

			$modelForms = new RedproductfinderModelForms;

			// Get Items
			$data = $modelForms->getItem($id);
			$data = RedproductfinderForms::filterForm($data);

			// Find override
			$templateName = JFactory::getApplication()->getTemplate(true)->template;

			$templatePath = JPATH_ROOT . '/templates/' . $templateName . "/html/layouts/com_redproductfinder/forms.php";

			if (file_exists($templatePath))
			{
				$layout = new JLayoutFile('forms', JPATH_ROOT . '/templates/' . $templateName . "/html/layouts/com_redproductfinder/");
			}
			else
			{
				$layout = new JLayoutFile('forms', JPATH_ROOT . '/components/com_redproductfinder/layouts');
			}

			// Get layout HTML
			$html = $layout->render(
					array(
						"data" => $data,
						"model" => $modelForms
					)
			);

			$row->text = str_replace($matches[0], $html, $row->text);
		}
	}
}
