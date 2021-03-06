<?php defined( '_JEXEC' ) or die( 'Restricted access' );

$this->name = strtolower($this->name);

$extname = 'com_' . $this->name;

//Delete cache folder if it exists
$cache = JPATH_ROOT.'/cache/'.$extname.'/';
if(JFolder::exists($cache)) JFolder::delete($cache);

// load the component language file
$language = &JFactory::getLanguage();
$language->load($extname, JPATH_ADMINISTRATOR, 'en-GB', true);

if(!function_exists('humanize'))
{
	function humanize ($word)
	{
		return ucwords(strtolower(str_replace("_", " ", $word)));
	}
}

// To allow multiple components to be installed in a single go, crucial to have this check
if(!function_exists('com_install'))
{
	/**
	 * This function is required by JInstallerComponent in order to run this script
	 *
	 * @return	boolean	true
	 */
	function com_install()
	{
		static $installable;
		
		if(isset($installable)) return $installable;
		
		$db = JFactory::getDBO();
		//Run check on the minimum required server specs. Will roll back install if any check fails
		foreach(array(
			class_exists('mysqli') => "Your server don't have MySQLi.",
			version_compare(phpversion(), '5.2', '>=') => "Your PHP version is older than 5.2.",
			version_compare($db->getVersion(), '5.0.41', '>=') => "Your MySQL server version is older than 5.0.41."
			
		) as $succeed => $fail) {
			if(!$succeed) {
				JError::raiseWarning(0, $fail);
				return $installable = false;
			}
		}
		
		if (extension_loaded('suhosin'))
		{
			//Attempt setting the whitelist value
			@ini_set('suhosin.executor.include.whitelist', 'tmpl://, file://');
		
			//Checking if the whitelist is ok
			if(!@ini_get('suhosin.executor.include.whitelist') || strpos(@ini_get('suhosin.executor.include.whitelist'), 'tmpl://') === false)
			{
				JError::raiseWarning(0, sprintf(JText::_('COM_NINJA_THE_INSTALL_FAILED_BECAUSE_SUHOSIN_LOADED'), 'https://nooku.assembla.com/wiki/show/nooku-framework/Known_Issues'));
				return $installable = false;
			}
		}
		
		if(version_compare('5.3', phpversion(), '<=') && extension_loaded('ionCube Loader')) {
		
			if(ioncube_loader_iversion() < 40002) {
				JError::raiseWarning(0, sprintf(JText::_('COM_NINJA_YOUR_SERVER_IS_AFFECTED_BY_A_BUG_IN_IONCUBE_LOADER_FOR_PHP_53_THAT_CAUSES_OUR_TEMPLATE_LAYOUT_PARSING_TO_FAIL_PLEASE_UPDATE_TO_A_VERSION_LATER_THAN_IONCUBE_LOADER_40_YOUR_SERVER_IS_BEFORE_REINSTALLING'), ioncube_loader_version()));
				return $installable = false;
			}
		}
		
		return $installable = true;
	}
}

//Do not execute the rest of the custom install script if the com_install check fails
if(!com_install()) return;

$db = JFactory::getDBO();


// method for appending a row to the install list
?>
<script type="text/javascript">
		function updateList(text, html) {
			window.addEvent("domready", function() {
				var row = ($$('#tasks tr').getLast().get('class').substring(3,4) == '1') ? 0 : 1;

				$('tasks').adopt(
					new Element('tr', {'class': 'row'+row}).adopt([
						new Element('td', {'class': 'key'}).set('text', text),
						new Element('td').set('html', '<strong>'+html+'</strong>')
					])
				);
			});
		}
	</script> 
<?

//Next, install com_ninja and plg_ninja
$ninja = $this->parent->getPath('source').'/ninja';
if(JFolder::exists($ninja))
{
	$installer = new JInstaller;
	$installer->install($ninja);

	// force com_ninja to be disabled
	if (version_compare(JVERSION,'1.6.0','ge')) {
		$query = "UPDATE `#__extensions` SET `enabled` = '0' WHERE type = 'component' AND element = 'com_ninja'";
		$db->setQuery($query);
		$db->query();
	}

	$language->load('com_ninja', JPATH_ADMINISTRATOR, 'en-GB', true);

	//install plg_ninja
	$installer->install($ninja.'/plugin')

	// add the line to the install list
	?>
	<script type="text/javascript">
		updateList('<?php echo JText::_("COM_NINJA_FRAMEWORK") ?>', '<?php echo JText::_("COM_NINJA_INSTALLED") ?>');
		updateList('<?php echo JText::_("COM_NINJA_PLUGIN") ?>', '<?php echo JText::_("COM_NINJA_INSTALLED") ?>');
	</script> 
	<?php
}

