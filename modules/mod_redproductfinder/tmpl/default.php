<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2005 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$db       = JFactory::getDBO();
$document = JFactory::getDocument();

$document->addStyleSheet('modules/mod_redproductfinder/redproductfinder.css');

$redfinder_js = JURI::root() . 'components/com_redproductfinder/helpers/redproductfinder.js';
$document->addScript($redfinder_js);

$component = JComponentHelper::getComponent('com_redproductfinder');
$config    = $params;

jimport('joomla.application.module.helper');

$modid            = $module->id;
$query            = "SELECT f.dependency FROM  #__redproductfinder_forms f
					where id          ='" . $form . "'
					ORDER BY id";
$db->setQuery($query);
$frmdependancy    = $db->loadObject();
$check_dependency = $frmdependancy->dependency;

if ($itemid != '')
{
	$Itemid = $itemid;
}
else
{
	$Itemid = JRequest::getVar('Itemid', '', 'request');
}

$formname = $form;
$getmyid = JRequest::getVar('myid');
?>
<form method="post" name="redPRODUCTFINDERFORM<?php echo $modid ?>"
	action="<?php echo JUri::root() . 'index.php?option=com_redproductfinder&task=findproducts&view=redproductfinder&Itemid=' . $Itemid; ?>">

    <?php
	if ($pretext)
	{
	?>
		<div class="inputfieldwrapper">
		<?php
			if ($pretext_link)
			{
				echo '<a href="' . $pretext_url . '">';
			}

			echo $pretext;

			if ($pretext_link)
			{
				echo '</a>';
			}
		?>
		</div>
	<?php
	}

if ($show_searchcriteria)
{
	echo JText::_('SEARCH_CRITERIA');
?>
        <div class="inputfieldwrapper">
            <input type="text" name="searchkey" value="<?php echo JRequest::getVar('searchkey'); ?>" />
        </div>
        <?php
}

if ($show_productprice)
{
?>
    <div class="inputfieldwrapper">
        <?php
	echo JText::_('PRODUCT PRICE');
?>
        <input type="text" name="productprice" value="<?php echo JRequest::getVar('productprice'); ?>" />
    </div>
    <?php
}

include_once "components/com_redproductfinder/helpers/fields.php";
include_once "components/com_redproductfinder/models/redproductfinder.php";

