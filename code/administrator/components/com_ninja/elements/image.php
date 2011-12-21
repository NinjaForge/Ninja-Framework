<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: image.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementImage extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;

		$doc 		=& JFactory::getDocument();
		$title      = ( $node['title'] ? $node['title'] : 'Image');
		
		$script = "\t".'function jInsertEditorText( image, e_name ) {
			document.getElementById(e_name).value = image;
			document.getElementById(e_name+\'preview\').innerHTML = image;
			if(!image.test(\'http\'))
			{
				var el	= $(e_name+\'preview\').getChildren().getLast();
				var src	= el.getProperty(\'src\');
				el.setProperty(\'src\', \''.JURI::root(true).'/\'+src);
				document.getElementById(e_name).value = document.getElementById(e_name+\'preview\').innerHTML;
			}
		}';
		if(!defined('JELEMENT_IMAGE'))
		{
			$doc->addScriptDeclaration($script);
			define('JELEMENT_IMAGE', true);
		}
		$media =& JComponentHelper::getParams('com_media');
		$ranks = array('publisher', 'editor', 'author', 'registered');
		$acl = & JFactory::getACL();
		for($i = 0; $i < $media->get('allowed_media_usergroup', 3); $i++)
		{
			$acl->addACL( 'com_media', 'popup', 'users', $ranks[$i] );
		}
		//Make sure the user is authorized to view this page
		$user = & JFactory::getUser();
		if (!$user->authorize( 'com_media', 'popup' )) {
			return JText::_('You\'re not authorized to access the media manager');
		}

		//Create the modal window link. &e_name let us have multiple instances of the modal window.
		$link = 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;e_name='.$control_name.$name;

		JHTML::_('behavior.modal');

		return ' <input type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.htmlspecialchars($value, ENT_QUOTES, 'UTF-8').'" /><div class="button2-left"><div class="image"><a class="modal" title="'.JText::_($title).'" href="'.$link.'"  rel="{handler: \'iframe\', size: {x: 570, y: 400}}">'.JText::_($title).'</a></div></div><br /><div id="'.$control_name.$name.'preview" class="image-preview">'.$value.'</div>';
	}
}
