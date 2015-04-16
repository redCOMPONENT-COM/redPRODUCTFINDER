<?php
/**
 * @copyright Copyright (C) 2008 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 * Products view
 */

/* No direct access */
defined('_JEXEC') or die('Restricted access');
/**
 * Associations View
 */
class RedproductfinderViewAssociations extends JViewLegacy
{
	/**
	 * redFORM view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		global $mainframe;
		/* Get the task */
		$task 				= JRequest::getCmd('task');
		$params 			= &JComponentHelper::getParams( 'com_redproductfinder' );
		$use_quality_score 	= $params->get('use_quality_score');
		$form_id 			= $params->get('form');

		/* add submenu here */
		RedproductfinderHelper::addSubmenu("associations");

		switch ($task)
		{
			case 'apply':
			case 'edit':
			case 'add':
				if ($task == 'apply') $row = $this->get('SaveAssociation');
				else $row = $this->get('Association');

				if ($row)
				{
					/* Get the published field */
					$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published);
				}
				else
				{
					$lists['published'] = '';
				}

				/* Get the Redshop products */
				$lists['products'] = JHTML::_('select.genericlist',  $this->get('Products'), 'product_id', '', 'product_id', 'full_product_name', $row->product_id);

				/* Get the tag names */
				$tags = $this->get('Tags', 'Tags');
				$associationtags = $this->get('AssociationTags');
				if (!is_array($associationtags)) $associationtags = array();
				$lists['tags'] = JHTML::_('select.genericlist', $tags, 'tag_id[]', 'multiple', 'id', 'tag_name', $associationtags);

				$types = $this->get('TypeTagList');
				/* Disabled as customer doesn't want a multi-select box now, who knows later??? */
				if (0) {
					/* Create the select list */
					$html = '<select id="tag_id" multiple="multiple" name="tag_id[]">';
					foreach ($types as $key => $type) {
						/* Add the type */
						$html .= '<option value="">'.JText::_('COM_REDPRODUCTFINDER_TYPE_LIST').' '.$type['type_name'].'</option>';
						/* Add the tags */
						if (count($type['tags']) > 0) {
							foreach ($type['tags'] as $tagid => $tag) {
								/* Check if the tag is selected */
								if (in_array($tagid, $associationtags)) $selected = 'selected="selected"';
								else $selected = '';
								$html .= '<option '.$selected.' value="'.$tagid.'" >--- '.JText::_('COM_REDPRODUCTFINDER_TAG_LIST').' '.$tag['tag_name'].'</option>';
							}
						}
					}
					$html .= '</select>';
					$lists['tags'] = $html;
				}

				/* Get the Quality Score data */
				$qs = $this->get('QualityScores', 'Tags');

				/* Get the association ID */
				$assoc_id = JRequest::getVar('cid');
				$assoc_id = $assoc_id[0];

