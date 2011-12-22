<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninja Settings Controller
 *
 * @package Ninja
 */
class NinjaControllerSetting extends NinjaControllerDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->registerCallback('before.browse', array($this, 'setDefault'));
		$this->registerCallback('before.save', array($this, 'resetDefaults'));
		$this->registerCallback('before.apply', array($this, 'resetDefaults'));
		$this->registerCallback('before.edit', array($this, 'resetDefaults'));

		$this->registerCallback('after.save', array($this, 'overrideRedirect'));
		$this->registerCallback('after.cancel', array($this, 'overrideRedirect'));
	}
	
	/**
	 * Overrides redirect as there's an issue with storing the redirect in session.com.dispatcher.referrer
	 * in KControllerView as it causes side effects when there are ajax requests to other singular forms within the main one 
	 * 
	 * This is still needed as ajax request and referrers on singular views are still an issue - Richie
	 *
	 * @TODO report this in the nooku mailing list
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 */
	public function overrideRedirect()
	{
		$identifier = $this->getIdentifier();
		
		$this->_redirect = 'index.php?option='.$identifier->type.'_'.$identifier->package.'&view='.KInflector::pluralize($identifier->name);
	}

	/**
	 * Resets default status on other rows if the current one is set to default
	 */
	public function resetDefaults(KCommandContext $context)
	{
		if(isset($context->data->default) && $context->data->default == 1){
			$model  = $this->getModel();
			$table  = $model->getTable();
			
			//Undefault any other default setting
			$table->select(array('default' => 1), KDatabase::FETCH_ROWSET)->setData(array('default' => 0))->save();
		}
	}
	
	/**
	 * Makes sure that at least one settings profile is set as default
	 *
	 * @param  KCommandContext $context
	 */
	public function setDefault(KCommandContext $context)
	{
		$table = $this->getModel()->getTable();
		
		//Don't do anything if there are no settings rows
		if($table->count(array()) === 0) return;
		
		//Don't do anything if there already exists rows that are enabled and default
		if($table->count(array('enabled' => true, 'default' => true)) > 0) return;
		
		//Don't do anything if there are no enabled rows
		if($table->count(array('enabled' => true)) === 0) return;

		//Undefault any other default setting
		$table->select(array('default' => true), KDatabase::FETCH_ROWSET)->setData(array('default' => false))->save();
		
		//Set one row as the default
		$table->select(array('enabled' => true), KDatabase::FETCH_ROW)->setData(array('default' => true))->save();
	}

	/**
	 * Generic method to modify the default status of items
	 *
	 * @return void
	 */
	protected function _actionDefault(KCommandContext $context)
	{
		$model  = $this->getModel();
		$table  = $model->getTable();
		$data	= $context->data;
		
		$context->data->default = 1;
		
		//Prevent more than one item to be set as default
		$context->data->id = array($context->data->id[0]);
		$this->_request->id = $context->data->id;
			
		$this->_redirect_message = JText::_('Default setting changed.');
		
		return $this->execute('edit', $context);
	}
}