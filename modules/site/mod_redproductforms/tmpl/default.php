<?php
/**
 * @package    RedPRODUCTFINDER.Module
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/* No direct access */
defined('_JEXEC') or die;

$input = JFactory::getApplication()->input;
$redform = $input->get('redform', array(), "array");

if ($redform)
{
	$pk = $redform;
}
else
{
	$json = $input->get('jsondata', "", "string");

	// Decode from string to array data
	$pk = json_decode($json, true);
}

$count = count($pk);
$keyTags = array();

if ($count > 0)
{
	if (isset($pk['cid']))
	{
		$catId = $pk['cid'];
	}
	else
	{
		$catId = 0;
	}

	if (isset($pk['manufacturer_id']))
	{
		$manufacturerId = $pk['manufacturer_id'];
	}
	else
	{
		$manufacturerId = 0;
	}

	if (isset($pk['filterprice']))
	{
		$filter = $pk['filterprice'];

		$min = $filter['min'];
		$max = $filter['max'];
	}
	else
	{
		$filter = array();
	}

	if ($searchBy == 1)
	{
		if (isset($pk['properties']))
		{
			$properties = $pk['properties'];
		}
		else
		{
			$properties = array();
		}
	}

	unset($pk["filterprice"]);
	unset($pk["template_id"]);
	unset($pk["manufacturer_id"]);
	unset($pk["cid"]);

	foreach ( $pk as $k => $value )
	{
		$values[] = $value;
	}

	if (isset($pk['attribute']))
	{
		$attributeCheck = $pk['attribute'];

		foreach ($attributeCheck as $pros)
		{
			if (isset($pros["subproperty"]))
			{
				foreach ($pros["subproperty"] as $k_s => $s_n)
				{
					$subName[$k_s] = $s_n;
				}
			}
		}
	}
}
?>

<div class="<?php echo $module_class_sfx; ?>">
	<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder&view=findproducts"); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
			<?php if ($searchBy == 0) : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($lists as $k => $type) :?>
						<div id='typename-<?php echo $type["typeid"];?>'>
							<label><?php echo $type["typename"];?></label>
							<ul class='taglist'>
								<?php foreach ($type["tags"] as $k_t => $tag) :?>
									<li>
										<label>
										<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'>
										<input <?php
										if (isset($value)):
											foreach ($values as $key => $value) :
												if ($value['typeid'] == $type['typeid']) :
													if (isset($value['tags'])) :
														foreach ($value['tags'] as $keyTag) :
															if ($keyTag == $tag["tagid"])
																echo 'checked="checked"';
															else
																echo '';
														endforeach;
													endif;
												endif;
											endforeach;
										endif; ?>
										 type="checkbox" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
										<span class='tagname'><?php echo $tag["tagname"]; ?></span>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<input type="hidden" name="redform[<?php echo $type["typeid"]?>][typeid]" value="<?php echo $type["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			<?php else : ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($attributes as $k_a => $attribute) :?>
							<div id='typename-<?php echo $attribute->attribute_id;?>'>
								<label><?php echo $attribute->attribute_name;?></label>
								<ul class='taglist'>
									<?php foreach($attributeProperties as $k_p => $property) :?>
										<?php
										if ($property->attribute_name == $attribute->attribute_name) : ?>
											<li>
												<label>
												<span class='taginput' data-aliases='<?php echo $attribute->attribute_name;?>'>
												<input
												<?php if (isset($pk['attribute'])) : ?>
													<?php foreach ($attributeCheck as $att) : unset($att["subproperty"]); ?>
														<?php foreach ($att as $pro) : ?>
															<?php if ($pro == $property->property_name) : ?>
																<?php echo 'checked' ?>
															<?php endif; ?>
														<?php endforeach ?>
													<?php endforeach ?>
												<?php endif; ?>
												<?php if (isset($subName)) : ?>
													<?php foreach ($subName as $key => $sub) : ?>
														<?php if ($key == $property->property_name) : ?>
															<?php echo 'checked' ?>
														<?php endif; ?>
													<?php endforeach ?>
												<?php endif; ?>
												type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][]" value="<?php echo $property->property_name; ?>">
												</span>
												<span class='tagname'><?php echo $property->property_name; ?></span>
												</label>
												<ul class='taglist'>
												<?php foreach($attributeSubProperties as $k_sp => $subProperty) :?>
													<?php
														if ($subProperty->property_name == $property->property_name) :
															$newArr[$subProperty->property_name][] = $subProperty->subattribute_color_name;
													?>
													<?php endif; ?>
												<?php endforeach;?>
												<?php  if (isset($newArr)) :
												foreach($newArr as $key => $value) :?>
													<?php if ($key == $property->property_name) : ?>
													<?php foreach(array_unique($value) as $key => $valueSub) :?>
													<li>
														<label>
														<span class='taginput' data-aliases='<?php echo $property->property_name;?>'>
														<input
														<?php if (isset($subName)) : ?>
															<?php foreach ($subName as $key => $sub) : ?>
																<?php foreach ($sub as $s) : ?>
																	<?php if ($property->property_name == $key ) : ?>
																		<?php if ($s == $valueSub) : ?>
																			<?php echo 'checked' ?>
																		<?php endif; ?>
																	<?php endif; ?>
																<?php endforeach ?>
															<?php endforeach ?>
														<?php endif; ?>
														type="checkbox" name="redform[attribute][<?php echo $attribute->attribute_name;?>][subproperty][<?php echo $property->property_name; ?>][]" value="<?php echo $valueSub; ?>">
														</span>
														<span class='tagname'><?php echo $valueSub; ?></span>
														</label>
													</li>
													<?php endforeach; ?>
													<?php endif; ?>
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
			<span><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_MIN"); ?></span><span><input type="number" class="span12" min="0" name="redform[filterprice][min]" value="<?php echo $range['min'];?>" required/></span><br>
			<span><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_MAX"); ?></span><span><input type="number" class="span12" min="0" name="redform[filterprice][max]" value="<?php echo $range['max'];?>" required/></span>
		</div>
	</div>
	<input type="submit" name="submit" value="<?php echo JText::_("MOD_REDPRODUCTFORM_FORM_FORMS_SUBMIT_FORM"); ?>" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $templateId;?>" />
	<input type="hidden" name="redform[cid]" value="<?php if ($cid) echo $cid; elseif ($count > 0) echo $catId;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php if ($manufacturer_id) echo $manufacturer_id; elseif ($count > 0) echo $manufacturerId;?>" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" >
</form>
</div>
