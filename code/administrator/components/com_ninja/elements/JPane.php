<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: JPane.php 794 2011-01-10 18:44:32Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementJPane extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$doc = & JFactory::getDocument();
		$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js");
		
		jimport('joomla.html.pane');
		$instance = $node['instance'];
		$tab = &JPane::getInstance($instance);
		$startpane = (!$node['nostartpane'])?$tab->startPane($name):'';
		$startpanel = (!$node['nostartpanel'])?$tab->startPanel(JText::_('BACKENDTABTITLE'), ($name)):'';
		
		
		
		$script = "
			jQuery(document).ready(function($){
				$('#$control_name$name').
			});";
		$doc->addScriptDeclaration($script);
		return "<div id=\"$control_name$name\"><div/>";	
	}
	
	function fetchTooltip($label, $description, &$node, $control_name, $name) {

		return false;
	}
	
	function javascriptHTML ($text)
	{
		$text = ereg_replace("'","\'", $text);
		$text = ereg_replace('"','\"', $text);
		return $text;
	}//end formatHTML
}