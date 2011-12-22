<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Gets modules, and optionally render them
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class NinjaHelperModule extends KObjectArray
{

	/**
	 * Modules array
	 *
	 * @var array
	 */
	public $modules = array();

	/**
	 * Document module renderer
	 *
	 * @var	JDocumentRendererModule
	 */
	public $renderer;

	/**
	 * Prepares for rendering by loading classes
	 *
	 * @param	KConfig		$config		object of configurations
	 */
	public function __construct(KConfig $config)
	{
	    jimport('joomla.module.helper');
		$this->renderer =  JFactory::getDocument()->loadRenderer('module');

		parent::__construct($config);
	}
	
	/**
	 * Create a module object
	 *
	 * @param $config
	 * @return stdClass
	 */
	public function create($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'id'			=> 0,
			'title'			=> false,
			'module'		=> 'mod_custom',
			'position'		=> 'left',
			'content'		=> false,
			'showtitle'		=> 1,
			'control'		=> '',
			'params'		=> '',
			'user'			=> 0,
			'name'			=> 'custom',
			'style'			=> ''
		));
		
		return (object) $config->toArray();
	}

	/**
	 * Renders multiple modules
	 *
	 * Returns the results as an string using the __onString magic method.
	 * Doing so allows you to use this method directly in an foreach()
	 *
	 * String example:
	 *	<pre>
	 *		<?= @ninja('module.render', 'left') ?>
	 *	</pre>
	 *
	 * Array example:
	 *	<pre>
	 *		<?= @$helper('tabs.startpane', 'tabs', array('display' => 0)) ?>
	 * 		<? foreach (@$helper('module.render', 'left') as $title => $content) : ?>
	 * 			<?= @$helper('tabs.startpanel', @text($title)) ?>
	 *				<?= $content ?>
	 *			<?= @$helper('tabs.endpanel') ?>
	 *		<? endforeach ?>
	 *		<?= @$helper('tabs.endpane') ?>
	 * </pre>
	 *
	 * @param	string			$position	The position of the modules to render
	 * @param	array			$params		Associative array of values
	 * @return	KObjectArray	$this		Allows you to either echo the result as a string
	 *										or iterate over each module
	 */
	public function render($position, $params = array(), $content = null)
	{
		// We need to clear the array in order to use this more than once
		$this->_data = array();

	    foreach(JModuleHelper::getModules($position) as $module)
	    {
	    	$this->_data[$module->title] = $this->renderer->render($module, $params, $content);
	    }

	    return $this->_data;
	    
//	    $renderer->render($mod, $params, $content);
		
	}
	
	public function __toString()
	{
		return implode($this->getIterator()->getArrayCopy());
	}
}