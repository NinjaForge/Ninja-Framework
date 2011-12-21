<?php
/**
* @version      $Id: spacer.php 2876 2011-03-07 22:19:20Z johanjanssens $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Spacer
 * 
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Toolbar
 * @subpackage  Button
 */
class KToolbarButtonSpacer extends KToolbarButtonAbstract
{
    public function render()
    {
        return '</tr></table><table class="toolbar"><td class="spacer"></td></table<table class="toolbar"><tr>';
    }

}