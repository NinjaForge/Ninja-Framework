<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: abstract.php 641 2010-11-09 15:07:03Z stian $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Base button class for a toolbar
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 *
 * @uses		KInflector
 */
abstract class ComNinjaToolbarButtonAbstract extends KToolbarButtonAbstract implements KToolbarButtonInterface
{

	/**
	 * KObject with the button attributes (href, class and such)
	 *
	 * @var KObject
	 */
	public $attribs;

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   array   Options
     * @return  array   Options
     */
    protected function _initialize(KConfig $options)
    {
    	$name = $this->getName();
		
		$this->attribs	= new KObject;
		$attribs		= array(
			'class'	=> 'toolbar toolbar-form-validate type:\'' . $this->_method . '\'',
			'href' => '#'
		);
		
		if(isset($options->attribs)) $attribs = array_merge($attribs, $options->attribs);
		
		$this->attribs->set($attribs);
		
		
        $options->append(array(
            'parent'	 => null,
            'icon'		 => 'icon-32-'.$name,
            'id'		 => $name,
			'text'		 => ucfirst($name),
            'method'	 => 'get',
        	'identifier' => null
        ));

        parent::_initialize($options);
    }

	public function render()
	{
		return $this->_parent->getTemplate()->loadIdentifier('button_default', array(
			'name'		=> $this->getName(),
			'text'		=> $this->getText(),
			'id'		=> $this->getId(),
			'attribs'	=> $this->attribs->get()
		))->render(true);
	}

	public function getLink()
	{
		return '#';
	}

	public function getOnClick()
	{
		return '';
	}

	public function getId()
	{
		return 'toolbar-'.$this->getParent()->getName().'-'.$this->_options['id'];
	}

	public function getIcon()
	{
		return $this->_options['icon'];
	}
	
	public function getAttribs()
	{
		return $this->_options['attribs'];
	}
	
	public function setAttribs($attribs)
	{
		return $this->_options['attribs'] = array_merge($this->_options['attribs'], $attribs);
	}
	
	public function getText()
	{
		return $this->_options['text'];
	}
	
	public function getForm($formid = null, $namespace = null)
	{
		return $formid ? $formid : KFactory::get('admin::com.ninja.helper.default')->formid($namespace);
	}
}