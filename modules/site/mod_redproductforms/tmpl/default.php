<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die('Restricted access');

?>
<form action="<?php echo JRoute::_("index.php?option=com_redproductfinder"); ?>" method="post" name="adminForm" id="redproductfinder-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<?php foreach($lists as $k => $type) :?>
						<div id='typename-<?php echo $type["typeid"];?>'>
							<label><?php echo $type["typename"];?></label>
							<ul class='taglist'>
								<?php foreach ($type["tags"] as $k_t => $tag) :?>
									<li>
										<span class='taginput' data-aliases='<?php echo $tag["aliases"];?>'><input type="checkbox" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
										<span class='tagname'><?php echo $tag["tagname"]; ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
						<input type="hidden" name="redform[<?php echo $type["typeid"]?>][typeid]" value="<?php echo $type["typeid"]; ?>">
					<?php endforeach;?>
				</div>
			</div>
		</div>
		<div  class="row-fluid">
			Min: <input type="text" name="redform[filterprice][min]" value="0"/>
			Max: <input type="text" name="redform[filterprice][max]" value="100"/>
		</div>
	</div>
	<input type="submit" name="submit" value="submit" />
<!-- 	<input type="hidden" name="view" value="findproducts" /> -->
	<input type="hidden" name="task" value="findproducts.find" />
	<input type="hidden" name="formid" value="<?php echo $params->get("form_id");?>" />
	<?php echo JHtml::_('form.token'); ?>

</form>
<div class="slide-wrapper">
	<div id="slider-range"></div>
</div>

<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#slider-range').slider({
			range: true,
			min: 0,
			max: 500,
			values: [ 75, 300 ]
		});
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
	});
</script>