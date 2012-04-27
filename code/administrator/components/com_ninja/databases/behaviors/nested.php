<?php
/**
 * @version     $Id: categories.php 19 2011-11-19 01:56:42Z richie $
 * @package     Ninja
 * @subpackage  Behaviors
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://ninjaforge.com
 *
 */
 defined( 'KOOWA' ) or die( 'Restricted access' );
 
 /**
 * Nested Database Behavior Class
 *
 * For handling nested data
 *
 * @author      Richie Mortimer <richie@ninjaforge.com>
 * @package     Ninja
 * @subpackage  Behaviors
 */
class NinjaDatabaseBehaviorNested extends KDatabaseBehaviorAbstract
{
    /**
     * Get the methods that are available for mixin based
     *
     * This function conditionaly mixies the behavior. Only if the mixer
     * has a 'lft' and 'rgt' property the behavior will be mixed in.
     *
     * @param object The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = array();

        if (isset($mixer->lft) && isset($mixer->rgt))
        {
            $methods = parent::getMixableMethods($mixer);
        }

        return $methods;
    }

    /**
     * Set the lft and rgt values if the parent or position of  
     * selected row has changed
     * 
     * @param KCommandContext $context   Nooku context
     */
    protected function _beforeTableUpdate(KCommandContext $context)
    {
        $row = &$context->data;
        $node = $this->getNode($row->id);

        if (isset($context->data->order))
        {
            $this->move($row->id, $context->data->order);
        }
        elseif ($node->parent_id != $row->parent_id)
        {
            $position = $row->parent_id == 0 ? 'after' : 'last-child';
            $this->moveByReference($row->id, $row->parent_id, $position);
        }
    }

    /**
     * Set the lft and rgt values for new row and all addicted rows
     * 
     * @param KCommandContext $context   Nooku context
     */
    protected function _beforeTableInsert(KCommandContext $context)
    {
        $parent = null;
        $prev = null;

        $table = $this->getTable();
        $query = $table->getDatabase()->getQuery();

        $row = &$context->data;

        $position = $row->parent_id == 0 ? 'after' : 'last-child';

        if (!$row->lft = $this->insertNode($row->parent_id, $position))
            return;

        $row->rgt = $row->lft + 1;
    }

    /**
     * Set the lft and rgt values for all addicted rows 
     * 
     * @param KCommandContext $context   Nooku context
     */
    protected function _beforeTableDelete(KCommandContext $context)
    {
        $row = &$context->data;
        $this->deleteNode($row->id);
    }

    /**
     * Add calculated rows for level and parent id as fields
     * 
     * @param KDatabaseQuery $query   Query object
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
     * Add twice the main table for handling nested sets
     * 
     * @param KDatabaseQuery $query   Query object
     */
    protected function _buildQueryFrom(KDatabaseQuery $query)
    {
        $name = $this->getTable()->getName();
        $query->from($name . ' AS n');
        $query->from($name . ' AS tbl');
    }

    /**
     * Set order to lft field as default
     * 
     * @param KDatabaseQuery $query   Query object
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
        $query->order('tbl.lft');
    }

    /**
     * Always add the group by because of level calculation.
     * Without this group by definition the result will be incorrect!
     * 
     * @param KDatabaseQuery $query   Query object
     */
    protected function _buildQueryGroup(KDatabaseQuery $query)
    {
        $query->group('tbl.lft');
    }

    /**
     * Make an MySQL db update
     * 
     * @param   string  $setters    the list of fields and values to set
     * @param   string  $where      the where clause to select entries
     * @return  boolean             True if update was successfully
     */
    protected function _dbUpdate($setters, $where)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();

