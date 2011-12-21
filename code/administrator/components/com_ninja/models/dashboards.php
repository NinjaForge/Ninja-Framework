<?php defined( 'KOOWA' ) or die( 'Restricted access' );

/**
 * @todo Dummy model until I figure out a better way to do it
 */
class ComNinjaModelDashboards extends KModelAbstract
{
	public function __construct(KConfig $options)
	{
		parent::__construct($options);
		
		$napi			= clone $this->getIdentifier();
		$napi->package	= 'ninja';
		$identifier		= $this->getIdentifier();
		
		$this->_state->insert('limit', 'int', 0);

		KFactory::tmp('admin::com.ninja.helper.installer', array('identifier' => $napi));
		KFactory::tmp('admin::com.ninja.helper.installer', array('identifier' => $identifier));
	}
}