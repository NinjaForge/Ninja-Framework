<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright   Copyright (C) 2011 - 2012 Timble CVBA and Contributors. (http://www.timble.net).
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */
 
/**
 * Spammable Behavior inspired from noooku server
 *
 * @author Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 */
class NinjaControllerBehaviorSpammable extends KControllerBehaviorAbstract 
{
	/**
     *
     * @var array An array of spam checks to be executed.
     */
    protected $_checks;

	/**
     *
     * @var array An array of fields to check for validation
     */
    protected $_validate_fields;

    /**
     *
     * @var array An array containing the checks that failed.
     */
    protected $_failed_checks;

    /**
     *
     * @var array An array of invalid data
     */
    protected $_invalid_data;

    public function __construct(KConfig $config = null)
    {
        if (!$config) {
                $config = new KConfig();
        }
        parent::__construct($config);

        $this->_checks = $config->checks;
        $this->_validate_fields = $config->validate_fields;
        $this->_failed_checks = array();
    }

    /**
     * Run the various checks before posting
     *
     * @param	KCommandContext	The context of the event
     */
    protected function _beforeAdd(KCommandContext $context)
    {
    	if ($this->spammed(array('data' => $context->data))) {

    		// show some warnings if the data failed
    		foreach ($this->_invalid_data as $invalid) {
    			JError::raiseWarning(21, JText::_('NAPI_DATA_VALIDATION_MISSING_FAILED_'.strtoupper($invalid)));
    		} 

    		return false;
    	} 

    	return true;
    }

    /**
     * Performs a spam check.
     *
     * @param  array An optional configuration array.
     * @throws KControllerBehaviorException If a requested spam check is not
     *         implemented.
     * @return boolean True if spam is suspected, false otherwise.
     */
    public function spammed($config = array())
    {
        if(!isset($this->_spammed)) {
                
            $config = new KConfig($config);

            /*$config->append(array('whitelist' => true));
                        
            if($config->whitelist && $this->whiteIp()) {
                // Client is whitelisted.
                $this->_spammed = false;
                return $this->_spammed;
            
            }*/
            
            if(!$config->checks) {
                // Use behavior checks.
                $config->checks = $this->_checks;
            }
            
            // Initialize the spammed status as false.
            $this->_spammed = false;

            // loop through our checks and see if we have passed them
            foreach($config->checks as $key => $val) {
                if(is_numeric($key)) {
                    $check = $val;
                    $params = array();
                } else {
                    $check = $key;
                    $params = KConfig::unbox($val);
                }
                // Append data (if any).
                $params['data'] = $config->data;
                $method = '_' . $check . 'Check';
                if(!method_exists($this, $method)) {
                        throw new KControllerBehaviorException('Unknown spam check.');
                }
                if(!$this->$method($params)) {
                    $this->_failed_checks[] = $check;
                }
            }

            //if we failed a check then we are spammers
            if(count($this->_failed_checks)) {
                $this->_spammed = true;
            }
        }
        
        return (bool) $this->_spammed;
    }

    /**
     * Validate Data check
     *
     * @param  array An optional configuration array.
     * @return boolean True if validation failed, false otherwise.
     */
    protected function _dataCheck($config = array())
    {
        $config = new KConfig($config);

        foreach ($this->_validate_fields as $field)
        {
            if ($field == 'email') {
                if (!$this->getService('koowa:filter.email')->validate($config->data->email)) $this->_invalid_data[] = $field;
            } elseif ($field == 'url') {
            	if (!$this->getService('koowa:filter.url')->validate($config->data->url)) $this->_invalid_data[] = $field;
            } else {
                if (!$config->data->{$field}) $this->_invalid_data[] = $field;
            }
        }

        return (empty($this->_invalid_data)) ? true : false;
    }
}