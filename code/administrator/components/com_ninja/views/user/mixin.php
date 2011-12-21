<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: mixin.php 980 2011-04-04 20:26:19Z stian $
 * @category	Napi
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaViewUserMixin extends KMixinAbstract
{
	public function setLoginLayout()
	{
	
		//Add alias filter for $this variables so they points to the view
		$this->getTemplate()->getFilter('alias')->append(array(
			'__FILE__'	=> "str_replace('tmpl://', '', __FILE__)"
		));
		
	
		KFactory::get('lib.joomla.language')->load('com_user');
		
		if($this->getIdentifier()->name == 'json') return $this->renderJsonLogin();

		$template = KFactory::get('lib.joomla.application')->getTemplate();
		$path     = JPATH_THEMES.DS.$template.DS.'html'.DS.'com_user'.DS.'login';
		KFactory::get($this->getTemplate())->addPath($path);
		$this->setLayout('site::com.user.view.login.default_login');
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
		$uri = KFactory::get('lib.koowa.http.uri');
		$uri->path = $url->path;
		$uri->query = $url->getQuery(1);
		
		$params->def( 'pageclass_sfx', 			'' );
		$params->def( 'login', 					$uri );
		$params->def( 'description_login', 		1 );
		$params->def( 'description_logout', 		1 );
		$params->def( 'description_login_text', 	JText::_( 'LOGIN_DESCRIPTION' ) );
		$params->def( 'description_logout_text',	JText::_( 'LOGOUT_DESCRIPTION' ) );
		$params->def( 'image_login', 				$this->img('/32/'.$this->getIdentifier()->package.'.png'));
		$params->def( 'image_login_align', 			'left' );
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$params->def( 'registration', 				$usersConfig->get( 'allowUserRegistration' ) );

		$title = JText::_( 'Login');

		// Set page title
		KFactory::get('lib.joomla.document')->setTitle( $title );

		// Build login image if enabled
		if ( $params->get( 'image_'.$type ) != -1 ) {
			$this->css('.login-icon{vertical-align: middle!important; -webkit-user-drag: none;}');
			$image = '<img class="login-icon" src="'. $params->get( 'image_'.$type )  .'" align="'. $params->get( 'image_'.$type.'_align' ) .'" alt="'.$this->getIdentifier()->package.' app icon" />';
		}

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
		))->mixin(KFactory::get('admin::com.ninja.template.mixin'));
		
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
		KFactory::get('lib.joomla.document')->setMimeEncoding('text/html');
		
		$url = KRequest::url();
		$uri = KFactory::get('lib.koowa.http.uri');
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