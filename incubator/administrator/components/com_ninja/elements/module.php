<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementModule extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$db 	=& JFactory::getDBO();
		$doc = & JFactory::getDocument();
		$cid 	= JRequest::getInt('id');
		if (!$cid) { 
			$cid = reset(JRequest::getVar( 'cid', array())); 
		}
		$and = '';
		if($cid) {
			$and .= ' AND id != '.$cid.' ';
		}
		if(!$node['show_disabled'])
		{
			$and .= ' AND published = 1 ';
		}
		$query = 'SELECT id'
		. ' FROM #__modules'
		. ' WHERE client_id != 1 '
		. $and
		. ' ORDER BY module, position, ordering, title ';
		$db->setQuery( $query );
		$items = $db->loadResultArray();
		$module =& JTable::getInstance('Module', 'JTable');
		
		$options = array();
		//$items[0]['value'] = 'diamond';
		//$items[0]['_data'] = 'stian';
		//$items[1]['value'] = 'runby';
		//$items[1]['_data'] = 'didriksen';
		//print_r($itemsr);
		$return = '';
		$active = ($value ? '' : '');
		if(count($options)===1&&!$value) 
		{		
			$active = 'ui-priority-primary';
		}
		$i = 1;
		foreach ($items as $key => $id)
		{
			if($id)
			{
				$module->load($id);
				if( $i && !$value )
				{
					$value = $id;
				}
				$options[] = JHTML::_('select.option', $id, 
	"<span class='ui-helper-toggle-label fg-button-icon-left ui-corner-all ui-widget-header fg-button ui-state-default'>".
		"<span class='ui-icon ui-icon-check'></span>".
		"<span class='ui-icon ui-icon-circle-check'></span>".
		"<strong>$module->title</strong>".
		"<em>$module->module</em>".
		"<span class='fg-button-icon-right'><span class='ui-icon ui-icon-info'></span></span>".
	"</span>");
			}
			//$val	= $option['value'];
			//$text	= $option['_data'];
			//
		$i=0;}
		$doc->addScript(JURI::root(true).'/media/napi/js/jquery.quicksearch.js');
		$script = "
				jQuery(document).ready(function($){
					//all hover and click logic for buttons
					$('.ui-helper-inherit:not(.ui-state-disabled)')
					.live('mouseover',
					function(){ 
						$(this).addClass('ui-state-hover').find('input').focus(function(){
							$(this).closest('.ui-helper-inherit').addClass('ui-state-active'); 
						});
					})
					.live('mouseout',
					function(){ 
						$(this).removeClass('ui-state-hover').find('input').blur(function(){
							$(this).closest('.ui-helper-inherit').removeClass('ui-state-active'); 
						});
					});
					$('.$name-container label').quicksearch({
						position: 'before',
						attached: '.$name-container > .fg-buttonset',
						loaderText: '',
						isFieldset: true,
						delay: 100
					}).live('submit', function(e){
						e.preventDefault;
					});
				});
			";
			$doc->addScriptDeclaration($script);
		$return .= '
		<span class="ui-helper-toggleset '.$name.'-container">
			<button type="button" class="fg-button fg-button-icon-left ui-state-default fg-button-toggleable ui-corner-all '.$active.'">
				<span class="ui-helper-toggle-active ui-icon ui-icon-triangle-1-s"></span>
				<span class="ui-helper-toggle-active">Close Module List</span>
				<span class="ui-helper-toggle-default ui-icon ui-icon-triangle-1-e"></span>
				<span class="ui-helper-toggle-default">Open Module List</span>
			</button>
			<span class="fg-buttonset fg-buttonset-single ui-corner-all">'.JHTML::_('select.radiolist', $options, ''.$control_name.'['.$name.']', 'class="ui-helper-toggle-item" style="display:none;"', 'value', 'text', $value, $control_name.$name ).'</span>
		</span>';
		if(count($options)===0)
		{
			$return = 'No modules. You can only select modules that are enabled';
		}
		return $return;
	}
}
