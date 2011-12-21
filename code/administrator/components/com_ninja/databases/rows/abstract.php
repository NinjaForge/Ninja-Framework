<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version		$Id: abstract.php 1399 2011-11-01 14:22:48Z stian $
* @category		Ninja
* @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
* @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link     	http://ninjaforge.com
*/

class NinjaDatabaseRowAbstract extends KDatabaseRowDefault
{
	/*public function __get($column)
	{
		if($column == 'params' && is_string($this->_data['params'])) {
			$this->_data['params'] = json_decode($this->_data['params'], true);
		}

		return parent::__get($column);
	}*/
}