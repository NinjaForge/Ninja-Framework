<?php
/**
 * @version     $Id: html.php 49 2011-03-28 23:04:49Z stiandidriksen $
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Extensions
 */
class ComExtensionsViewDashboardHtml extends ComDefaultViewHtml
{
    public function display()
    {
        //Updater functionality is currently not supported. Abort.
        return '';
        
        KFactory::get('admin::com.extensions.toolbar.'.$this->getName())->reset()->setTitle('Nooku Framework');

        $this->assign('manifest', simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR.'/manifest.xml'));

        $file = @simplexml_load_file('http://nooku.assembla.com/code/nooku-framework/subversion/nodes/tags');
        $file->registerXPathNamespace('t', 'http://www.w3.org/1999/xhtml');

        $rows = $file->xpath("//t:td[@class='linked node-type']//*");
        foreach ($rows as $row) {
            $tags[] = (string) $row;
        }
        rsort($tags);

        $this->assign('latest', $tags[0]);
        return parent::display();
    }
}