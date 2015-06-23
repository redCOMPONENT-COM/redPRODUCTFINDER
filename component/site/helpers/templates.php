<?php
/**
 * @copyright  Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license    can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER helpers
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Redproductfinder Component Template
 *
 * @since  3.0
 */
class RedproductfinderTemplates
{
	/**
	 * Method to get Template file path
	 *
	 * @param   string   $filename  Template File Name
	 * @param   boolean  $is_admin  Check for administrator call
	 *
	 * @return  string              Template File Path
	 */
	public static function getTemplatefilepath($filename, $is_admin = false)
	{
		$app           = JFactory::getApplication();
		$filename      = str_replace(array('/', '\\'), '', $filename);
		$section       = str_replace(array('/', '\\'), '', $section);
		$tempate_file  = "";
		$template_view = self::getTemplateView($section);
		$layout        = JRequest::getVar('layout');

		if (!$is_admin && $section != 'categoryproduct')
		{
			$tempate_file = JPATH_SITE . '/templates/' . $app->getTemplate() . "/html/com_redshop/$template_view/$section/$filename.php";
		}
		else
		{
			$tempate_file = JPATH_SITE . '/templates/' . $app->getTemplate() . "/html/com_redshop/$section/$filename.php";
		}

		if (!file_exists($tempate_file))
		{
			if ($section == 'categoryproduct' && $layout == 'categoryproduct')
			{
				$templateDir = JPATH_SITE . "/components/com_redshop/templates/$section/$filename.php";
			}

			if ($template_view && $section != 'categoryproduct')
			{
				$templateDir = JPATH_SITE . "/components/com_redshop/views/$template_view/tmpl/$section";
				@chmod(JPATH_SITE . "/components/com_redshop/views/$template_view/tmpl", 0755);
			}

			else
			{
				$templateDir = $this->redshop_template_path . '/' . $section;

				@chmod($this->redshop_template_path, 0755);
			}

			if (!is_dir($templateDir))
			{
				JFolder::create($templateDir, 0755);
			}

			$tempate_file = "$templateDir/$filename.php";
		}

		return $tempate_file;
	}
}
