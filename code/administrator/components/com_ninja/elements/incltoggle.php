<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package     gantry
 * @subpackage  admin.elements
 * @version		2.0.3 January 10, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPLv3 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
/**
 * @version		$Id: incltoggle.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Renders a toggle element
 *
 * @package     gantry
 * @subpackage  admin.elements
 */
class ComNinjaElementInclToggle extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name='params')
	{
		$hidden = '<input type="hidden" name="'.$name.'" value="_" />';
		
		$options = array ();
        $options[] = array('value'=>1,'text'=>'Include/Exclude','id'=>$name);

		$document = JFactory::getDocument();

		if (!defined('NINJA_INCLTOGGLE')) {
						
            $document->addScript(JURI::root(true).'/media/com_ninja/js/elements/toggle/touch.js');
            $document->addScript(JURI::root(true).'/media/com_ninja/js/elements/incltoggle/incltoggle.js');
            define('NINJA_INCLTOGGLE',1);
        }


		$document->addScriptDeclaration($this->toggleInit($name));
		
		$checked = ($value == 0) ? '' : 'checked="checked"';
		
		return "
		<div class='wrapper'>
			<input name='".$control_name."[".$name."]' value='$value' type='hidden' />
			<input type='checkbox' class='toggle inclToggle' id='params$name' $checked />
		</div>
		";
    }

	function toggleInit($id) {
		$js = "
			window.addEvent('domready', function() {
				window.incltoggle".str_replace("-", "", $id)." = new inclToggle('params".$id."', {
					focus: true, 
					onChange: function(state) {
						var value = (state) ? 1 : 0;
						this.container.getPrevious().value = value;
					}
				});
			});
		";
		
		return $js;
	}
}
