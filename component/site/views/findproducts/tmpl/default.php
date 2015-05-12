<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

?>
<div class="row">
	<?php foreach ( $this->products as $key => $value ) : ?>
	  <div>
	  	<div><?php echo $value->product_name?></div>
	  	<div>
	  		<?php $thumbUrl = RedShopHelperImages::getImagePath(
									$value->product_full_image,
									'',
									'thumb',
									'product'
								);?>
	  	</div>
	  </div>
  	<?php endforeach; ?>
</div>