<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: application.php 794 2011-01-10 18:44:32Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link	 	http://ninjaforge.com
 */


class ComNinjaHelperApplication
{

	/**
	* Get a path
	*
	* @access public
	* @param string $varname
	* @param string $user_option
	* @return string The requested path
	* @since 1.0
	*/
	public function getPath( $varname, $user_option=null )
	{
		// check needed for handling of custom/new module xml file loading
		$check = ( ( $varname == 'mod0_xml' ) || ( $varname == 'mod1_xml' ) );

		if ( !$user_option && !$check ) {
			$user_option = JRequest::getCmd('option');
		} else {
			$user_option = JFilterInput::clean($user_option, 'path');
		}

		$result = null;
		$name 	= substr( $user_option, 4 );

		switch ($varname) {
			case 'front':
				$result = self::_checkPath( DS.'components'.DS. $user_option .DS. $name .'.php', 0 );
				break;

			case 'html':
			case 'front_html':
				if ( !( $result = self::_checkPath( DS.'templates'.DS. JApplication::getTemplate() .DS.'components'.DS. $name .'.html.php', 0 ) ) ) {
					$result = self::_checkPath( DS.'components'.DS. $user_option .DS. $name .'.html.php', 0 );
				}
				break;

			case 'toolbar':
				$result = self::_checkPath( DS.'components'.DS. $user_option .DS.'toolbar.'. $name .'.php', -1 );
				break;

			case 'toolbar_html':
				$result = self::_checkPath( DS.'components'.DS. $user_option .DS.'toolbar.'. $name .'.html.php', -1 );
				break;

			case 'toolbar_default':
			case 'toolbar_front':
				$result = self::_checkPath( DS.'includes'.DS.'HTML_toolbar.php', 0 );
				break;

			case 'admin':
				$path 	= DS.'components'.DS. $user_option .DS.'admin.'. $name .'.php';
				$result = self::_checkPath( $path, -1 );
				if ($result == null) {
					$path = DS.'components'.DS. $user_option .DS. $name .'.php';
					$result = self::_checkPath( $path, -1 );
				}
				break;

			case 'admin_html':
				$path	= DS.'components'.DS. $user_option .DS.'admin.'. $name .'.html.php';
				$result = self::_checkPath( $path, -1 );
				break;

			case 'admin_functions':
				$path	= DS.'components'.DS. $user_option .DS. $name .'.functions.php';
				$result = self::_checkPath( $path, -1 );
				break;

			case 'class':
				if ( !( $result = self::_checkPath( DS.'components'.DS. $user_option .DS. $name .'.class.php' ) ) ) {
					$result = self::_checkPath( DS.'includes'.DS. $name .'.php' );
				}
				break;

			case 'helper':
				$path	= DS.'components'.DS. $user_option .DS. $name .'.helper.php';
				$result = self::_checkPath( $path );
				break;

			case 'com_xml':
				$path 	= DS.'components'.DS. $user_option .DS. $name .'.xml';
				$manifests  = JFolder::files(JPATH_ADMINISTRATOR . '/components/' . $user_option, '.xml$', 0, true);
				if (!empty($manifests)) {
					foreach($manifests as $result)
					{
						$xml = simplexml_load_file($result);
						if(isset($xml['type'])) break;
					}
				}
				break;

			case 'mod0_xml':
				$path = DS.'modules'.DS. $user_option .DS. $user_option. '.xml';
				$result = self::_checkPath( $path );
				break;

			case 'mod1_xml':
				// admin modules
				$path = DS.'modules'.DS. $user_option .DS. $user_option. '.xml';
				$result = self::_checkPath( $path, -1 );
				break;

			case 'bot_xml':
				// legacy value
			case 'plg_xml':
				// Site plugins
				$path 	= DS.'plugins'.DS. $user_option .'.xml';
				$result = self::_checkPath( $path, 0 );
				break;

			case 'menu_xml':
				$path 	= DS.'components'.DS.'com_menus'.DS. $user_option .DS. $user_option .'.xml';
				$result = self::_checkPath( $path, -1 );
				break;
		}

		return $result;
	}

	/**
	 * Tries to find a file in the administrator or site areas
	 *
	 * @access private
	 * @param string 	$parth			A file name
	 * @param integer 	$checkAdmin		0 to check site only, 1 to check site and admin, -1 to check admin only
	 * @since 1.5
	 */
	protected function _checkPath( $path, $checkAdmin=1 )
	{
		$file = JPATH_SITE . $path;
		if ($checkAdmin > -1 && file_exists( $file )) {
			return $file;
		} else if ($checkAdmin != 0) {
			$file = JPATH_ADMINISTRATOR . $path;
			if (file_exists( $file )) {
				return $file;
			}
		}

		return null;
	}
}
