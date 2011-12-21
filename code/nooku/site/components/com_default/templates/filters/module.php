<?php
/**
 * @version     $Id: module.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Module Template Filter
 * 
 * This filter allow to dynamically inject data into module position.
 * 
 * Filter will parse elements of the form <modules position="[position]">[content]</modules> 
 * and prepend or append the content to the module position. 
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterModule extends KTemplateFilterAbstract implements KTemplateFilterWrite
{  
    /**
	 * Find any <module></module> elements and inject them into the JDocument object
	 *
	 * @param string Block of text to parse
	 * @return ComDefaultTemplateFilterModule
	 */
    public function write(&$text)
    {   
		$matches = array();
		
		if(preg_match_all('#<module([^>]*)>(.*)</module>#siU', $text, $matches)) 
		{	
		    foreach($matches[0] as $key => $match)
			{
			    //Remove placeholder
			    $text = str_replace($match, '', $text);
			    
			    //Create attributes array
				$attributes = array(
					'style' 	=> 'component',
					'params'	=> '',	
					'title'		=> '',
					'class'		=> '',
					'prepend'   => true
				);
				
		        $attributes = array_merge($attributes, $this->_parseAttributes($matches[1][$key])); 
				
		        //Create module object
			    $module   	       = new KObject();
			    $module->id        = uniqid();
				$module->content   = $matches[2][$key];
				$module->position  = $attributes['position'];
				$module->params    = $attributes['params'];
				$module->showtitle = !empty($attributes['title']);
				$module->title     = $attributes['title'];
				$module->attribs   = $attributes;
				$module->user      = 0;
				$module->module    = 'mod_dynamic';
				
			    JFactory::getDocument()->modules[$attributes['position']][] = $module;
			}
		}
		
		return $this;
    }    
}

/**
 * Modules Renderer
 * 
 * This is a specialised modules renderer which prepends or appends the dynamically created modules 
 * to the list of modules before rendering them.
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class JDocumentRendererModules extends JDocumentRenderer
{
	public function render( $position, $params = array(), $content = null )
	{
        //Get the modules
		$modules = JModuleHelper::getModules($position);
		
		if(isset($this->_doc->modules[$position])) 
		{
		    foreach($this->_doc->modules[$position] as $module) 
		    { 
		        if($module->attribs['prepend']) {
		            array_push($modules, $module);   
		        } else {
		            array_unshift($modules, $module);
		        }
		    }
		}
		
		//Render the modules
		$renderer = $this->_doc->loadRenderer('module');
		
		$contents = '';
		foreach ($modules as $module)  {
			$contents .= $renderer->render($module, $params, $content);
		}
		
		return $contents;
	}
}