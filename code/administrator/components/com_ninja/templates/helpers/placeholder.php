<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Placeholder Helper - Render a placeholder if we have no items
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @todo deprecate/clean tidy this up, no reason it should be split across view/helper
 */
class NinjaTemplateHelperPlaceholder extends KTemplateHelperAbstract
{	
	/**
	 * Constructor
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $options)
	{
	    parent::__construct($options);
	    
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
		$document = JFactory::getDocument();
		if($this->disableToolbar) $document->addScriptDeclaration(implode(PHP_EOL, $script));
	}
	
	public function append($name = null, $attr = null,  $msg = 'Add %s&hellip;')
	{
		if(!$name) $name = $this->name;
		$attributes = array('class' => $name, 'style' => '-moz-user-select: none', 'onselectstart' => 'return false;', 'ondragstart' => 'return false;', 'onclick' => "this.addClass('active'); return this;");
		if(is_string($attr)) $attributes['href'] = $attr;
		else $attributes = array_merge($attributes, (array)$attr);

		$icon = $this->getService('ninja:template.helper.document')->img('/32/' . $name . '.png');
		$style = '.placeholder .'.$attributes['class'].' > span { background-image: url('.$icon.'); }';
		if($icon) JFactory::getDocument()->addStyleDeclaration($style);

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
		$html[] = $this->getService('ninja:template.helper.document')->render('/grid.css');
		$html[] = $this->getService('ninja:template.helper.document')->render('/placeholder.js');
	
		$html[] = '<div class="'.$this->class.'"><h1 class="title"> ' . $this->title . '</h1>';
		$html[] = implode('&nbsp;&nbsp;&nbsp;&nbsp;', $this->buttons);
		$html[] = '</div>';

		return implode($html);
	}
}