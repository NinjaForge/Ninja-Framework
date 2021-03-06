<?php
/**
* @version      $Id: read.php 4477 2012-02-10 01:06:38Z johanjanssens $
* @category		Koowa
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template Write Filter Interface
 *
 * Processes the template on input
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Template
 * @subpackage  Filter 
 */
interface KTemplateFilterRead
{
    /**
     * Parse the text and filter it
     *
     * @param string Block of text to parse
     * @return KTemplateFilterRead
     */
    public function read(&$text);
}