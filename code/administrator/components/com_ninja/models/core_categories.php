<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: core_categories.php 898 2011-02-26 18:19:25Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Core Joomla! Categories Model Class
 * returns list of sections / Category
 *
 * @author		Richie Mortimer <richie@ninjaforge.com>
 */
class ComNinjaModelCore_categories extends ComDefaultModelDefault
{	

	/**
     * Builds LEFT JOINS clauses for the query
     */
	protected function _buildQueryJoins(KDatabaseQuery $query)
	{
		$query->join('LEFT', 'sections AS s', 's.id = tbl.section');
	}
	
	/**
     * Builds SELECT fields list for the query
     */
	protected function _buildQueryColumns(KDatabaseQuery $query)
	{
		$query->select('tbl.id');
		$query->columns[] = 'CONCAT_WS( " / ",s.title, tbl.title ) AS title';  
		
		parent::_buildQueryColumns($query);
	}
	
	/**
     * Builds a WHERE clause for the query
     */
	protected function _buildQueryWhere(KDatabaseQuery $query)
	{
		$query->where('tbl.published', '=',  '1', 'AND')
		      ->where('s.scope', '=', 'content');	
	}
	
	/**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
    	$query->order('s.title', 'ASC')
    		  ->order('tbl.title', 'ASC');
    }
}