<?php
/**
 * @package     Redproductfinder.Frontend
 * @subpackage  redPRODUCTFINDER Module
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die;

?>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  jQuery(function() {
    $( "#slider-range" ).slider({
      range: true,
      values: [ 17, 67 ]
    });
  });
  </script>

<div id="slider-range" height="10px"></div>

