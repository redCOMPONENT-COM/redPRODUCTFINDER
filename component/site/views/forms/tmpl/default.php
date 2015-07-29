<?php
/**
 * @package    RedPRODUCTFINDER
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_REDCORE') or die;

JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');
JLoader::import('helper', JPATH_SITE . '/modules/mod_redproductforms');

$data = RedproductfinderForms::filterForm($this->item);
$model = $this->getModel('forms');
$attributes = $model->getAttribute();
$attributeProperties = $model->getAttributeProperty();
$attributeSubProperties = $model->getAttributeSubProperty();
$param = JComponentHelper::getParams('com_redproductfinder');
$searchBy = $param->get('search_by');
$cid = 0;
$manufacturer_id = 0;
$template_id = $param->get('prod_template');
$formid = $param->get('form');
$filterPriceMin = $param->get("filter_price_min_value", 0);
$filterPriceMax = $param->get("filter_price_max_value", 100);
?>
<div class="">
	<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder&view=findproducts"); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
			<?php if ($searchBy == 0) : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($data as $key => $value) :?>
						<div id='typename-<?php echo $value["typeid"];?>'>
							<label><?php echo $value["typename"];?></label>
							<ul class='taglist' style="list-style: none">
								<?php foreach ($value["tags"] as $k_t => $tag) :?>
									<li>
										<label>
										<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'><input type="checkbox" name="redform[<?php echo $value["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
										<span class='tagname'><?php echo $tag["tagname"]; ?></span>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<input type="hidden" name="redform[<?php echo $value["typeid"]?>][typeid]" value="<?php echo $value["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			<?php else : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($attributes as $k_a => $attribute) :?>
						<div id='typename-<?php echo $attribute->attribute_id;?>'>
							<label><?php echo $attribute->attribute_name;?></label>
							<ul class='taglist' style="list-style: none">
								<?php foreach($attributeProperties as $k_p => $property) :?>
									<?php
									if ($property->attribute_name == $attribute->attribute_name) : ?>
										<li>
											<label>
											<span class='taginput'><input type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][]" value="<?php echo $property->property_name; ?>"></span>
											<span class='tagname'><?php echo $property->property_name; ?></span>
											</label>
											<ul class='taglist' style="list-style: none">
											<?php foreach($attributeSubProperties as $k_sp => $subProperty) :?>
												<?php
													if ($subProperty->property_name == $property->property_name) :
														$newArr[] = $subProperty->subattribute_color_name;
												endif; ?>
											<?php endforeach;?>
											<?php  if (isset($newArr)) :
											foreach(array_unique($newArr) as $key => $value) :?>
												<li>
													<label>
													<span class='taginput'>
													<input type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][subproperty][<?php echo $property->property_name;?>][]" value="<?php echo $value; ?>"></span>
													<span class='tagname'><?php echo $value; ?></span>
													</label>
												</li>
											<?php endforeach;
											endif;?>
											</ul>
										</li>
									<?php endif; ?>
								<?php endforeach;?>
							</ul>
						</div>
					<?php endforeach;?>
				</div>
			<?php endif; ?>
			</div>
		</div>
		<div  class="row-fluid">
			<span><?php echo JText::_("COM_REDPRODUCTFINDER_VIEWS_FORMS_DEFAULT_MIN"); ?></span><span><input type="number" min="0" name="redform[filterprice][min]" value="<?php echo $filterPriceMin ?>" /></span>
			<span><?php echo JText::_("COM_REDPRODUCTFINDER_VIEWS_FORMS_DEFAULT_MAX")?></span><span><input type="number" min="0" name="redform[filterprice][max]" value="<?php echo $filterPriceMax ?>" /></span>
		</div>
	</div>

	<input type="submit" name="submit" value="<?php echo JText::_("COM_REDPRODUCTFINDER_FORM_FORMS_SUBMIT_FORM"); ?>" />
	<input type="hidden" name="view" value="findproducts" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $template_id;?>" />
	<input type="hidden" name="redform[cid]" value="<?php echo $cid;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php echo $manufacturer_id;?>" />
	<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
