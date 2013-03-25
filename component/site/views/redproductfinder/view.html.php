<?php
/**
 * @copyright Copyright (C) 2008-2009 redCOMPONENT.com. All rights reserved.
 * @license can be read in this package of software in the file license.txt or
 * read on http://redcomponent.com/license.txt
 * Developed by email@recomponent.com - redCOMPONENT.com
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 */
class RedproductfinderViewRedproductfinder extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		$mainframe=JFactory::getApplication();
		$document = JFactory::getDocument();

		$redshop_css_link = JURI::root().'components/com_redshop/assets/css/redshop.css';
		$document->addStyleSheet($redshop_css_link);
		$redfinder_js = JURI::root().'components/com_redproductfinder/helpers/redproductfinder.js';
		$document->addScript($redfinder_js);
		if (PRODUCT_HOVER_IMAGE_ENABLE)
			$document->addStyleSheet ( 'components/com_redshop/assets/css/style.css' );

		$model = $this->getModel();

		$tags = $model->getType('id, tag_name','tags',1);

		// If search
		if (JRequest::getVar('task') == 'findproducts')
		{
			$title = array();
			$post = JRequest::get('request');

			$typeData = $model->getType();

			foreach ($post as $key => $value)
			{
				if (substr($key, 0, 4) == 'type' && $value > 0)
				{
					$typeArr = explode('type', $key);

					$type = $typeArr[1];

					if(is_array($value))
					{
						$types = $model->getType('id, type_name','types',1,'id');

						if (!in_array($types[$type]['type_name'], $title)) $title[] = $types[$type]['type_name'];

						for($k=0;$k<count($value);$k++)
						{
							$title[] = $tags[$value[$k]]['tag_name'];
						}

					}else{
						$types = $model->getType('id, type_name','types',1,'');
						for($i=0;$i<count($value);$i++)
						{
							$tag = $value[$i];
							$title[] = $types[$type]['type_name'];
							$title[] = $tags[$tag]['tag_name'];

						}
					}
				}
			}


			$pageTitle = join(' | ' , $title);
			$document->setTitle($pageTitle);
			$pageKeywords = join(' , ' , $title);
			$document->setMetaData( 'keywords', $pageKeywords );
			$pageDesc = join(' ' , $title);
			$document->setMetaData('description', $pageDesc);

			$searchresult = $this->get('FindProducts');
			
			// Assign the necessary data
			$this->assignRef('searchresult', $searchresult);

		}
		else
		{
			//$type_model = $this->getModel('Redproductfinder');
			// Load the types

			$types = $this->get('Types');
			$post = JRequest::get('post');

			// Assign the necessary data
			$this->assignRef('lists', $lists);
			$this->assignRef('types', $types);
		}

		parent::display($tpl);
	}
}
?>