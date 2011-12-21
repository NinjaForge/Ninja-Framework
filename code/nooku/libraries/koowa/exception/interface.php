<?php
/**
 * @version		$Id: interface.php 2725 2010-10-28 01:54:08Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Exception
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Exception Interface
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Exception
 */
interface KExceptionInterface
{
	/**
	 * Return the exception message
	 *
	 * @return string
	 */
    public function getMessage();

    /**
	 * Return the user defined exception code
	 *
	 * @return integer
	 */
    public function getCode();

    /**
	 * Return the source filename
	 *
	 * @return string
	 */
    public function getFile();

    /**
	 * Return the source line number
	 *
	 * @return integer
	 */
    public function getLine();

    /**
	 * Return the backtrace information
	 *
	 * @return array
	 */
    public function getTrace();

    /**
	 * Return the backtrace as a string
	 *
	 * @return string
	 */
    public function getTraceAsString();

    /**
	 * Format the exception for display
	 *
	 * @return string
	 */
    public function __toString();

    /**
	 * Constructor
	 *
	 * @param string  The exception message
	 * @param integer The exception code
	 */
    public function __construct($message = null, $code = 0);
}