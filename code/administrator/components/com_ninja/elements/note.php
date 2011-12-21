<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Inserts a note, including an optional slider to open/close the note
 *
 * Use the slide attribute - hide/show/none - to determine the initial state of the slide. Default is none
 *
 * Use the class attribute -note,tip,important,alert,download,help - to style the note
 * 
 */
 
class ComNinjaElementNote extends ComNinjaElementAbstract
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'note';
	
	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		echo '';
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$return = '';
		$option = JRequest::getCmd( 'option', '');
		$noteId = str_replace("[","",str_replace("]","",$name));
		
		if (isset($node['slide'])){
			$slideState = $node['slide'];
		}else{
			$slideState = 'none';
		}
		
		if (isset($node['class'])){
			$noteClass = ucfirst($node['class']);
		}else{
			$noteClass = 'Note';
		}
		
		$show = isset($node['show']) ? JText::_((string)$node['show']) : JText::_('Show '.$noteClass);
		$hide = isset($node['hide']) ? JText::_((string)$node['hide']) : JText::_('Hide '.$noteClass);
		
		//only activate this element inside ninja components, otherwise return blank
		//this is so that parameter forms in com_menu for example don't have an aneurysm 
		if (substr($option, 0,9)  == 'com_ninja'){ 	  
			
			$return .= '<div class="note note'.$noteClass.'">';
			
			if ($slideState != 'none') {
				if($slideState = 'hide'){
					$startText = $show;
				}else{
					$startText = $hide;
				}
				//I shouldn't need this define check, but for some reason scripts are being loaded to teh head twice.
				if (!defined('NINJA_NOTE'.$noteId )){
				
			
				//Do js
				KFactory::get('admin::com.ninja.helper.default')->js('window.addEvent("domready", function() { 
											var noteSlide'.$noteId.' = new Fx.Slide("'.$noteId.'").hide();
											
											$("noteToggle'.$noteId.'").addEvent("click", function(e){
													e.stop();
													if(!this.getParent("fieldset").hasClass("disabled")){
													
														noteSlide'.$noteId.'.toggle().chain(function(){
																			    if(noteSlide'.$noteId.'.open){
																			    	$("noteToggle'.$noteId.'").set("text", '.json_encode($hide).');
																			    }else{
																			    	$("noteToggle'.$noteId.'").set("text", '.json_encode($show).');
																			    }
																			});
													}
												});
										})');	
				define('NINJA_NOTE'.$noteId,1);
				}
			
				//setup controls div
				$return .= '<div class="noteToggle"><span id="noteToggle'.$noteId.'" href="#">'.$startText.'</span></div>';
			} else {
				//heading instead of a toggle
				$return .= '<div class="noteHeading"><span id="noteHeading'.$noteId.'" href="#">'.$noteClass.'</span></div>';
			}
		  
		  	//If there's an eval attribute, then there's an dynamic value in the description
		  	$description = JText::_((string)$node['description']);
		  	$description = isset($node['eval']) ? sprintf($description, eval($node['eval'])) : $description;
			$return .= '<div id="'.$noteId.'"><div class="noteBody">'.$description.'</div></div>';
			
			//Close the outermost div
			$return .= '</div>';
	    }	     		
		return $return;
	}
}