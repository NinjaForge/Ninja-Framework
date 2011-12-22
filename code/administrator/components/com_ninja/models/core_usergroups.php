<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Core Joomla! Usergroups Model Class
 * returns a list of available usergroups, supporting both j! 1.5 and 1.7+
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class NinjaModelCore_usergroups extends NinjaModelAbstract
{
	/**
     * Builds LEFT JOINS clauses for the query
     */
	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		$query->join('LEFT', $this->getTable()->getBase().' AS joined', 'tbl.lft > joined.lft AND tbl.rgt < joined.rgt');
	}
	
	/**
	 * Builds GROUP clause for the query
	 */
	protected function _buildQueryGroup(KDatabaseQuery $query)
	{
		$query->group('tbl.id');
		
		parent::_buildQueryGroup($query);
	}
	
	/**
     * Builds SELECT fields list for the query
     */
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		$query->select(array('tbl.*', 'COUNT(DISTINCT joined.id) AS level'));

		parent::_buildQueryColumns($query);
	}
	
	/**
     * Builds a WHERE clause for the query
     */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
	    if($this->getTable()->getBase() == 'core_acl_aro_groups') {
	        $query->where('tbl.value', 'not in', array('ROOT', 'USERS'));
	    }

        parent::_buildQueryColumns($query);
	}
	
	/**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
    	$query->order('tbl.lft', 'ASC');
    }
}