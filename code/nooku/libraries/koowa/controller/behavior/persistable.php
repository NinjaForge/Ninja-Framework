<?php
/**
 * @version		$Id: persistable.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Koowa
 * @package		Koowa_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Persistable Controller Behavior Class
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package     Koowa_Controller
 * @subpackage	Behavior
 */
class KControllerBehaviorPersistable extends KControllerBehaviorAbstract
{ 
	/**
	 * Load the model state from the request
	 *
	 * This functions merges the request information with any model state information
	 * that was saved in the session and returns the result.
	 *
	 * @param 	KCommandContext		The active command context
	 * @return 	void
	 */
	protected function _beforeBrowse(KCommandContext $context)
	{
		// Built the session identifier based on the action
		$identifier  = $this->getModel()->getIdentifier().'.'.$context->action;
		$state       = KRequest::get('session.'.$identifier, 'raw', array());
			
		//Append the data to the request object
		$this->getRequest()->append($state);
		
		//Push the request in the model
		$this->getModel()->set($this->getRequest());
	}
	
	/**
	 * Saves the model state in the session
	 *
	 * @param 	KCommandContext		The active command context
	 * @return 	void
	 */
	protected function _afterBrowse(KCommandContext $context)
	{
		$model  = $this->getModel();
		$state  = $model->get();

		// Built the session identifier based on the action
		$identifier  = $model->getIdentifier().'.'.$context->action;
		
		//Prevent unused state information from being persisted
		KRequest::set('session.'.$identifier, null);
		
		//Set the state in the session
		KRequest::set('session.'.$identifier, $state);
	}
}