//Install Nooku unless we're on Nooku Server
$nooku         = $this->parent->getPath('source').'/nooku';
$isNookuServer = create_function('$var', 'return strpos($var, "Nooku-Server") !== false;');
if(!array_filter(headers_list(), $isNookuServer) && JFolder::exists($nooku))
{
	$installer = new JInstaller;
	$installer->install($nooku);

	// force com_koowa to be disabled
	if (version_compare(JVERSION,'1.6.0','ge')) {
		$query = "UPDATE `#__extensions` SET `enabled` = '0' WHERE type = 'component' AND element = 'com_koowa'";
		$db->setQuery($query);
		$db->query();
	}

	// add the line to the install list
	?>
	<script type="text/javascript">
		updateList('<?php echo JText::_("COM_NINJA_NOOKU_FRAMEWORK") ?>', '<?php echo JText::_("COM_NINJA_INSTALLED") ?>');
	</script> 
	<?php
}

$source			= $this->parent->getPath('source');
$extension		= simplexml_load_file($this->parent->getPath('manifest'));
$versiontext	= '<em>'.JText::_('YOU_NEED_AT_LEAST_TO_INSTALL_'.humanize($extension->name).'_YOU_ARE_USING').'</em>';

// If we have additional packages, move them to a safe place (or JInstaller will delete them)
// and later install them by using KInstaller
$document = JFactory::getDocument();
$packages = false;
if(JFolder::exists($source.'/packages'))
{
	$packages = JPATH_ADMINISTRATOR.'/components/com_koowa/packages';
	if(JFolder::exists($packages)) JFolder::delete($packages);
	JFolder::copy($source.'/packages', $packages);
	JFolder::delete($source.'/packages');

	//Because 1.6 sucks, we have to do these inline
	//$document->addScript(JURI::root(1).'/media/com_koowa/js/install.js');
}

//Because 1.6 sucks, we have to do these inline
//$document->addStyleSheet(JURI::root(1).'/media/com_koowa/css/install.css');
//$document->addStyleDeclaration('.log {padding-left:270px}');

//Always render install log
$class = 'debug';
//$config = JFactory::getConfig();
//$class = $config->getValue('debug', null) ? 'debug' : null;
$jversion	= version_compare(JVERSION,'1.6.0','ge') ? '1.6' : '1.5';


//Run upgrade procedures if we're upgrading from a dashboard
//@TODO make dashboard upgraders support com.extensions instead of doing the following
if(JRequest::getCmd('view', false) == 'dashboard' || array_filter(headers_list(), $isNookuServer))
{
	//To prevent script timeouts, set limit to 5 minutes for those slow godaddy hosts
	@set_time_limit(300);

	Jloader::register('JArchive', JPATH_LIBRARIES.'/joomla/filesystem/archive.php');
	$root	= JPATH_ADMINISTRATOR.'/components/com_koowa/packages';
	$files  = JFolder::files($root);
	
	foreach($files as $file)
	{
		$installer	= new JInstaller;
		$folder		= JFile::stripExt($file);

		//If there already is a folder, delete it before extracting
		if(JFolder::exists($root.'/'.$folder)) JFolder::delete($root.'/'.$folder);

		JArchive::extract($root.'/'.$file, $root.'/'.$folder);
		JFile::delete($root.'/'.$file);
		$installer->install($root.'/'.$folder.'/');

		//Delete exctracted zip post install as we no longer need it
		if(JFolder::exists($root.'/'.$folder)) JFolder::delete($root.'/'.$folder);
	}
}

