<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @since  1.6
 */

class JFormFieldProduct extends JFormField
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'product';

	public function getInput()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->get("id", 0, "INT");
		$catid = $input->get("catid", 0, "INT");
		$modelAssociations = JModelLegacy::getInstance("Associations", "RedproductfinderModel");

		if ($id)
		{
			$products = $modelAssociations->getProducts();
		}
		else
		{
			$products = $modelAssociations->getProductByCategory($catid);
		}


		$layout = new JLayoutFile('product');
		$selected = array();

		// Get association ID
		if ($id != 0)
		{
			$proSelected = $modelAssociations->getAssociationProduct($id);

			if (count($proSelected) > 0)
			{
				foreach ($proSelected as $k => $pro)
				{
					$selected[] = $pro["product_id"];
				}
			}
		}

		$html = $layout->render(
			array(
				"selected" => $selected,
				"products" => $products
			)
		);

		return $html;
	}
}
