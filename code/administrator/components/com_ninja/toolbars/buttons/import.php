<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: import.php 762 2010-12-17 15:18:34Z stian $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Enable button class for a toolbar
 * 
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonImport extends KToolbarButtonPost
{
	public function __construct(KConfig $options)
	{
		$options->icon = 'icon-32-import';
		$options->text = 'Import';
		parent::__construct($options);
	}
	
	public function getOnChange()
	{
		JHTML::script('koowa.js', 'media/plg_koowa/js/');
		$html[]	= 'if(confirm(\''.JText::sprintf('Click OK to start importing %s.', '\'+ this.options[this.selectedIndex].text +\'').'\')){';
		$if[]	= 'Koowa.Form.addField(\'action\', \'import\')';
		$if[]	= 'Koowa.Form.addField(\'import\', this.value)';
		$if[]	= 'Koowa.Form.submit(\'post\')';
		$if 	= implode(';', $if);
		$html[] = '}else{';
		$else[] = 'this.selectedIndex = 0';
		$else	= implode(';', $else);
		$html[] = '}';
		return current($html).$if.next($html).$else.next($html);
	}
	
	public function render()
	{
		$name = $this->getName();
		$img = KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.img', '/32/'.$name.'.png');
		if($img)
		{
			KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.css', '.toolbar .icon-32-'.$name.' { background-image: url('.$img.'); }');
		}
	
		$text 		= JText::_($this->_options['text']);
	
		$options[]	= JHTML::_('select.option', null, $text );
  		$options[]	= JHTML::_('select.option', 'kunena', JText::_('Kunena') );
  		$options[]	= JHTML::_('select.option', 'sample', JText::_('Sample Content') );
  		// TEST: Uncomment this line to test the fireboard import option
  		//$options[]	= JHTML::_('select.option', 'fireboard', JText::_('FireBoard') );
  		//$options[]	= JHTML::_('select.option', 'test', JText::_('Test Unsupported') );
  		
		$select		= KTemplateAbstract::loadHelper('select.genericlist', $options, 'import', array(
			'class' 	=> 'inputbox',
			'onchange' 	=> $this->getOnChange(),
			'style' 	=> 'cursor:pointer;width:70px;'
		) );
	
		$html[]	= '<td class="button" id="'.$this->getId().'">';
		$html[]	= '<a class="toolbar hasTip" title="' . JText::_('Select what you want to import') . '" style="cursor:default;" >';

		$html[]	= '<span class="'.$this->getClass().'" title="'.$text.'">';
		$html[]	= '</span>';
		$html[]	= $select;
		$html[]	= '</a>';
		$html[]	= '</td>';

		return implode(PHP_EOL, $html);
	}
}