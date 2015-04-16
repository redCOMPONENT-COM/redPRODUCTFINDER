<?php

/**

* @version		$Id: mod_banners.php 10381 2008-06-01 03:35:53Z pasamio $

* @package		Joomla

* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.

* @license		GNU/GPL, see LICENSE.php

* Joomla! is free software. This version may have been modified pursuant

* to the GNU General Public License, and as distributed it includes or

* is derivative of works licensed under the GNU General Public License or

* other free or open source software licenses.

* See COPYRIGHT.php for copyright notices and details.

*/



// no direct access

defined('_JEXEC') or die('Restricted access');



$type	= trim( $params->get( 'type' ) );

$form	= trim( $params->get( 'form' ) );
$chkconfig=$params->get( 'show_type' );


$buttonname	= trim( $params->get( 'buttonname','Find..' ) );

$db = JFactory::getDBO();

$url = JRequest::getURI();

$parseurl = parse_url($url);



require_once(JPATH_ROOT.DS.'components/com_redshop'.DS.'helpers'.DS.'helper.php');



$getItemid = new redhelper();



$Itemid = $getItemid->getItemid();



$id = JRequest::getInt('tid');

		/* Get all the fields based on the limits */

$query = "SELECT t.*, f.formname AS form_name FROM #__redproductfinder_types t

		LEFT JOIN #__redproductfinder_forms f

		ON t.form_id = f.id

		WHERE t.published = '1'

		ORDER BY t.ordering asc";

$db->setQuery($query);

$types = 	   $db->loadObjectList();




$session = JSession::getInstance('none',array());



$getredfilter = $session->get('redfilter');

/******************Category via filter start*****/

if(JRequest::getVar('cid')!='')

{

	$cid=JRequest::getVar('cid');

	$sql="SELECT count(p.product_id) as c ,t.tag_id,t.type_id,at.published FROM `#__redshop_product` as p left join `#__redproductfinder_associations` as at on p.product_id=at.product_id left join `#__redproductfinder_association_tag` as t on at.id=t.association_id WHERE p.product_id IN (SELECT product_id FROM `#__redshop_product_category_xref` WHERE category_id = $cid) and at.published=1 and t.tag_id is not null group by t.tag_id,t.type_id order by c desc ";

	$db->setQuery($sql);

	//echo $db->getQuery();

	$mainlist = $db->loadObjectList();

	$redfilter = array();	

	for($k=0;$k<count($mainlist);$k++)

	{

		if(isset($mainlist[$k]->tag_id)){

			$tagid = $mainlist[$k]->tag_id;

			$typeid= $mainlist[$k]->type_id;

			$redfilter[$typeid] = $tagid;

			//echo "<pre>";print_r($mainlist[0]);


		}

	}

$session->set('redfilter',$redfilter);

$getredfilter = $session->get('redfilter');

}

/******************Category via filter end*****/

$redfilterproduct = $session->get('redfilterproduct');

$redproducttotal = count($redfilterproduct);



foreach ($types as $key => $type) {



	if (@!array_key_exists($type->id,$getredfilter))

	{

		$types[$key]->type_name_css =  replace_accent($type->type_name);



		$tags = getTagsDetail($type->id);



		$tagname = "";

		//$ptot = getProducttotal($type->id);

		/* Only show if the type has tags */

		if (count($tags) > 0 ) {

			/* Create the selection boxes */



			for ($t=0;$t<count($tags);$t++){



				 //$ptotal = getProducttotal($type->id,$tags[$t]->tagid,1);



				$type_id = explode('.',$tags[$t]->tag_id);



				$query = "SELECT count(*) as count,rp.* FROM #__redproductfinder_association_tag as ra

							left join #__redproductfinder_associations as a on ra.association_id = a.id

							left join #__redshop_product as rp on rp.product_id = a.product_id

							WHERE type_id = '".$type_id[1]."' AND tag_id ='".$type_id[0]."' AND rp.published = 1 ";

				$db->setQuery($query);

				$published = $db->loadObjectList();
				



				if ($published[0]->count > $redproducttotal && $redproducttotal > 0){

					$finalcount = $redproducttotal;

				}else{

					$finalcount = $published[0]->count;

				}

				$myproid=$published[0]->product_id;

				if($chkconfig==0 || JRequest::getVar('main')!='' || $cid=='')
				{

					if($finalcount > 0){

							$tagname .= "&nbsp;&nbsp;<a  href='".JRoute::_('index.php?option=com_redshop&view=search&layout=redfilter&cnt='.$finalcount.'&pid='.$published[0]->product_id.'&typeid='.$type->id.'&tagid='.$tags[$t]->tag_id.'&Itemid='.$Itemid)."' title='".$tags[$t]->tag_name."' >".$tags[$t]->tag_name."</a> ( ".$finalcount." )<br/>";
					}
				}else{
				$sql="SELECT pc.*,cat.* FROM  `#__redshop_product_category_xref` as pc left join `#__redshop_category_xref` as cat on pc.category_id=cat.category_child_id where pc.product_id=".$myproid." and (cat.category_parent_id=".$cid." or cat.category_child_id=".$cid.")";

				$db->setQuery($sql);
				//echo $db->getQuery();
				$finalproid = $db->loadObjectList();				
					if(count($finalproid)>0)
					{
						if($finalcount > 0)
						{
							if($finalcount==1){
							$pid='&pid='.$published[0]->product_id;
							}else{
							$pid='';
							}
							$tagname .= "&nbsp;&nbsp;<a  href='".JRoute::_('index.php?option=com_redshop&view=search&layout=redfilter&cnt='.$finalcount.''.$pid.'&main=1&typeid='.$type->id.'&tagid='.$tags[$t]->tag_id.'&Itemid='.$Itemid)."' title='".$tags[$t]->tag_name."' >".$tags[$t]->tag_name."</a> ( ".$finalcount." )<br/>";
		
						}
					}
				}	
			}



			if ($tagname != "" )

				$lists['type'.$key] = $tagname;



		}

		else unset($types[$key]);

	}

}


