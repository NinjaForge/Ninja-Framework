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
		$form  = '<form'.$src.'>';
		echo('<pre>'.print_r(get_class_methods($this->form), true));
		die('<pre>'.print_r($this->form, true));
		foreach($this->_parent->_xml as $group => $xml)
		{
			if($group == '_default') $xml->addAttribute('group', 'basic');
			$form .= $xml->toString();
		}
		$form .= '</form>';
	
		$grouptag  = $this->element['grouptag'];
		if(!$grouptag) $grouptag = 'params';
		$groupname  = $this->element['formname'];
		if(!$groupname) $groupname = 'params';
		$data = $this->_parent->_raw;
		if(!$data) $data = $this->_parent->_registry['_default']['data'];
		$parameter = $this->getService('ninja:form.parameter', array(
					  		'data' 	   => $data,
					  		'xml'  	   => $form,
					  		'render'   => 'inline',
					  		'groups'   => false,
					  		'grouptag' => $grouptag,
					  		'name'	   => $groupname
					  ));
	
		$html[] = '</td></tr></tbody></table>';
		$html[] = $parameter->render();
		$html[] = '<table id="'.$this->name.'"><tbody><tr><td>';
					
		$this->getService('ninja:template.helper.document')->load('js', 'window.addEvent(\'domready\', function(){
			$(\''.$this->name.'\').getParent().getChildren().each(function(el){
				if(el.tagName == "TABLE") {
					(function(){this.setStyle(\'height\', \'\');}.bind(el.getParent())).delay(601);
					el.remove();
				}
			});
		});');
		$this->getService('ninja:template.helper.document')->load('/form.css');
		
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