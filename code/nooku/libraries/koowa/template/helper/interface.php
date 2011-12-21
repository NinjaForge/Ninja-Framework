<?php
/**
* @version      $Id: interface.php 1372 2011-10-11 18:56:47Z stian $
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template helper interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Template
 * @subpackage	Helper
 */
interface KTemplateHelperInterface
{
 	/**
     * Get the template object
     *
     * @return  object	The template object
     */
    public function getTemplate();
}