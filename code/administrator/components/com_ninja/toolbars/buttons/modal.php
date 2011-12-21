<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: modal.php 887 2011-02-16 13:52:21Z stian $
 * @category	NinjaForge Plugin Manager
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Modal button class for a toolbar
 * 
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonModal extends ComNinjaToolbarButtonAbstract
{
	protected $_name;

	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		$this->_name = KInflector::underscore($options->text);
		$img = KFactory::get('admin::com.ninja.helper.default')->img('/32/'.$this->_name.'.png');
		if($img)
		{
			KFactory::get('admin::com.ninja.helper.default')->css('.toolbar .'.$options->icon.' { background-image: url('.$img.'); }');
		}
		if(!isset($this->_options['x'])) $this->_options['x'] = 720;
		if(!isset($this->_options['y'])) $this->_options['y'] = 'window.getSize().size.y-80';
		if(!isset($this->_options['handler'])) $this->_options['handler'] = 'iframe';
		if(!isset($this->_options['ajaxOptions'])) $this->_options['ajaxOptions'] = '{}';
	}
	
	public function getName()
	{
		return isset($this->_name) ? $this->_name : parent::getName();
	}
	
	public function render()
	{				

		$this->attribs->set(array(
			'class' => 'toolbar modal',
			'href'  => JRoute::_($this->_options['link']),
			'rel'	=> '{handler:\''.$this->_options['handler'].'\', size: {x: '.$this->_options['x'].', y: '.$this->_options['y'].'},ajaxOptions:'.$this->_options['ajaxOptions'].'}'
		));
		
		if(isset($this->_options['title'])) $this->attribs->title = $this->_options['title'];
		
		return parent::render();
	}
}