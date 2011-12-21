<?php
/**
 * @version		$Id: table.php 2876 2011-03-07 22:19:20Z johanjanssens $
 * @category	Koowa
 * @package		Koowa_Model
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Table Model Class
 *
 * Provides interaction with a database table
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Model
 */
class KModelTable extends KModelAbstract
{
    /**
     * Table object or identifier (APP::com.COMPONENT.table.TABLENAME)
     *
     * @var string|object
     */
    protected $_table;
    
    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        if(!empty($config->table)) {
            $this->setTable($config->table);
        }

        // Set the static states
        $this->_state
            ->insert('limit'    , 'int')
            ->insert('offset'   , 'int')
            ->insert('sort'     , 'cmd')
            ->insert('direction', 'word', 'asc')
            ->insert('search'   , 'string');

        //Try getting a table object
        if($table = $this->getTable())
        {
            //Set the table behaviors
            if(!empty($config->table_behaviors)) {
                $table->addBehaviors($config->table_behaviors);
            }
        
            // Set the dynamic states based on the unique table keys
            foreach($table->getUniqueColumns() as $key => $column) {
                $this->_state->insert($key, $column->filter, null, true, $table->mapColumns($column->related, true));
            }
        }
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'table'             => null,
            'table_behaviors'   => array()
        ));

        parent::_initialize($config);
    }

    /**
     * Set the model state properties
     *
     * This function overloads the KDatabaseTableAbstract::set() function and only acts on state properties.
     *
     * @param   string|array|object The name of the property, an associative array or an object
     * @param   mixed               The value of the property
     * @return  KModelTable
     */
    public function set( $property, $value = null )
    {
        parent::set($property, $value);
        
        // If limit has been changed, adjust offset accordingly
        if($limit = $this->_state->limit) {
             $this->_state->offset = $limit != 0 ? (floor($this->_state->offset / $limit) * $limit) : 0;
        }

        return $this;
    }
    
    /**
     * Get the identifier for the table with the same name
     * 
     * Function catches KDatabaseTableExceptions that are thrown for tables that don't exist.
     *
     * @return  KIdentifierInterface
     */
    public function getTable()
    {
        if(!isset($this->_table))
        {
            try 
            {               
                $identifier         = clone $this->_identifier;
                $identifier->name   = KInflector::tableize($identifier->name);
                $identifier->path   = array('database', 'table');

                $this->_table = KFactory::get($identifier);
            } 
            catch (KDatabaseTableException $e) 
            {
                //Set the table object to false
                $this->_table = false;
            }
        }

        return $this->_table;
    }

    /**
     * Method to set a table object attached to the model
     *
     * @param   mixed   An object that implements KObjectIdentifiable, an object that
     *                  implements KIndentifierInterface or valid identifier string
     * @throws  KDatabaseRowsetException    If the identifier is not a table identifier
     * @return  KModelTable
     */
    public function setTable($table)
    {
        if(!($table instanceof KDatabaseTableAbstract))
        {
            $identifier = KFactory::identify($table);
    
            if($identifier->path[0] != 'database' && $identifier->path[1] != 'table') {
                throw new KModelException('Identifier: '.$identifier.' is not a table identifier');
            }

            $table = KFactory::get($identifier);
        }
        
        $this->_table = $table;
        return $this;
    }

    /**
     * Method to get a item object which represents a table row
     *
     * If the model state is unique a row is fetched from the database based on the state.
     * If not, an empty row is be returned instead.
     *
     * @return KDatabaseRow
     */
    public function getItem()
    {
        if (!isset($this->_item))
        {
            if($table = $this->getTable())
            {
                $query  = null;

                if($this->_state->isUnique())
                {
                    $query = $table->getDatabase()->getQuery();

                    $this->_buildQueryColumns($query);
                    $this->_buildQueryFrom($query);
                    $this->_buildQueryJoins($query);
                    $this->_buildQueryWhere($query);
                    $this->_buildQueryGroup($query);
                    $this->_buildQueryHaving($query);   
                }
                
                $this->_item = $table->select($query, KDatabase::FETCH_ROW);
            }
        }

        return $this->_item;
    }

    /**
     * Get a list of items which represnts a  table rowset
     *
     * @return KDatabaseRowset
     */
    public function getList()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_list))
        {
            if($table = $this->getTable())
            {
                $query  = null;
                
                if(!$this->_state->isEmpty())
                {
                    $query = $table->getDatabase()->getQuery();
                
                    $this->_buildQueryColumns($query);
                    $this->_buildQueryFrom($query);
                    $this->_buildQueryJoins($query);
                    $this->_buildQueryWhere($query);
                    $this->_buildQueryGroup($query);
                    $this->_buildQueryHaving($query);
                    $this->_buildQueryOrder($query);
                    $this->_buildQueryLimit($query);
                }
        
                $this->_list = $table->select($query, KDatabase::FETCH_ROWSET);
            }
        }

        return $this->_list;
    }

    /**
     * Get the total amount of items
     *
     * @return  int
     */
    public function getTotal()
    {
        // Get the data if it doesn't already exist
        if (!isset($this->_total))
        {
            if($table = $this->getTable())
            {
                //Excplicitly get a count query, build functions can then test if the
                //query is a count query and decided how to build the query.
                $query = $table->getDatabase()->getQuery()->count(); 
                
                $this->_buildQueryFrom($query);
                $this->_buildQueryJoins($query);
                $this->_buildQueryWhere($query);

                $total = $table->count($query);
                $this->_total = $total;
            }
        }

        return $this->_total;
    }

    /**
     * Get the list of items based on the distinct column values
     *
     * @param string    The column name
     * @return KDatabaseRowset
     */
    public function getColumn($column)
    {   
        if (!isset($this->_column[$column])) 
        {   
            if($table = $this->getTable()) 
            {
                $query = $table->getDatabase()->getQuery()
                    ->distinct()
                    ->group('tbl.'.$table->mapColumns($column));

                $this->_buildQueryOrder($query);
                        
                $this->_column[$column] = $table->select($query);
            }
        }
            
        return $this->_column[$column];
    }

    /**
     * Builds SELECT columns list for the query
     */
    protected function _buildQueryColumns(KDatabaseQuery $query)
    {
        $query->select(array('tbl.*'));
    }

    /**
     * Builds FROM tables list for the query
     */
    protected function _buildQueryFrom(KDatabaseQuery $query)
    {
        $name = $this->getTable()->getName();
        $query->from($name.' AS tbl');
    }

    /**
     * Builds LEFT JOINS clauses for the query
     */
    protected function _buildQueryJoins(KDatabaseQuery $query)
    {

    }

    /**
     * Builds a WHERE clause for the query
     */
    protected function _buildQueryWhere(KDatabaseQuery $query)
    {
        //Get only the unique states
        $states = $this->_state->getData(true);
        
        if(!empty($states))
        {
            $states = $this->getTable()->mapColumns($states);
            foreach($states as $key => $value)
            {
                if(isset($value)) {
                    $query->where('tbl.'.$key, 'IN', $value);
                }
            }
        }
    }

    /**
     * Builds a GROUP BY clause for the query
     */
    protected function _buildQueryGroup(KDatabaseQuery $query)
    {

    }

    /**
     * Builds a HAVING clause for the query
     */
    protected function _buildQueryHaving(KDatabaseQuery $query)
    {

    }

    /**
     * Builds a generic ORDER BY clasue based on the model's state
     */
    protected function _buildQueryOrder(KDatabaseQuery $query)
    {
        $sort       = $this->_state->sort;
        $direction  = strtoupper($this->_state->direction);

        if($sort) { 
            $query->order($this->getTable()->mapColumns($sort), $direction); 
        } 

        if(array_key_exists('ordering', $this->getTable()->getColumns())) {
            $query->order('ordering', 'ASC');
        }
    }

    /**
     * Builds LIMIT clause for the query
     */
    protected function _buildQueryLimit(KDatabaseQuery $query)
    {
        $limit = $this->_state->limit;
        
        if($limit) 
        {
            $offset = $this->_state->offset;
            $total  = $this->getTotal();

            //If the offset is higher than the total recalculate the offset
            if($offset !== 0 && $total !== 0)        
            {
                if($offset >= $total) 
                {
                    $offset = floor(($total-1) / $limit) * $limit;    
                    $this->_state->offset = $offset;
                }
             }
            
             $query->limit($limit, $offset);
        }
    }
}