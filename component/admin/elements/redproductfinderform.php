<?php
 
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a Productfinder Form
 *
 * @package		Joomla
 * @subpackage	Banners
 * @since		1.5
 */
class JFormFieldredproductfinderform extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	public	$type = 'redproductfinderform';

	protected function getInput()
	{
		$db = &JFactory::getDBO();
		$name = $this->name;
		$control_name = $this->name;
		// This might get a conflict with the dynamic translation - TODO: search for better solution
		$query = 'SELECT id, formname' .
				' FROM #__redproductfinder_forms WHERE published=1' .
				' ORDER BY formname';
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select Form').' -', 'id', 'formname'));
 
		return  JHTML::_('select.genericlist',  $options, $name , 'class="inputbox"', 'id', 'formname', $this->value, $this->id );
		//return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'formname', $this->value, $control_name.$name );
	}
}
