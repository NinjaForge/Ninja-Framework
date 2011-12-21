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
  * @package     com_ninja
  * @subpackage  admin.elements
  * @version	 2.0.4 January 17, 2010
  * @author		Ninja Forge http://ninjaforge.com
  * @copyright 	Copyright (C) 2007 - 2010 Ninja Forge
  * @license	http://www.gnu.org/licenses/gpl.html GNU/GPLv3 only
  */
 
/**
 * Renders chained element
 *
 * @package com_ninja
 * @subpackage admin.elements
 */
class ComNinjaElementStdPanel extends ComNinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
	
		$buffer = '';
		$option = JRequest::getCmd( 'option', '');
		
		//only activate the panels in this element inside ninja components, otherwise just return the contents
		//this is so that parameter forms in com_menu for example don't have an aneurysm 
		if (substr($option, 0,9)  == 'com_ninja'){
		
		  $buffer = '</div>';
		
		
       jimport('joomla.html.pane');
	    $panel = JPane::getInstance('sliders', array('allowAllClose'=>'true'));
	  	
			//if($node['panename']){			
			  $buffer .= $panel->startPane('boo'); 			  
		//	}
			
			$buffer .= $panel->startPanel(JText::_($node['panellabel']), $name);
		}//if (substr($option, 0,9)  == 'com_ninja')
		//process the contents of the pane
	
    $panelChildren = $node->children();
		
		$buffer .= "<div class=''>";
        foreach ($panelChildren as $panelChild) {
            
            //get the type of the parameter
        		$type = (string) $panelChild['type'];
        		try
        		{
        			$identifier = new KIdentifier($type);
        		}
        		catch(KException $e)
        		{
        			$identifier = 'admin::com.ninja.element.'.$type;
        		}
        
        		$element = KFactory::tmp($identifier, array(
        								'parent'		=> $this->_parent,
        								'node'			=> $panelChild,
        								'value'			=> $this->_parent->get($name . '_' .(string) $panelChild['name']),
        								'field'			=> $this->field,
        								'group'			=> $this->_parent->getGroup(),
        								'name'			=> $name . '_' . (string) $panelChild['name'],
        								'fetchTooltip'	=> false
        				  			));
    			
    		    			
                $buffer .= '<div class="element">';
                $buffer .= '<label id="'.$name.'-lbl" class="hasTip key" for="'.$name.'">'.JText::_($element->label).'</label>';
                $buffer .= $element->toString();
                $buffer .= "</div>";
    
        }
		//$buffer .= "</div>";

    //close the pane
    if (substr($option, 0,9)  == 'com_ninja'){
      $buffer .= $panel->endPanel();
			
      //if($node['endpane']){			
			   $buffer .= $panel->endPane();
			//}       		
		}//if (substr($option, 0,9)  == 'com_ninja')	     		
		return $buffer;
	}
}
