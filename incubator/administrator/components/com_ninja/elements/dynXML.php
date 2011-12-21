<?php
/**
 * @version		$Id: dynXML.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementdynXML extends ComNinjaElementAbstract
{
	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		return false;
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		jimport('joomla.html.pane');
        // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
		$panel = &JPane::getInstance('sliders');
		
		$return = '';
		
		if ( $this->_parent->get('dynXMLStyle') ) {
			$return .= '</td></tr></tbody></table>';
			$return .= $panel->endPanel();
		}
		
		$id 	= JRequest::getVar( 'id', 0, 'method', 'int' );
		if (!$id) { 
			$id = reset(JRequest::getVar( 'cid', array(0))); 
		}
		$db 	=& JFactory::getDBO();
		
		$query = 'SELECT params'
		. ' FROM #__modules'
		. ' WHERE id ='. $id;
		$db->setQuery( $query );
		$values = $db->loadResult();
		
		//Get the module name, in a slightly hacky way.
		$module		= JRequest::getWord('module');
		$mod		=& JTable::getInstance('Module', 'JTable');
		if ($id) {
			$mod->load($id);
			$modname = $mod->module;
		} elseif($module) {
			$modname = $module;
		}
		
		$dynXML = new JParameter($values, JPATH_ROOT.DS.'modules'.DS.$modname.DS.'tmpl'.DS.$this->_parent->get('layout').'.xml');
		
		if ( $this->_parent->get('dynXMLStyle') ) {
			$return .= $panel->startPanel(JText :: sprintf('LAYOUTPARAMS', $this->_parent->get('layout'), $this->_parent->get('style')), "layout");
		}
		
		$return .= $dynXML->render('params');
		if ( $this->_parent->get('dynXMLStyle') ) {
			$return .= '<table class="paramlist admintable" width="100%" cellspacing="1"><tbody>';
		}
	
		
		return $return;
	}
}