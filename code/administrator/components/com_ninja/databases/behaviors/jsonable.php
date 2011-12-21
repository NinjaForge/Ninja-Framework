<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: jsonable.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * A JSON behavior for decoding and encoding multi level data to a json format
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninja
 * @subpackage 	Behaviors
 */
class ComNinjaDatabaseBehaviorJsonable extends KDatabaseBehaviorAbstract
{
	/**
	 * An array over the fields to encode and decode
	 *
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Decodes json data on each field
	 * 
	 * @return boolean	true.
	 */
	protected function _afterTableSelect(KCommandContext $context)
	{
		$rows = $context['data'];
		$fields = $context['caller']->json_fields;

//		die('<pre>'.var_export(is_a($rows, 'KDatabaseRowInterface'), true).'</pre>');
		if(is_a($rows, 'KDatabaseRowInterface')) $rows = array($rows);
		foreach($rows as $row)
		{
			foreach($fields as $field)
			{
				if(!is_string($row->$field)) continue;
				$data = json_decode($row->$field, true);

				$row->$field = $data;
			}
		}
		
		return true;
	}
	
	/**
	 * Encodes to json during update
	 * 
	 * @return boolean true
	 */
	protected function _beforeTableUpdate(KCommandContext $context)
	{
		return $this->json_encode($context);
	}
	
	/**
	 * Encodes to json during update
	 * 
	 * @return boolean true
	 */
	protected function _beforeTableInsert(KCommandContext $context)
	{
		return $this->json_encode($context);
	}
	
	/**
	 * Encode the data for each field
	 *
	 * @param  KDatabaseRowAbstract
	 * @return void
	 */
	private function json_encode(KCommandContext $context)
	{
		$row	= $context['data'];

		foreach($context['caller']->json_fields as $field)
		{
			$row->$field = json_encode((array)$row->$field);
		}
		
		return true;
	}
	
}