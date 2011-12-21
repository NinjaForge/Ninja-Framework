<?php
/**
 * @version		$Id: interface.php 3011 2011-03-27 01:52:06Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Command
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Command Interface 
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Command
 */
interface KCommandInterface extends KObjectHandlable
{
	/**
	 * Generic Command handler
	 * 
	 * @param 	string 	The command name
	 * @param 	object  The command context
	 * @return	boolean
	 */
	public function execute( $name, KCommandContext $context);
	
	/**
	 * Get the priority of the command
	 *
	 * @return	integer The command priority
	 */
  	public function getPriority();
}
