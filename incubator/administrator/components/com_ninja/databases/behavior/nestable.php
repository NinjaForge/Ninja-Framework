<?php
/**
 * @category	Koowa
 * @package		Koowa_Database
 * @subpackage 	Behavior
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * Database Nestable Behavior (WIP)
 *
 * @TODO The code just got pasted over from a row class, so it's not ready for use right now.
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage 	Behavior
 * @based on    KDatabaseBehaviorOrderable
 */
class KDatabaseBehaviorNestable extends KDatabaseBehaviorAbstract
{
 	/**
 	 * Saves the row to the database.
 	 *
 	 * This performs an update on all children rows when the path changes.
 	 *
 	 * @return KDatabaseRowAbstract
 	 */
 	public function save()
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
 	
 	/**
 	 * Deletes the row and the row children from the database.
 	 *
 	 * @return KDatabaseRowAbstract
 	 */
 	public function delete()
 	{
 		parent::delete();
 		
 		$query = $this->_table->getDatabase()->getQuery();
 		$query->where('path', 'like', '%'.$this->id.'%');
 		$this->_table->delete($query);
 	    return $this;
 	}
}