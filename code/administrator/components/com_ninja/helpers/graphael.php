<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: graphael.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Class for creating g.raphael graphics (charts mostly)
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaHelperGraphael extends KTemplateHelperAbstract
{
	/**
	 * Constructor
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		KFactory::get('admin::com.ninja.helper.default')->js('/raphael.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/g.raphael.js');
	}
	
	/**
	 * Renders a piechart
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function piechart($config = array())
	{
		KFactory::get('admin::com.ninja.helper.default')->js('/g.pie.js');
	}
}