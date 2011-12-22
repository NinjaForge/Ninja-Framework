<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaModelTable extends ComDefaultModelDefault
{
	
	/**
	 * Model pathway data
	 *
	 * @var array
	 */
	protected $_pathway;
	
	/**
	 * Model tree list data
	 *
	 * @var array
	 */
	protected $_treelist;
	
	/**
	 * Since we can't call protected methods from mixins (as they result in a BadMethodCallException error)
	 * We have to have an method for building the query.
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  $query	KDatabaseQuery
	 * @return KDatabaseQuery
	 */
	public function buildQuery(KDatabaseQuery $query)
	{
		$this->_buildQueryColumns($query);
		$this->_buildQueryFrom($query);
		$this->_buildQueryJoins($query);
		$this->_buildQueryWhere($query);
		$this->_buildQueryGroup($query);
		$this->_buildQueryHaving($query);
		$this->_buildQueryOrder($query);
		$this->_buildQueryLimit($query);
		
		return $query;
	}
	
	
	protected function _buildQueryTreeJoins(KDatabaseQuery $query)
	{
		$table			= $this->getService($this->getTable());
		$table_name		= $table->getName();
		$primary_key	= $table->getIdentityColumn();
		
		
	}
	
	protected function _buildQueryGroup(KDatabaseQuery $query)
	{
		$table			= $this->getService($this->getTable());
		$table_name		= $table->getName();
		$primary_key	= $table->getPrimaryKey();
		
	}
	
	protected function _buildQueryOrder(KDatabaseQuery $query)
    {
    	//We need to write a proper sort function first.
		//$query->order('tbl.lft', 'ASC');
		$query->order('path_ordering', 'ASC');
		
		$order      = $this->_state->order;
       	$direction  = strtoupper($this->_state->direction);

    	if($order) {
    		$query->order($order, $direction);
    	}
    }
    
    public function getTreeList()
	{
		if(!$this->_list) $this->getList();
		
		if(!$this->_treelist)
		{
			$this->_treelist = array();
			foreach($this->_list as $i => $item)
			{
				$this->_treelist[$item->parent_id][$i] = $item;
			}
		}
	
		if(!isset($this->_treelist[(int)$this->_state->parent])) return array();
		return $this->_treelist[$this->_state->parent];
	}
	
	public function getTreeLevel()
	{
		$lft = $this->_state->lft;
		$rgt = $this->_state->rgt;
		$key = $lft.'.'.$rgt;
		
		// Get the data if it doesn't already exist
	    if (!isset($this->_treelevel)||!array_key_exists($key, $this->_treelevel))
	    {
	    	$table = $this->getService($this->getTable());
	    	$query = $this->_db->getQuery()->where('tbl.lft', '<', $lft, 'and')->where('tbl.rgt', '>', $rgt, 'and');
	    	$this->_treelevel[$key] = $table->count($query);
	    }
		if(!array_key_exists($key, $this->_treelevel)) return 0;
	    return $this->_treelevel[$key];
	}
}