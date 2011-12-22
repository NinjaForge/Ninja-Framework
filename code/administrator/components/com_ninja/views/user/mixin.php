<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaViewUserMixin extends KMixinAbstract
{
	public function setLoginLayout()
	{
	
		//Add alias filter for $this variables so they points to the view
		$this->getTemplate()->getFilter('alias')->append(array(
			'__FILE__'	=> "str_replace('tmpl://', '', __FILE__)"
		));
		
	
		JFactory::getLanguage()->load('com_user');
		
		if($this->getIdentifier()->name == 'json') return $this->renderJsonLogin();

		$template = JFactory::getApplication()->getTemplate();
		$path     = JPATH_THEMES.DS.$template.DS.'html'.DS.'com_user'.DS.'login';
		//$this->getService($this->getTemplate())->addPath($path);
		$this->setLayout('com://site/user.view.login.default_login');
		$this->params = new KObject;
		
		$menu   =& JSite::getMenu();
		$item   = $menu->getActive();
		if($item)
			$params	=& $menu->getParams($item->id);
		else
			$params	=& $menu->getParams(null);
		
		$xml = JFactory::getXMLParser('Simple');
		
		if ($xml->loadFile(JPATH_ROOT.'/components/com_user/views/login/tmpl/default.xml'))
		{
			if ($groups = $xml->document->state[0]->params) {
				foreach ($groups as $param)
				{
					$params->setXML( $param );
				}
			}
		}
		
		$type = 'login';
		
		// Set some default page parameters if not set
		$params->def( 'show_page_title', 				1 );
		if (!$params->get( 'page_title')) {
				$params->set('page_title',	JText::_( 'Login' ));
			}
		if(!$item)
		{
			$params->def( 'header_login', 			'' );
			$params->def( 'header_logout', 			'' );
		}
		
		$url = KRequest::url();
		$uri = $this->getService('koowa:http.url');
		$uri->path = $url->path;
		$uri->query = $url->getQuery(1);
		
		$params->def( 'pageclass_sfx',             '');
		$params->def( 'login', 					    $uri );
		$params->def( 'description_login', 		    1);
		$params->def( 'description_logout', 		1);
		$params->def( 'description_login_text', 	JText::_( 'LOGIN_DESCRIPTION' ) );
		$params->def( 'description_logout_text',	JText::_( 'LOGOUT_DESCRIPTION' ) );
		$params->def( 'image_login', 				'');
		$params->def( 'image_login_align', 			'left' );
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$params->def( 'registration', 				$usersConfig->get( 'allowUserRegistration' ) );

		$title = JText::_( 'Login');

		// Set page title
		JFactory::getDocument()->setTitle( $title );

		// Get the return URL
		if (!$url = JRequest::getVar('return', '', 'method', 'base64')) {
			$url = base64_encode($params->get('login'));
		}

		$this->assign('image' , $image);
		$this->assign('type'  , $type);
		$this->assign('return', $url);

		$this->assign('params', $params);
		
		$this->getTemplate()->set(array(
			'image'		=> $image,
			'type'		=> $type,
			'return'	=> $url,
			'params'	=> $params
		))->mixin($this->getService('ninja:template.mixin'));
		
		return $this;
	}
	
	/**
	 * Render json formatted login variables, like the token and action uri
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	private function renderJsonLogin()
	{
		//$this->_document->setMimeEncoding('application/json');
		JFactory::getDocument()->setMimeEncoding('text/html');
		
		$url = KRequest::url();
		$uri = $this->getService('koowa:http.url');
		$uri->path = $url->path;
		$uri->query = $url->getQuery(1);
		
		$data = array(
			'message' => JText::_( 'LOGIN_DESCRIPTION' ),
			'token'   => JUtility::getToken(),
			'return'  => base64_encode($uri)
		);
		
		echo json_encode($data);
	}
}