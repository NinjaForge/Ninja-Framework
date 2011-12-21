<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: about.php 794 2011-01-10 18:44:32Z stian $
 * @category	NinjaForge Plugin Manager
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Install button class for a toolbar
 * 
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonAbout extends KToolbarButtonNew
{
	public function __construct(KConfig $options)
	{
		$options->icon	= 'icon-32-about';
		$options->text	= 'About';
		$options->option	= !empty($options->option) ? $options->option : KRequest::get('get.option', 'cmd');
		parent::__construct($options);
		$img = KFactory::get('admin::com.ninja.helper.default')->img('/32/about.png');
		if($img)
		{
			KFactory::get('admin::com.ninja.helper.default')->css('.toolbar .icon-32-about { background-image: url('.$img.'); }');
		}
	}
	
	public function getLink()
	{
		$option = $this->_options['option'];
		$view	= KInflector::singularize(KRequest::get('get.view', 'cmd'));
		//return 'index.php?option='.$option.'&view=plugins&layout=default';
		// modify url
		$url = clone KRequest::url();
		$query = $url->getquery(1);
		//$query['view']	= KInflector::singularize(KRequest::get('get.view', 'cmd'));
		$query['view']	= 'dashboard';
		$query['tmpl']= 'component';
		$url->setQuery($query);
		// render html
		return $url;
	}
	
	public function render()
	{
		$text	= JText::_($this->_options['text']);

		//Tooltip
		//KTemplateAbstract::loadHelper('behavior.tooltip');
		
		//Call the modal behavior
		JHTML::_('behavior.modal');
		
		$html 	= array ();
		$html[]	= '<td class="button" id="'.$this->getId().'">';
		$html[]	= '<a href="'.JRoute::_($this->getLink()).'" onclick="'. $this->getOnClick().'" rel="{handler:\'iframe\'}" class="toolbar modal">';

		$html[]	= '<span class="'.$this->getClass().'" title="'.$text.'">';
		$html[]	= '</span>';
		$html[]	= $text;
		$html[]	= '</a>';
		$html[]	= '</td>';

		return implode(PHP_EOL, $html);
	}
}