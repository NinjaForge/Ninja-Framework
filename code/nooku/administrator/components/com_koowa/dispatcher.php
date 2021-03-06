<?php
/**
 * @version     $Id: dispatcher.php 1481 2012-02-10 01:46:24Z johanjanssens $
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Koowa
 * @copyright   Copyright (C) 2010 - 2012 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Dispatcher for Koowa component
 *
 * @author      Stian Didriksen <stian@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Koowa
 */
class ComKoowaDispatcher extends ComDefaultDispatcher
{
 	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'controller' => 'dashboard'
        ));

        parent::_initialize($config);
    }
}
