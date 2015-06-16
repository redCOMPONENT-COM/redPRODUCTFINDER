<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die('Restricted access');
$input = JFactory::getApplication()->input;
$redform = $input->post->get('redform', array(), "filter");

if ($redform)
{
	$pk = $redform;
}
else
{
	$json = $input->post->get('jsondata', "", "filter");
	$pk = json_decode($json, true);
}
$count = count($pk);

if ($count > 0)
{
	$catid = $pk['cid'];
	$manufacturerid = $pk['manufacturer_id'];
	$filter = $pk['filterprice'];
	$properties = $pk['properties'];
	$min = $filter['min'];
	$max = $filter['max'];

	foreach ( $pk as $k => $value )
	{
		if (!isset($value["tags"]))
		{
			continue;
		}

		foreach ( $value["tags"] as $k_t => $tag )
		{
			$keyTags[] = $tag;
		}
	}
}
?>
<div class="<?php echo $module_class_sfx; ?>">
	<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder"); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
			<?php if ($search_by == 0) { ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($lists as $k => $type) :?>
						<div id='typename-<?php echo $type["typeid"];?>'>
							<label><?php echo $type["typename"];?></label>
							<ul class='taglist'>
								<?php foreach ($type["tags"] as $k_t => $tag) :?>
									<li>
										<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'>
										<input <?php foreach ($keyTags as $key => $keyTag) {
											if ($keyTag == $tag["tagid"]) echo 'checked="checked"'; else echo ''; } ?>
										 type="checkbox" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
										<span class='tagname'><?php echo $tag["tagname"]; ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<input type="hidden" name="redform[<?php echo $type["typeid"]?>][typeid]" value="<?php echo $type["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			<?php } else { ?>
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($attributes as $k_a => $attribute) :?>
						<div id='typename-<?php echo $attribute->attribute_id;?>'>
							<label><?php echo $attribute->attribute_name;?></label>
							<ul class='taglist'>
								<?php foreach($attribute_properties as $k_p => $property) :?>
									<?php
									$attname = $model->getAttributeName($property->attribute_id);
									if ($attname[0] == $attribute->attribute_name) { ?>
										<li>
											<span class='taginput' data-aliases='<?php echo $attribute->attribute_name;?>'>
											<input type="checkbox" <?php if ($count > 0) { foreach ($properties as $ppt) {
											if ($ppt == $property->property_name) echo 'checked="checked"'; else echo ''; } } ?>
											 name="redform[properties][]" value="<?php echo $property->property_name; ?>"></span>
											<span class='tagname'><?php echo $property->property_name; ?></span>
											<ul class='taglist'>
											<?php foreach($attribute_subproperties as $k_sp => $subproperty) :?>
												<?php
													$proname = $model->getPropertyName($subproperty->subattribute_id);
													if ($proname[0] == $property->property_name) { ?>
												<li>
													<span class='taginput' data-aliases='<?php echo $property->property_name;?>'>
													<input type="checkbox" name="redform[properties][]" value="<?php echo $subproperty->subattribute_color_name; ?>"></span>
													<span class='tagname'><?php echo $subproperty->subattribute_color_name; ?></span>
												</li>
												<?php } ?>
											<?php endforeach;?>
											</ul>
										</li>
									<?php } ?>
								<?php endforeach;?>
							</ul>
						</div>
					<?php endforeach;?>
				</div>
			<?php } ?>
			</div>
		</div>
		<div  class="row-fluid">
			Min: <input type="text" name="redform[filterprice][min]" value="<?php echo $range['min'];?>"/>
			Max: <input type="text" name="redform[filterprice][max]" value="<?php echo $range['max'];?>"/>
		</div>
	</div>
	<input type="submit" name="submit" value="submit" />
	<input type="hidden" name="task" value="findproducts.find" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="view" value="<?php echo $view; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $template_id;?>" />
	<input type="hidden" name="redform[cid]" value="<?php if ($cid) echo $cid; elseif ($count > 0) echo $catid;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php if ($manufacturer_id) echo $manufacturer_id; elseif ($count > 0) echo $manufacturerid;?>" />
	<input type="hidden" name="limitstart" value="0" />
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" >
</form>


