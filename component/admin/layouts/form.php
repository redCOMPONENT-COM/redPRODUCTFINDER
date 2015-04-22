<?php
	$types = $displayData["types"];
	$checked = $displayData["checked"];
?>

<?php if (count($types) > 0): ?>
	<?php foreach ($types as $typeid => $type) :?>
	<div id="select_box">
		<div class="select_box_parent"><?php echo JText::_('COM_REDPRODUCTFINDER_TYPE_LIST').' '.$type['type_name'] ?></div>
		<div class="select_box_child">
			<?php
				$counttags = count($type['tags']);
			?>

			<?php if ($counttags > 0) : ?>
				<ul>
				<?php foreach ($type['tags'] as $tagid => $tag) : ?>
					<?php
						$isChecked = "";

						$tag_type = $tagid . "." . $typeid;
						if (in_array($tag_type, $checked))
						{
							$isChecked = "checked='checked'";
						}
					?>
					<li>
						<input type="checkbox" class="select_box" name="jform[tag_id][]" value="<?php echo $typeid . '.' . $tagid ?>" <?php echo $isChecked; ?>/>
						<?php echo JText::_('COM_REDPRODUCTFINDER_TAG_LIST') . ' ' . $tag['tag_name']; ?>
					</li>
				<?php endforeach;?>
				</ul>
			<?php endif;?>
		</div>
	</div>
	<?php endforeach;?>
<?php else : ?>
	<?php echo JText::_("No Type to select"); ?>
<?php endif;?>