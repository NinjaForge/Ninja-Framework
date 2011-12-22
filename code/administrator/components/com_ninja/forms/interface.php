<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Koowa
 * @package		Koowa_Form
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Form Interface
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Form
 */
interface NinjaFormInterface 
{
	/**
	 * Render the form as html
	 *
	 * @return 	string	Html
	 */
	public function renderHtml();
	
	/**
	 * Render the form as DOM
	 *
	 * @return 	DOMDocument
	 */
	public function renderDom();
}