<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 */

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>
<div id="response">
<?php
if (count($getredfilter) != 0)
{
	?>
		<!--Hide the search Header Text-->
	<!--<div id="pfsearchheader"><?php echo JText::_('SEARCH_RESULT');?></div>-->

<div class="hrdivider"><hr></div>
<?php
	//foreach ($getredfilter as $typeid => $tag_id)
	//{
		foreach ($types as $key => $type) {

			//if ($typeid == $type->id)
			//{
	?>
	<div id="typename_<?php echo $type->id;?>" class="typename <?php echo $type->type_name_css; ?>" ><?php echo $type->type_name; ?>
	<?php if (strlen($type->tooltip) > 0) {
		echo ' '.JHTML::tooltip($type->tooltip, $type->type_name, 'tooltip.png', '', '', false);
	} ?>
	</div>
	<div id="typevalue_<?php echo $type->id;?>" class="typevalue <?php echo $type->type_name_css; ?>" ><?php echo $filteredlists['type'.$key];?></div>
	<div class="hrdivider <?php echo $type->type_name_css; ?>" ></div>

	<?php
			//}
		}
	//}
	?>
<div style="padding-top:10px;"><hr><a href="<?php echo 'index.php?option=com_redshop&view=category&layout=detail&cid='.JRequest::getVar('cid').'&Itemid='.$Itemid; ?>" title="<?php echo JText::_('Clear All'); ?>" ><?php echo JText::_('Reset All Filters'); ?></a></div>
<?php
}
?>
<div>
</div>

</div>
<script>
function xml_object()
{
		var xmlHttp;
			try {   xmlHttp=new XMLHttpRequest();  }
			catch (e)
			{   try    {     xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");    }

				catch (e)   {

				 try   {     xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");     }

				catch (e)     {   alert("Your browser does not support AJAX!");     return false;

				  } 	}
		  }
		 return xmlHttp;
}
function deleteTag(typeid,itemid)
{
	//var option = document.getElementById('option').value;
	xmlHttp=xml_object();
//	alert("index2.php?option=com_redshop&view=search&layout=redfilter&remove=1&typeid="+typeid+"&Itemid="+itemid);
	xmlHttp.onreadystatechange=function()
  	 {

		   if(xmlHttp.readyState==4)
		   {

				var resp=xmlHttp.responseText.split('~');
				document.getElementById("response").innerHTML = resp[0];
				/*document.getElementById("search_inner").innerHTML = '';
				document.getElementById("search_inner").innerHTML = resp[1];*/

		   }
	  }

  	 xmlHttp.open("GET", "<?php echo JURI::root();?>index.php?tmpl=component&option=com_redshop&view=search&layout=redfilter&for=true&remove=1&typeid="+typeid+"&Itemid="+itemid,  true);
	 xmlHttp.send(null);
}
</script>