<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Core Joomla! Editors Model Class
 * returns list of Editors
 *
 * @author		Richie Mortimer <richie@ninjaforge.com>
 */
class NinjaModelCore_Editors extends ComDefaultModelDefault
{	
	/**
     * Builds SELECT fields list for the query
     */
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		$query->select('tbl.element AS value')
		 	  ->select('tbl.name AS title');

		 parent::_buildQueryColumns($query);
	}
	
	/**
     * Builds a WHERE clause for the query
     */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		$query->where('tbl.folder', '=',  'editors', 'AND')
		      ->where('tbl.published', '=', '1');	
	}
	
	/**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
    	$query->order('tbl.ordering', 'ASC')
    		  ->order('title', 'ASC');
    }
}