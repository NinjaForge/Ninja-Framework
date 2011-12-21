<?php
/**
 * @version		$Id: loadgroup_backup.php 365 2010-06-23 11:43:38Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementLoadGroup extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{	
		if($node['secret'] && !JRequest::getBool($node['secret']))
		{
			return null;
		}
		
			$order = ( $node['order']=='after' ? 'after' : 'before' );
			$label = ( $node['label'] ? $node['label'] : 'Layout Parameters' );
			$doc = & JFactory::getDocument();		
			$style = "
		#$control_name$name-tabs {
			border-top-width: 0px;
			border-right-width: 0px;
			border-bottom-width: 0px;
			border-left-width: 0px;
		}";
			$doc->addStyleDeclaration($style);
	
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
			nimport('napi.html.parameter');
			$load = new NParameter($values, JPATH_ROOT.DS.'modules'.DS.$modname.DS.$modname.'.xml');
			
			if(!$node->children()) {
	
			$content = $load->renderFieldset('params', $value, $node['suffix']);
			
			$container = ( $node['panel'] ? "
			<div id='$control_name$name' class='panel ui-widget ui-widget-content'>
				<h3 class='title jpane-toggler' id='$name-page'>
					<span>".JFilterOutput::ampReplace($label)."</span>
				</h3>
				<div class='jpane-slider ui-widget-content content' style='background:transparent;'>$content</div>
			</div>" 
			: 
			" <div id='$control_name$name'>$content</div>" );
			
			$script = "
				jQuery.noConflict();
	 
				jQuery(document).ready(function($){
					$('#$control_name$name').closest('.pane-sliders > .panel').$order($('#$control_name$name'));
				});
				";
			$doc->addScriptDeclaration($script);
		} else {
		
			$content = null;
			$panel = null;
			$title = null;
			
			$container = ( $node['panel'] 
				? 
				" <div id='$control_name$name' class='panel ui-widget ui-widget-content'>" 
				: 
				" <div id='$control_name$name'>" );
				
            $opt['useCookie'] = $node['cookie'] ? '\''.$node['cookie'].'\'' : null;
            JHTML::script('tabs.js');
			$panel .= JPaneTabs::startPane($control_name.$name.'tabs');
			
			foreach($node->children() as $group)
			{
				$content = $load->renderFieldset('params', $group['value'], $node['suffix']);
				
				$title .= ( $node['panel'] ? ' <span>'.JFilterOutput::ampReplace($group->data()).'</span>' 
				: 
				JFilterOutput::ampReplace($group->data()) );
				
				$panel .= ( $node['panel'] 
				? 
				JPaneTabs::startPanel(JFilterOutput::ampReplace($group->data()), $group['value']).$content.JPaneTabs::endPanel() 
				: 
				$content );
				
			}
			$panel .= JPaneTabs::endPane();
			
			$container .= ( $node['panel'] 
				? 
				'<h3 class="title jpane-toggler" id="'.$value.'-page"><span>'.JFilterOutput::ampReplace($label).'</span></h3>
					<div class="jpane-slider jpane-current ui-widget-content content" style="background:transparent;">'.$panel.'</div>
				</div>'
				: 
				$panel.'</div>' );
				
				$script = "
		jQuery.noConflict();
 
		jQuery(document).ready(function($){
			$('#$control_name$name').closest('.pane-sliders > .panel').$order($('#$control_name$name'));
		});
				
				
		window.addEvent('domready', function(){ 
			
			var dur = 600;
			var trans = Fx.Transitions.Quad.easeInOut;
			var el = $$('#$control_name$name div.jpane-slider').getFirst();
			var fx = new Fx.Styles(el, {duration: dur, transition: trans});
			
			$$('dl.tabs').each(function(tabs){ new JTabs(tabs, {
			onActive: function(title, description){
                description.effects({duration: dur, transition: trans}).start({'opacity': 1, 'height': description.getSize().scrollSize.y});
                title.addClass('open').removeClass('closed');
	            el.effects({duration: dur, transition: trans}).start({'height': description.getSize().scrollSize.y + title.getParent().getSize().scrollSize.y + title.getParent().getStyle('margin-top').toInt()});
            },
            onBackground: function(title, description){
            	description.effects({duration: dur, transition: trans}).start({'opacity': 0});
            	description.effects({duration: dur, transition: trans}).start({'height': 0});
                title.addClass('closed').removeClass('open');
            },
            cookie: 'showcase'}); }); 
        });";
				$doc->addScriptDeclaration($script);
				$doc->addStyleDeclaration('.jpane-current div.current { padding: 0px 0px; border-width:0px; border-top-width:1px;}');
		}
			return $container;	
	}
	
	function fetchToolTip()
	{
		return false;
	}
}