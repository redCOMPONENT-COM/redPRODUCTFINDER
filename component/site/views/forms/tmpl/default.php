<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER
 */

JLoader::import('forms', JPATH_COMPONENT . '/helpers');

$data = redproductfinderForms::filterForm($this->item);

echo "<pre>";
print_r($data);

?>



