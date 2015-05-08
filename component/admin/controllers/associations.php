<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedPRODUCTFINDER Associations controller.
 *
 * @package  RedPRODUCTFINDER.Administrator
 * @since    2.0
 */
class RedproductfinderControllerAssociations extends RControllerAdmin
{
	/**
	 * Method construct Associations controller
	 */
	function __construct()
	{
		parent::__construct();

		$this->registerTask('apply', 'edit');
	}

	/**
	 * Method to get model from table class
	 *
	 * @param   string  $name    Default value is Association
	 * @param   string  $prefix  Default value is RedproductfinderModel
	 * @param   array   $config  Default value is array
	 *
	 * @return object
	 */
	public function getModel($name = 'Association', $prefix = 'RedproductfinderModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