				$model = $this->getModel('associations');
				/* Create the select list as checkboxes */
				$html = '<div id="select_box">';
				foreach ($types as $typeid => $type) {
					$counttags = count($type['tags']);
					$rand = rand();
					/* Add the type */
					$html .= '<div class="select_box_parent" onClick="showBox('.$rand.')">'.JText::_('COM_REDPRODUCTFINDER_TYPE_LIST').' '.$type['type_name'].'</div>';
					$html .= '<div id="'.$rand.'" class="select_box_child';
					$html .= '">';
					/* Add the tags */
					if ($counttags > 0) {
						foreach ($type['tags'] as $tagid => $tag) {
							/* Check if the tag is selected */
							$myassotype=$model->getAssociationTypes($tagid);


							if(in_array($tagid, $associationtags) && $typeid==$myassotype->type_id)
							{
								//echo "<pre>";print_r($associationtags);
								$selected = 'checked="checked"';
							}
							else{
							$selected = '';
							}
							$html .= '<input type="checkbox" class="select_box" '.$selected.' name="tag_id[]" value="'.$typeid.'.'.$tagid.'" />'.JText::_('COM_REDPRODUCTFINDER_TAG_LIST').' '.$tag['tag_name'];
							$html .= '<br />';
							if (array_key_exists($assoc_id.'.'.$typeid.'.'.$tagid, $qs)) $qs_value = $qs[$assoc_id.'.'.$typeid.'.'.$tagid]['quality_score'];
							else $qs_value = '';
							if($use_quality_score)
							{
								$html .= '<span class="quality_score">'.JTEXT::_('COM_REDPRODUCTFINDER_QUALITY_SCORE').'</span> <input type="text" class="quality_score_input"  name="qs_id['.$typeid.'.'.$tagid.']" value="'.$qs_value.'" />';
								$html .= '<br />';
							}
							if($form_id!="" && $form_id!="0")
							{
								$form_detail = $model->getFormDetail($form_id);
								if($form_detail[0]->dependency==1)
								{
									$html .= '<select name="sel_dep'.$typeid.'_'.$tagid.'[]" id="sel_dep'.$typeid.'_'.$tagid.'" multiple="multiple" size="10"  >';

									foreach($types as $sel_typeid => $sel_type)
									{
										if($typeid==$sel_typeid)
											continue;
										$dependent_tag = $model->getDependenttag($row->product_id,$typeid,$tagid);

										$html .= '<optgroup label="'.$sel_type['type_name'].'">';
										foreach ($sel_type['tags'] as $sel_tagid => $sel_tag) {
											$selected = in_array($sel_tagid,$dependent_tag) ? "selected" : "";
											$html .= '<option value="'.$sel_tagid.'" '.$selected.' >'.$sel_tag['tag_name'].'</option>';
										}
										$html .= '</optgroup>';
									}
									$html .= '</select>&nbsp;<a href="#" onClick="javascript:add_dependency('.$typeid.','.$tagid.');" >'.JText::_('COM_REDPRODUCTFINDER_ADD_DEPENDENCY').'</a><br />';
								}
							}
						}
					}
					$html .= '</div>';
				}
				$html .= '</div>';
				$lists['tags'] = $html;

				/* Set variabels */
				$this->assignRef('row', $row);
				$this->assignRef('lists', $lists);

				break;
			default:
				switch($task) {
					case 'save':
						$this->get('SaveAssociations');
						break;
					case 'saveorder':
						$this->get('SaveOrder');
						break;
					case 'remove':
						$this->get('RemoveAssociation');
						break;
					case 'publish':
					case 'unpublish':
						$this->get('Publish');
					break;
				}
				/* Get the pagination */
				$pagination = $this->get('Pagination');

				/* Get the fields list */
				$associations = $this->get('Associations');

				/* Get the fields list */
				$tags = $this->get('AssociationTagNames');

				/* Check if there are any forms */
				$countassociations = $this->get('Total');

				/* Set variabels */
				$this->assignRef('pagination', $pagination);
				$this->assignRef('associations', $associations);
				$this->assignRef('tags', $tags);
				$this->assignRef('lists', $lists);
				$this->assignRef('countassociations', $countassociations);

				break;
		}
		/* Get the toolbar */
		$this->toolbar();

		/* Display the page */
		parent::display($tpl);
	}

	function toolbar()
	{
		switch (JRequest::getCmd('task'))
		{
			case 'edit':
			case 'apply':
			case 'add':
				switch (JRequest::getCmd('task'))
				{
					case 'add':
						JToolBarHelper::title(JText::_( 'Add Association' ), 'redproductfinder_association');
						break;
					default:
						JToolBarHelper::title(JText::_( 'Edit Association' ), 'redproductfinder_association');
						break;
				}

				JToolBarHelper::save();

				JToolBarHelper::cancel();
				break;
			default:
				JToolBarHelper::title(JText::_('Association'), 'redproductfinder_association');
				JToolBarHelper::publishList();
				JToolBarHelper::unpublishList();
				JToolBarHelper::spacer();
				JToolBarHelper::deleteList(JText::_('Are you sure you want to delete the associations?'));
				JToolBarHelper::addNew();
				break;
		}
	}
}
?>
