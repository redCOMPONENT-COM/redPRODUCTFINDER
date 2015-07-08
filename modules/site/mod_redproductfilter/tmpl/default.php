
<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
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
}

?>
<div class="<?php echo $module_class_sfx; ?>">
	<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder&view=findproducts&Itemid=" . $Itemid); ?>" method="post" name="adminFilterForm" id="redproductfinder-form" class="form-validate">
	<?php if ($saveFilter) : ?>
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
				<?php foreach($saveFilter as $key => $value) : ?>
					<?php foreach($types as $k => $type) :?>
						<?php if ($key == $type->id) : ?>
							<?php foreach($value as $tag_id => $type_tag) : ?>
								<div id='typename-<?php echo $type->id?>'>
									<?php foreach($tags as $kt => $tag) : ?>
										<?php if ($tag->type_id == $type->id) : ?>
											<?php if ($tag->tag_id == $tag_id) : ?>
											<span> <?php echo $tag->type_name . ' - ' . $tag->tag_name ?></span>
											<a style="float: right" href="javascript:void(0)" onclick="submitForm('<?php echo $tag->type_id ?>', '<?php echo $tag->tag_id?>', 'delete')" > <?php echo JText::_("MOD_REDPRODUCTFILTER_DELETE"); ?></a>
											<?php endif; ?>
										<?php endif; ?>
									<?php endforeach;?>
								</div>
							<?php endforeach;?>
						<?php endif; ?>
					<?php endforeach;?>
				<?php endforeach;?>
			</div>
		</div>
	</div>
	<a href="javascript:void(0)" onclick="submitForm('', '', 'clear')" > <?php echo JText::_("MOD_REDPRODUCTFILTER_CLEAR_ALL"); ?></a>
	<hr>
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($types as $k => $type) :?>
						<div id='typename-<?php echo $type->id?>'>
							<label><?php echo $type->type_name;?></label>
							<?php foreach($tags as $kt => $tag) : ?>
								<?php if ($tag->type_id == $type->id) : ?>
									<br><a href="javascript:void(0)" onclick="submitForm('<?php echo $tag->type_id ?>', '<?php echo $tag->tag_id?>', 'add')" > <?php echo $tag->tag_name ?></a>
								<?php endif; ?>
							<?php endforeach;?>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>
	<?php else: ?>
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($types as $k => $type) :?>
						<div id='typename-<?php echo $type->id?>'>
							<label><?php echo $type->type_name;?></label>
							<?php foreach($tags as $kt => $tag) : ?>
								<?php if ($tag->type_id == $type->id) : ?>
									<br><a href="javascript:void(0)" onclick="submitForm('<?php echo $tag->type_id ?>', '<?php echo $tag->tag_id?>', 'add')" > <?php echo $tag->tag_name ?></a>
								<?php endif; ?>
							<?php endforeach;?>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $templateId;?>" />
	<input type="hidden" name="redform[cid]" value="<?php if ($cid) echo $cid; elseif ($count > 0) echo $catId;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php if ($manufacturer_id) echo $manufacturer_id; elseif ($count > 0) echo $manufacturerId;?>" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" >
	<input type="hidden" name="tempType" value="<?php echo $tempType; ?>"/>
	<input type="hidden" name="tempTag" value="<?php echo $tempTag; ?>"/>
	<input type="hidden" name="act" value="<?php echo $act; ?>"/>
</form>
</div>

<script type="text/javascript">
	function submitForm(typeId, tagId, action)
	{
		var form = document.adminFilterForm;

		document.adminFilterForm.tempType.value = typeId;
		document.adminFilterForm.tempTag.value = tagId;
		document.adminFilterForm.act.value = action;

		form.submit();
	}
</script>