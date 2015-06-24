<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . "/administrator/components/com_redshop/helpers/redshop.cfg.php";
JLoader::import('redshop.library');
JLoader::load('RedshopHelperUser');
JLoader::import('product', JPATH_SITE . '/libraries/redshop/helper');

/**
 * Findproducts controller.
 *
 * @package     RedPRODUCTFINDER.Frontend
 * @subpackage  Controller
 * @since       2.0
 */
class RedproductfinderControllerFindproducts extends JControllerForm
{
	/**
	 * This method are core process to get product from ajax
	 *
	 * @return void
	 */
	function find()
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$param = JComponentHelper::getParams('com_redproductfinder');
		$input = $app->input;

		$model = JModelLegacy::getInstance("FindProducts", "RedproductfinderModel");

		$layout = new JLayoutFile('result');

		$post = $input->post->get('redform', array(), 'filter');
		$view = $input->post->get("view", "", 'filter');

		$model->setState("redform.data", $post);
		$model->setState("redform.view", $view);
		
		$list = $model->getItem();

		// Get all product from here
		foreach ( $list as $k => $value )
		{
			$products[] = $value;
		}

		$pagination = $model->getPagination();
		$orderBy = $model->getState('order_by');

		// Get layout HTML
		$html = $layout->render(
			array(
				"products" => $products,
				"model" => $model,
				"post"	   => $post,
				"template_id" => $post["template_id"],
				"getPagination" => $pagination,
				"orderby" => $orderBy
			)
		);

		echo $html;
		$app->close();
	}
}
