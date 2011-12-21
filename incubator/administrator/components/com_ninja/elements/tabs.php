<?php
/**
 * @version		$Id: tabs.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementTabs extends ComNinjaElementAbstract
{
	function fetchToolTip()
	{
		return false;
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$doc = & JFactory::getDocument();
		$root = JURI::root();
		$namespace = $node['namespace'] ? $node['namespace'] : 'params'; 
		
		$group = $node->attributes('loopgroup', 'loop');
		$titleprefix = $node->attributes('titleprefix', 'Tab ');
		$placeholder = $node->attributes('placeholder', '');
		$path = null;
		
		switch(JRequest::getCmd('option')) {
			case 'com_modules':
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
				
				//Get the module name, in a slightly hacky way.
				$module		= JRequest::getWord('module');
				$mod		=& JTable::getInstance('Module', 'JTable');
				if ($id) {
					$mod->load($id);
					$modname = $mod->module;
				} elseif($module) {
					$modname = $module;
				}
				
				$path = JApplicationHelper::getPath('mod0_xml', $modname);
				
				//deactivate autosave
				$autosave = false;
				if(JRequest::getInt($name.'save') && $autosave)
				{
					echo '<pre>';
					$orderArray = JRequest::getVar($name.'order', '', '', 'array');
					echo "orderArray\n";
					//print_r($orderArray);
					$orderImplode = implode(',', $orderArray);
					echo "\n orderImplode\n";
					//print_r($orderImplode);
					$loop = new NParameter($values, $path);
					$loop->set($name, $orderImplode);
					$valueArray = $loop->_registry['_default']['data'];
					
					$paramArray = array();
					foreach($valueArray as $loopkey => $loopval) {
						$paramArray[] = $loopkey.'='.$loopval;
					}
					$orderQuery = implode("\n", $paramArray);
					//echo $orderQuery;
					$db 	=& JFactory::getDBO();
					//$query = "UPDATE #__modules SET params = '$orderQuery' WHERE id = '$id'";
					//$db->setQuery( $query );
					//$done = $db->query();
					echo '</pre>';
				}
				break;
			case 'com_k2'&&is_null($node['values']):
				$id 	= JRequest::getInt('cid');
				$db 	=& JFactory::getDBO();
				$query = 'SELECT plugins'
				. ' FROM #__k2_items'
				. ' WHERE id ='. $id;
				$db->setQuery( $query );
				$values = $db->loadResult();
				
				
				$path = JApplicationHelper::getPath('plg_xml', 'k2'.DS.$node['useroption']);
				break;
			default:
				jimport('joomla.environment.request');
				$id 	= $node['getvalue'] ? JRequest::getInt($node['getvalue']) : JRequest::getInt('id');

				if (!$id) { 
					$id = reset(JRequest::getVar( 'cid', array())); 
				}
				$db			= & JFactory::getDBO();
				
				$db->setQuery(sprintf($node['values'], $id));
				$values = $db->loadResult();
				
				$aid 	= $node['queryvalue'] ? JRequest::getInt($node['queryvalue']) : JRequest::getInt('id');
				//die(sprintf($node['values'], $id));
				$db->setQuery(sprintf($node['query'], $aid));
				$order = $titles = array();
				foreach($db->loadObjectList() as $tmp) 
				{
					$titles[$tmp->id] = $tmp->name;	
					$order[]          = $tmp->id;			
				}
				$value = implode(',', $order);
				$nosort = true;

				$path = JApplicationHelper::getPath($node['getpath'], $node['plugingroup'] ? $node['plugingroup'].DS.$node['useroption'] : $node['useroption'] );
				break;
		}
		//die($values);
		$order = $node['order']!==0 ? $node['order'] : 1;
		$afteradd = $node->attributes('afteradd', '');
		
		$special = ( $this->_parent->get($name.'_special') ? $this->_parent->get($name.'_special') : '0' );

		$loop = new NParameter($values, $path);
		$loopcontent = '';
		$looptitle   = '';
		//$value = explode(', ', $value);
		//$switch = ( $value==='1'?'one': ( is_numeric($value)?'loop':'foreach' ) );
		$switch = 'foreach';
		$hiddenvalue = $value;
		switch($switch) {
			case 'loop':
				$i = 0;
				for ($i = 0; $i < ($value); $i++) {
					$loop = new NParameter($values, $path);
					$title = $this->_parent->get($node['titleparam'].$i, $titleprefix.$i );
					$looptitle   .= $this->javascriptHTML('<li id="'.$control_name.$name.'-order_'.$i.'"><a id="mp_'.$control_name.$node['titleparam'].$i.'" href="#'.$name.'_'.$i.'">'.$title.'</a></li>');
					$loopcontent .= $this->javascriptHTML('<div id="'.$name.'_'.$i.'">'.$loop->renderFieldset($namespace, $group, $i).'</div>');
					//echo $this->javascriptHTML($this->setLoopName($placeholder, $i, ($tab->startPanel($titleprefix.($i+1), $name.'-tab').$loop->render($namespace, $group).$tab->endPanel())));
				}
				break;
			case 'one':
					$loop = new NParameter($values, $path);
					$title = $this->_parent->get($node['titleparam'].$i, $titleprefix.$i );
					$looptitle   .= $this->javascriptHTML('<li id="'.$control_name.$name.'-order_'.$i.'"><a id="mp_'.$control_name.$node['titleparam'].$i.'" href="#'.$name.'_'.$i.'">'.$title.'</a></li>');
					$loopcontent .= $this->javascriptHTML('<div id="'.$name.'_'.$i.'">'.$loop->renderFieldset($namespace, $group, $i).'</div>');
				break;
			case 'foreach':
				unset($i);
				$count = 0;
				$hiddenvalue = $value;
				$value = explode(',', $value);
				foreach($value as $item => $i) {	
						$isDefault = ( ($special == $count++) ? ' ui-helper-default-active' : '' );
						$loop = new NParameter($values, $path);
						
						$title = $titles ? $titles[$i] : $this->_parent->get($node['titleparam'].$i, $titleprefix.($i!=0?$i:'') );
						$loopcontrols = !$nosort ? '<span class="ui-helper-default ui-icon ui-icon-star'.$isDefault.'" rel="'.$i.'"></span><span class="remove ui-icon ui-icon-close">x</span>' : null;
						$looptitle   .= '<li id="'.$control_name.$name.'-order-items_'.$i.'">'.$loopcontrols.'<a id="mp_'.$control_name.$node['titleparam'].$i.'" style="display:block;width:100%;" href="#'.$name.'_'.$i.'">'.$title.'</a></li>';
						$loopcontent .= '<div id="'.$name.'_'.$i.'">'.$loop->renderFieldset($namespace, $group, $i).'</div>';
					}
				unset($i);
				break;
		}		
		$deletetip = JText::_('DELETETIP');
		$controls = !$nosort ? '<li class="add"><span class="ui-icon ui-icon-plusthick ui-helper-tabsadd"></span><span class="ui-icon ui-icon-minusthick ui-helper-tabsremove hasTip" title="'. $deletetip .' "></span></li>' : null;
		$container = ' <div id="'.$control_name.$name.'" class="panel"><ul>'.$looptitle.$controls.'</ul>'.$loopcontent;
				
		//Backup
		$headData = $doc->getHeadData();
		$scriptBackup = $headData['script'];
		
		unset($doc->_script);
		//code for the add tab function
		$newTabGroup = new NParameter($values, $path);
		$title = $this->_parent->get($node['titleparam'], $titleprefix.' @');
		$newTabGroup = $this->javascriptHTML($newTabGroup->renderFieldset($namespace, $group, '#{id}'));

		$headScript = $doc->getHeadData();
		$dynScript = $headScript['script']['text/javascript'];
		$dynScript = $this->javascriptOnEval($dynScript);
		
		unset($doc->_script);
		$insert = $node['inject'] ? $node['inject'] : 'insertBefore';
		$appendTo = $node['selector'] ? JText::sprintf($node['selector'], $order) : '#menu-pane > div:eq('.$order.')';
		//die($appendTo);
		$script = "
			jQuery.noConflict();
 
			jQuery(document).ready(function($){
				//Tabs are a waitin'
				$('#$control_name$name')
					.$insert('$appendTo')
					.tabs({ 
						show: function(event, ui)
						{
							$('.ui-tabs-panel:visible .ui-state-active:visible:not(.modal)').click();
							$('#$control_name$name-default').val(ui.index);
						}, 
						tabTemplate: '<li id=\"$control_name$name-order-items_#{label}\"><span class=\"ui-helper-default ui-icon ui-icon-star\"></span><span class=\"remove ui-icon ui-icon-close\">x</span><a id=\"mp_$control_name".$node['titleparam']."#{label}\" href=\"#{href}\">Item #{label}</a></li>', 
						panelTemplate: '<div></div>'})
					.find('.ui-tabs-nav')";
					if(!$nosort) { $script .= ".sortable({
						axis:'y', 
						revert: 'true', 
						appendTo: 'parent',
						cancel: 'button, .add', 
						update: function() 
			 				{
								var order = $('#$control_name$name').find('.ui-tabs-nav').sortable('toArray');
								var item = [];
								
								$(order).each(function() {
									var res = this.match((/(.+)[-=_](.+)/));
									if(res) item.push(res[2]);
								});
								item.join(',');
								var ordersave = item + '&option=com_modules&task=edit&id=18&$name\save=1';
								$('#$control_name$name-val').val(item);
							}, 
						change: function(event, ui) 
							{
								$('#$control_name$name .ui-tabs-nav .add').appendTo('#$control_name$name .ui-tabs-nav');
							},
						stop: function(event, ui)
							{
								$(ui.item).removeAttr('style');
							}
					})";
				}
				$script .= ";
				$('#$control_name$name').bind('tabsadd', function(event, ui) {
					$('#$control_name$name').tabs('select', '#' + ui.panel.id);
					var order = $('#$control_name$name').find('.ui-tabs-nav').sortable('toArray');
							var item = [];
							
							$(order).each(function() {
								var res = this.match((/(.+)[-=_](.+)/));
								if(res) item.push(res[2]);
							});
							var contents = '$newTabGroup';
							$(contents.replace(/#\{id\}/g, item[ui.index])).appendTo('#'+ui.panel.id);
							
							
					var order = $('#$control_name$name').find('.ui-tabs-nav').sortable('toArray');
					var item = [];
					
					$(order).each(function() {
						var res = this.match((/(.+)[-=_](.+)/));
						if(res) item.push(res[2]);
					});
					item.join(',');

					$('#$control_name$name-val').val(item);
					$afteradd
				});
				
				var $"."tabs = $('#$control_name$name').tabs();
				var selected = $"."tabs.tabs('option', 'selected');

				$('#$control_name$name .remove').live('uitabsremove', function(){
					$afteradd
					if( $('#$control_name$name .ui-tabs-nav .remove').length === 1 ) return false;
					var index = $('#$control_name$name .ui-tabs-nav .remove').index(this);
					if( $('#$control_name$name .ui-tabs-nav .remove').length === 2 ) $('#$control_name$name .ui-tabs-nav .remove').fadeOut();
					$('#$control_name$name').tabs('remove', index);
					var order = $('#$control_name$name').find('.ui-tabs-nav').sortable('toArray');
					var item = [];
					
					$(order).each(function() {
						var res = this.match((/(.+)[-=_](.+)/));
						if(res) item.push(res[2]);
					});
					item.join(',');

					$('#$control_name$name-val').val(item);
				});
				
				$('#$control_name$name li.add .ui-helper-tabsadd').click(
					function(){ 	
							//$('#$control_name$name .ui-tabs-nav .remove').fadeIn();
					
							var order = $('#$control_name$name').find('.ui-tabs-nav').sortable('toArray');
							var item = [];
							var newitem = [];
							var ordercount = order.length;
							var itemchecker = false;
							
							$(order).each(function(i) {
								var res = this.match((/(.+)[-=_](.+)/));
								if(res) item.push(res[2]);
							});
							var checker = 0;
									for (checker=0;checker<ordercount;checker++)
									{
										$(item).each(function(i) {
											if(item[i]!=checker) { itemchecker = true; }
											if(item[i]==checker) { itemchecker = false; return false; }
										});
										if(itemchecker==true) { 
											newitem = checker; 
											$('#$control_name$name').tabs('add', '#items_'+newitem, newitem);
											$('#$control_name$name .ui-tabs-nav .add').appendTo('#$control_name$name .ui-tabs-nav');
											return true; 
										}
									}
					}
				);
				var valueme = $('#$control_name$name-default').val();
				//$('#$control_name$name li:nth-child('+$('#$control_name$name-default').val()+')').click().mouseenter().mouseleave();
			
				$('#$control_name$name').bind('tabsshow', function(event, ui) {
					$('.fg-buttonset:visible .ui-state-active').trigger('filterme');
					var selectedVal = ui.panel.id;
					$('#$control_name$name"."_selected').val(selectedVal);		
				});
				var valueme = $('#$control_name$name"."_selected').val();
				$('#$control_name$name').tabs('select', valueme);
				
				$('ul.ui-tabs-nav', '#$control_name$name').bind('height',
			      function (e, many) {
			      	if(many==null){ var many = 2; }
			      	var uHeight = $('li', this).length - many;
			      	if(dur==null){ var dur = 1000; }
			      	var uOffset = $('li:eq('+uHeight+')', this).position();
			      	var nHeight = uOffset.top + $('li:last-child', this).height()-11;
			      	var pHeight = nHeight+19;
			      	if(nHeight>=$('#$control_name$name').height())
			      	{
				      	$(this)
				      		.stop().animate({height: nHeight+'px', overflow: 'visible'}, dur).removeClass('ui-tabs-overflow');
				        $('#$control_name$name')
				        	.stop().animate({minHeight: pHeight}, dur);
				    }    	
				}); 
				
				$('ul.ui-tabs-nav', '#$control_name$name').mouseenter(function()
				{
					$(this).trigger('height');
				});
			   $('#$control_name$name').mouseleave(function (e) {
			      var bHeight = ($('div.ui-tabs-panel:visible > *', '#$control_name$name').height()+7);
			      	$('ul.ui-tabs-nav', '#$control_name$name')
			      		.addClass('ui-tabs-overflow').stop().animate({opacity: 1}, 2000).animate({height: bHeight, overflow: 'hidden'}, 1000).stop();
					$('#$control_name$name')
						.stop().animate({opacity: 1}, 2000).animate({minHeight: bHeight}, 1000).stop();
			      }
			    );
			    $('ul.ui-tabs-nav .ui-helper-tabsremove', '#$control_name$name').mousedown(function(){
			    	var stall = $(this).css('float');
			    	$(this).animate({float: stall}, 2000, 'linear', function(){
			    		$(this).addClass('stall-is-true');
			    		if(confirm('".JText::_('Click ok to delete all items but your selected one.')."'))
			    		{
			    			$('li:not(.ui-tabs-selected) .remove', '#$control_name$name > ul.ui-tabs-nav').trigger('uitabsremove');
			    		}
			    	});
			    })	
			    .mouseup(function(){
			    	if($(this).hasClass('stall-is-true'))
			    	{
			    		$(this).removeClass('stall-is-true');
			    		return true;
			    	}
			    	$(this).stop();
			    	if(confirm('".JText::_('Click ok to delete selected item. Tip: If you want to delete all items excerpt your selected item, click and hold for 2 seconds.')."'))
			    	{
			    		$('li.ui-tabs-selected .remove', '#$control_name$name > ul.ui-tabs-nav').trigger('uitabsremove');
			    	}
			    });
			    $('ul.ui-tabs-nav .ui-helper-tabsadd', '#$control_name$name').click(function(){
			    	$('ul.ui-tabs-nav', '#$control_name$name').stop().trigger('height');
			    });
//				$('.panel.ui-tabs').resizable({ handles: 's',  });
//				alert($('div.ui-tabs-panel:visible', '#$control_name$name').height());
				$('ul.ui-tabs-nav span.ui-helper-default', '#$control_name$name').live('click', function(){
					if(!$(this).hasClass('ui-helper-default-active'))
					{
						$('ul.ui-tabs-nav span.ui-helper-default-active', '#$control_name$name').removeClass('ui-helper-default-active');
						var index = $('#$control_name$name ul.ui-tabs-nav li span.ui-helper-default').index(this);
						$('#$control_name$name"."_special').val(index);
						$(this).addClass('ui-helper-default-active');
					} 
				});
			});
			";
		$doc->addScriptDeclaration($script);
		
		//Recover head data
		$doc->addScriptDeclaration($headData['script']['text/javascript']);
		
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="text_area"' );
		$selected = ( $this->_parent->get($node['name'].'_selected')==true ? $this->_parent->get($node['name'].'_selected') : '0' );
		//echo $this->_parent->get($name.'_default');
		return $container.'<input type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'-val" value="'.$hiddenvalue.'" '.$class.' />
		<input type="hidden" name="'.$control_name.'['.$name.'_special]" id="'.$control_name.$name.'_special" value="'.$special.'" '.$class.' />
		<input type="hidden" name="'.$control_name.'['.$name.'_selected]" id="'.$control_name.$name.'_selected" value="'.$selected.'" '.$class.' /></div>';	
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
		$text = trim( preg_replace('/"/','\"', $text ) );
		return $text;
	}
}