if ($show_type)
{
?>
    <div id='mod_rep_search<?php echo $modid ?>'>
    <div class="inputfieldwrapper">
        <?php
	$hide_dropdown = '';
	$query = "SELECT id, tag_name FROM #__redproductfinder_tags";
	$db->setQuery($query);
	$tags = $db->loadAssocList('id');
	$hidepost = JRequest::get('request');
	$hide_dropdownvalue = '';

	foreach ($hidepost as $key => $value)
	{
		if (substr($key, 0, 4) == 'type')
		{
			if (is_array($value))
			{
				$maintag = '';

				foreach ($value as $v)
				{
					$query = "SELECT t.*,type.*,ts.* FROM #__redproductfinder_tags as t left outer join #__redproductfinder_tag_type as type on t.id=type.tag_id left outer join #__redproductfinder_types as ts on type.type_id=ts.id where t.tag_name='" . $tags[$v]['tag_name'] . "' group by type.type_id ";
					$db->setQuery($query);
					$type_id = $db->loadObject();
					$hide_dropdownvalue .= $tags[$v]['id'] . ",";
				}
			}
			else
			{
				$hide_dropdownvalue .= $value . ",";
			}

			$hide_dropdown .= $key . ",";
		}
	}

	if ($hide_dropdown == '')
	{
		$hide_dropdown = "";
	}

	$j               = 0;
	$getmyid         = JRequest::getVar('myid');
	$searchkey       = JRequest::getVar('searchkey');
	$takedropval     = $hide_dropdownvalue;
	$takedropname    = $hide_dropdown;
	$chktakedropval  = JRequest::getVar('hide_dropdownvalue1');
	$chktakedropname = JRequest::getVar('hide_dropdown1');

	if ($chktakedropval != '')
	{
		$chkmaindropval = explode(',', $chktakedropval);
		$chkmaindropname = explode(',', $chktakedropname);
	}

	if ($takedropval != '')
	{
		$maindropval = explode(',', $takedropval);
		$maindropname = explode(',', $takedropname);
	}

	foreach ($types as $key => $type)
	{
		$type_name_css = RedproductfinderModelRedproductfinder::replace_accents($type->type_name);
		echo '<div class="inputfieldwrapper">';
		echo '<div class="typename ' . $type_name_css . '">';
		echo $type->type_name;
		echo '</div>';
		echo '<div class="typevalue ' . $type_name_css . '">';

		if ($type->type_select == "generic")
		{
			$extras = "";

			if ($check_dependency != 0)
			{
				$extras = "onChange='javascript:mod_getDependent(" . $j . "," . $modid . ");' id='finder_sel_" . $type->id . "'";

				if ($j != 0 && $getmyid == '')
				{
					$extras .= " disabled='disabled'";
				}
				else
				{
					$extras .= " ";
				}
			}

			if ($type->form_id == $form)
			{
				$rs = RedproductfinderModelRedproductfinder::typeTags($type->id);

				if ($hide_dropdown != '')
				{
					echo redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type" . $type->id, "type" . $type->id, $maindropval, true, $extras);
				}
				else
				{
					echo redPRODUCTFINDERHelperFields::generateSelectBox($rs, "tag_name", "type" . $type->id, "type" . $key, 0, true, $extras);
				}
			}
		}
		elseif ($type->type_select == "checkbox")
		{
			$tags_object = RedproductfinderModelRedproductfinder::typeTags($type->id);

			foreach ($tags_object as $i => $row)
			{
				echo $tags_object[$i]->tag_name;

				if ($check_dependency != 0)
				{
					$extras = "onChange='javascript:mod_getDependent(" . $j . "," . $modid . ");'";

					if ($j != 0 && $getmyid == '')
					{
						$extras .= " disabled='disabled'";
					}
					else
					{
						$extras .= " ";
					}

					if ($hide_dropdown != '')
					{
						echo redPRODUCTFINDERHelperFields::generateCheckbox("type[]", "finder_sel_" . $type->id, $tags_object[$i]->id, $extras, $maindropval) . " ";
					}
					else
					{
						echo redPRODUCTFINDERHelperFields::generateCheckbox("type[]", "finder_sel_" . $type->id, $tags_object[$i]->id, $extras) . " ";
					}
				}
				else
				{
					if ($hide_dropdown != '')
					{
						echo redPRODUCTFINDERHelperFields::generateCheckbox("type" . $type->id . "[]", "type", $tags_object[$i]->id, "", $maindropval) . " ";
					}
					else
					{
						echo redPRODUCTFINDERHelperFields::generateCheckbox("type" . $type->id . "[]", "type", $tags_object[$i]->id) . " ";
					}
				}
			}
		}
		elseif ($type->type_select == "Productfinder datepicker")
		{
			if ($type->picker == 0)
			{
				$sdate = JRequest::getVar('from_startdate');
				$edate = JRequest::getVar('to_enddate');
				echo "From : " . JHTML::_('calendar', $sdate, 'from_startdate_ajax', 'from_startdate_ajax', $format = '%d-%m-%Y', array(
					'class' => 'inputbox',
					'size' => '15',
					'maxlength' => '19'
				)
				);
				echo " To : " . JHTML::_('calendar', $edate, 'to_enddate_ajax', 'to_enddate_ajax', $format = '%d-%m-%Y', array(
					'class' => 'inputbox',
					'size' => '15',
					'maxlength' => '19'
				)
				);
			}
			else
			{
				$myarr = $params->get('monthpicker');

				if ($myarr == '')
				{
					$m = array(
						"01",
						"02",
						"03",
						"04",
						"05",
						"06",
						"07",
						"08",
						"09",
						"10",
						"11",
						"12"
					);
				}
				else
				{
					$m = $myarr;
				}

				$currMonth = date("M");
				$maincurmonth = date("m");
				$currYear = date("Y");
				$fullyear = date("Y");
				$getmonth = JRequest::getVar('month');

				if ($getmonth != '')
				{
					$finalmnth = explode(",", $getmonth);
				}

				echo '<input type="text" name="month[]" onclick="toggleHolder(this,\'monthholder\');" autocomplete="off" value="" state="false" id="fieldkeyvalue">';
				echo ' <div id="monthholder" style="display: none;" class="monthholder">';

				for ($i = 0;$i <= 2;$i++)
				{
					foreach ($m as $value)
					{
						if ($value != '02')
						{
							$month_name1 = strtoupper(date('F', mktime(0, 0, 0, $value, 1, 0)));
						}
						else
						{
							$month_name1 = "February";
						}

						$month_name = $month_name1;
						$mval = $value . "-" . $month_name . "-" . $fullyear;

						if ($myarr == '')
						{
							if ($params->get('month_year') == 0)
							{
								$display1 = $month_name . "-" . $fullyear;
								$display2 = $currMonth . "-" . $currYear;

								if (strtotime($display1) >= strtotime($display2))
								{
									$display = $month_name . "-" . $fullyear;
								}
							}
							else
							{
								$display = $month_name . "-" . $fullyear;
							}
						}
						else
						{
							if ($params->get('month_year') == 0)
							{
								$display1 = $month_name . "-" . $fullyear;
								$display2 = $currMonth . "-" . $currYear;

								if (strtotime($display1) >= strtotime($display2))
								{
									$display = $month_name . "-" . $fullyear;
								}
							}
							else
							{
								$display = $month_name . "-" . $fullyear;
							}
						}

						if (($value == $currMonth) && ($currYear == $fullyear))
						{
							if ($display != '')
							{
								echo "<a class=\"rounded\" state=\"false\" onmouseover='rstoggle(this,\"over\",\"\");' onmouseout='rstoggle(this,\"out\",\"\");' onmousedown='return rstoggle(this,\"down\",\"" . $mval . "\");'><span>$display </span></a>";
							}
						}
						else
						{
							if ($display != '')
							{
								echo "<a class=\"rounded\" state=\"false\" onmouseover='rstoggle(this,\"over\",\"\");' onmouseout='rstoggle(this,\"out\",\"\");' onmousedown='return rstoggle(this,\"down\",\"" . $mval . "\");'><span>$display </span></a>";
							}
						}
					}

					$fullyear++;
				};

				echo '</div>';
			}
		}

		echo (strlen($type->tooltip) > 0) ? ' ' . JHTML::tooltip($type->tooltip, $type->type_name, 'tooltip.png', '', '', false) : "";
		echo '</div>';
		echo '</div>';
		echo '<div class="hrdivider ' . $type_name_css . '"></div>';
		$j++;
	}
?>
    </div>
    </div>
    <?php
}
?>

    <div class="button">
        <input type="submit" value="<?php echo JText::_($buttonname); ?>" />
    </div>
    <input type="hidden" name="formname" value="<?php echo $formname; ?>" />
    <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
    <input type="hidden" name="option" value="com_redproductfinder" />
    <input type="hidden" name="task" value="findproducts" />
    <input type="hidden" name="controller" value="redproductfinder" />

	<input type="hidden" name="hide_dropdown" value="<?php echo $hide_dropdown; ?>" />
	<input type="hidden" name="hide_dropdownvalue" value="<?php echo $hide_dropdownvalue; ?>" />
	<input type="hidden" name="mod_id_main" id="mod_id_main" value="<?php echo $modid; ?>" />
	<input type="hidden" name="myid" value="1" />
