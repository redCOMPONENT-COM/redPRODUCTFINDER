<?php
/**
 * @package    RedPRODUCTFINDER.Frontend
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access
defined('_JEXEC') or die('Restricted access');



$type = trim($params->get('type'));

$form = trim($params->get('form'));
$chkconfig = $params->get('show_type');


$buttonname	= trim($params->get('buttonname', 'Find..'));

$db = JFactory::getDBO();

$url = JRequest::getURI();

$parseurl = parse_url($url);

$input = JFactory::getApplication()->input;

JLoader::import('redshop.library');
JLoader::load('RedshopHelperHelper');

$getItemid = new redhelper;

$Itemid = $getItemid->getItemid();

/* Get all the fields based on the limits */
$query = $db->getQuery(true);

$query->select("t.*")
	->from($db->qn("#__redproductfinder_types", "t"))
	->where($db->qn("t.published") . " = " . $db->q(1))
	->order($db->qn("ordering"));

$db->setQuery($query);

$types = $db->loadObjectList();
$tags = getTagsDetail();

	/**
	 * Method replace accent.
	 *
	 * @param   string  $str  text
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
function replaceAccent($str)
{
	$str = htmlentities($str, ENT_COMPAT, "UTF-8");

	$str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|elig|slash|ring);/', '$1', $str);

	$str = str_replace(' ', '', $str);

	return html_entity_decode($str);
}

/**
 * Method get tags.
 *
 * @return  void
 *
 * @since   1.6
 */
function getTagsDetail()
{
	$db = JFactory::getDBO();
	$query = $db->getQuery(true)
		->select("tg.tag_name, tg.id as tag_id, t.type_name, t.id as type_id")
		->from($db->qn("#__redproductfinder_tags", "tg"))
		->join("INNER", $db->qn("#__redproductfinder_tag_type", "tt") . " ON tt.tag_id = tg.id")
		->join("LEFT", $db->qn("#__redproductfinder_types", "t") . " ON t.id = tt.type_id");

	$db->setQuery($query);
	$data = $db->loadObjectList();

	return $data;
}

$act = $input->getString("act");
$tempType = $input->getInt("tempType");
$tempTag = $input->getInt("tempTag");

$session = JFactory::getSession();
$saveFilter = $session->get('saveFilter');

require JModuleHelper::getLayoutPath('mod_redproductfilter');
