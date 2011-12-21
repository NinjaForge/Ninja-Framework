<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: html.php 794 2011-01-10 18:44:32Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaViewTemplatesHtml extends ComNinjaViewDefault
{
	public function display()
	{	
		$this->assign('date', KFactory::get('lib.joomla.utilities.date'));
	
		$this->_createToolbar()
			->reset();
			//->append(KFactory::get('admin::com.ninja.toolbar.button.install'))
			//->append('uninstall');

		$this->setLayout('admin::com.ninja.view.templates.default');
		return parent::display();
	}
}