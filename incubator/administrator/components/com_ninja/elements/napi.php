<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

// import library dependencies
jimport('joomla.application.component.helper');
jimport('joomla.application.component.controller');

// verify the NAPI libraries are available and the correct version
if (!function_exists('nimport')) {
	JError::raiseWarning(500, JText::_('NAPI_MISSING'));
} else {
	if (version_compare('1.0.0', NAPI)) {
		JError::raiseWarning(500, JText::sprintf('NAPI_OUTDATED', '1.0.0'));
	}
}
/**
 * Checks wether or not the framework plugin is loaded
 *
 * @package 	Napi
 * @subpackage	Napi_Parameter
 */

class ComNinjaElementNapi extends ComNinjaElementAbstract
{	
	/**
	* Collection of panel definitions
	*
	* @access	protected
	* @var		array
	*/
	var	$_panels = array();
	
	/**
	* Rendered parameter groups
	*
	* @access	protected
	* @var		array
	*/
	var	$_groups = array();

	function fetchElement($name, $value, &$node, $control_name, $html = null)
	{	
	
	//Prepare data for other following elements
	$this->setExt($name, $value, $node, $control_name);
	
		foreach($node->children() as $e)
		{
			switch($e->name())
			{
				case 'panel':
						$check = true;
						if($e->children())
						{
							foreach($e->children() as $tab)
							{
								if($tab['if'])
								{
									$check = $this->checkIf($tab);
									//die($this->checkIf($tab));
								} 
								if($check) 
								{
									$html .= $tab->data()." | ";
								}
							}
						}
					break;
				default:
						$html .= $e->name();
					break;
			}
		}
		$doc = & JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(true).'/media/napi/css/jquery-ui-extended.css');
		$doc->addStyleSheet(JURI::root(true).'/media/napi/css/themes/napi/jquery-ui.css');
		$class = ( $node['class'] ? 'class="'.$node['class'].'"' : 'class="text_area"' );
		
