<?php
/**
 * @package    RedPRODUCTFINDER.Backend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$param = JComponentHelper::getParams('com_redproductfinder');
$template_id = $param->get('prod_template');
$input = JFactory::getApplication()->input;
$redform = $input->post->get('redform', array(), "filter");
$isredshop = JComponentHelper::isEnabled('com_redshop');
$app = JFactory::getApplication();
$menu = $app->getMenu();
$menuname = $menu->getItems();
$home = $menuname[0];
$id_home = $home->id;
$title_home = $home->title;
$link_home = $home->link;

if (!$isredshop)
{
	JError::raiseError('500', 'redShop Component is not installed');
}

// Set path to redshop
$redshop_site_path = JPATH_SITE . '/components/com_redshop';
$redshop_admin_path = JPATH_SITE . '/administrator/components/com_redshop';

// Getting the redshop configuration
if (file_exists(JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/redshop.cfg.php';
}

require_once $redshop_admin_path . '/helpers/template.php';
require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/configuration.php';
require_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/stockroom.php';

require_once $redshop_site_path . '/helpers/helper.php';
require_once $redshop_admin_path . '/helpers/text_library.php';
require_once $redshop_site_path . '/models/category.php';
require_once $redshop_site_path . '/models/manufacturers.php';
require_once $redshop_site_path . '/models/search.php';

$producthelper = new producthelper;

// $search_model = new searchModelSearch;
$objhelper = new redhelper;
$Redconfiguration = new Redconfiguration;
$Redconfiguration->defineDynamicVars();
$extraField = new extraField;
$stockroomhelper = new rsstockroomhelper;
$redTemplate = new Redtemplate;
$texts = new text_library;
$productHelper = new producthelper;

$order_data            = $objhelper->getOrderByList();
$getorderby            = JRequest::getString('order_by', DEFAULT_PRODUCT_ORDERING_METHOD);
$lists['order_select'] = JHTML::_('select.genericlist', $order_data, 'order_by', 'class="inputbox" size="1" onchange="document.orderby_form.submit();" ', 'value', 'text', $getorderby);
$option = 'com_redshop';
$loadCategorytemplate = '';
$layout = JRequest::getCmd('layout', '');
$model  = $this->getModel('findproducts');
$catid = $model->getState('catid');
$attribute_template = '';
$userfieldArr = '';
$count_no_user_field = '';
$product_data = '';
$extraFieldName = '';
$Itemid = JRequest::getInt('Itemid');
$fieldArray = $extraField->getSectionFieldList(17, 0, 0);

$template_array = $redTemplate->getTemplate("redproductfinder", $template_id);
$template_desc = $template_array[0]->template_desc;

// Begin replace template
$template_desc = str_replace("{total_product_lbl}", JText::_('COM_REDSHOP_TOTAL_PRODUCT'), $template_desc);
$template_desc = str_replace("{total_product}", count($this->products), $template_desc);

if (strstr($template_desc, "{product_loop_start}") && strstr($template_desc, "{product_loop_end}"))
{
	// Get only Product template
	$template_d1 = explode("{product_loop_start}", $template_desc);
	$template_d2 = explode("{product_loop_end}", $template_d1[1]);
	$template_product = $template_d2[0];

	// Loop product lists
	foreach ($this->products as $key => $pd)
	{
		$pid = $pd->product_id;
		$product = $producthelper->getProductById($pid);

		// Count accessory
		$accessorylist = $producthelper->getProductAccessory(0, $product->product_id);
		$totacc = count($accessorylist);

		$data_add = $template_product;

		// ProductFinderDatepicker Extra Field Start
		$data_add = $producthelper->getProductFinderDatepickerValue($template_product, $product->product_id, $fieldArray);

		$ItemData = $producthelper->getMenuInformation(0, 0, '', 'product&pid=' . $product->product_id);

		$catidmain = JRequest::getVar("cid");

		if (count($ItemData) > 0)
		{
			$pItemid = $ItemData->id;
		}
		else
		{
			$pItemid = $objhelper->getItemid($product->product_id, $catidmain);
		}

		$data_add = str_replace("{product_id_lbl}", JText::_('COM_REDSHOP_PRODUCT_ID_LBL'), $data_add);
		$data_add = str_replace("{product_id}", $product->product_id, $data_add);
		$data_add = str_replace("{product_number_lbl}", JText::_('COM_REDSHOP_PRODUCT_NUMBER_LBL'), $data_add);
		$product_number_output = '<span id="product_number_variable' . $product->product_id . '">' . $product->product_number . '</span>';
		$data_add = str_replace("{product_number}", $product_number_output, $data_add);

		// Replace VAT information
		$data_add = $producthelper->replaceVatinfo($data_add);

		$link = JRoute::_('index.php?option=com_redshop&view=product&pid=' . $product->product_id . '&cid=' . $catid . '&Itemid=' . $pItemid);

		$pname = $Redconfiguration->maxchar($product->product_name, CATEGORY_PRODUCT_TITLE_MAX_CHARS, CATEGORY_PRODUCT_TITLE_END_SUFFIX);

		$product_nm = $pname;

		if (strstr($data_add, '{product_name_nolink}'))
		{
			$data_add = str_replace("{product_name_nolink}", $product_nm, $data_add);
		}

		if (strstr($data_add, '{product_name}'))
		{
			$pname = "<a href='" . $link . "' title='" . $product->product_name . "'>" . $pname . "</a>";
			$data_add = str_replace("{product_name}", $pname, $data_add);
		}

		if (strstr($data_add, '{category_product_link}'))
		{
			$data_add = str_replace("{category_product_link}", $link, $data_add);
		}

		if (strstr($data_add, '{read_more}'))
		{
			$rmore = "<a href='" . $link . "' title='" . $product->product_name . "'>" . JText::_('COM_REDSHOP_READ_MORE') . "</a>";
			$data_add = str_replace("{read_more}", $rmore, $data_add);
		}

		if (strstr($data_add, '{read_more_link}'))
		{
			$data_add = str_replace("{read_more_link}", $link, $data_add);
		}

		if (strstr($data_add, '{product_s_desc}'))
		{
			$p_s_desc = $Redconfiguration->maxchar($product->product_s_desc, CATEGORY_PRODUCT_SHORT_DESC_MAX_CHARS, CATEGORY_PRODUCT_SHORT_DESC_END_SUFFIX);
			$data_add = str_replace("{product_s_desc}", $p_s_desc, $data_add);
		}

		if (strstr($data_add, '{product_desc}'))
		{
			$p_desc = $Redconfiguration->maxchar($product->product_desc, CATEGORY_PRODUCT_DESC_MAX_CHARS, CATEGORY_PRODUCT_DESC_END_SUFFIX);
			$data_add = str_replace("{product_desc}", $p_desc, $data_add);
		}

		if (strstr($data_add, '{product_rating_summary}'))
		{
			// Product Review/Rating Fetching reviews
			$final_avgreview_data = $producthelper->getProductRating($product->product_id);
			$data_add = str_replace("{product_rating_summary}", $final_avgreview_data, $data_add);
		}

		if (strstr($data_add, '{manufacturer_link}'))
		{
			$manufacturer_link_href = JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=detail&mid=' . $product->manufacturer_id . '&Itemid=' . $Itemid);
			$manufacturer_link = '<a href="' . $manufacturer_link_href . '" title="' . $product->manufacturer_name . '">' . $product->manufacturer_name . '</a>';
			$data_add = str_replace("{manufacturer_link}", $manufacturer_link, $data_add);

			if (strstr($data_add, "{manufacturer_link}"))
			{
				$data_add = str_replace("{manufacturer_name}", "", $data_add);
			}
		}

		if (strstr($data_add, '{manufacturer_product_link}'))
		{
			$manufacturerPLink = "<a href='" . JRoute::_('index.php?option=com_redshop&view=manufacturers&layout=products&mid=' . $product->manufacturer_id . '&Itemid=' . $Itemid) . "'>" . JText::_("COM_REDSHOP_VIEW_ALL_MANUFACTURER_PRODUCTS") . " " . $product->manufacturer_name . "</a>";
			$data_add = str_replace("{manufacturer_product_link}", $manufacturerPLink, $data_add);
		}

		if (strstr($data_add, '{manufacturer_name}'))
		{
			$data_add = str_replace("{manufacturer_name}", $product->manufacturer_name, $data_add);
		}

		if (strstr($data_add, "{product_thumb_image_3}"))
		{
			$pimg_tag = '{product_thumb_image_3}';
			$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT_3;
			$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH_3;
		}
		elseif (strstr($data_add, "{product_thumb_image_2}"))
		{
			$pimg_tag = '{product_thumb_image_2}';
			$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT_2;
			$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH_2;
		}
		elseif (strstr($data_add, "{product_thumb_image_1}"))
		{
			$pimg_tag = '{product_thumb_image_1}';
			$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT;
			$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH;
		}
		else
		{
			$pimg_tag = '{product_thumb_image}';
			$ph_thumb = CATEGORY_PRODUCT_THUMB_HEIGHT;
			$pw_thumb = CATEGORY_PRODUCT_THUMB_WIDTH;
		}

		$hidden_thumb_image = "<input type='hidden' name='prd_main_imgwidth' id='prd_main_imgwidth' value='" . $pw_thumb . "'><input type='hidden' name='prd_main_imgheight' id='prd_main_imgheight' value='" . $ph_thumb . "'>";
		$thum_image = $producthelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb, 2, 1);
		/* product image flying addwishlist time start */
		$thum_image = "<span class='productImageWrap' id='productImageWrapID_" . $product->product_id . "'>" . $producthelper->getProductImage($product->product_id, $link, $pw_thumb, $ph_thumb, 2, 1) . "</span>";

		/* product image flying addwishlist time end*/
		$data_add = str_replace($pimg_tag, $thum_image . $hidden_thumb_image, $data_add);

		/* front-back image tag */
		if (strstr($data_add, "{front_img_link}") || strstr($data_add, "{back_img_link}"))
		{
			if ($this->_data->product_thumb_image)
			{
				$mainsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_thumb_image;
			}
			else
			{
				$mainsrcPath = $url . "components/com_redshop/helpers/thumb.php?filename=product/" . $product->product_full_image . "&newxsize=" . $pw_thumb . "&newysize=" . $ph_thumb . "&swap=" . USE_IMAGE_SIZE_SWAPPING;
			}

			if ($this->_data->product_back_thumb_image)
			{
				$backsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_back_thumb_image;
			}
			else
			{
				$backsrcPath = $url . "components/com_redshop/helpers/thumb.php?filename=product/" . $product->product_back_full_image . "&newxsize=" . $pw_thumb . "&newysize=" . $ph_thumb . "&swap=" . USE_IMAGE_SIZE_SWAPPING;
			}

			$ahrefpath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_full_image;
			$ahrefbackpath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_back_full_image;

			$product_front_image_link = "<a href='#' onClick='javascript:changeproductImage(" . $product->product_id . ",\"" . $mainsrcPath . "\",\"" . $ahrefpath . "\");'>" . JText::_('COM_REDSHOP_FRONT_IMAGE') . "</a>";
			$product_back_image_link = "<a href='#' onClick='javascript:changeproductImage(" . $product->product_id . ",\"" . $backsrcPath . "\",\"" . $ahrefbackpath . "\");'>" . JText::_('COM_REDSHOP_BACK_IMAGE') . "</a>";

			$data_add = str_replace("{front_img_link}", $product_front_image_link, $data_add);
			$data_add = str_replace("{back_img_link}", $product_back_image_link, $data_add);
		}
		else
		{
			$data_add = str_replace("{front_img_link}", "", $data_add);
			$data_add = str_replace("{back_img_link}", "", $data_add);
		}
		/* front-back image tag end */


		/* product preview image. */
		if (strstr($data_add, '{product_preview_img}'))
		{
			if (is_file(REDSHOP_FRONT_IMAGES_RELPATH . 'product/' . $product->product_preview_image))
			{
				$previewsrcPath = $url . "components/com_redshop/helpers/thumb.php?filename=product/" . $product->product_preview_image . "&newxsize=" . CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH . "&newysize=" . CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT . "&swap=" . USE_IMAGE_SIZE_SWAPPING;
				$previewImg = "<img src='" . $previewsrcPath . "' class='rs_previewImg' />";
				$data_add = str_replace("{product_preview_img}", $previewImg, $data_add);
			}
			else
			{
				$data_add = str_replace("{product_preview_img}", "", $data_add);
			}
		}

		// 	product preview image end.

		/* front-back preview image tag... */
		if (strstr($data_add, "{front_preview_img_link}") || strstr($data_add, "{back_preview_img_link}"))
		{
			if ($product->product_preview_image)
			{
				$mainpreviewsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_preview_image . "&newxsize=" . CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH . "&newysize=" . CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT . "&swap=" . USE_IMAGE_SIZE_SWAPPING;
			}

			if ($product->product_preview_back_image)
			{
				$backpreviewsrcPath = REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $product->product_preview_back_image . "&newxsize=" . CATEGORY_PRODUCT_PREVIEW_IMAGE_WIDTH . "&newysize=" . CATEGORY_PRODUCT_PREVIEW_IMAGE_HEIGHT . "&swap=" . USE_IMAGE_SIZE_SWAPPING;
			}

			$product_front_image_link = "<a href='#' onClick='javascript:changeproductPreviewImage(" . $product->product_id . ",\"" . $mainpreviewsrcPath . "\");'>" . JText::_('COM_REDSHOP_FRONT_IMAGE') . "</a>";
			$product_back_image_link = "<a href='#' onClick='javascript:changeproductPreviewImage(" . $product->product_id . ",\"" . $backpreviewsrcPath . "\");'>" . JText::_('COM_REDSHOP_BACK_IMAGE') . "</a>";

			$data_add = str_replace("{front_preview_img_link}", $product_front_image_link, $data_add);
			$data_add = str_replace("{back_preview_img_link}", $product_back_image_link, $data_add);
		}
		else
		{
			$data_add = str_replace("{front_preview_img_link}", "", $data_add);
			$data_add = str_replace("{back_preview_img_link}", "", $data_add);
		}
		/* front-back preview image tag end */

		$data_add = $producthelper->getJcommentEditor($product, $data_add);

		/*
		* product loop template extra field
		* lat arg set to "1" for indetify parsing data for product tag loop in category
		* last arg will parse {producttag:NAMEOFPRODUCTTAG} nameing tags.
		* "1" is for section as product
		*/
		if (count($loadCategorytemplate) > 0)
		{
			$data_add = $producthelper->getExtraSectionTag($extraFieldName, $product->product_id, "1", $data_add, 1);
		}

		/************************************
		*  Conditional tag
		*  if product on discount : Yes
		*  {if product_on_sale} This product is on sale {product_on_sale end if} // OUTPUT : This product is on sale
		*  NO : // OUTPUT : Display blank
		************************************/
		$data_add = $producthelper->getProductOnSaleComment($product, $data_add);

		/* replace wishlistbutton */
		$data_add = $producthelper->replaceWishlistButton($product->product_id, $data_add);

		/* replace compare product button */
		$data_add = $producthelper->replaceCompareProductsButton($product->product_id, $catid, $data_add);

		if (strstr($data_add, "{stockroom_detail}"))
		{
			$data_add = $stockroomhelper->replaceStockroomAmountDetail($data_add, $product->product_id);
		}

		/* checking for child products */
		$childproduct = $producthelper->getChildProduct($product->product_id);

		if (count($childproduct) > 0)
		{
			if (PURCHASE_PARENT_WITH_CHILD == 1)
			{
				$isChilds = false;
				/* get attributes */
				$attributes_set = array();

				if ($product->attribute_set_id > 0)
				{
					$attributes_set = $producthelper->getProductAttribute(0, $product->attribute_set_id, 0, 1);
				}

				$attributes = $producthelper->getProductAttribute($product->product_id);
				$attributes = array_merge($attributes, $attributes_set);
			}
			else
			{
				$isChilds = true;
				$attributes = array();
			}
		}
		else
		{
			$isChilds = false;

			/*  get attributes */
			$attributes_set = array();

			if ($product->attribute_set_id > 0)
			{
				$attributes_set = $producthelper->getProductAttribute(0, $product->attribute_set_id, 0, 1);
			}

			$attributes = $producthelper->getProductAttribute($product->product_id);
			$attributes = array_merge($attributes, $attributes_set);
		}

		/* Product attribute  Start */
		$totalatt = count($attributes);
		/* check product for not for sale */

		$data_add = $producthelper->getProductNotForSaleComment($product, $data_add, $attributes);
		/* echo $data_add;die(); */
		$data_add = $producthelper->replaceProductInStock($product->product_id, $data_add, $attributes, $attribute_template);

		$data_add = $producthelper->replaceAttributeData($product->product_id, 0, 0, $attributes, $data_add, $attribute_template, $isChilds);

		/* get cart tempalte */
		$data_add = $producthelper->replaceCartTemplate($product->product_id, $catid, 0, 0, $data_add, $isChilds, $userfieldArr, $totalatt, $totacc, $count_no_user_field);

		$product_data .= $data_add;
	}

	$product_tmpl = $product_data;

	$db    = JFactory::getDbo();
	$query = 'SELECT category_name'
	. ' FROM #__redshop_category  '
	. 'WHERE category_id=' . $catid;
	$db->setQuery($query);

	$cat_name = null;

	if ($catid)
	{
		if ($catname_array = $db->loadObjectList())
		{
			$cat_name = $catname_array[0]->category_name;
		}
	}

	$db    = JFactory::getDbo();
	$query = 'SELECT category_name, category_id'
	. ' FROM #__redshop_category AS c '
	. ' INNER JOIN #__redshop_category_xref AS cx ON cx.category_parent_id = c.category_id'
	. ' WHERE cx.category_child_id = ' . $catid;
	$db->setQuery($query);

	$cat_parent_name = null;

	if ($catid)
	{
		if ($catparentname_array = $db->loadObjectList())
		{
			$cat_parent_name = $catparentname_array[0]->category_name;
			$cat_parent_id = $catparentname_array[0]->category_id;
			$parentItemid = $objhelper->getItemid("", $cat_parent_id);
			$cat_bread = "<li><a class='pathway' href='" . JURI::root() . "index.php?option=com_redshop&amp;view=category&amp;layout=detail&amp;cid=" . $cat_parent_id . "&amp;Itemid=" . $parentItemid . "'>" . $cat_parent_name . "</a>";
			$cat_bread .= "<span class='divider'><i class='icon-caret-right'></i></span></li>";
		}
	}

	// Breadcrumbs
	$breadcrumb = "<div id='breadcrumbs'><div class='module'><ul class='breadcrumb'>";
	$breadcrumb .= "<li class='active'><span class='divider icon-map-marker'></span></li>";
	$breadcrumb .= "<li><a class='pathway' href='" . JURI::root() . $link_home . "&amp;Itemid=" . $id_home . "'>" . $title_home . "</a>";
	$breadcrumb .= "<span class='divider'><i class='icon-caret-right'></i></span></li>";

	if ($parentItemid)
	{
		$breadcrumb .= $cat_bread;
	}

	$breadcrumb .= "<li class='active'><span>" . $cat_name . "</span></li>";
	$breadcrumb .= "</ul></div></div>";

	// Order By
	$order_by     = "";
	$orderby_form = "<form name='orderby_form' action='index.php?option=com_redproductfinder&view=findproducts' method='post' >";
	$orderby_form .= $lists['order_select'];
	$orderby_form .= "<input type='hidden' name='view' value='findproducts'>
	<input type='hidden' name='limitstart' value='$start'>
	<input type='hidden' name='jsondata' value='" . $this->json . "'></form>";

	if (strstr($template_desc, '{order_by}'))
	{
		$order_by = $orderby_form;
	}

	if (strstr($template_desc, "{pagination}"))
	{
		$pagination = $model->getPagination();
		$link = JURI::root() . substr(JRoute::_('index.php?option=com_redproductfinder&cid=' . $catid . '&view=findproducts', false), strlen(JURI::base(true)) + 1);
		
		$pagination->setAdditionalUrlParam('', $link);
		$template_desc = str_replace("{pagination}", $pagination->getPagesLinks(), $template_desc);
	}

	$usePerPageLimit = false;

	if (strstr($template_desc, "perpagelimit:"))
	{
		$usePerPageLimit = true;
		$perpage       = explode('{perpagelimit:', $template_desc);
		$perpage       = explode('}', $perpage[1]);
		$template_desc = str_replace("{perpagelimit:" . intval($perpage[0]) . "}", "", $template_desc);
	}

	if (strstr($template_desc, "{product_display_limit}"))
	{
		if ($usePerPageLimit == false)
		{
			$limitBox = '';
		}
		else
		{
			$limitBox = "<form action='index.php?option=com_redproductfinder&view=findproducts' method='post'>
				<input type='hidden' name='view' value='findproducts'>
				<input type='hidden' name='jsondata' value='" . $this->json . "'>"
				. $pagination->getLimitBox() . "</form>";
		}

		$template_desc = str_replace("{product_display_limit}", $limitBox, $template_desc);
	}

	$template_desc = str_replace("{order_by}", $orderby_form, $template_desc);
	$template_desc = str_replace("{product_loop_start}", "", $template_desc);
	$template_desc = str_replace("{product_loop_end}", "", $template_desc);
	$template_desc = str_replace("{category_main_name}", $cat_name, $template_desc);
	$template_desc = str_replace("{breadcrumbs}", $breadcrumb, $template_desc);
	$template_desc = str_replace("{category_main_description}", '', $template_desc);
	$template_desc = str_replace($template_product, $product_tmpl, $template_desc);
	$template_desc = str_replace("{with_vat}", "", $template_desc);
	$template_desc = str_replace("{without_vat}", "", $template_desc);
	$template_desc = str_replace("{attribute_price_with_vat}", "", $template_desc);
	$template_desc = str_replace("{attribute_price_without_vat}", "", $template_desc);
	$template_desc = str_replace("{redproductfinderfilter_formstart}", "", $template_desc);
	$template_desc = str_replace("{product_price_slider1}", "", $template_desc);
	$template_desc = str_replace("{redproductfinderfilter_formend}", "", $template_desc);
	$template_desc = str_replace("{redproductfinderfilter:rp_myfilter}", "", $template_desc);

	/** todo: trigger plugin for content redshop**/
	$template_desc = $redTemplate->parseredSHOPplugin($template_desc);

	$template_desc = $texts->replace_texts($template_desc);
}

echo $template_desc;
