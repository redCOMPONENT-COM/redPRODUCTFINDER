<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Tags Controller
 */
class RedproductfinderControllerTags extends JControllerAdmin
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct()
	{
		parent::__construct();

		/* Redirect templates to templates as this is the standard call */
		$this->registerTask('unpublish','publish');
		$this->registerTask('apply','edit');
	}

	public function getModel($name = 'Tag', $prefix = 'RedproductfinderModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
?>
