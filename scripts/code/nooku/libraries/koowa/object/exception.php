<?php
/**
 * @version     $Id:exception.php 368 2008-08-25 12:28:02Z mathias $
 * @category    Koowa
 * @package     Koowa_Object
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Koowa Date Exception class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Object
 */
class KObjectException extends KException 
{
    /**
     * Constructor
     *
     * @param string  The exception message
     * @param integer The exception code
     * @param object  The previous exception
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        
        //Get the call stack
        $traces = $this->getTrace();

        //Traverse up the trace stack to find the actuall function that was not found
        if($traces[0]['function'] == '__call') 
        {
            foreach($traces as $trace)
            {
                if($trace['function'] != '__call')
                {
                    $this->message = "Call to undefined method : ".$trace['class'].$trace['type'].$trace['function'];
                    $this->file    = $trace['file'];
                    $this->line    = $trace['line'];
                    break;
                }
            }
        }
    }
}

    