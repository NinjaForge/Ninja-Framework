<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: graphael.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Graphael Helper - for creating g.raphael graphics (charts mostly)
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperGraphael extends KTemplateHelperAbstract
{
	/**
	 * Constructor
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->getService('ninja:template.helper.document')->render(array('/raphael.js','/g.raphael.js'));
	}
	
	/**
	 * Renders a piechart
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function piechart($config = array())
	{
		$this->getService('ninja:template.helper.document')->render('/g.pie.js');
	}
}