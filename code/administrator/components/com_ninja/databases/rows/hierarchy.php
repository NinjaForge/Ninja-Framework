<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: hierarchy.php 919 2011-03-21 21:45:13Z stian $
 * @category	Ninja
 * @package     Ninja_Rows
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Database Row Class capable of dealing with hierarchies
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package     Ninja_Rows
 */
class ComNinjaDatabaseRowHierarchy extends KDatabaseRowDefault
{

	/**
	 * Saves the row to the database.
	 *
	 * This performs an update on all children rows when the path changes.
	 *
	 * @return KDatabaseRowAbstract
	 */
	public function saves()
    {
    	if(!empty($this->id)) 
    	{
    		$id    = $this->id;
    		
    		$table = KFactory::get($this->getTable());
    		
    		$query = $table->getDatabase()->getQuery();
    		$query->where('path', 'like', '%'.$id.'%');
    		$path  = $this->path;
    		$path  = $path ? $path.'/'.$id : $id;
    		foreach($table->select($query) as $row)
    		{
    			$parts = explode($id, $row->path);
    			$part = isset($parts[1]) ? $parts[1] : null;
    			$row->path = $path.$part;
    			$row->save();
    		}	
    	}
    	
    	parent::save();
    	
        return $this;
    }
}