//if we have packages loop through them and install them
if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_koowa/packages')) {
	Jloader::register('JArchive', JPATH_LIBRARIES.'/joomla/filesystem/archive.php');
	$root	= JPATH_ADMINISTRATOR.'/components/com_koowa/packages';
	$files  = JFolder::files($root);
	
	foreach($files as $file)
	{
		$installer	= new JInstaller;
		$folder		= JFile::stripExt($file);

		//If there already is a folder, delete it before extracting
		if(JFolder::exists($root.'/'.$folder)) JFolder::delete($root.'/'.$folder);

		JArchive::extract($root.'/'.$file, $root.'/'.$folder);
		JFile::delete($root.'/'.$file);
		$installer->install($root.'/'.$folder.'/');

		//Delete exctracted zip post install as we no longer need it
		if(JFolder::exists($root.'/'.$folder)) JFolder::delete($root.'/'.$folder);

		?>
			<script type="text/javascript">
				updateList('<?php echo JText::_($folder) ?>', '<?php echo JText::_("COM_NINJA_INSTALLED") ?>');
			</script> 
		<?php
	}
}
?>

<div class="installation-panel">
	<link rel="stylesheet" href="<?php echo JURI::root(1) ?>/media/com_koowa/css/install.css" />

	<a class="extension-logo" href="<?php echo JRoute::_('&option='.$extname) ?>"><img src="<?php echo JURI::root() ?>media/com_<?php echo $this->name ?>/images/256/<?php echo $this->name ?>.png" alt="<?php echo JText::_('COM_NINJA_EXTENSION_LOGO') ?>" title="<?php echo JText::_('COM_NINJA_EXTENSION_LOGO') ?>" /></a>
	<table class="install-list">
		<tbody valign="top">
			<tr>
				<td width="100%">
					<table class="adminlist ninja-list">
						<thead>
							<tr>
								<th><?php echo JText::_('COM_NINJA_TASK') ?></th>
								<th width="30%"><?php echo JText::_('COM_NINJA_STATUS') ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5"></td>
							</tr>
						</tfoot>
						<tbody id="tasks">
							<tr class="row0">
								<td class="key hasTip" title="<?php echo sprintf($versiontext, 'PHP v5.2', phpversion()) ?>"><?php echo JText::_('COM_NINJA_PHP_VERSION') ?></td>
								<td>
									<?php echo version_compare(phpversion(), '5.2', '>=')
										? '<strong>'.JText::_('COM_NINJA_OK').'</strong> - '.phpversion()
										: sprintf($versiontext, 'PHP v5.2', phpversion()); ?>
								</td>
							</tr>
							<tr class="row1">
								<td class="key hasTip" title="<?php echo sprintf($versiontext, 'MySQL server v5.0.41', $db->getVersion()) ?>"><?php echo JText::_('COM_NINJA_MYSQL_SERVER_VERSION') ?></td>
								<td>
									<?php echo version_compare($db->getVersion(), '5.0.41', '>=')
									? '<strong>'.JText::_('COM_NINJA_OK').'</strong> - '.$db->getVersion()
									: sprintf($versiontext, 'MySQL server v5.0.41', $db->getVersion()); ?>
								</td>
							</tr>
							<tr class="row0">
								<td class="key hasTip" title="<?php echo JText::_($extension->description) ?>"><?php echo sprintf('%s %s', JText::_(humanize($extension->name)), JText::_(ucfirst($extension['type']))) ?></td>
								<td><strong><?php echo JText::_('COM_NINJA_INSTALLED'); ?></strong> - <?php echo $extension->version ?></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<style type="text/css">
		.extension-logo {float:left; width: 20%;}
		.install-list {width:80%;}
		.log {padding-left:270px}
		.installation-panel {width:100%;}
	</style>
<?php
	//Delete admin cache to allow upgrade procedures to run
	$cache = JPATH_CACHE.'/'.$extname;
	if(JFolder::exists($cache)) JFolder::delete($cache);

	//If the extension has an older joomla version with an admin.name.php entry point file, remove it.
	if(!isset($extension->migrate)) return;
	
	jimport('joomla.filesystem.file');
	$admin	   = JPATH_ADMINISTRATOR."/components/$extname/admin.$this->name.php";	
	if(file_exists($admin)) JFile::delete($admin);