        $sql = "UPDATE $tabName SET $setters WHERE $where";
        return $db->execute($sql);
    }

    /**
     * Make an MySQL db delete
     * 
     * @param   string   $where    the where clause of deletion
     * @return  boolean            True if deletion was successfully
     */
    protected function _dbDelete($where)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();

        $sql = "DELETE FROM $tabName WHERE $where";
        return $db->execute($sql);
    }

    /**
     * Insert a node on specific position
     * 
     * @param   integer  $referenceId    Id of reference node
     * @param   string   $position       where to instert the node
     *                                   after -> after reference node
     *                                   before -> before reference node
     *                                   first-child -> as new child at the beginning of reference node
     *                                   last-child -> as new child at the end of reference node
     * 
     * @return  integer                  The position for new node to use as lft value
     */
    public function insertNode($referenceId = null, $position = 'after')
    {
        $table = $this->getTable();
        $query = $table->getDatabase()->getQuery();
        if (is_null($referenceId) || $referenceId == 0)
        {
            $query->select($table->getIdentityColumn());
            $query->limit(1);
            if ($position == 'first-child' || $position == 'before')
                $query->order('lft', 'ASC');
            else
                $query->order('rgt', 'DESC');
            $referenceId = $table->select($query, KDatabase::FETCH_FIELD);
        }

        if (!$node = $this->getNode($referenceId))
            return null;

        $new_lft = null;

        switch ($position)
        {
            case 'first-child':
                $new_lft = $node->lft + 1;
                $setter = 'rgt = rgt + 2';
                $where = 'rgt > ' . $node->lft;
                $this->_dbUpdate($setter, $where);
                $setter = 'lft = lft + 2';
                $where = 'lft > ' . $node->lft;
                $this->_dbUpdate($setter, $where);
                break;

            case 'last-child':
                $new_lft = $node->rgt;
                $setter = 'rgt = rgt + 2';
                $where = 'rgt >= ' . $node->rgt;
                $this->_dbUpdate($setter, $where);
                $setter = 'lft = lft + 2';
                $where = 'lft > ' . $node->rgt;
                $this->_dbUpdate($setter, $where);
                break;

            case 'before':
                $new_lft = $node->lft;
                $setter = 'rgt = rgt + 2';
                $where = 'rgt > ' . $node->lft;
                $this->_dbUpdate($setter, $where);
                $setter = 'lft = lft + 2';
                $where = 'lft >= ' . $node->lft;
                $this->_dbUpdate($setter, $where);
                break;

            default:
            case 'after':
                $new_lft = $node->rgt + 1;
                $setter = 'rgt = rgt + 2';
                $where = 'rgt > ' . $node->rgt;
                $this->_dbUpdate($setter, $where);
                $setter = 'lft = lft + 2';
                $where = 'lft > ' . $node->rgt;
                $this->_dbUpdate($setter, $where);
                break;
        }

        return $new_lft;
    }

    /**
     * Reset all addicted lft and rgt values after row deletion
     *
     * @param   integer  $pk        The primary key of the node to delete.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.joomla.org/JTableNested/delete
     * @since   11.1
     */
    public function deleteNode($pk)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();

        // Get the node by id.
        if (!$node = $this->getNode($pk))
            return false;

        $node->width = $node->rgt - $node->lft + 1;

        // Shift all node's children up a level.
        $setter = 'lft = lft - 1, rgt = rgt - 1';
        $where = 'lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt;
        $this->_dbUpdate($setter, $where);

        // Shift all of the left values that are right of the node.
        $setter = 'lft = lft - 2';
        $where = 'lft > ' . (int) $node->rgt;
        $this->_dbUpdate($setter, $where);

        // Shift all of the right values that are right of the node.
        $setter = 'rgt = rgt - 2';
        $where = 'rgt > ' . (int) $node->rgt;
        $this->_dbUpdate($setter, $where);

        return true;
    }

    /**
     * Get nested set properties for a node in the tree.
     *
     * @param   integer  $id   Value to look up the node by.
     * @param   string   $key  Key to look up the node by.
     *
     * @return  mixed    Boolean false on failure or node object on success.
     */
    public function getNode($id, $key = null)
    {
        $having = false;

        $table = $this->getTable();
        $query = $table->getDatabase()->getQuery();
        $this->_buildQueryColumns($query);
        $this->_buildQueryGroup($query);
        switch ($key)
        {
            case 'parent':
                $k = 'parent_id';
                $having = true;
                break;
            case 'left':
                $k = 'lft';
                break;
            case 'right':
                $k = 'rgt';
                break;
            default:
                $k = $table->getIdentityColumn();
                break;
        }

        if ($having)
            $query->having($k . ' = ' . (int) $id);
        else
            $query->where($k . ' = ' . (int) $id);

        $row = $table->select($query, KDatabase::FETCH_ROW);
        return $row;
    }

    /**
     * Get a list of nodes from a given node to its root.
     *
     * @param   integer  $pk          Primary key of the node for which to get the path.
     * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
     *
     * @return  mixed    Boolean false on failure or array of node objects on success.
     */
    public function getPath($pk)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();
        $query = $db->getQuery();

        // Get the path from the node to the root.
        $this->_buildQueryColumns($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryOrder($query);
        $this->_buildQueryGroup($query);

        $query->where('n.lft BETWEEN tbl.lft AND tbl.rgt');
        $query->where('n.' . $colId . ' = ' . (int) $pk);

        $path = $table->select($query, KDatabase::FETCH_ROWSET);
        return $path;
    }

    /**
     * Get a node and all its child nodes.
     *
     * @param   integer  $pk          Primary key of the node for which to get the tree.
     * @param   boolean  $diagnostic  Only select diagnostic data for the nested sets.
     *
     * @return  mixed    Boolean false on failure or array of node objects on success.
     */
    public function getTree($pk)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();
        $query = $db->getQuery();

        // Get the node and children as a tree.
        $this->_buildQueryColumns($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryOrder($query);
        $this->_buildQueryGroup($query);

        $query->where('tbl.lft BETWEEN n.lft AND n.rgt');
        $query->where('n.' . $colId . ' = ' . (int) $pk);

        $tree = $table->select($query, KDatabase::FETCH_ROWSET);
        return $tree;
    }

    /**
     * Determine if a node is a leaf node in the tree (has no children).
     *
     * @param   integer  $pk  Primary key of the node to check.
     *
     * @return  boolean  True if a leaf node.
     */
    public function isLeaf($pk)
    {
        // Get the node by primary key.
        if (!$node = $this->getNode($pk))
        {
            // Error message set in getNode method.
            return false;
        }

        // The node is a leaf node.
        return (($node->rgt - $node->lft) == 1);
    }

    /**
     * Move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
     * Negative numbers move the row up in the sequence and positive numbers move it down.
     *
     * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
     * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the
     *                           ordering values.
     *
     * @return  mixed    Boolean true on success.
     */
    public function move($pk, $delta)
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();

        $query = $db->getQuery();

        $row = $this->getNode($pk);

        $this->_buildQueryColumns($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryGroup($query);
        $query->having('parent_id = ' . $row->parent_id);

        $position = 'after';
        if ($delta > 0)
        {
            $query->where('tbl.rgt > ' . $row->rgt);
            $query->order('rgt');
            $position = 'after';
        }
        else
        {
            $query->where('tbl.lft < ' . $row->lft);
            $query->order('lft', 'DESC');
            $position = 'before';
        }

        $parent = $table->select($query, KDatabase::FETCH_ROW);
        $referenceId = $parent->id;

        if (!$referenceId)
            return false;

        return $this->moveByReference($pk, $referenceId, $position);
    }

    /**
     * Move a node and its children to a new location in the tree.
     *
     * @param   integer  $referenceId  The primary key of the node to reference new location by.
     * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
     * @param   integer  $pk           The primary key of the node to move.
     *
     * @return  boolean  True on success.
     */
    public function moveByReference($pk, $referenceId, $position = 'after')
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();

        $query = $db->getQuery();

        // Get the node by id.
        if (!$node = $this->getNode($pk))
        {
            // Error message set in getNode method.
            return false;
        }

        $node->width = $node->rgt - $node->lft + 1;

        // Get the ids of child nodes.
        $this->_buildQueryColumns($query);
        $this->_buildQueryFrom($query);
        $this->_buildQueryOrder($query);
        $this->_buildQueryGroup($query);
        $query->where('tbl.lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);

        $childrens = $table->select($query, KDatabase::FETCH_ROWSET);

        /*
         * Move the sub-tree out of the nested sets by negating its left and right values.
         */
        $setter = 'lft = lft * (-1), rgt = rgt * (-1)';
        $where = 'lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt;
        $this->_dbUpdate($setter, $where);

        /*
         * Close the hole in the tree that was opened by removing the sub-tree from the nested sets.
         */
        // Compress the left values.
        $setter = 'lft = lft - ' . (int) $node->width;
        $where = 'lft > ' . (int) $node->rgt;
        $this->_dbUpdate($setter, $where);

        // Compress the right values.
        $setter = 'rgt = rgt - ' . (int) $node->width;
        $where = 'rgt > ' . (int) $node->rgt;
        $this->_dbUpdate($setter, $where);

        // We are moving the tree relative to a reference node.
        if ($referenceId)
        {
            // Get the reference node by primary key.
            if (!$reference = $this->getNode($referenceId))
                return false;

            // Get the reposition data for shifting the tree and re-inserting the node.
            if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, $position))
                return false;
        }

        // We are moving the tree to be the last child of the root node
        else
        {
            // Get the last root node as the reference node.
            $query = $db->getQuery();
            $this->_buildQueryColumns($query);
            $this->_buildQueryFrom($query);
            $this->_buildQueryGroup($query);

            $query->having('parent_id = 0');
            $query->order('lft', 'DESC');

            $reference = $table->select($query, KDatabase::FETCH_ROW);

            // Get the reposition data for re-inserting the node after the found root.
            if (!$repositionData = $this->_getTreeRepositionData($reference, $node->width, $position))
                return false;
        }

        /*
         * Create space in the nested sets at the new location for the moved sub-tree.
         */
        // Shift left values.
        $setter = 'lft = lft + ' . (int) $node->width;
        $where = $repositionData->left_where;
        $this->_dbUpdate($setter, $where);

        // Shift right values.
        $setter = 'rgt = rgt + ' . (int) $node->width;
        $where = $repositionData->right_where;
        $this->_dbUpdate($setter, $where);

        /*
         * Calculate the offset between where the node used to be in the tree and
         * where it needs to be in the tree for left ids (also works for right ids).
         */
        $offset = $repositionData->new_lft - $node->lft;
        $levelOffset = $repositionData->new_level - $node->level;

        // Move the nodes back into position in the tree using the calculated offsets.
        $setter = 'rgt = ' . (int) $offset . ' - rgt, lft = ' . (int) $offset . ' - lft';
        $where = 'lft < 0';
        $this->_dbUpdate($setter, $where);
        return true;
    }

    /**
     * Get various data necessary to make room in the tree at a location
     * for a node and its children.  The returned data object includes conditions
     * for SQL WHERE clauses for updating left and right id values to make room for
     * the node as well as the new left and right ids for the node.
     *
     * @param   object   $referenceNode  A node object with at least a 'lft' and 'rgt' with
     *                                   which to make room in the tree around for a new node.
     * @param   integer  $nodeWidth      The width of the node for which to make room in the tree.
     * @param   string   $position       The position relative to the reference node where the room
     *                                   should be made.
     *
     * @return  mixed    Boolean false on failure or data object on success.
     */
    protected function _getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before')
    {
        $table = $this->getTable();
        $db = $table->getDatabase();
        $tabName = $db->getTablePrefix() . $table->getName();
        $colId = $table->getIdentityColumn();

        $query = $db->getQuery();

        // Make sure the reference an object with a left and right id.
        if (!is_object($referenceNode) && isset($referenceNode->lft) && isset($referenceNode->rgt))
            return false;

        // A valid node cannot have a width less than 2.
        if ($nodeWidth < 2)
            return false;

        // Initialise variables.
        $data = new stdClass;

        // Run the calculations and build the data object by reference position.
        switch ($position)
        {
            case 'first-child':
                $data->left_where = 'lft > ' . $referenceNode->lft;
                $data->right_where = 'rgt >= ' . $referenceNode->lft;

                $data->new_lft = $referenceNode->lft + 1;
                $data->new_rgt = $referenceNode->lft + $nodeWidth;
                $data->new_parent_id = $referenceNode->$colId;
                $data->new_level = $referenceNode->level + 1;
                break;

            case 'last-child':
                $data->left_where = 'lft > ' . ($referenceNode->rgt);
                $data->right_where = 'rgt >= ' . ($referenceNode->rgt);

                $data->new_lft = $referenceNode->rgt;
                $data->new_rgt = $referenceNode->rgt + $nodeWidth - 1;
                $data->new_parent_id = $referenceNode->$colId;
                $data->new_level = $referenceNode->level + 1;
                break;

            case 'before':
                $data->left_where = 'lft >= ' . $referenceNode->lft;
                $data->right_where = 'rgt >= ' . $referenceNode->lft;

                $data->new_lft = $referenceNode->lft;
                $data->new_rgt = $referenceNode->lft + $nodeWidth - 1;
                $data->new_parent_id = $referenceNode->parent_id;
                $data->new_level = $referenceNode->level;
                break;

            default:
            case 'after':
                $data->left_where = 'lft > ' . $referenceNode->rgt;
                $data->right_where = 'rgt > ' . $referenceNode->rgt;

                $data->new_lft = $referenceNode->rgt + 1;
                $data->new_rgt = $referenceNode->rgt + $nodeWidth;
                $data->new_parent_id = $referenceNode->parent_id;
                $data->new_level = $referenceNode->level;
                break;
        }

        return $data;
    }
}