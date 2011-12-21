<?php
/**
 * @version     $Id: uninstall.php 49 2011-03-28 23:04:49Z stiandidriksen $
 * @category    Koowa
 * @package     Koowa_Components
 * @subpackage  Extensions
 * @copyright   Copyright (C) 2010 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This function is required by JInstallerComponent in order to run this script
 *
 * @return  boolean true
 */
function com_uninstall()
{
    return true;
}

// Define variables
$database   = JFactory::getDBO();
$type       = 'com';
$manifest   = simplexml_load_file($this->parent->getPath('manifest'));
$package    = (string) $manifest->name;
$version    = (string) $manifest->version;
$logo       = JURI::root(1).'/media/com_extensions/images/logo.png';
$jversion   = JVersion::isCompatible('1.6.0') ? '1.6' : '1.5';

//Run platform specific procedures
require JPATH_ROOT.'/administrator/components/com_'.$package.'/install/install.'.$jversion.'.php';

// Delete framework folders, like com_default, mod_default
foreach ($manifest->framework->folder as $folder)
{
    if(JFolder::exists(JPATH_ROOT.$folder)) JFolder::delete(JPATH_ROOT.$folder);
}
// Delete framework files, like the koowa plugin, and the default plugin
foreach ($manifest->framework->file as $file)
{
    if(JFile::exists(JPATH_ROOT.$file)) JFile::delete(JPATH_ROOT.$file);
}

// Preload the logo
$document = JFactory::getDocument();
$document->addScriptDeclaration('(new Image).src = "'.$logo.'";');

$document->addStyleSheet(JURI::root(1).'/media/com_extensions/css/install.css');
?>
<table>
    <tbody valign="middle">
        <tr>
            <td>
                
                <img src="<?php echo $logo ?>" alt="<?php echo JText::_('Nooku') ?>" width="190" height="80" />
            </td>
            <td width="100%">
                <table class="adminlist">
                    <thead>
                        <tr>
                            <th><?php echo JText::_('Task') ?></th>
                            <th width="30%"><?php echo JText::_('Status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="row0">
                            <td class="key"><?php echo JText::_('Nooku Framework') ?></td>
                            <td><strong><?php echo JText::_('Uninstalled') ?></strong> - <?php echo $version ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>