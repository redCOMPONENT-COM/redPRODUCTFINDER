<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * redPRODUCTFINDER
 */

JLoader::import('forms', JPATH_SITE . '/components/com_redproductfinder/helpers');

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
$range = 0;
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
							<ul class='taglist'>
								<?php foreach ($value["tags"] as $k_t => $tag) :?>
									<li>
										<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'><input type="checkbox" name="redform[<?php echo $value["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
										<span class='tagname'><?php echo $tag["tagname"]; ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<input type="hidden" name="redform[<?php echo $value["typeid"]?>][typeid]" value="<?php echo $value["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			<?php else : ?>
				<div  class="row-fluid form-horizontal-desktop">
					<?php foreach($attributes as $k_a => $attribute) :?>
						<div id='typename-<?php echo $attribute->attribute_id;?>'>
							<label><?php echo $attribute->attribute_name;?></label>
							<ul class='taglist'>
								<?php foreach($attributeProperties as $k_p => $property) :?>
									<?php
									$attname = $model->getAttributeName($property->attribute_id);
									if ($attname[0] == $attribute->attribute_name) : ?>
										<li>
											<span class='taginput'><input type="checkbox" name="redform[properties][]" value="<?php echo $property->property_name; ?>"></span>
											<span class='tagname'><?php echo $property->property_name; ?></span>
											<ul class='taglist'>
											<?php foreach($attributeSubProperties as $k_sp => $subProperty) :?>
												<?php
													$proName = $model->getPropertyName($subProperty->subattribute_id);
													if ($proName[0] == $property->property_name) : ?>
												<li>
													<span class='taginput'>
													<input type="checkbox" name="redform[properties][]" value="<?php echo $subProperty->subattribute_color_name; ?>"></span>
													<span class='tagname'><?php echo $subProperty->subattribute_color_name; ?></span>
												</li>
												<?php endif; ?>
											<?php endforeach;?>
											</ul>
										</li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			</div>
		</div>
		<div  class="row-fluid">
			Min: <input type="text" name="redform[filterprice][min]" value="<?php echo $range['min'];?>"/>
			Max: <input type="text" name="redform[filterprice][max]" value="<?php echo $range['max'];?>"/>
		</div>
	</div>
	<input type="submit" name="submit" value="submit" />
	<input type="hidden" name="view" value="findproducts" />
	<input type="hidden" name="formid" value="<?php echo $formid; ?>" />
	<input type="hidden" name="redform[template_id]" value="<?php echo $template_id;?>" />
	<input type="hidden" name="redform[cid]" value="<?php echo $cid;?>" />
	<input type="hidden" name="redform[manufacturer_id]" value="<?php echo $manufacturer_id;?>" />
	<?php echo JHtml::_('form.token'); ?>

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
			values: [ <?php echo $range['max']/4;?>, <?php echo $range['max']-($range['max']/4);?> ],
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
