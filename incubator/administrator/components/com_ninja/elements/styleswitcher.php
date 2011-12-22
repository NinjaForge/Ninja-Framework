<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementStyleSwitcher extends ComNinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$doc = & JFactory::getDocument();
		$doc->addScript(JURI::root(true)."/media/napi/js/styleswitchertool.js");
		$label 	= JText::_(($node['label'] ? $node['label'] : 'Select Style'));
		$copy 	= $node['copy'];
		$thumbpath = ( $node['thumbpath'] ? JURI::root(true).$node['thumbpath'] : JURI::root(true).'/media/napi/img/tmpl/' );
		$stylepath = ( $node['stylepath'] ? JURI::root(true).$node['stylepath'] : JURI::root(true).'/media/napi/css/widgets/' );
		$script = "
		jQuery.noConflict();
		jQuery(document).ready(function($){
			$('#$control_name$name').styleswitcher({loadTheme: '$value', stylepane: '<div class=\"jquery-ui-styleswitcher\"><div id=\"themeGallery\">	<ul>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-lightness/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_ui_light.png\" alt=\"UI Lightness\" title=\"UI Lightness\" />			<span class=\"styleName\">Lightness</span>		</a></li>				<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/ui-darkness/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_ui_dark.png\" alt=\"UI Darkness\" title=\"UI Darkness\" />			<span class=\"styleName\">UI darkness</span>		</a></li>			<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/smoothness/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_smoothness.png\" alt=\"Smoothness\" title=\"Smoothness\" />			<span class=\"styleName\">Smoothness</span>		</a></li>					<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/start/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_start_menu.png\" alt=\"Start\" title=\"Start\" />			<span class=\"styleName\">Start</span>		</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/redmond/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_windoze.png\" alt=\"Redmond\" title=\"Redmond\" />			<span class=\"styleName\">Redmond</span>		</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/cupertino/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_cupertino.png\" alt=\"Cupertino\" title=\"Cupertino\" />			<span class=\"styleName\">Cupertino</span>				</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/south-street/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_south_street.png\" alt=\"South St\" title=\"South St\" />			<span class=\"styleName\">South Street</span>				</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/blitzer/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_blitzer.png\" alt=\"Blitzer\" title=\"Blitzer\" />			<span class=\"styleName\">Blitzer</span>		</a></li>			<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/humanity/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_humanity.png\" alt=\"Humanity\" title=\"Humanity\" />			<span class=\"styleName\">Humanity</span>		</a></li>			<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/hot-sneaks/jquery-ui.css\">		<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_hot_sneaks.png\" alt=\"Hot Sneaks\" title=\"Hot Sneaks\" />			<span class=\"styleName\">Hot sneaks</span>		</a></li>			<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/excite-bike/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_excite_bike.png\" alt=\"Excite Bike\" title=\"Excite Bike\" />			<span class=\"styleName\">Excite Bike</span>			</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/vader/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_black_matte.png\" alt=\"Vader\" title=\"Vader\" />			<span class=\"styleName\">Vader</span>			</a></li>				<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/dot-luv/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_dot_luv.png\" alt=\"Dot Luv\" title=\"Dot Luv\" />			<span class=\"styleName\">Dot Luv</span>			</a></li>			<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/mint-choc/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_mint_choco.png\" alt=\"Mint Choc\" title=\"Mint Choc\" />			<span class=\"styleName\">Mint Choc</span>		</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/black-tie/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_black_tie.png\" alt=\"Black Tie\" title=\"Black Tie\" />			<span class=\"styleName\">Black Tie</span>		</a></li>		<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/trontastic/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_trontastic.png\" alt=\"Trontastic\" title=\"Trontastic\" />			<span class=\"styleName\">Trontastic</span>			</a></li>			<li><a href=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/swanky-purse/jquery-ui.css\">			<img src=\"http://static.jquery.com/ui/themeroller/images/themeGallery/theme_30_swanky_purse.png\" alt=\"Swanky Purse\" title=\"Swanky Purse\" />			<span class=\"styleName\">Swanky Purse</span>			</a></li>	</ul></div></div>',
			butPreText: ' ', initialText: '$label', cookieName: '$name', onSelect: function(href){ $('#$control_name$name"."_value').val(href); }}).find('a.jquery-ui-themeswitcher-trigger').bind('filterstyle', {foo: 'bar'}, $name){
				$('.ui-styleswitcher', $('#$control_name$name')).filterable(filterVal, 'ui-styleswitcher');
			});
		});";
		$html = null;
		$select = null;
		//$doc->addScriptDeclaration($script);
		
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		nimport('napi.html.parameter');
		
		$id 	= JRequest::getInt('id');
		if (!$id) { 
			$id = reset(JRequest::getVar( 'cid', array())); 
		}
		
		//Get the module name, in a slightly hacky way.
		$module		= JRequest::getWord('module');
		$mod		=& JTable::getInstance('Module', 'JTable');
		if ($id) {
			$mod->load($id);
			$modname = $mod->module;
		} elseif($module) {
			$modname = $module;
		}

		// path to images directory
		$path		= ( $node['directory'] ? JPATH_ROOT.DS.$node['directory'] : JPATH_ROOT.DS.'templates' );
		$filter		= $node['filter'];
		$exclude	= $node['exclude'];
		$folders	= JFolder::folders($path, $filter);
		
		if($copy) 
		{
			foreach($this->_parent->_xml as $_xml) 
			{
				foreach($_xml->children() as $children) 
				{
					if(isset($children->_attributes['name']))
					{
						if($children->_attributes['name']===$copy)
						{
							//echo print_r($children->children());
							//echo print_r($key, true).' => '.print_r($val, true).' ';
							foreach($children->children() as $key => $val)
							{
								$options->name = $val->_data;
								$options->filter = JFilterOutput::stringURLSafe($val->_data);
								$options->filename = $val->_attributes['value'];
								//$html .= $options->thumb = $val->_attributes['thumbnail'];
								$html .= ' <li class="ui-styleswitcher l-'.JFilterOutput::stringURLSafe($val->_data).'"><a href="'.$stylepath.$val->_attributes['value'].'.css"><img class="ui-styleswitcher-thumb" src="'.$thumbpath.$val->_attributes['thumbnail'].'" alt=\"'.$val->_data.'\" title=\"'.$val->_data.'\" /><span class=\"styleName\">--'.JText::_('Default').'--</span></a></li>';
//								Children, for sublayouts in more complex plugins.
//								echo print_r($val->children());
								foreach ($folders as $folder)
								{
									if ($exclude)
									{
										if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $folder )) {
											continue;
										}
									}
									if (JFile::exists($path.DS.$folder.DS.'templateDetails.xml'))
									{
										$templateDetails 	= new NParameter('', $path.DS.$folder.DS.'templateDetails.xml');
										
										// Set base path
										$templateDetails->_elementPath[] = JPATH_PLUGINS.DS.'system'.DS.'napi'.DS.'elements';
										//$templateDetails 	= new NParameter('', $path.DS.'ja_purity_showcase'.DS.'templateDetails.xml');
										//die('<pre>'.var_export($templateDetails, true).'</pre>');
										$stylexml 			= $templateDetails->renderToArray('', $modname);
										if (!isset($stylexml[$name]))
											{
												/*$stylexml = new NParameter(null);
												$tylexml  = $stylexml->loadArray(array (
												  0 => 'stylepicker',
												  1 => 
												  array (
												    0 => 
												    JSimpleXMLElement::__set_state(array(
												       '_attributes' => 
												      array (
												        'value' => '3dcarousel',
												        'file' => '3dcarousel',
												      ),
												       '_name' => 'option',
												       '_data' => 'Vertical',
												       '_children' => 
												      array (
												      ),
												       '_level' => 3,
												       '_errors' => 
												      array (
												      ),
												    )),
												    1 => 
												    JSimpleXMLElement::__set_state(array(
												       '_attributes' => 
												      array (
												        'value' => 'spacegallery',
												        'file' => 'spacegallery_dark',
												      ),
												       '_name' => 'option',
												       '_data' => 'Dark',
												       '_children' => 
												      array (
												      ),
												       '_level' => 3,
												       '_errors' => 
												      array (
												      ),
												    )),
												    2 => 
												    JSimpleXMLElement::__set_state(array(
												       '_attributes' => 
												      array (
												        'value' => 'spacegallery',
												        'file' => 'spacegallery_light',
												      ),
												       '_name' => 'option',
												       '_data' => 'Light',
												       '_children' => 
												      array (
												      ),
												       '_level' => 3,
												       '_errors' => 
												      array (
												      ),
												    )),
												  ),
												  2 => NULL,
												  3 => 'stylepicker',
												  4 => NULL,
												  5 => 'stylepicker',
												));*/
											}
										//die('<pre>'.print_r($stylexml, true).'</pre>');
										if (isset($stylexml[$name]))
										{
											foreach($stylexml[$name][1] as $style)
											{
												if ($style->_attributes['value']==$val->_attributes['value'])
													{
														$css = '';
														
														$html .= ' <li class=\"ui-styleswitcher l-'.JFilterOutput::stringURLSafe($val->_data).'\"><a href=\"'.JURI::root(true).'/templates/'.$folder.'/html/'.$modname.'/'.$val->_attributes['value'].'/'.( isset($style->_attributes['file']) ? $style->_attributes['file'] : $style->_attributes['value'] ).'.css\">			<img class=\"ui-styleswitcher-thumb\" src=\"'.JURI::root(true).'/templates/'.$folder.'/html/'.$modname.'/'.$val->_attributes['value'].'/'.( isset($style->_attributes['file']) ? $style->_attributes['file'] : $style->_attributes['value'] ).'.png\" alt=\"'.$style->_data.'\" title=\"'.$style->_data.'\" />			<span class=\"styleName\">'.$style->_data.'</span>			</a></li>';
														$select .= ' l-'.JFilterOutput::stringURLSafe($val->_data);
														$select .= ' l-'.$val->_data;
													}
											}
											//die('<pre>'.$styles[0][0]->_attributes['value'].'</pre>');
										}
										else if (!isset($nostyle))
													{
														$nostyle = true;
														$css = '';
														$html .= ' &#xFEFF;';
														$select .= ' l-&#xFEFF;';

													}
										//die($path.DS.$folder.DS.'templateDetails.xml');
									}
									
								}
							}
						}
					}
				}
			}
		}
		$reset = ( ( $node['resetitem'] && $node['reseton'] ) ? '$(\''.$node['resetitem'].'\').livequery(\''.$node['reseton'].'\', function(){ $(\'#'.$control_name.$name.' .jquery-ui-styleswitcher-title\').text(\''.$label.'\'); });' : '' );
		$script = "
		jQuery.noConflict();
 		
		jQuery(document).ready(function($){
			$('#$control_name$name').styleswitcher({loadTheme: '$value', stylepane: '<div class=\"jquery-ui-styleswitcher\"><div id=\"themeGallery\"><ul class=\"ui-styleswitcher-wrapper\">$html</ul></div></div>',
			butPre: '<a href=\"#\" class=\"jquery-ui-styleswitcher-trigger ui-state-default ui-corner-all\"><span class=\"jquery-ui-styleswitcher-icon ui-icon ui-icon-triangle-1-s\"></span><span class=\"jquery-ui-styleswitcher-title\">',
			initialText: '$label', onSelect: function(href){ $('#$control_name$name"."_value').val(href); }}).find('a.jquery-ui-themeswitcher-trigger');
			$reset
		});";
		$doc->addScriptDeclaration($script);
		$selectors = null;
		$nselectors = null;
		$lockdesc = JText::_($node['description2']);
		if ($select)
		{
			$selectors = ' class="'.$select.' fl f-select ui-helper-hidden"';
			$nselectors = '<span class="'.$select.' fl hasTip" title="'.$lockdesc.'"><a class="ui-state-default jquery-ui-styleswitcher-trigger '.$select.' fl ui-state-default" style="font-family: \'Trebuchet MS\', Verdana, sans-serif; font-size: 11px; border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; -webkit-border-top-right-radius: 6px 6px; -webkit-border-top-left-radius: 6px 6px; -webkit-border-bottom-left-radius: 6px 6px; -webkit-border-bottom-right-radius: 6px 6px; text-decoration: none; padding-top: 3px; padding-right: 3px; padding-bottom: 3px; padding-left: 8px; width: 149px; display: block; height: 14px; outline-width: 0px; outline-style: initial; outline-color: initial; -webkit-background-clip: initial; -webkit-background-origin: initial;  cursor: normal;background-position: initial initial; "><span class="ui-icon ui-icon-cancel" style="float: right; width: 16px;margin-top:-1px;"></span><span class="jquery-ui-styleswitcher-title">Using Default</span></a></span>';
		}
		return '<span id="'.$control_name.$name.'"'.$selectors.'></span><input type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'_value" value="'.$value.'" />'.$nselectors;
	}

	/*function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {
		return false;
	}*/
}
