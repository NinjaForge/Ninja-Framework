<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: ajax.php 1038 2011-05-18 20:07:24Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaViewAjax extends KViewAjax
{		
	/**
	 * Modules array
	 *
	 * @var string
	 */
	public $_modules = null;
	
	/**
	 * Modules array
	 *
	 * @var string
	 */
	public $_modules_backup = null;	

	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
        parent::__construct($options);
		                
        //Add the template override path
        $parts = $this->_identifier->path;
        
        array_shift($parts);
        if(count($parts) > 1) 
		{
			$path    = KInflector::pluralize(array_shift($parts));
			$path   .= count($parts) ? DS.implode(DS, $parts) : '';
			$path   .= DS.strtolower($this->getName());	
		} 
		else $path  = strtolower($this->getName());
		       
        $template = KFactory::get('lib.joomla.application')->getTemplate();
        $path     = JPATH_THEMES.DS.$template.DS.'html'.DS.'com_'.$this->_identifier->package.DS.$path;
          
        KFactory::get($this->getTemplate())->addPath($path);
	}

	public function display()
	{
		$model		= KFactory::get($this->getModel());
		$identifier	= $model->getIdentifier();
		$package	= $identifier->package;
				
		KFactory::map('admin::com.' . $package . '.form.default', 'admin::com.ninja.form.default');
		
		return parent::display();
	}
	
	public function js($src = false)
	{		
		return KFactory::get('admin::com.ninja.helper.default')->js($src);
	}
	
	public function css($href = false)
	{
		return KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.css', $href);
	}
	
	public function img($src)
	{		
		return KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.img', $src);
	}
	
	public function placeholder($name = null, $attr = null,  $text = 'Add %s&hellip;', $notice = false)
	{
		if (!$name) $name = $this->getName();
		if (count($this->$name) > 0) return false;
		
		$attr = (is_array($attr) || is_object($attr)) 
			? (array) $attr : array('href' => $this->createRoute( $attr ? (string) $attr 
			: 'view='.KInflector::singularize($name) ));
		
		$options = array('name' => $name);
		if($notice) $options['notice'] = $notice;
		
		return KFactory::get('admin::com.ninja.helper.placeholder', $options)->append($name, $attr, $text);
	}

	protected function helper($type)
	{
		$base 	= 'admin::com.ninja.helper.';
		
		$args 	= func_get_args();
		$count	= count(explode('.', $type));
		
		if($count <= 2)	$args[0] = $count == 2 ? $base.$type : $base.'default.'.$type ;

		return call_user_func_array( array( 'KTemplate', 'loadHelper' ), $args );
	}
	
	public function edit($row, $i, $name = 'name', $id = 'id')
	{
		return self::helper('grid.ischeckedout',$row, $i) ? '<span style="cursor:default;">' . $row->{$name} . '</span>' : '<a href="'. self::createRoute('view='. KInflector::singularize($this->getName()) .'&format=html&layout=form&id='.$row->{$id}) .'">'. $this->escape($row->{$name}) . '</a>';
	}
	
	public function render( $content = ' ', $title = false, array $module = array(), $attribs = array() )
	{
	/*
		KLoader::load('lib.joomla.application.module.helper');
		$load =& JModuleHelper::_load();
		$load[] = (object) array(
			'id' => 50,
			'title' => $title,
			     'module' => 'mod_custom',
			     'position' => 'above',
			     'content' => $content,
			     'showtitle' => 1,
			     'control' => '',
			     'params' => '
			moduleclass_sfx=-slide red
			cache=0
			
			',
			     'user' => 0,
			     'name' => 'custom',
			     //'style' => isset($module['style']) ? $module['style'] : 'rounded',
			     'style' => 'xhtml'
		);
		return null;
		die('<pre>'.print_r($load, true).'</pre>');
		//*/
	/*
	 0 => 
	  stdClass::__set_state(array(
	     'id' => '50',
	     'title' => 'Breadcrumb',
	     'module' => 'mod_breadcrumbs',
	     'position' => 'breadcrumb',
	     'content' => '',
	     'showtitle' => '0',
	     'control' => '',
	     'params' => 'showHome=1
	homeText=Home
	showLast=1
	separator=
	moduleclass_sfx=
	cache=0
	
	',
	     'user' => 0,
	     'name' => 'breadcrumbs',
	     'style' => NULL,
	  )),
	  */
	  
	  
	  
		$tmp = $module;
		$module = new KObject;
		$module->params = 'moduleclass_sfx='.@$tmp['moduleclass_sfx'];
		$module->module = 'mod_' . $this->_identifier->package . '_' . $this->_identifier->name;
		$module->id = KFactory::tmp('lib.koowa.filter.int')->sanitize(uniqid());
		$module->title = (string) $title;
		$module->style = isset($tmp['style']) ? $tmp['style'] : 'rounded';
		$module->position = $this->_identifier->package . '-' . $this->_identifier->name;
		$module->showtitle = (bool) $title;
		$module->name = $this->_identifier->package . '_' . $this->_identifier->name;
		$module->user = 0;	
		$module->content = $content;
		$module->set($tmp);
		
		if(!isset($attribs['name'])) $attribs['name'] = $module->position;
		if(!isset($attribs['style'])) $attribs['style'] = $module->style;
		if(!isset($attribs['first'])) $attribs['first'] = null;
		if(!isset($attribs['last'])) $attribs['last'] = null;
				
		if (($yoofix = JPATH_THEMES.DS.KFactory::get('lib.joomla.application')->getTemplate().DS.'lib'.DS.'php'.DS.'template.php') && ($isYoo = file_exists($yoofix))) require_once $yoofix;
		if($isYoo) $attribs['style'] = 'yoo';
	
		static $chrome;
		$mainframe = JFactory::getApplication();

		$scope = $mainframe->scope; //record the scope
		$mainframe->scope = $module->module;  //set scope to Component name


		// Get module parameters
		$params = new JParameter( $module->params );

		// Get module path
		$module->module = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
		$path = JPATH_BASE.DS.'modules'.DS.$module->module.DS.$module->module.'.php';

		// Load the module
		if (!$module->user && file_exists( $path ) && empty($module->content))
		{
			$lang =& JFactory::getLanguage();
			$lang->load($module->module);

			$content = '';
			ob_start();
			require $path;
			$module->content = ob_get_contents().$content;
			ob_end_clean();
		}

		// Load the module chrome functions
		if (!$chrome) {
			$chrome = array();
		}
		
		KLoader::load('lib.joomla.application.module.helper');
		$load =& JModuleHelper::_load();
		
		if(!$this->_modules) $this->_modules_backup = $this->_modules = $load;
		
		$this->_modules[] = $module;
		$load = $this->_modules;

		require_once JPATH_BASE.DS.'templates'.DS.'system'.DS.'html'.DS.'modules.php';
		$chromePath = JPATH_BASE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'modules.php';
		

		if (!isset( $chrome[$chromePath]))
		{
			if (file_exists($chromePath)) {
				require_once ($chromePath);
			}
			$chrome[$chromePath] = true;
		}

		//make sure a style is set
		if(!isset($attribs['style'])) {
			$attribs['style'] = 'rounded';
		}

		//dynamically add outline style
		if(JRequest::getBool('tp')) {
			$attribs['style'] .= ' outline';
		}

		$tpl = KFactory::get('lib.joomla.application')->getTemplate();
		$yoofix	  = false;
		$warpfive = 'templates/'.$tpl.'/layouts/module.php';
		if(JFile::exists(JPATH_ROOT.'/'.$warpfive)) {
    		
    		$tpl_dir = JPATH_ROOT."/templates/$tpl";
    		
    		//include warp
    		require_once($tpl_dir.'/warp/warp.php');
    		
    		$warp = Warp::getInstance();
    		$warp->getHelper('path')->register($tpl_dir.'/warp/systems/joomla.1.5/helpers','helpers');
    		$warp->getHelper('path')->register($tpl_dir.'/warp/systems/joomla.1.5/layouts','layouts');
    		$warp->getHelper('path')->register($tpl_dir.'/layouts','layouts');
    		
    		$template = KFactory::tmp($this->getTemplate())->addFilters(array('alias'));
    		$template->getFilter('alias')->append(array('$this' => '$warp_helper_template'));
    		
    		$data = array(
    		'warp_helper_template' => $warp->getHelper("template"),
    		'$warp'	=> $warp
    		);
    		
    		$module->menu = false;
    		$yoofix	 = true;
		}

		foreach(explode(' ', $attribs['style']) as $style)
		{
			$chromeMethod = 'modChrome_'.$style;

			//Warp 5.5 fix
			if($yoofix)
			{
				$module->parameter = new JParameter($module->params);
				$data['module'] = $module;
				$data['params'] = array();
				//@TODO count this
				$count = 1;
				$index = 0;
				$data['params']['count'] = $count;
				$data['params']['order'] = $index + 1;
				$data['params']['first'] = $data['params']['order'] == 1;
				$data['params']['last'] = $data['params']['order'] == $count;
				$data['params']['suffix'] = $module->parameter->get('moduleclass_sfx', '');
				$data['params']['menu'] = false;

				// get class suffix params
				$parts = preg_split('/[\s]+/', $data['params']['suffix']);
			
				foreach ($parts as $part) {
					if (strpos($part, '-') !== false) {
						$yoostyles = explode('-', $part, 2);
						$data['params'][$yoostyles[0]] = $yoostyles[1];
					}
				}

				$module->content = $template->loadPath($warpfive, $data)->render(true);
			}
			// Apply chrome and render module
			elseif (function_exists($chromeMethod))
			{
				$module->style = $attribs['style'];

				ob_start();
				$chromeMethod($module, $params, $attribs);
				$module->content = ob_get_contents();
				ob_end_clean();
			}
		}
		$mainframe->scope = $scope; //revert the scope
		return $module->content;
	}
}