$main_sal_sp = array();
$main_sal_type = array();

if(JRequest::getVar('main_sel')!="")
{
	$main_sal_sp = explode(",",JRequest::getVar('main_sel'));
	for($f=0;$f<count($main_sal_sp);$f++)
	{
		if($main_sal_sp[$f]!="")
		{
			$main_typeid = explode(".",$main_sal_sp[$f]);
			$main_sal_type[] = $main_typeid[1];
		}
	}
}


if (count($getredfilter) != 0)

{

	$main_sal_sp = array();
	$main_sal_type = array();
	$main_sal_tag = array();
  	if(JRequest::getVar('main_sel')!="")
	{
		$main_sal_sp = explode(",",JRequest::getVar('main_sel'));
		for($f=0;$f<count($main_sal_sp);$f++)
		{
			if($main_sal_sp[$f]!="")
			{
				$main_typeid = explode(".",$main_sal_sp[$f]);
				$main_sal_type[] = $main_typeid[1];
				$main_sal_tag[]  =  $main_typeid[0];
			}
		}
	}

	foreach ($getredfilter as $typeid => $tag_id)

	{

		foreach ($types as $key => $type) {



			if ($typeid == $type->id)

			{

				$types[$key]->type_name_css =  replace_accent($type->type_name);



				$tags = getTagsDetail($type->id,0);



				$tagname = "";



				/* Only show if the type has tags */

				if (count($tags) > 0) {

					/* Create the selection boxes */



					for ($t=0;$t<count($tags);$t++){
					$dep_cond = array();

					$type_id = explode('.',$tags[$t]->tag_id);

					$q  = "SELECT count(*) as count
						  FROM #__redproductfinder_association_tag AS ta
						  LEFT JOIN #__redproductfinder_associations AS a ON a.id = ta.association_id
						  LEFT JOIN #__redshop_product AS p ON p.product_id = a.product_id 
						  LEFT JOIN #__redshop_product_category_xref x ON x.product_id = a.product_id ";
			
	  		for ($i = 0; $i < count($main_sal_type); $i++)
			{
						$q .= " LEFT JOIN #__redproductfinder_association_tag AS t".$i." ON t".$i.".association_id=ta.association_id ";
			}
			
			$q .= "where ( ";
			$dep_cond = array();
			for ($i = 0; $i < count($main_sal_type); $i++)
			{
				
					$chk_q = "";
					//Search for checkboxes
					//if($i!=0)
						$chk_q .= "t".$i.".tag_id='".$main_sal_tag[$i]."' ";
					//else
					//	$chk_q .= "ta.tag_id='".$main_sal_tag[$i]."' ";
	
					if($chk_q!="")
						$dep_cond[] = " ( ".$chk_q." ) ";
				
			}
			$chk_q1 = "ta.tag_id='".$type_id[0]."' ";
			$dep_cond[] = " ( ".$chk_q1." ) ";
			if(count($dep_cond)<=0)
				$dep_cond[] = "1=1";
			$q .= implode(" AND ",$dep_cond);
		
	
			$q .= ") AND p.published = '1' AND x.category_id='".JRequest::getVar('cid')."' order by p.product_name ";
			//echo $q; 
			$db->setQuery($q);
			$published = $db->loadObjectList();
			$finalcount = $published[0]->count;
				

						if($finalcount > 0 && !in_array($type->id,$main_sal_type)){
						
							$main_sel="";
							if(JRequest::getVar('main_sel')!="")
							{
								$main_sel=JRequest::getVar('main_sel').",";
							}
							$main_sel.=$tags[$t]->tag_id;
		
							$tagname .= "&nbsp;&nbsp;<a  href='".JRoute::_('index.php?option=com_redshop&view=search&layout=redfilter&typeid='.$type->id.'&tagid='.$tags[$t]->tag_id.'&Itemid='.$Itemid.'&cid='.JRequest::getVar('cid').'&main_sel='.$main_sel)."' title='".$tags[$t]->tag_name."' >".$tags[$t]->tag_name."</a> ( ".$finalcount." )<br/>";
		
						}

						if (in_array($tags[$t]->tag_id,$main_sal_sp)){
						
							$main_sel="";
							$main_sel=str_replace($tags[$t]->tag_id,"",JRequest::getVar('main_sel'));
							
							//$ptotal = getProducttotal($type->id,$tags[$t]->tagid,0);

							$tagname .= "<span style='float:left;'>&nbsp;&nbsp;".$tags[$t]->tag_name."</span><span style='float:right;'><a  href='".JRoute::_('index.php?option=com_redshop&view=search&layout=redfilter&typeid='.$type->id.'&tagid='.$tags[$t]->tag_id.'&Itemid='.$Itemid.'&cid='.JRequest::getVar('cid').'&main_sel='.$main_sel)."' title='".JText::_('DELETE')."' >".JText::_('DELETE')."</a></span><br/>";

							//$tagname .= "<span style='float:left;'>&nbsp;&nbsp;".$tags[$t]->tag_name."</span><span style='float:right;'><a href='".JRoute::_('index.php?option=com_redshop&view=search&layout=redfilter&typeid='.$type->id.'&remove=1&Itemid='.$Itemid)."' title='".JText::_('DELETE')."' >".JText::_('DELETE')."</a></span><br/>";

						}

					}



					if ($tagname != "")

						$filteredlists['type'.$key] = $tagname;



				}

				else unset($types[$key]);

			}

		}

	}

}

