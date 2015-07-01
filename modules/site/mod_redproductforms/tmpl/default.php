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
										<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'>
										<input <?php
										foreach ($values as $key => $value) :
											if ($value['typeid'] == $type['typeid']) :
												foreach ($value['tags'] as $keyTag) :
													if ($keyTag == $tag["tagid"])
														echo 'checked="checked"';
													else
														echo '';
												endforeach;
											endif;
										endforeach; ?>
										 type="checkbox" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
										<span class='tagname'><?php echo $tag["tagname"]; ?></span>
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
									$attname = $model->getAttributeName($property->attribute_id);
									if ($attname[0] == $attribute->attribute_name) { ?>
										<li>
											<span class='taginput' data-aliases='<?php echo $attribute->attribute_name;?>'>
											<input type="checkbox"
										<?php if ($count > 0) :
												foreach ($properties as $ppt) :
													if ($ppt == $property->property_name)
														echo 'checked="checked"';
													else
														echo '';
												endforeach;
											endif;?>
											 name="redform[properties][]" value="<?php echo $property->property_name; ?>"></span>
											<span class='tagname'><?php echo $property->property_name; ?></span>
											<ul class='taglist'>
											<?php foreach($attributeSubProperties as $k_sp => $subProperty) :?>
												<?php
													$proName = $model->getPropertyName($subProperty->subattribute_id);
													if ($proName[0] == $property->property_name) : ?>
												<li>
													<span class='taginput' data-aliases='<?php echo $property->property_name;?>'>
													<input type="checkbox" name="redform[properties][]" value="<?php echo $subProperty->subattribute_color_name; ?>"></span>
													<span class='tagname'><?php echo $subProperty->subattribute_color_name; ?></span>
												</li>
												<?php endif; ?>
											<?php endforeach;?>
											</ul>
										</li>
									<?php } ?>
								<?php endforeach;?>
							</ul>
						</div>
					<?php endforeach;?>
				</div>
			<?php endif; ?>
			</div>
		</div>
		<div  class="row-fluid">
			<span><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_MIN"); ?></span><span><input type="text" name="redform[filterprice][min]" value="<?php echo $range['min'];?>"/></span>
			<span><?php echo JText::_("MOD_REDPRODUCTFORM_TMPL_DEFAULT_MAX"); ?></span><span><input type="text" name="redform[filterprice][max]" value="<?php echo $range['max'];?>"/></span>
		</div>
	</div>
	<input type="submit" name="submit" value="submit" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $templateId;?>" />
	<input type="hidden" name="redform[cid]" value="<?php if ($cid) echo $cid; elseif ($count > 0) echo $catId;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php if ($manufacturer_id) echo $manufacturer_id; elseif ($count > 0) echo $manufacturerId;?>" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" >
</form>
</div>
