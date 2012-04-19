<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		JForm_Overrides
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * JForm Overrides
 *
 * @package 	Napi
 * @subpackage	Napi_Parameter
 */
class JFormFieldListbox extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Listbox';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$model = KService::get((string)$this->element['model'])->limit(0);
		
		if(isset($this->element['states']))
		{
			$json 	= '{"'.str_replace(array(';', ':'), array('","', '":"'), (string)$this->element['states']).'"}';
			$states = json_decode(str_replace('",""}', '"}', $json));
			$model->set($states);
		}

		if ($this->element['deselect']) $options[] = JHTML::_('select.option', '', '- '.JText::_($this->element['deselect']).' -');

		foreach ($model->getList() as $item)
		{
			// Create a new option from the item
			$options[] = JHTML::_('select.option', $item->id, $item->title);
		}

		return $options;
	}
}
