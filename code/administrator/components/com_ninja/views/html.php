<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaViewHtml extends ComDefaultViewHtml
{	
	/**
	 * Document title
	 *
	 * @var string
	 */
	public $_title = null;
	
	/**
	 * An boolean to disable the toolbar when needed
	 *
	 * @var boolean
	 */
	protected $_toolbar = true;
	
	/**
	 * Document  subtitle
	 *
	 * @var string
	 */
	public $_subtitle = null;
	
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
	 * Wether to automatically set the page title or not
	 *
	 * @var boolean
	 */
	public $_auto_title = true;

	public function display()
	{
		//Add the template override path
		$parts = $this->getIdentifier()->path;
		
		array_shift($parts);
		if(count($parts) > 1)
		{
			$path    = KInflector::pluralize(array_shift($parts));
			$path   .= count($parts) ? DS.implode(DS, $parts) : '';
			$path   .= DS.strtolower($this->getName());	
		} 
		else $path  = strtolower($this->getName());
		       
		$model		= $this->getModel();
		$identifier	= $model->getIdentifier();
		$type		= $identifier->type;
		$package	= $identifier->package;
		$isAdmin	= JFactory::getApplication()->isAdmin();
				
		//Set the document title
		if($this->_auto_title) $this->setDocumentTitle();
		
		// Hide the toolbar if we're in an iframe
		if(KRequest::get('get.tmpl', 'cmd') == 'component') $this->_toolbar = false;

		$this->assign('length', $this->getService($this->getModel()->getIdentifier())->getTotal());

        //@TODO fix up toolbar
		/*if($this->_toolbar)
		{
			$toolbar = $this->getToolbar();
			if(!$this->length && KInflector::isPlural($this->getName())) $toolbar->removeListButtons();
			$this->_document->setBuffer($toolbar->renderTitle(), 'modules', 'title');
			$this->_document->setBuffer($toolbar->render(), 'modules', 'toolbar');
			
			//Needed for templates like AdminPraise2
			//@TODO submit patch to com_default's dispatcher
			JFactory::getApplication()->set('JComponentTitle', $toolbar->renderTitle());
		}*/
		
		KService::setAlias('com://admin/' . $package . '.form.default', 'ninja:form.default');
		
		return parent::display();
	}
	
	public function js($src = false)
	{
	    throw new BadMethodCallException(__METHOD__.' is deprecated.');
	}
	
	public function css($href = false)
	{
	    throw new BadMethodCallException(__METHOD__.' is deprecated.');
	}
	
	public function img($src)
	{
	    throw new BadMethodCallException(__METHOD__.' is deprecated.');
	}
	
	public function placeholder($name = null, $attr = null,  $text = 'Add %s', $notice = false, $options = array())
	{
		if (!$name) $name = $this->getName();
		if (!$text) $text = 'Add %s';
		if(!isset($this->length)) $this->length = $this->getService($this->getModel()->getIdentifier())->getTotal();
		if ($this->length > 0) return false;
		
		$attr = (is_array($attr) || is_object($attr)) 
			? (array) $attr : array('href' => $this->createRoute( $attr ? (string) $attr 
			: 'view='.KInflector::singularize($name) ));
		
		$options['name'] = $name;
		if($notice) $options['notice'] = $notice;
				
		return $this->getService('ninja:template.helper.placeholder', $options)->append($name, $attr, $text);
	}
	
	public function getDocumentSubTitle()
	{
		if(!$this->_subtitle) $this->_subtitle = JText::_(KInflector::humanize($this->getName()));
		return $this->_subtitle; 
	}
	
	public function getDocumentTitle()
	{
		if(!$this->_title)
		{
			$app = JFactory::getApplication();
			$identifier = $this->getService($this->getModel())->getIdentifier();
			
			if($app->isAdmin())
			{
				$this->_title = htmlspecialchars_decode($this->getDocumentSubTitle() 
								. ' | ' . JText::_($identifier->type.'_'.$identifier->package.'_'.strtoupper($identifier->name)) 
								. ' | ' . JText::_( 'Admin' ) . ' ' . JFactory::getApplication()->getCfg('sitename'));
			}
			else
			{
				
				$this->_title = htmlspecialchars_decode($this->getDocumentSubTitle());
			}
			
		}
		return $this->_title;
	}
	
	public function setDocumentTitle()
	{
		JFactory::getDocument()->setTitle($this->getDocumentTitle());
	}
	
	public function render( $content = ' ', $title = false, array $module = array(), $attribs = array() )
	{
	/*
		jimport('joomla.application.module.helper');
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
		$module->module = 'mod_' . $this->getIdentifier()->package . '_' . $this->getIdentifier()->name;
		$module->id = $this->getService('koowa:filter.int')->sanitize(uniqid());
		$module->title = (string) $title;
		$module->style = isset($tmp['style']) ? $tmp['style'] : 'rounded';
		$module->position = $this->getIdentifier()->package . '-' . $this->getIdentifier()->name;
		$module->showtitle = (bool) $title;
		$module->name = $this->getIdentifier()->package . '_' . $this->getIdentifier()->name;
		$module->user = 0;	
		$module->content = $content;
		$module->set($tmp);
		
		if(!isset($attribs['name'])) $attribs['name'] = $module->position;
		if(!isset($attribs['style'])) $attribs['style'] = $module->style;
		if(!isset($attribs['first'])) $attribs['first'] = null;
		if(!isset($attribs['last'])) $attribs['last'] = null;
				
		if (($yoofix = JPATH_THEMES.DS.JFactory::getApplication()->getTemplate().DS.'lib'.DS.'php'.DS.'template.php') && ($isYoo = file_exists($yoofix))) require_once $yoofix;
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

		$tpl = JFactory::getApplication()->getTemplate();
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
    		
    		$template = $this->getService($this->getTemplate())->addFilter(array('alias'));
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

				$module->content = $template->loadFile($warpfive, $data)->render(true);
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
	
	public function renderLinkTitle($title, $link, $render, $attribs = array())
	{
		$words = explode(' ', $title);
		
		
//		$search[] = ' ';

		foreach($words as $word)
		{
			$search[] = '/' . preg_quote($word) . '{1}/';
		}

		foreach($words as $word)
		{
			$replace[] = '<a ' . KHelperArray::toString(array_merge(array('href' => $link), $attribs)) . '>' . $word . '</a>';
		}

		return preg_replace($search, $replace, $render, count($search));
	}

	/**
	 * Overrides the default createRoute to allow usage in JavaScript
	 *
	 * @see	KViewAbstract::createRoute
	 * @param	string	The query string used to create the route
	 * @param	boolean	If true, encode &amp; to & for JavaScript usage
	 * @return 	string 	The route
	 */
	public function createRoute( $route = '', $js = false)
	{
		if($js)	return str_replace('&amp;', '&', parent::createRoute($route));

		return parent::createRoute($route);
	}
}