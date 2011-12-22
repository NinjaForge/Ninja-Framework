<?php
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2010 NinjaForge. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaElementFilter extends ComNinjaElementAbstract
{	
	public function fetchTooltip($label, $description, &$node, $control_name, $name) {

		return false;
	}

	public function fetchElement($name, $value, &$node, $control_name)
	{	
		$doc = & JFactory::getDocument();
		$selector = (  $node['selector'] ? $node['selector'] : null  );
		$init = (  $node['initial'] ? $node['initial'] : null  );
		
		// Setup options object
		$params['items'] = (  $node['items'] ? $node['items'] : null  );
		$params['prefix'] = (  $node['prefix'] ? $node['prefix'] : null  );
		$params['trigger'] = (  $node['trigger'] ? $node['trigger'] : null  );
		$params['container'] = (  $node['container'] ? $node['container'] : null  );
		$params['selector'] = (  $node['selector'] ? $node['selector'] : null  );
		$params['inverter'] = (  $node['inverterclass'] ? $node['inverterclass'] : null  );
		$params['onfilter'] =(  $node['onfilter'] ? $node['onfilter'] : null  );
		$params['beforefilter'] =(  $node['beforefilter'] ? $node['beforefilter'] : null  );
		
		return JHTML::_('behavior.filter', $params);
	}
}