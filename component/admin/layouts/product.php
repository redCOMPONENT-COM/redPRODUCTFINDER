<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

$products = $displayData["products"];
$selected = $displayData["selected"];

?>
			<select id="select-products" name="jform[product_id]">
				<option><?php echo JText::_('COM_REDPRODUCTFINDER_MODELS_FORMS_ASSOCIATION_PRODUCT_ID_LABEL') ?></option>
				<?php foreach ($products as $product) :?>

					<?php
					$isSelected = "";
						foreach ($selected as $select) :

							if ($select == $product['product_id'])
							{
								$isSelected = "selected";
							}

						endforeach;
					?>
				<option value="<?php echo $product['product_id'] ?>" <?php echo $isSelected; ?>><?php echo $product['full_product_name'] ?></option>
				<?php endforeach; ?>
			</select>