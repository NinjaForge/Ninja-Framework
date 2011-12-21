<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dashboard.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Dashboard Controller Toolbar Class
 */
class NinjaControllerToolbarDashboard extends ComDefaultControllerToolbarDefault
{
    public function getCommands()
    {
        $this->addAbout();

        return parent::getCommands();
    }
    
    /**
     * About toolbar command
     *
     * Shows an about button, that when clicked displays a popup with more information about the extension, like the changelog
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandAbout(KControllerToolbarCommand $command)
    { 
        $url = clone KRequest::url();
        $url->query['view'] = 'dashboard';
        $url->query['tmpl'] = 'component';

        $command->append(array(
            'width'   => '720',
            'height'  => 'window.getSize().size.y-80',
            'href'	  => (string)$url
        ));

        $this->_commandModal($command);

        $helper = $this->getService('ninja:template.helper.document');
        $image  = $helper->img('/32/about.png');
        if($image){
        	$helper->load('css', '.toolbar .icon-32-about { background-image: url('.$image.'); }');
        }
    }
}