<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: core_categories.php 794 2011-01-10 18:44:32Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaDatabaseTableCore_Categories extends KDatabaseTableAbstract
{
	public function __construct(KConfig $options)
	{
		$options->name		= 'categories';
		$options->identity_column	= 'id';
		
		parent::__construct($options);
	}
}