<div class="slide-wrapper">
	<div id="slider-range"></div>
</div>
</div>

<style type="text/css">
	.slide-wrapper >div:not(#slider-range)
	{
		height: 50px!important;
		overflow: visible!important;
	}

	#slider-range
	{
		margin: 0!important;
		overflow: visible!important;
	}
</style>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#slider-range').slider({
			range: true,
			min: <?php echo $range['min'];?>,
			max: <?php echo $range['max'];?>,
			values: [ <?php if ($count > 0) echo $min; else echo $range['max']/4;?>, <?php if ($count > 0) echo $max; else echo $range['max']-($range['max']/4);?> ],
			slide: function(event, ui){
				$("[name='redform[filterprice][min]']").val(ui.values[ 0 ]);
				$("[name='redform[filterprice][max]']").val(ui.values[ 1 ]);
			},
			change: function(event, ui){
				//submit form when sliding range
				$("#redproductfinder-form").submit();
			}
		});
		$("[name='redform[filterprice][min]']").val($('#slider-range').slider( "values", 0 ));
		$("[name='redform[filterprice][max]']").val($('#slider-range').slider( "values", 1 ));

		$('#redproductfinder-form').each(function(index, el) {
			var finder_form = $(this);
			var parent_suffix = '-parent';
			var parent_tagname = [];
			$(this).find('[id*="typename-"]').each(function(index, el) {
				cur_typename = $(this);
				$(this).find('.taginput').each(function(index, el) {
					if ($(this).is('[data-aliases*="'+parent_suffix+'"]')) {
						parent_tagname.push($(this).attr('data-aliases'));
					};
				});
				$(this).find('>.taglist').each(function(index, el) {
					if (parent_tagname.length>0) {
						//create tabheader
						$(this).before('<ul class="tabheader-bar"></ul>');

						while(parent_tagname.length > 0){
							root_tagname = parent_tagname.pop();
							child_tagname = root_tagname.replace(parent_suffix, "");

							//create selecttab
							$(this).after('<div id="tab-'+root_tagname+'"><ul></ul></div>');

							// append elemt to created selecttab
							$(this).find('[data-aliases="'+child_tagname+'"]').each(function(index, el) {
								var temp_pos = $('div#tab-'+root_tagname).find('>ul');
								$(this).parent('li').appendTo(temp_pos);
							});

							$(this).find('[data-aliases="'+root_tagname+'"]').each(function(index, el) {
								$(this).parent('li').appendTo(cur_typename.find('ul.tabheader-bar'));
							});
						}
						//remove this empty ul
						$(this).remove();
					};
				});
				//change this list into filter filed
				$(this).find('.tabheader-bar').each(function(index, el) {
					$(this).find('>li').each(function(index, el) {
						li_aliases = $(this).find('.taginput').attr('data-aliases');
						li_title = $(this).find('.tagname').html();
						li_content = "<a href='#tab-"+li_aliases+"'>"+li_title+"</a>";
						$(this).html(li_content);
					});
				});
				//add html to tab by jquery UI
				$(this).tabs();
			});
		});
		var ajaxpos = $(this).find('#main');
		$("#redproductfinder-form").submit(function(ev) {
		    var frm = $("#redproductfinder-form");
		    $.ajax({
					type: "POST",
					url: frm.attr('action'),
					data: frm.serialize(), // serializes the form's elements.
					success: function(data)
					{
						if (data !== false)
						{
							ajaxpos.html(data);
						}
					}
		         });
		    return false; // avoid to execute the actual submit of the form.
		    ev.preventDefault();
		});

		//submit form when input clicked
		$("#redproductfinder-form").each(function(index, el) {
			var submit_frm = $(this);
			$(this).find('input').on('change', function(event) {
				submit_frm.submit();
			});
		});

	});
</script>