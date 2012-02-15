<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Used to render ninja elements in JForm joomla forms 
 *
 * We have to use the JFormField naming format for this purpose
 *
 * @package 	Napi
 * @subpackage	Napi_Parameter
 */
class JFormFieldNapi extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Napi';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$src = null;
		if($src = $this->element['src']) $src = ' class="'. $src . '"';

		$form = JPATH_ROOT.'/'.$this->element['xml'];
		$form = simplexml_load_file($form)->form;

		$grouptag  = $this->element['grouptag'];
		//if(!$grouptag) $grouptag = 'jform[params]';
		if(!$grouptag) $grouptag = 'params';
		$groupname  = $this->element['formname'];
		if(!$groupname) $groupname = 'jform[params]';

		$data = array();
		foreach($form->children() as $group)
		{
			foreach($group->children() as $element)
			{
				$key = (string)$group['group'];
				$value = $this->form->getValue((string)$element['name'], $key);
				if(!is_null($value)) $data[$key] = $value;
			}
		}

		$parameter = KService::get('ninja:form.parameter', array(
					  		'data' 	   => $data,
					  		'xml'  	   => $form->asXML(),
					  		'render'   => 'inline',
					  		'groups'   => false,
					  		'grouptag' => $grouptag,
					  		'name'	   => $groupname
					  ));

		$html[] = '</li></ul>';
		$html[] = $parameter->render();
		$html[] = '<ul id="'.$this->name.'"><li>';
					
		KService::get('ninja:template.helper.document')->load('js', 'window.addEvent(\'domready\', function(){
			$(\''.$this->name.'\').getParent().getChildren().each(function(el){
				if(el.tagName == "UL") {
					(function(){this.setStyle(\'height\', \'\');}.bind(el.getParent())).delay(601);
					el.dispose();
				}
			});
		});');
		KService::get('ninja:template.helper.document')->load('/form.css');
		
		return implode($html);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return	string	The field label markup.
	 * @since	1.6
	 */
	protected function getLabel()
	{
		return false;
	}
}