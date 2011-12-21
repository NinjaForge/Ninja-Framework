<?php
/**
 * @version		$Id: dynLoop.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */
 
class ComNinjaElementdynLoop extends ComNinjaElementAbstract
{
	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		return 'test';
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
	
		$doc = & JFactory::getDocument();
		$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js");
		$doc->addScript("http://ajax.googleapis.com/ajax/libs/jqueryui/1.7/jquery-ui.min.js");
		
		jimport('joomla.html.pane');
		jimport('joomla.filter.filteroutput');
        // TODO: allowAllClose should default true in J!1.6, so remove the array when it does.
		$tab = &JPane::getInstance('tabs');
		$url =& JURI::getInstance();
		$query = $url->getQuery(true);
		$return	= null;
		$group = $node['group'];
		$position = $this->_parent->get($node['position'], 'insertAfter');

		$id 	= JRequest::getInt('id');
		if (!$id) { 
			$id = reset(JRequest::getVar( 'cid', array())); 
		}
		$db 	=& JFactory::getDBO();
		$query = 'SELECT params'
		. ' FROM #__modules'
		. ' WHERE id ='. $id;
		$db->setQuery( $query );
		$values = $db->loadResult();
		print_r($values);
		$loop = new JParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
		//In order allow new input elements dynamically from other functions and loop them we put it in a hidden div
		echo '<div id="ajax-'.$name.'" style="display:none;">'.$tab->startPanel(JText::_('BACKENDTABTITLE'), "tab").$loop->render('params', $group).$tab->endPanel().'</div>';
		//Create variables to avoid conflicts if more than one dynLoop are rolling and more.
		$jqname = $name;
		$jqhtml = $loop->render('params', $group);
		$values = explode("\n", $values);
		$defaults = array();
		$i = null;
		$c = 1;
		$count = count($values);
		
		//print_r($values);
		foreach($values as $value) {
			$tmp = split('[=]', $value);
			if(isset($tmp[0])){
				$jqparam = array($tmp[0]);
				foreach($jqparam as $key => $default) {
					$jqparam = $default;
				}
			} else {
				$jqparam = null;
			}
			$i = (($count===$c)?"\n":",\n");
			
			if((isset($tmp[1]))&&(isset($jqparam))){
				$jqvalue = array($tmp[1]);
				foreach($jqvalue as $key => $default) {
				$default = $this->javascriptHTML($default);
				$defaults[] =	"					$jqparam: \"$default\"";
				}
			} 
		}
		$defaults = implode(",\n", $defaults);
		$script = "
			jQuery.noConflict();

			jQuery(document).ready(function($){
				$('#dynTabs').closest('tr').addClass('delete-me');
				$('#dynTabs').insertBefore($('#menu-pane').children().eq(0));
				$('tr.delete-me').remove();
				var loopTimes	= $('#$control_name$control').val();
				var defaults = {
$defaults
						};
				var loopOnLoad = function(loopTimes, start) 
				{	
					var limitstart = $('#dynTabs dt').length;
					var current = ' ';
					
					$('#dynTabs .current').fadeOut;
					
					var i=0;
					for (i=1;i<=loopTimes;i++)
					{
							$('#ajax-$name').children().eq(0).clone().attr('id', ('tab-'+(i+limitstart))).appendTo('#dynTabs .tabs');
							$('#ajax-$name').children().eq(1).clone().appendTo('#dynTabs .tabs').hide();
					}
					$('#dynTabs dt.open').show();

					$('#dynTabs dt').children().each(function(i){
						var no = i+1;
						$(this).html('Tab <span>'+(no)+'</span>');
					});
				
					$('#dynTabs dd').each(function(i){
					var countLoop = i;
						$('#dynTabs dd:eq('+i+') .paramlist_value').each(function(i){
							$(this).contents().filter(':input').each(function(i){
								function thisName(str) {
									return str.replace(/^params\[+|\]+$/g, '');
								}
								$(this).attr('name', ('params['+thisName($(this).attr('name'))+countLoop+']'));
							});
						});
					});
					for(key in defaults) {
						$('[name=\"params\['+key+'\]\"]:not(:radio, )').not('#$control_name$control').val(defaults[key]);
					}
				}
				
				
				alert('nei nei nei!!!!');
				
				
				loopOnLoad(loopTimes);
				
				$('#$control_name$control').change(function(){
					if(($('#$control_name$control').val())>($('#dynTabs dd').length)){
						loopOnLoad(  (  $('#$control_name$control').val()  )  -  (  $('#dynTabs dd').length  ), $('#dynTabs dd').length    );
					}
				});
				
			});";
		$doc->addScriptDeclaration($script);
		return '<div id="dynTabs" style="margin-bottom: 3px;">'.$tab->startPane('dynLoop').$tab->endPane().'</div>';	
	}
	function javascriptHTML ($text)
	{
		$text = ereg_replace("'","\'", $text);
		$text = ereg_replace('"','\"', $text);
		return $text;
	}//end formatHTML
	
	function javascriptXML ($text)
	{
		$text = ereg_replace("\r",'',$text);
		//the following lines are formatted with urlencoding, then the xml file is parsed in flash and the text is escaped using unescape(string);
		$text = ereg_replace("\n\n","%0a%0a",$text);
		$text = ereg_replace("\n","%0a",$text);
		$text = ereg_replace("\'","%27", $text);
		$text = ereg_replace("'","%27", $text);
		$text = ereg_replace("\&#8217;","%27", $text);
		return $text;
	}//end formatXML
}