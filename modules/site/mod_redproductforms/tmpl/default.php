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
						<div><?php echo $type["typename"];?></div>
						<ul>
							<?php foreach ($type["tags"] as $k_t => $tag) :?>
								<li>
									<span><input type="checkbox" name="redform[<?php echo $type["typeid"]?>][tags][]" value="<?php echo $tag["tagid"]; ?>"></span>
									<span><?php echo $tag["tagname"]; ?></span>
								</li>
							<?php endforeach; ?>

							<input type="hidden" name="redform[<?php echo $type["typeid"]?>][typeid]" value="<?php echo $type["typeid"]; ?>">
						</ul>
					<?php endforeach;?>
				</div>
			</div>
		</div>
	</div>

	<input type="submit" name="submit" value="submit" />
	<input type="hidden" name="view" value="findproducts" />
	<input type="hidden" name="formid" value="<?php echo $params->get("form_id");?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>