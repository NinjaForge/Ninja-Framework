<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: install.php 794 2011-01-10 18:44:32Z stian $
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
class ComNinjaToolbarButtonInstall extends ComNinjaToolbarButtonLink
{
//	public function __construct(KConfig $options)
//	{
//		$options->icon	= 'icon-32-upload';
//		$options->text	= 'Install';
//		$options->link	= null;
//		$options->option	= !empty($options->option) ? $options->option : KRequest::get('get.option', 'cmd');
//		parent::__construct($options);
//	}
//	
//	public function getLink()
//	{
//		if($link = $this->_options['link']) return $link;
//		
		// render html
//		return 'index.php?option=com_installer&tmpl=component';
//	}
//	
//	public function render()
//	{
//		$name = $this->getName();
//		$img = KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.img', '/32/'.$name.'.png');
//		if($img)
//		{
//			KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.css', '.toolbar .icon-32-'.$name.' { background-image: url('.$img.'); }');
//		}
//	
//		$text	= JText::_($this->_options['text']);
//		
		//Tooltip
//		KTemplateAbstract::loadHelper('behavior.tooltip');
//		
		//Call the modal behavior
//		KTemplateAbstract::loadHelper('behavior.modal');
//		
//		$html 	= array ();
//		$html[]	= '<td class="button" id="'.$this->getId().'">';
//		$html[]	= '<a href="'.JRoute::_($this->getLink()).'" onclick="'. $this->getOnClick().'" rel="{handler:\'iframe\',size:{x:\'650\', y:300}}" class="toolbar modal">';
//
//		$html[]	= '<span class="'.$this->getClass().'" title="'.$text.'">';
//		$html[]	= '</span>';
//		$html[]	= $text;
//		$html[]	= '</a>';
//		$html[]	= '</td>';
//
//		return implode(PHP_EOL, $html);
//	}
}