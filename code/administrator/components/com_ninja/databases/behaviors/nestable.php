<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */ 

/**
 * Nestable behavior for hierarchy type of data
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninja
 * @subpackage 	Behaviors
 */
class NinjaDatabaseBehaviorNestable extends KDatabaseBehaviorAbstract
{
	/**
	 * Get the methods that are available for mixin based
	 * 
	 * This functions allows for conditional mixing of the behavior. Only if 
	 * the mixer has a 'path' property the behavior will allow to be 
	 * mixed in.
	 * 
	 * @param object The mixer requesting the mixable methods. 
	 * @return array An array of methods
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
		$methods = array();
		
		if(isset($mixer->path)) {
			$methods = parent::getMixableMethods($mixer);
		}
	
		return $methods;
	}

	public function getParents($states = array())
	{
		$ids = array_filter(explode('/', $this->path));		
		$identifier = clone $this->getTable()->getIdentifier();
		$identifier->path = (array) 'model';
		$model = $this->getService($identifier)->limit(0);
		
		$model->id($ids);
		foreach($states as $state => $value)
		{
			$model->$state($value);
		}
		
		return $ids ? $model->getList() : array();
	}
	
	/**
	 * Get children
	 *
	 * @TODO					Currently it's required to extend NinjaModelTable
	 *							for this function to work, or that you have an
	 *							equalent buildQuery function in your model to
	 *							NinjaModelTable::buildQuery().
	 *
	 * @param  $states	array	Pass states to the model if needed.
	 */
	public function getChildren($states = array())
	{
		$table = $this->getService($this->getTable());
		$query = $table->getDatabase()->getQuery()->where('path', 'LIKE', '%/'.$this->id.'/');
		
		$identifier = clone $this->getTable();
		$identifier->path = (array) 'model';
		$model = $this->getService($identifier);
		
		foreach($states as $state => $value)
		{
			$model->$state($value);
		}
		
		$model->buildQuery($query);
		
		return $table->select($query);
	}
	
	/**
	 * Delete children rows
	 *
	 * @TODO					We previously had a query like this: '%/'.$context['data']->id.'/%'
	 *							That fetched all children with a single query.
	 *							However the problem is that the _afterTableDelete function we have here,
	 *							Triggers on each of these rows, causing all sorts of crazy errors due to
	 *							delete() being called on rows that are already removed and such.
	 *							So until we figure out a smart way of doing this, we need to recurse down the tree on
	 *							purpose to avoid errors.
	 *
	 * @param  $context KCommandContext
	 */
	protected function _afterTableDelete(KCommandContext $context)
	{
		$query	= $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('path', 'LIKE', '%/'.$context['data']->id.'/');
		$context['caller']->select($query)->delete();
	}
	
	/**
	 * Update children paths if the path changed
	 * 
	 * @return void
	 */
	protected function _afterTableUpdate(KCommandContext $context)
	{
		$row = $context['data'];
		if(!array_key_exists('path', $row->getModified())) return;
		
		$id    = $row->id;
		$table = $context['caller'];
		
		$query = $table->getDatabase()->getQuery()->where('path', 'LIKE', '%/'.$id.'/%');
		
		$path  = $row->path.$id;
		foreach($table->select($query) as $child)
		{
			$parts = explode($id, $child->path);
			
			$part = isset($parts[1]) ? $parts[1] : null;
			$child->path = $path.$part;
			$child->save();
		}
	}
}