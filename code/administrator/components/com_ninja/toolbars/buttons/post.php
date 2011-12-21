<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: post.php 819 2011-01-13 22:26:34Z stian $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * POST button class for a toolbar
 * 
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
abstract class ComNinjaToolbarButtonPost extends ComNinjaToolbarButtonAbstract
{
	protected $_fields = array();
	
	public function __construct(KConfig $options)
	{
		$this->setMethod('post');
		
		parent::__construct($options);

		$this->setField('action', $this->getName());

		KFactory::get('admin::com.ninja.helper.default')->js('/toolbar.js');
		KFactory::get('admin::com.ninja.helper.default')->css('/form.css');
		self::addScriptDeclaration();
	}
	
	public static function addScriptDeclaration()
	{
		if(!defined('NINJA_TOOLBAR_SCRIPT_LOADED'))
		{
			$document = KFactory::get('lib.joomla.document');
			
			$document->addScriptDeclaration('
				window.addEvent(\'domready\', function(){
					var toolbarForm = $(\'' . self::getForm() . '\');
					if(toolbarForm) {
						toolbarForm.toolbar();
					}
				});
			');
			
			define('NINJA_TOOLBAR_SCRIPT_LOADED', true);
		}
	}
		
	public function setField($name, $value)
	{
		$this->_fields[$name] = $value;
		if(isset($this->attribs->class))
		{
			$this->attribs->class .= ' '.$name.':\''.$value.'\' invalid';
		}
		else
		{
			$this->attribs->class = $name.':\''.$value.'\' invalid';
		}
		return $this;
	}
}