</form>
<script>

		var finderkey = new Array();

		function rstoggle(me,state,val){

			var inputel = document.getElementById('fieldkeyvalue');
			var hostname = window.location.hostname;
			var pathname = window.location.pathname;
			var mainurl=hostname+pathname;

			switch(state)
			{

				case 'over':
					if(me.childNodes[0].nodeName == 'SPAN'){
						var spanholderel = me.childNodes[0];
						spanholderel.style.background = "transparent url( "+mainurl+"modules/mod_redproductfinder/images/off_btn_bg.png ) repeat-x scroll left top";
					}
					me.style.background = "transparent url( "+mainurl+"modules/mod_redproductfinder/images/off_btn_bg.png ) repeat-x scroll right top";
					//me.childNodes[0]

				  break;
				case 'out':
					var mystate = me.getAttribute('state');
					if(mystate == "true") return;
					if(me.childNodes[0].nodeName == 'SPAN'){
						var spanholderel = me.childNodes[0];
						spanholderel.style.background = "transparent url( "+mainurl+"modules/mod_redproductfinder/images/btn_bg.png ) repeat-x scroll left top";
					}
					me.style.background = "transparent url( "+mainurl+"modules/mod_redproductfinder/images/btn_bg.png ) repeat-x scroll right top";
					//me.childNodes[0]

				  break;
				case 'down':

					var mystate = me.getAttribute('state');

					var onoff = (mystate == "false") ? "off_":"";

					if(me.childNodes[0].nodeName == 'SPAN'){
						var spanholderel = me.childNodes[0];
						spanholderel.style.background = "transparent url( "+mainurl+"modules/mod_redproductfinder/images/"+onoff+"btn_bg.png ) repeat-x scroll left top";
					}
					me.style.background = "transparent url( "+mainurl+"modules/mod_redproductfinder/images/"+onoff+"btn_bg.png ) repeat-x scroll right top";
					//me.childNodes[0]

					if(mystate == "false"){
						me.setAttribute('state',"true");
						finderkey.push(val);
					}else{
						me.setAttribute('state',"false");

						for(g in finderkey){
							if(val == finderkey[g])	finderkey.splice(g,1);
						}
					}

					inputel.value = finderkey;

				  break;
				default:
				  break;
			}

		}

		function toggleHolder(me,holder){

			var holderstate = me.getAttribute('state');

			var holderelm = document.getElementById(holder);
			if(holderstate == "false"){

				holderelm.style.display = "";
				setTimeout("byeholder()",5000);
				me.setAttribute('state',"true");
			}else{
				holderelm.style.display = "none";
				me.setAttribute('state',"false");
			}
		}
		function byeholder(){

			document.getElementById('monthholder').style.display = "none";
			document.getElementById('fieldkeyvalue').setAttribute('state',"false");
		}

	</script>
