<?php
/**
 * @version		$Id: loopgroup.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */


//
//class NParameter extends JParameter
//{
//	function render($name = 'params', $group = '_default', $suffix = '')
//	{
//		if (!isset($this->_xml[$group])) {
//			return false;
//		}
//
//		$params = $this->getParams($name, $group, $suffix);
//		$html = array ();
//		$html[] = '<table width="100%" class="paramslist admintable" cellspacing="1">';
//
//		if ($description = $this->_xml[$group]['description']) {
			// add the params description to the display
//			$desc	= JText::_($description);
//			$html[]	= '<tr><td class="paramlist_description" colspan="2">'.$desc.'</td></tr>';
//		}
//
//		foreach ($params as $param)
//		{
//			$html[] = '<tr>';
//
//			if ($param[0]) {
//				$html[] = '<td width="40%" class="paramlist_key"><span class="editlinktip">'.$param[0].'</span></td>';
//				$html[] = '<td class="paramlist_value">'.$param[1].'</td>';
//			} else {
//				$html[] = '<td class="paramlist_value" colspan="2">'.$param[1].'</td>';
//			}
//
//			$html[] = '</tr>';
//		}
//
//		if (count($params) < 1) {
//			$html[] = "<tr><td colspan=\"2\"><i>".JText::_('There are no Parameters for this item')."</i></td></tr>";
//		}
//
//		$html[] = '</table>';
//
//		return implode("\n", $html);
//	}
//	
//	/**
//	 * Render all parameters
//	 *
//	 * @access	public
//	 * @param	string	The name of the control, or the default text area if a setup file is not found
//	 * @return	array	Aarray of all parameters, each as array Any array of the label, the form element and the tooltip
//	 * @since	1.5
//	 */
//	function getParams($name = 'params', $group = '_default', $suffix = '')
//	{
//		if (!isset($this->_xml[$group])) {
//			return false;
//		}
//		$results = array();
//		foreach ($this->_xml[$group]->children() as $param)  {
//			$results[] = $this->getParam($param, $name, $suffix);
//		}
//		return $results;
//	}
//
//	/**
//	 * Render a parameter type
//	 *
//	 * @param	object	A param tag node
//	 * @param	string	The control name
//	 * @return	array	Any array of the label, the form element and the tooltip
//	 * @since	1.5
//	 */
//	function getParam(&$node, $control_name = 'params', $suffix = '', $group = '_default')
//	{
		//get the type of the parameter
//		$type = $node['type'];
//
		//remove any occurance of a mos_ prefix
//		$type = str_replace('mos_', '', $type);
//
//		$element =& $this->loadElement($type);
//
		// error happened
//		if ($element === false)
//		{
//			$result = array();
//			$result[0] = $node['name'];
//			$result[1] = JText::_('Element not defined for type').' = '.$type;
//			$result[5] = $result[0];
//			return $result;
//		}
//
		//get value & add name suffix
//		$name = $node['name'];
//		$node->removeAttribute('name');
//		$node->addAttribute('name', $name.$suffix);
//		
//		$value = $this->get($node['name'], $node['default'], $group);
//
//		return $element->render($node, $value, $control_name);
//	}
//	
//	function searchReplace($from, $with = null, $replace = null)
//	{
//		$from = preg_replace( '/'.$replace.'/', $with, $from );
//		return $from;
//	}
//}
 
class ComNinjaElementLoopGroup extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$howmany = $value;
		$doc = & JFactory::getDocument();
		$root = JURI::root();
		$style = "
#$control_name$name-pane {
		border-top-width: 0px;
		border-right-width: 0px;
		border-bottom-width: 0px;
		border-left-width: 0px;
	}
	
	#$control_name$name-newtab {
		position: relative;
	}
	
	.prev-class, .next-active {
		opacity: 0.5;
	}
	
dl.tabs dt.newtab {
	background-color: #e1e1e1;
	background-image: none;
	color: #5f5f5f;
	cursor: pointer;
}

dl.tabs dt.newtab span {
	background: url(".$root."modules/mod_ninjatabs/images/newtab.png) no-repeat 0 0;
	border-color: #3c4785;
	padding-right: 19px;
	height: 0;
	margin-right: -7px;
}



dl.tabs dt.newtab {
	width: 19px;
	padding-right: 0;
	padding-left: 7px;
	background: #fff 0;
	opacity: 0.65;
	border-width: 0;
	margin: 1px 4px;
}

dl.tabs dt.newtab:hover {
	opacity: 1;
	border-width: 1px;
	margin: 0 3px;
}

.panel dl.tabs dt {
	padding-left: 24px;
	border-bottom: 0px;
	height:15px;
}

.nf-tab-close {
	display:none;
	height: 1.8em;
	position: absolute;
	top: 2px;
	width: 1.8em;
}

