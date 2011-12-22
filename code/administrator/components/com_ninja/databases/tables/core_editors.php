<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaDatabaseTableCore_Editors extends KDatabaseTableAbstract
{
	public function __construct(KConfig $options)
	{
		$options->name		= 'plugins';
		$options->identity_column	= 'id';
		
		parent::__construct($options);
	}
}