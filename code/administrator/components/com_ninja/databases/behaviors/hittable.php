<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: hittable.php 1399 2011-11-01 14:22:48Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * A hittable behavior for incrementing a hits property
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninja
 * @subpackage 	Behaviors
 */
class NinjaDatabaseBehaviorHittable extends KDatabaseBehaviorAbstract
{
	/**
     * Get the methods that are available for mixin based
     * 
     * This function conditionaly mixies the behavior. Only if the mixer 
     * has a 'hits' property the behavior will be mixed in.
     * 
     * @param object The mixer requesting the mixable methods. 
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = array();
          
        if(isset($mixer->hits)) {
            $methods = parent::getMixableMethods($mixer);
        }
      
       return $methods;    
    }
        
    /**
     * Increase hit counter by 1
     *
     * Requires a 'hits' column
     */
    public function hit()
    {
         $this->hits++;
                
         if(!$this->isNew()) {
             $this->save();
         }

         return $this->_mixer;
     }
}