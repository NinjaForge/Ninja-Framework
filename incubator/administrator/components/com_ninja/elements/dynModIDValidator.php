<?php
/**
 * @version		$Id: dynModIDValidator.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementdynModIDValidator extends ComNinjaElementAbstract
{
	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		
		$return = '';
		if ( $this->_parent->get('modIDs') ) {
			$return = '<span class="hasTip" id="'.$control_name.$name.'-lbl" title="'.JText::_($label).'::'.JText::_($description).'">'.JText::_($label).'</span>';
		}	
			
		return $return;
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$return = '';
		if ( $this->_parent->get('modIDs') ) {
			//Get the module IDs
			$modIDs = $this->_parent->get('modIDs');
			
			//Explode the modIDs variable so we can count how many ids we have.
    		$modIDforeach = explode( ",", $modIDs );
    		
    		//Check the entered IDs
    		$cid	    = JRequest::getVar( 'cid', array());
			$id = implode($cid);
			if (!$id) {
			$id			= JRequest::getInt('id');
		    }
    		$count		= count($modIDforeach);
    		$i			= 1;
    		$s			= ',';
    		foreach( $modIDforeach as $m ) {
    			if($count === $i) {
    				$s = '';
    			}
    			
		    	if( $m === $id ) {
		    		$return .= '<span class="hasTip" title="'.JText::_('WARNING1').'::'.JText::_('WARNING1TXT').'" style="color:red;">'.$m.$s.'</span>';
		    	} elseif(!ComNinjaElementdynModIDValidator::exist($m)) {
		    		$return .= '<span class="hasTip" title="'.JText::_('WARNING2').'::'.JText::_('WARNING2TXT').'" style="color:red;">'.$m.$s.'</span>';
		    	} else {
		    		$return .= '<span class="hasTip" title="'.JText::_('WARNING3').'::'.JText::_('WARNING3TXT').'" style="color:green;">'.$m.$s.'</span>';
		    	}
		    $i++;
		    }
		}
		
		return $return;
	}
	
/****
* Check if module exist
****/
	function exist( $id ) {
		
			//We need the databaseloaded to do our query
			$database     = &JFactory::getDBO();
		
			$query    = "SELECT *" 
						. "\n FROM #__modules AS m"
						. "\n where m.id = '" . $id . "' "
						. "\n AND m.client_id != 1";

            $database->setQuery( $query );
            $exist  = $database->loadResult();
		 
		return $exist;
	
	}//exist function
}