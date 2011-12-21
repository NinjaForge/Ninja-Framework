<?php
/**
* @version      $Id: link.php 2785 2011-01-08 19:59:35Z johanjanssens $
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template filter to parse link tags
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Filter
 */
class KTemplateFilterLink extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
	/**
	 * Find any <link /> elements and render them
	 *
	 * @param string Block of text to parse
	 * @return KTemplateFilterLink
	 */
	public function write(&$text)
	{
		//Parse the script information
		$scripts = $this->_parseLinks($text);
		
		//Prepend the script information
		$text = $scripts.$text; 
		
		return $this;
	}
	
	/**
	 * Parse the text for script tags
	 * 
	 * @param string Block of text to parse
	 * @return string
	 */
	protected function _parseLinks(&$text)
	{
		$scripts = '';
		
		$matches = array();
		if(preg_match_all('#<link\ href="([^"]+)"(.*)\/>#iU', $text, $matches))
		{
			foreach(array_unique($matches[1]) as $key => $match) 
			{
				$attribs = $this->_parseAttributes( $matches[2][$key]);
				$scripts .= $this->_renderScript($match, $attribs);
			}
			
			$text = str_replace($matches[0], '', $text);
		}
			
		return $scripts;
	}
	
	/**
	 * Render script information
	 * 
	 * @param string	The script information
	 * @param array		Associative array of attributes
	 * @return string 	
	 */
	protected function _renderLink($link, $attribs = array())
	{
		$attribs = KHelperArray::toString($attribs);
		
		$html = '<link href="'.$link.'" '.$attribs.'/>'."\n";
		return $html;
	}
}