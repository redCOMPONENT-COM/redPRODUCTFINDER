<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$redconfig = new Redconfiguration;
$productHelper = new producthelper;
?>
<div class="row">
	<?php foreach ( $this->products as $key => $product ) : ?>
	  <div>
	  	<div>
	  		<?php
	  			$pname  = $redconfig->maxchar($product->product_name, CATEGORY_PRODUCT_TITLE_MAX_CHARS, CATEGORY_PRODUCT_TITLE_END_SUFFIX);
	  			$catId  = $productHelper->getCategoryProduct($product->product_id);
	  			$specificLink = $this->dispatcher->trigger('createProductLink', array($product));

	  			if (empty($specificLink))
	  			{
	  				$link = JRoute::_(
	  					'index.php?option=com_redshop' .
	  					'&view=product&pid=' . $product->product_id .
	  					'&cid=' . $catId .
	  					'&Itemid=' . $this->Itemid
	  				);
	  			}
	  			else
	  			{
	  				$link = $specificLink[0];
	  			}
	  		?>
	  		<a href="<?php echo $link; ?>"><?php echo $pname; ?></a>
	  	</div>
	  	<div>
	  		<?php $thumbUrl = RedShopHelperImages::getImagePath($product->product_full_image, '', 'thumb', 'product', 200, 200);?>

	  		<img src="<?php echo $thumbUrl; ?>"/>
	  	</div>
	  </div>
  	<?php endforeach; ?>
</div>