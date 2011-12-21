<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: editor.php 842 2011-01-20 00:08:16Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaTemplateHelperEditor extends ComDefaultTemplateHelperEditor
{
	/**
	 * Generates an HTML editor
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function display($config = array())
	{
		$editor	= KFactory::get('lib.joomla.application')->getCfg('editor');
		$editor	= KFactory::get('lib.joomla.user')->getParam('editor', $editor);
		
		$config = new KConfig($config);
		$config->append(array(
			'editor' => $editor,
			'height' => 300
		));

		return parent::display($config);
	}
}