function replace_accent($str)

{

	  $str = htmlentities($str, ENT_COMPAT, "UTF-8");

	  $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|elig|slash|ring);/','$1',$str);

	  $str = str_replace(' ', '', $str);

	  return html_entity_decode($str);

}



function getTagsDetail($id,$all=1)

{

	// for session

 	$session = JSession::getInstance('none',array());

	$getredfilter = $session->get('redfilter');



	$db = JFactory::getDBO();

	$productids = "";

	if (count($getredfilter) > 0 && $all == 1)

	{

		$type_id = array();

		$tag_id = array();



		$k=0;

		foreach ($getredfilter as $typeid => $tags)

		{

			$type_id[] = $typeid;

			$tags = explode(".",$tags);

			$tag_id[] = $tags[0];



			if (count($getredfilter)-1 == $k){

	  			$lasttypeid = $typeid;

	  			$lasttagid = $tags[0];

	  		}



			$k++;

		}

		$typeids = implode(",",$type_id);

		$tagids = implode(",",$tag_id);



 	 	$query = "SELECT ra.product_id FROM `#__redproductfinder_association_tag` as rat

					LEFT JOIN #__redproductfinder_associations as ra ON rat.`association_id` = ra.id

					WHERE  rat.`type_id` IN (".$lasttypeid.") ";



			$query .= "AND  rat.`tag_id` IN (".$lasttagid.") ";







	 	$db->setQuery($query);

	 	$product = $db->loadObjectList();



	 	$products = array();



	 	for ($i=0;$i<count($product);$i++)

	 	{

	 		$products[] = $product[$i]->product_id;

	 	}



		$productids = implode(",",$products);



	}



 	$q = "SELECT DISTINCT j.tag_id as tagid ,ra.product_id,count(ra.product_id) as ptotal ,CONCAT(j.tag_id,'.',j.type_id) AS tag_id, t.tag_name

			FROM ((#__redproductfinder_tag_type j, #__redproductfinder_tags t )

			LEFT JOIN #__redproductfinder_association_tag as rat ON  t.`id` = rat.`tag_id`)

			LEFT JOIN #__redproductfinder_associations as ra ON ra.id = rat.association_id

			WHERE j.tag_id = t.id

			AND j.type_id = ".$id."  ";





	if ($productids != "")

		$q .=	" AND ra.product_id  IN ( ".$productids." ) ";

	$q .=	" GROUP BY t.id ORDER BY t.ordering  ";



	$db->setQuery($q);

 	return $db->loadObjectList();



}







require(JModuleHelper::getLayoutPath('mod_redPRODUCTFILTER'));



