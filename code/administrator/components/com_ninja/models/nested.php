<?php
/**
 * @version     $Id: categories.php 40 2011-11-23 13:31:46Z richie $
 * @category    Ninja
 * @package     Model
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     http://www.gnu.org/licenses/gpl.html
 * @link        http://ninjaforge.com
 */
defined('_JEXEC') or die('Restricted access');
 
 /**
 * Nested Model class
 *
 * @author      Richie Mortimer <richie@richiemortimer.com>
 * @category    Ninja
 * @package     Model
 */
class NinjaModelNested extends ComDefaultModelDefault
{

    /**
     * Select all columns of table and also two calculated
     * columns called "level" and "parent_id" which aren't
     * saved in database
     *
     * @param KDatabaseQuery $query  Query object for table access
     */
    protected function _buildQueryColumns(KDatabaseQuery $query)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $name = $db->getTablePrefix() . $table->getName();
        $idCol = $table->getIdentityColumn();

        $query->select('tbl.*');
        $query->select('COUNT(*)-1 AS level');
        $query->select("IFNULL((SELECT $idCol FROM $name AS pr"
            . " WHERE pr.lft < tbl.lft AND pr.rgt > tbl.rgt"
            . " ORDER BY pr.lft DESC LIMIT 1),0) AS parent_id");
    }

    /**
     * Set the current table twice in FROM clause for
     * having the nested tables
     *
     * @param KDatabaseQuery $query  Query object for table access
     */
    protected function _buildQueryFrom(KDatabaseQuery $query)
    {
        $name = $this->getTable()->getName();

        $query->from($name . ' AS n');
        $query->from($name . ' AS tbl');
    }

    /**
     * Join the two tables (defined in _buildQueryFrom) every
     * time so otherwise you will have an incorrect result list
     *
     * @param KDatabaseQuery $query Query object for table access
     */
    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $name = $db->getTablePrefix() . $table->getName();
        $idCol = $table->getIdentityColumn();

        $query->where('tbl.lft BETWEEN n.lft AND n.rgt');

        parent::_buildQueryWhere($query);
    }

    /**
     * Add default sort always to lft field for proper sequence of rows
     *
     * @param KDatabaseQuery $query Query object for table access
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
        $query->order('tbl.lft');
        parent::_buildQueryOrder($query);
    }

    /**
     * Always group the rows by lft field for correct calculation
     * of level and parent_id fields
     * If you don't set the GROUP BY, you will always have only
     * one row as result!
     *
     * @param KDatabaseQuery $query Query object for table access
     */
    protected function _buildQueryGroup(KDatabaseQuery $query)
    {
        $query->group('tbl.lft');
        parent::_buildQueryGroup($query);
    }
}