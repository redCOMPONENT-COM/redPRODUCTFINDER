<?php
 
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a Productfinder Form
 *
 * @package		Joomla
 * @subpackage	Banners
 * @since		1.5
 */
class JFormFieldredproductfindertemplate extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$type = 'redproductfindertemplate';
	protected function getInput()
	{
	
		$db = &JFactory::getDBO();
		$name = $this->name;
		$control_name = $this->name;
		// This might get a conflict with the dynamic translation - TODO: search for better solution
		$query = 'SELECT template_id, template_name' .
				' FROM #__redshop_template WHERE published=1 AND template_section="redproductfinder"' .
				' ORDER BY template_id';
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Template').' -', 'template_id', 'template_name'));
		return  JHTML::_('select.genericlist',  $options, $name , 'class="inputbox"', 'template_id', 'template_name', $this->value, $this->template_id );
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'template_id', 'template_name', $value, $control_name.$name );
	}
}
