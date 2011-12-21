<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: placeholder.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Template Accordions Behavior Helper
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class ComNinjaHelperPlaceholder extends KTemplateHelperAbstract
{	
	/**
	 * Constructor
	 *
	 * @param array Associative array of values
	 */
	public function __construct(KConfig $options)
	{	
		$this->set($options->append(array(
			'name' => KRequest::get('get.view', 'cmd', 'item'),
			'title' => null,
			'class'	=> 'placeholder',
			'disableToolbar' => false,
			'notice' => 'You don\'t have any %s yet.',
			'showButton'=>true,
			'buttons' => array()
		))->toArray());
		
		$notice = JText::_($this->notice);
		$name   = '<em>'.JText::_(KInflector::humanize($this->name)).'</em>';
		if(!$this->title) $this->title = sprintf($notice, $name);
		
		$script[] = "window.addEvent('domready',function(){";
		$script[] = "	$$('a.toolbar').filterByAttribute('href', '=', '#').addClass('disabled');";
		$script[] = "});";
		$document = KFactory::get('lib.joomla.document');
		if($this->disableToolbar) $document->addScriptDeclaration(implode(PHP_EOL, $script));
		
		KFactory::get('admin::com.ninja.helper.default')->css('/grid.css');
		KFactory::get('admin::com.ninja.helper.default')->js('/placeholder.js');
	}
	
	public function append($name = null, $attr = null,  $msg = 'Add %s&hellip;')
	{
		if(!$name) $name = $this->name;
		$attributes = array('class' => $name, 'style' => '-moz-user-select: none', 'onselectstart' => 'return false;', 'ondragstart' => 'return false;', 'onclick' => "this.addClass('active'); return this;");
		if(is_string($attr)) $attributes['href'] = $attr;
		else $attributes = array_merge($attributes, (array)$attr);

		$icon = KFactory::get('admin::com.ninja.helper.default')->img('/32/' . $name . '.png');
		$style = '.placeholder .'.$attributes['class'].' > span { background-image: url('.$icon.'); }';
		if($icon) KFactory::get('lib.joomla.document')->addStyleDeclaration($style);

		$attributes['class'] .= ' button';

		$title	= KInflector::humanize(KInflector::singularize($name));
		$text	= sprintf(JText::_($msg), JText::_($title));
		if($this->showButton){
			$this->buttons[$name] = '<a '.KHelperArray::toString($attributes).'><span><span></span></span>'.$text.'</a>';
		}

		return $this;
	}
	
	public function __toString()
	{
	
		//KFactory::get('admin::com.ninja.helper.default')->js('/placeholder.js');
	
		$html[] = '<div class="'.$this->class.'"><h1 class="title"> ' . $this->title . '</h1>';
		$html[] = implode('&nbsp;&nbsp;&nbsp;&nbsp;', $this->buttons);
		$html[] = '</div>';

		return implode($html);
	}
}