		if(empty($this->_parent->_napi)) {
			$this->header();
			return $this->html($html, $node);
		}
	}


	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {
		return false;
	}
	
	function checkIf($node)
	{
		$check = explode('.', $node['if']);
		switch($check['0'])
		{
			case 'request':
					switch($check['1'])
					{
						case 'int':
								if($check['3'])
								{
									return (JRequest::getInt($check['2'], false)==$check['3']);
								} else {
									return JRequest::getInt($check['2'], false);
								}
							break;
						case 'bool':
								return JRequest::getBool($check['2'], false);
							break;
						case 'cmd':
								return (JRequest::getCmd($check['2'], false)===$check['3']);
							break;
						case 'com':
								return (JRequest::getCmd('option', false)===$check['1'].'_'.$check['2']);
							break;
						default:
								return false;
					}
				break;
			default:
				return false;
		}
	}
	
	function html( $html, $node, $start = null, $end = null, $panes = array() )
	{
		global $option;
		$doc = & JFactory::getDocument();
		//$doc->addScript(JURI::root(true).'/media/napi/js/accordion.fix.js');
		//$doc->addScript(JURI::root(true).'/media/napi/js/tabs.fix.js');
		switch(get_class($this->_parent))
		{
			default:
			case 'JParameter':
					$start = '</td></tr></table></div></div></div>';
					$parent = $this->_parent;
					$xml 	= $parent->_xml;
					
					$params = new NParameter($parent);
					
					nimport('napi.html.pane');
					$pane = NPane::getInstance('sliders');
					//$start .= $pane->startPane($node['id']?$node['id']:( $node['name'] ? $node['name'] : $node['type'] ).'-pane');
					foreach($params->_xml as $group => $object)
					{
						if( ($object->attributes($option)!='hide') && ($object['if']?$this->checkIf($object):true) && $group!='_default' ) 
						{
							//Create default title
							$title = JText::sprintf($object['title']?$object['title']:'%s Parameters', ucwords($object['group']));
							//Create tabtitle
							$tabtitle = JText::sprintf($object['tabtitle']?$object['tabtitle']:'%s', ucwords($object['group']));
							
							//Check a new sliders instance is started	
							if($object['sliders']!='end'&&$object['sliders']) 
							{
								$sliders = NPane::getInstance('sliders');
								$html .= $sliders->startPane($group.'-pane');
							}
							
							//Check a new slider instance is started	
							if((($object['slider']!=='false'&&$object['slider']!='end')||($object['slider']=='start'&&$object['slider']))&&$sliders) 
							{
								$html .= $sliders->startPanel($title, $object['id']?$object['id']:$group.'-page');
							}
							
							//Check a new tabs instance is started	
							if($object['tabs']!='end'&&$object['tabs']) 
							{
								$tabs = NPane::getInstance('tabs', array('useCookie' => '\''.$node['name'].'\''));
								$html .= $tabs->startPane($group.'-pane');
							}
							
							//Check a new tab instance is started	
							if((($object['tab']!=='false'&&$object['tab']!='end')||($object['tab']=='start'&&$object['tab']))&&isset($tabs)) 
							{
								$html .= $tabs->startPanel($tabtitle, $object['id']?$object['id']:$group.'-page');
							}
						
//							if($object['panel'])
//							{
//								$html .= $pane->endPane();
//								$id = !$object['id']?$group.'-pane':$object['id'];
//								$html .= $pane->startPane($id);
//							}
							//$html .= $pane->startPanel(JText::sprintf($object['title']?$object['title']:'%s Parameters', ucwords($object['group'])), $object['id']?$object['id']:$group.'-page');
							$html .= $object['render']=='inline' ? $params->renderInline('params', $group) : $params->renderFieldset('params', $group);
							//$html .= $pane->endPanel();
							
							//Check a new tab instance is ended	
							if((($object['tab']!=='false'&&$object['tab']!='start')||($object['tab']=='end'&&$object['tab']))&&isset($tabs)) 
							{
								$html .= $tabs->endPanel();
							}
							
							//Check if tabs instance is ended
							if($object['tabs']!='start'&&$object['tabs']&&isset($tabs))
							{
								$html .= $tabs->endPane();
								unset($tabs);
							}
							
							//Check a new slider instance is ended	
							if((($object['slider']!=='false'&&$object['slider']!='start')||($object['slider']=='end'&&$object['slider']))&&$sliders) 
							{
								$html .= $sliders->endPanel();
							}
							
							//Check if sliders instance is ended
							if($object['sliders']!='start'&&$object['sliders']&&$sliders)
							{
								$html .= $sliders->endPane();
								unset($sliders);
							}
						}
					}
//					gettype($html)!='array' ? $html = array() : '';
//					foreach($this->_panels as $group => $panel)
//					{
//						if($group=='sliders'||$group=='tabs')
//						{
//							$html[] = $$group->startPane($group.'-pane');
//							foreach($panel as $innergroup => $innerpanel)
//							{
//								$html[] = $$group->startPanel(JText::_('Parameters '.$innergroup), $innergroup);
								//$html[] = $pane->startPane($test.'-pane');
//								if($innergroup=='sliders'||$innergroup=='tabs')
//								{ 
//									$html[] = $$innergroup->startPane($innergroup.'-panse');
//									foreach($innerpanel as $childgroup => $childpanel)
//									{
//										$html[] = $$innergroup->startPanel($childgroup, $childgroup.'ss');
										//$html[] = $pane->startPane($test.'-pane');
//										$html[] = $this->_groups[$childgroup];
										//$me = 'sliders';
										//die('<pre>'.print_r($$me->startPanel('me'), true).'</pre>');
//										$html[] = $$innergroup->endPanel();
//									}
//									$html[] = $$innergroup->endPane();
//								} else {
//								
//									$html[] = $this->_groups[$innergroup];
//								}
//								$html[] = $$group->endPanel();
//							}
							//$me = 'sliders';
							//die('<pre>'.print_r($$me->startPanel('me'), true).'</pre>');
//							$html[] = $$group->endPane();
//						}
//					}
					//die('<pre>'.print_r($this->_panels, true).'</pre>');
					//$end .= $pane->endPane();
					$params->_napi = true;
					$this->_parent->_napi = true;
					//$start .= print_r($params);
					//$end .= '<pre>'.print_r($this->_parent, true).'</pre>';
					JHTML::_('behavior.mootools');
					$doc->addStyleDeclaration('#menu-pane, #deletediv { display:none; }');
					$doc->addScriptDeclaration('window.addEvent(\'domready\', function(){ $(\'deletediv\').remove(); $(\'menu-pane\').remove(); });');
					$end   = '<div id="deletediv"><div><div><table><tr><td class="paramlist_value">';
				break;
			case 'NParameter':
				break;
		}
		return $start.$html.$end;
	}
	function header()
	{
		if (JRequest::getCmd('format', 'html')=='style') {
			$doc =& JFactory::getDocument();
			
		}
	}
	
	function _switch($name='napi')
	{
		
	}
	
	function tab()
	{
	
	}
	
	function slider()
	{
	
	}
	
	//Set the extension information
	function setExt($name, $value, $node, $control_name)
	{
		jimport('joomla.application.helper');
		if(!isset($this->_parent->_path)||!is_link($this->_parent->_path))
		{
			$explode = explode('::', $node['path']);
			$this->_parent->_path = JApplicationHelper::getPath(current($explode), next($explode)); 
		}
	}
}
