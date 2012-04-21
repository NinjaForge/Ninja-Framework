<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaDatabaseTableCore_usergroups extends KDatabaseTableAbstract
{
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
	    $config->append(array(
	        'name'              => version_compare(JVERSION,'1.6.0','ge') ? 'usergroups' : 'core_acl_aro_groups',
	        'column_map'        => array('title' => 'name'),
	        'identity_column'   => 'id',
	    ));
	    
	     parent::_initialize($config);
	}
}