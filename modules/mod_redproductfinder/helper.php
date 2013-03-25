<?php
class modRedproductfinderHelper
{
    function getTypes($type, $form)
    {
        /*$db = JFactory::getDBO();
        $query = "SELECT t.*, f.formname AS form_name FROM #__redproductfinder_types t
                  LEFT JOIN #__redproductfinder_forms f
                  ON t.form_id = f.id
                  ORDER BY ordering";
        $db->setQuery($query);
        $types = $db->loadObjectList();
        
        foreach ($types as $key => $row)
        {
            $types[$key]->type_name_css = modRedproductfinderHelper::replace_accents($row->type_name);
            
            $q = "SELECT j.type_id AS tag_id, tag_name 
                  FROM #__redproductfinder_tag_type j, #__redproductfinder_tags t
                  WHERE j.tag_id = t.id
                  AND j.type_id = ".$row->id."
                  ORDER BY t.ordering";
            $db->setQuery($q);
            $tags = $db->loadObjectList();
         
            // If the type has tags
            if (count($tags) > 0)
            {
                 // Create the selection filtering boxes
                 switch ($row->type_select)
                 {
                    case 'checkbox':
                        $html = '';
                        foreach ($tags as $tagkey => $tag) {
                            $html .= '<div class="typename'.$row->type_name_css.'">'.$row->type_name.'</div>';
                            $html .= '<input type="checkbox" name="type'.$key.'[]" value="'.$tag->tag_id.'"';
                            if (isset($post['type']) && in_array($tag->tag_id, $post['type']))
                                $html .= 'checked="checked"';
                            $html .= '>'.$tag->tag_name.'</input>';
                        }
                        $lists['type'] = $html;
                        break;
                    case 'generic':
                    default:
                        array_unshift($tags, array('tag_id' => 0, 'tag_name' => JText::_('MAKE CHOICE')));
                        $lists['type'] = '<div class="typename'.$row->type_name_css.'">'.$row->type_name.'</div>';
                        //Define selected type
                        $selected_type = ($form == "" || $type == "") ? JRequest::getVar('type') : $type;
                        $lists['type'] .= JHTML::_('select.genericlist', $tags, 'type'.$key, '', 'tag_id', 'tag_name', $selected_type);
                        break;
                 }
            }
            else
            {
                //Empty array to avoid error
                $lists = array();
            }
            
            return $lists;
        }*/
        
       	if(count($type)>0)
		{
	        	$finder_products_types = implode("','",$type);
		}
        $db = JFactory::getDBO();
        // Get all the fields based on the limits
        $query =  "SELECT * FROM #__redproductfinder_types
                   WHERE published = 1 ";
        if (count($type)>0)
        $query .= "AND id IN ('".$finder_products_types."') ";
        if ($form)
        $query .= "AND form_id = '".$form."' ";
        $query .= "ORDER BY ordering";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function replace_accents($str)
    {
        $str = htmlentities($str, ENT_COMPAT, "UTF-8");
        $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|elig|slash|ring);/','$1',$str);
        $str = str_replace(' ', '', $str);
        return html_entity_decode($str);
    }
}
?>