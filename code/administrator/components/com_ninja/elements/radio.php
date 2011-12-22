<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementRadio extends NinjaElementAbstract
{
	function fetchElement($name, $value, &$node, $control_name)
	{	
		$config = new KConfig;
		$config->append(array(
			'name'   	=> 'id',
			'attribs'	=> array(),
			'key'		=> 'id',
			'text'		=> 'title',
			'selected'	=> $value,
			'translate'	=> false
		));
		
		$name    = $config->name;
		$attribs = KHelperArray::toString($config->attribs);

		$options = array();
		foreach ($node->children() as $option)
		{
			$options[] = (object) array(
				$config->key	=> $option['value'],
				$config->text	=> (string)$option
			);
		}
		
		$config->list = $options;

		$class = isset($node['class']) ? $node['class'] : 'value';
		$html = array('<ul id="'.$this->name.'_id" class="'.$class.'">');
		foreach($config->list as $row)
		{
			$key  = $row->{$config->key};
			$text = $config->translate ? JText::_( $row->{$config->text} ) : $row->{$config->text};
			$id	  = isset($row->id) ? $row->id : null;

			$extra = '';
			
			if ($config->selected instanceof KConfig)
			{
				foreach ($config->selected as $value)
				{
					$sel = is_object( $value ) ? $value->{$config->key} : $value;
					if ($key == $sel)
					{
						$extra .= 'checked="checked"';
						break;
					}
				}
			} 
			else $extra .= ($key == $config->selected ? 'checked="checked"' : '');
			
			$html[] = '<li class="value">';
			$html[] = '<label for="'.$this->name.'_'.$key.'"><input type="radio" name="'.$this->name.'" id="'.$this->name.'_'.$key.'" value="'.$key.'" '.$extra.' '.$attribs.' />'.JText::_($text).'</label>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';

		return implode(PHP_EOL, $html);
	}
}