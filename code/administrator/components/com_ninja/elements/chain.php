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
class NinjaElementChain extends NinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$buffer = '';
        $chain = $node->children();
      
     if (!defined('NINJA_CHAIN')){  
      $document = JFactory::getDocument();
			$document->addStyleDeclaration("
				.wrapper {
					-webkit-border-radius: 3px;
					-moz-border-radius: 3px;
					border-radius: 3px;
					background-color: #EBEBEB;
					background-color: hsla(0, 0%, 94%, 0.8);
					border: 1px solid #E6E6E6;
					border-color: hsla(0, 0%, 90%, 0.8);
					overflow: hidden;
					padding: 6px;
				}
				
				.wrapper .chain-label {
					display: block;
					
				}
				.wrapper .chain .value, .wrapper .chain ul.group {
					margin-left: auto!important;
				}
			");
		define('NINJA_CHAIN',1);
		
		}
		//
		$buffer .= "<div class='wrapper'>";
        foreach ($chain as $item) {
            
            //get the type of the parameter
    		$type = (string) $item['type'];
    		try
    		{
    			$identifier = new KServiceIdentifier($type);
    		}
    		catch(KException $e)
    		{
    			$identifier = 'ninja:element.'.$type;
    		}
    		
    	
    		$chaindata = $this->_parent->get((string) $node['name']);
    		
    		    
    		if (isset($chaindata[(string) $item['name']])&& $chaindata[(string) $item['name']] !==false){
    				$value = $chaindata[(string) $item['name']];
    		} else {
    			$value = (string)$item['default'];
    		}
    
    		$element = $this->getService($identifier, array(
    								'parent'		=> $this->_parent,
    								'node'			=> $item,
    								'value'			=> $value,
    								'field'			=> $this->field,
    								'group'			=> $this->_parent->getGroup(),
    								'name'			=> $name . '[' . (string) $item['name']. ']',
    								'fetchTooltip'	=> false
    				  			));
			

            $buffer .= '<div class="chain '.$name . '_' . (string) $item['name'].' chain-'.$type.'">';
            $buffer .= '<span class="chain-label">'.JText::_($element->label).'</span>';
            $buffer .= $element->toString();
            $buffer .= "</div>";

        }
		$buffer .= "</div>";

        return $buffer;
	}
}


