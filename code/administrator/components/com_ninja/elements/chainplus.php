<?php defined( 'KOOWA' ) or die( 'Restricted access' );
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
class NinjaElementChainplus extends NinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		
		
				
		$buffer = '';
        $chain = $node->children();
        $chaindata = $this->_parent->get((string) $node['name']);
        
        
        if (!is_array($chaindata))
        	$chaindata = array(1=>'');
        	
        	
        
        $this->getService('ninja:template.helper.document')->load('/chainplus.css');		
		
		$chainContId = str_replace("[","",str_replace("]","",$name));
		$buffer .= '<div class="wrapper" id="'.$chainContId.'">';
		
		
		//I shouldn't need this define check, but for some reason scripts are being loaded to teh head twice.
		if (!defined('NINJA_CHAINPLUS'.$chainContId)){
		
				//$jsHTMLBuffer = '<div class="chainPlusRow">';
				$jsHTMLBuffer = '';
				//write the JS to add new rows
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
				        		
				        	
				        		
				       
				        		$element = $this->getService($identifier, array(
				        								'parent'		=> $this->_parent,
				        								'node'			=> $item,
				        								'value'			=> (string)$item['default'] ? (string)$item['default'] : '' ,
				        								'field'			=> $this->field,
				        								'group'			=> $this->_parent->getGroup(),
				        								'name'			=> $name . '[{chaindivid}][' . (string) $item['name']. ']',
				        								'fetchTooltip'	=> false
				        				  			));
				    			
				    
				                $jsHTMLBuffer .= '<div class="chain '.$name . '_' . (string) $item['name'].' chain-'.$type.'">';
				                $jsHTMLBuffer .= $element->toString();
				                $jsHTMLBuffer .= "</div>";
				    
				}
				$jsHTMLBuffer .= '<a class="ninja-button buttonRemoveChainLevel brcl'.$chainContId.'" title="'. JText::_('Remove'). '" id="RemoveChainLevel_'.$chainContId.'{chaindivid}">'. JText::_('Remove'). '</a>';
				//$jsHTMLBuffer .= '</div>';
				
				 $this->getService('ninja:template.helper.document')->load('js',  '
				 var rows_'.$chainContId.' = '.count($chaindata).';
				 var template_'.$chainContId.' = \''.$jsHTMLBuffer.'\';
				 
				 
				 window.addEvent("domready", function() {
				 	var click_'.$chainContId.' = function(e) {
				 		e.stop();
				 		if(!this.getParent("fieldset").hasClass("disabled")){
					 		rows_'.$chainContId.'++;
					 		var temp_'.$chainContId.' = {chaindivid: rows_'.$chainContId.'};
					 		
					 		var inner_template_'.$chainContId.' = template_'.$chainContId.'.substitute(temp_'.$chainContId.');	
					 		
					 		var rowDiv = new Element("div", {
					 		    \'class\': "chainPlusRow",
					 		    html: inner_template_'.$chainContId.'						 		    
					 		});	 
					 		
					 		rowDiv.inject($("'.$chainContId.'"));		
					 							 							 	
					 		$("'.$chainContId.'").getLast(".chainPlusRow").getLast(".brcl'.$chainContId.'").addEvent("click",click_remove'.$chainContId.'); 
					 		
					 		//$$(".brcl'.$chainContId.'").each(function(el) {
					 		//						 		el.addEvent("click",click_remove'.$chainContId.'); 
					 		//						 	});
					 	}
				 	};
				 	
				 	var click_remove'.$chainContId.' = function(e) {
				 						 	e.stop();
				 						 	if(!this.getParent("fieldset").hasClass("disabled")){
					 						 	var parent = this.getParent("div");
					 						 	parent.dispose();
				 						 	}
				 						};
				 	
			 		
				 	$("addChainLevel_'.$chainContId.'").addEvent("click",click_'.$chainContId.');
				 	$$(".brcl'.$chainContId.'").each(function(el) {
											 		el.addEvent("click",click_remove'.$chainContId.'); 
											 	});
				 	
				 }); ');
				 
		define('NINJA_CHAINPLUS'.$chainContId,1);
				 
		
		
		}
		
		//now process the actual rows if any exist
		
		$i = 0;
		//fakei is the index we put into the arrays on the page. This basically resorts them, taking out any empty lines
		$fakei = 0;
		while (list($chaindatakey, $chaindataval) = each($chaindata)){
			
			//to remove any empty rows in the data
			$notemptycount= 0;
			foreach ($chain as $item) {
                if (isset($chaindataval[(string) $item['name']]) && $chaindataval[(string) $item['name']] )
      				$notemptycount++;        							    
			}
			
			if($notemptycount > 0 || $i == 0) {
						$buffer .= '<div class="chainPlusRow">';
				
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
			            		
			            		if (isset($chaindataval[(string) $item['name']])&& $chaindataval[(string) $item['name']] !==false){
			            				$value = $chaindataval[(string) $item['name']];
			            		} else {
			            			$value = (string)$item['default'];
			            		}
			            		
			            		$element = $this->getService($identifier, array(
			            								'parent'		=> $this->_parent,
			            								'node'			=> $item,
			            								'value'			=> $value,
			            								'field'			=> $this->field,
			            								'group'			=> $this->_parent->getGroup(),
			            								'name'			=> $name . '['.$fakei.'][' . (string) $item['name']. ']',
			            								'fetchTooltip'	=> false
			            				  			));
			        			
			        
			                    $buffer .= '<div class="chain '.$name . '_' . (string) $item['name'].' chain-'.$type.'">';
			                    //only put titles on the top row
			                    if($i == 0){
			                    	$buffer .= '<span class="chain-label">'.JText::_($element->label).'</span>';
			                    }
			                    $buffer .= $element->toString();
			                    $buffer .= "</div>";
			        
			    }
			    if($i == 0){
			    	$buffer .= '<a class="ninja-button buttonAddChainLevel" title="'. JText::_('Add'). '" id="addChainLevel_'.$chainContId.'">'. JText::_('Add'). '</a>';
			    }else{
			    	$buffer .= '<a class="ninja-button buttonRemoveChainLevel brcl'.$chainContId.'" title="'. JText::_('Remove'). '" id="RemoveChainLevel_'.$chainContId.$fakei.'">'. JText::_('Remove'). '</a>';
			    	
			    }
			    $buffer .= "</div>";
			    $fakei++;
			    
		    }
		    $i++;
		}
	    
		$buffer .= "</div>";

        return $buffer;
	}
}