.nf-tab-close span.ui-state-default {
	display: block;
	left: 50%;
	margin-left: -8px;
	margin-top: -7px;
	position: absolute;
	top: 50%;
	text-indent: -99999px;
}

.nf-button { outline: 0; margin:0 4px 0 0; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
	.nf-button .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
	
	a.nf-button { float:left; }
	
	/* remove extra button width in IE */
	button.nf-button { width:auto; overflow:visible; }
	
	.nf-button-icon-left { padding-left: 2.1em; }
	.nf-button-icon-right { padding-right: 2.1em; }
	.nf-button-icon-left .ui-icon { right: auto; left: .2em; margin-left: 0; }
	.nf-button-icon-right .ui-icon { left: auto; right: .2em; margin-left: 0; }
	
	.nf-button-icon-solo { display:block; width:8px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */	
	
	.nf-buttonset { float:left; }
	.nf-buttonset .nf-button { float: left; }
	.nf-buttonset-single .nf-button, 
	.nf-buttonset-multi .nf-button { margin-right: -1px;}
	
	.nf-toolbar { padding: .5em; margin: 0;  }
	.nf-toolbar .nf-buttonset { margin-right:1.5em; padding-left: 1px; }
	.nf-toolbar .nf-button { font-size: 1em;  }

	/*demo page css*/
	h2 { clear: both; padding-top:1.5em; margin-top:0; } 
	.strike { text-decoration: line-through; }
	";
		$doc->addStyleDeclaration($style);
		$doc->addScript("http://localhost:8888/Joomla!/1.5/media/ninjatabs/js/jquery.min.js");
		$doc->addScript('http://localhost:8888/Joomla!/1.5/media/ninjatabs/js/jquery-ui.min.js');
		$doc->addScript("http://localhost:8888/Joomla!/1.5/media/ninjatabs/js/themeswitchertool.js");
		
		$return	= null;
		$group = $node->attributes('loadgroup', 'loop');
		$titleprefix = $node->attributes('titleprefix', 'Tab ');
		$placeholder = $node->attributes('placeholder', '');
		//$tab = &JPane::getInstance('tabs');

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

		if(JRequest::getInt($name)==='1')
		{
			echo '<pre>';
			$orderArray = JRequest::getVar($control_name.$name.'tab', '', '', 'array');
			$orderImplode = implode(',', $orderArray);
			
			$loop = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
			$loop->set($name, $orderImplode);
			$valueArray = $loop->_registry['_default']['data'];
			
			$paramArray = array();
			foreach($valueArray as $loopkey => $loopval) {
				$paramArray[] = $loopkey.'='.$loopval;
			}
			$orderQuery = implode("\n", $paramArray);
			echo $orderQuery;
			$db 	=& JFactory::getDBO();
			$query = "UPDATE #__modules SET params = '$orderQuery' WHERE id = '$id'";
			$db->setQuery( $query );
			$done = $db->query();
			echo '</pre>';
		}

		$order = $node->attributes('order', '0');

		$loop = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
		$loopcontent = '';
		$looptitle   = '';
		$container = $this->javascriptHTML('<div id="'.$control_name.$name.'-pane" class="panel"></div>');
		//$value = explode(', ', $value);
		$switch = ($value==='1'?'one':(is_numeric($value)?'loop':'foreach'));
		switch($switch) {
			case 'loop':
				$i = 0;
				for ($i = 0; $i < ($value); $i++) {
					$loop = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
					$title = $this->_parent->get($node['titleparam'].$i, $titleprefix.$i );
					$looptitle   .= $this->javascriptHTML('<dt id="'.$control_name.$name.'tab-'.$i.'" style="cursor: pointer; display: none;" class="nf-button ui-state-default nf-button-icon-left"><span class="ui-icon ui-icon-close ui-state-default delete-tab"></span>'.$title.'</dt>');
					$loopcontent .= $this->javascriptHTML('<dd style="display: none;" class="'.$control_name.$name.'tab-'.$i.'">'.$loop->render('params', $group, $i).'</dd>');
					//echo $this->javascriptHTML($this->setLoopName($placeholder, $i, ($tab->startPanel($titleprefix.($i+1), $name.'-tab').$loop->render('params', $group).$tab->endPanel())));
				}
			case 'one':
					$loop = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
					$title = $this->_parent->get($node['titleparam'].$i, $titleprefix.$i );
					$looptitle   .= $this->javascriptHTML('<dt id="'.$control_name.$name.'tab-'.$i.'" style="cursor: pointer; display: none;" class="nf-button ui-state-default nf-button-icon-left"><span class="ui-icon ui-icon-close ui-state-default delete-tab"></span>'.$title.'</dt>');
					$loopcontent .= $this->javascriptHTML('<dd style="display: none;" class="'.$control_name.$name.'tab-'.$i.'">'.$loop->render('params', $group, $i).'</dd>');
				break;
			case 'foreach':
				unset($i);
				$value = explode(',', $value);
				foreach($value as $i) {	
						$loop = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
						$title = $this->_parent->get($node['titleparam'].$i, $titleprefix.$i );
						$looptitle   .= $this->javascriptHTML('<dt id="'.$control_name.$name.'tab-'.$i.'" style="cursor: pointer; display: none;" class="nf-button ui-state-default nf-button-icon-left"><span class="ui-icon ui-icon-close ui-state-default delete-tab"></span>'.$title.'</dt>');
						$loopcontent .= $this->javascriptHTML('<dd style="display: none;" class="'.$control_name.$name.'tab-'.$i.'">'.$loop->render('params', $group, $i).'</dd>');
					}
				unset($i);
				break;
		}		
		//Create variables to avoid conflicts if more than one dynLoop are rolling and more.
		$jqname = $name;
		$jqhtml = $loop->render('params', $group);
		$valuesArr = explode("\n", $values);
		$defaults = array();
		$i = null;
		$c = 1;
		$count = count($valuesArr);
		
		//print_r($values);
		foreach($valuesArr as $value) {
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
				$defaults[] =	"$jqparam: \"$default\"";
				}
			} 
		}
		
		$defaults = $this->setLoopName($placeholder, '', implode(",\n", $defaults));
		$script = "
			jQuery.noConflict();
			
			jQuery.fn.switchClass = function(class1,class2) { 
				if(this.hasClass(class1)){ 
					remove = class1; 
					add = class2; 
				} else { 
					remove = class2; 
					add = class1; 
				} 
				this.removeClass(remove); 
				this.addClass(add); 
			}; 
 
			jQuery(document).ready(function($){
				$('$container').insertBefore($('#menu-pane').children().eq($order));
				$('<div class=\"current\">$loopcontent</div>').appendTo('#$control_name$name-pane');
				$('<dl class=\"tabs\" id=\"$control_name$name-tabs\">$looptitle</dl>').insertBefore('#$control_name$name-pane .current');
				$('<dl class=\"tabs\" id=\"$control_name$name-newtab\"><div id=\"switcher\" style=\"position: absolute; right: -139px;\"></div></dl>').insertAfter('#$control_name$name-tabs');
				$('#$control_name$name-tabs dt').not(':first').addClass('closed');
				$('#$control_name$name-tabs dt:first').addClass('open').show();
				$('#$control_name$name-pane dd:first').show();
				
				$('<dt id=\"new-$control_name$name-tab\" class=\"newtab\"><span></span></dt>').prependTo('#$control_name$name-newtab');
		
	 			var loadTabs = $('#$control_name$name-pane .tabs dt:eq(0)').fadeIn('slow',function(){
		 			$(this).next().fadeIn('normal', arguments.callee);
		 		});
 $('#title').click(function(){
      $('#$control_name$name-tabs dt:first .delete-tab').triggerHandler('click');

    });
		 		$('#$control_name$name-tabs dt:not(.newtab)').live('click', function(){

		 			if($(this).hasClass('open'))
		 			{
		 				return false;
		 			}
		 			var id = $(this).attr('id');
		 			$('#$control_name$name-pane dd').hide();
		 			$('#$control_name$name-tabs dt').switchClass('open', 'closed');
		 			$('#$control_name$name-pane dd.'+id).show();
		 			$(this).switchClass('open', 'closed');	
		 		});
		 		
		 		//Sort tabs function
		 		$('#$control_name$name-tabs').sortable({ items: 'dt', cancel: '.newtab', revert: 'true', containment: 'parent', delay: '100', update: function() 
		 				{
							var order = $(this).sortable('serialize') + '&option=com_modules&task=edit&id=18&$name=1';
							$.post('index.php', order);
						}
				});				
				
                $('#delete-$name').appendTo('#footer');
				var loopTimes	= $('#$control_name$name df').val();
				var defaults = {
$defaults
						};
				//var loopOnLoad = function(loopTimes, start) 
				{	
					var limitstart = $('#dynTabs dt').length;
					var current = ' ';
					
					$('#dynTabs .current').fadeOut;
					
					var i=0;
					for (i=0;i<=loopTimes;i++)
					{
							$('#ajax-$name').children().eq(0).clone().attr('id', ('tab-'+(i+limitstart))).appendTo('#dynTabs .tabs');
							$('#ajax-$name').children().eq(1).clone().appendTo('#dynTabs .tabs').hide();
					}
					$('#dynTabs dt.open').show();

					$('#dynTabs dt').children().each(function(i){
						$(this).html('Tab <span>'+(i)+'</span>');
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
						//$('[name=\"params\['+key+'\]\"]:not(:radio, )').not('#$control_name$name').val(defaults[key]);
					}
				}
				
				//loopOnLoad(loopTimes);
				
				$('#$control_name $name').change(function(){
					if(($('#$control_name$name').val())>($('#dynTabs dd').length)){
						loopOnLoad(  (  $('#$control_name$name').val()  )  -  (  $('#dynTabs dd').length  ), $('#dynTabs dd').length    );
					}
				});
				
			";
		$doc->addScriptDeclaration($script);
		echo '<pre>';
		
		//Untouched
		echo "\n Untouched ".count($doc->_script)."\n";
		
		//Backup
		$headData = $doc->getHeadData();
		$scriptBackup = $headData['script'];
		
		unset($doc->_script);
		unset($looptitle);
		//code for the add tab function
		$newTabGroup = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.'mod_ninjatabs'.DS.'mod_ninjatabs.xml');
		$title = $this->_parent->get($node['titleparam'], $titleprefix.' @');
		$looptitle   = $this->javascriptOnEval('<dt id="'.$control_name.$name.'tab-@" style="cursor: pointer;" class=\"closed\"><span>'.$title.'</span></dt>');
		//$newTabGroup = $this->javascriptHTML('<p style="">'.$newTabGroup->render('params', $group, '@@').'</p>');
		//echo print_r($newTabGroup);
	
			
		//Recover head data
		$doc->addScriptDeclaration($headData['script']['text/javascript']);
		
		echo '</pre>';
		
		$script = "
function addTabGroup(tab) {
	tab = (tab - 0) + 1;
}
						
						var tabtitle = '$looptitle';
						var tabbody  = '$looptitle';
						
						
						$('.newtab').click(function(){
							var changetitle = tabtitle;
							var changebody = tabbody;
							var test = $('#$control_name$name-tabs').attr('id');
							var test = test.replace(/paramstablooptab-/g, ' ');
							//var test = (test - 0) + 1;
							var thisval = $(this).prev().attr('id').split(\"-\");
							//console.log(thisval);
							thisval = jQuery.makeArray(thisval);
							//console.log(thisval);
							thisval++;
							idArray = [];
//							for (i = 0; i < 10; i++)
//							{
//								var idArray = $('#$control_name$name-tabs dt:eq('+i+')').attr('id');
//								console.log('The number is '+ i + idArray);
//								i++;
//							}
							
							test = $('#$control_name$name-tabs dt:not(.newtab)');
							
							var testlength = test.length;
							//test = test.replace(/paramstablooptab-/g, ' ');
							jQuery.each(test, function(i, n) {
								thisval = $(n).attr('id').replace(/paramstablooptab-/g, ' ');						      
							
						        //return (thisval != i);
						        if(!jQuery.inArray(i, test))
						        {
						        	//console.log('inArray');	
						        } else if(i===testlength) {
						        	//console.log('testlength '+testlength + ' i '+i);
						        } else {
						        	//console.log('true');
						        }
						    });
							
							addTabGroup('myname');
							
							//$(title.replace(/@/g, thisval)).insertBefore($(this));
							
							//$('#$control_name$name-tabs dt.closed:last').trigger('click');
							
							//$('#$control_name$name-tab-'+thisval).trigger('click');
							//$('#$control_name$name-tabs').sortable( 'refresh' );
							//$('#footer').append('<scr' + 'ipt>'+tabscript+'</scr' + 'ipt>');
							
						});
						
						$('.tabs .delete-tab').live('click', function(){
						//console.log($('.tabs.ui-sorable dt').length);
						if($('#$control_name$name-tabs .ui-sorable dt').length != 1) {
							//console.log($('.tabs.ui-sorable dt').length);
							var removeMe = $(this).parent('dt').attr('id');
							$('#paramstabloop-tabs dt:only-child').slideToggle();
							$('#'+removeMe).stop().fadeOut('normal', function(){ 
								$(this).remove();
								$('.tabs dt:first').removeClass('closed').addClass('open');
							});
							$('.'+removeMe).stop().fadeOut('normal', function(){ 
								$(this).remove(); 
								$('.panel .current dd:first').fadeIn();
							});
						}
							return false;
						});
						
						
					});";
		$doc->addScriptDeclaration($script);
		
		return '&nbsp;';	
	}
	
	function setLoopName($replace, $with, $from)
	{
		//$from = preg_replace( '/'.$replace.'/', $with, $from );
		return $from;
	}
	
	function javascriptHTML($text)
	{
		$text = trim( preg_replace( '/\s+/', ' ', $text ) ); 
		$text = trim( preg_replace( "/'/", "\'", $text ) );
		//$text = ereg_replace('"','\"', $text);
		return $text;
	}
	
	function javascriptOnEval($text)
	{
		$text = trim( preg_replace( '/\s+/', ' ', $text ) ); 
		$text = trim( preg_replace( "/'/", "\'", $text ) );
		//$text = ereg_replace('"','\"', $text);
		return $text;
	}
}