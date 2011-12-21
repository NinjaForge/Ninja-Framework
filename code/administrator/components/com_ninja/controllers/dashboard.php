<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dashboard.php 1041 2011-05-22 17:40:00Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
/**
 * Dashboard Controller
 */
class ComNinjaControllerDashboard extends KControllerView
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		if(KRequest::get('get.action', 'cmd') == 'update') $this->update();
		if(KRequest::get('get.action', 'cmd') == 'updatespurss') $this->execute('updatespurss');
		if(KRequest::get('get.action', 'cmd') == 'checkversion') $this->execute('checkversion');
		if(KRequest::get('get.action', 'cmd') == 'checkversionspurss') $this->execute('checkversionspurss');
	}

	/**
	 * Generic framework update action
	 *
	 * 		Using CURL and SourceForge to get the latest release from a spurss enclosure
	 *		and runs a update procedure.
	 *
	 * @TODO		Support the SVN package later,
	 *				for increased performance
	 *				and to let us update from Assembla.
	 */
	protected function _actionUpdatespurss()
	{
		// Import used APIs
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		jimport('joomla.installer.installer');


		$path = KFactory::get('admin::com.ninja.helper.application')->getPath('com_xml');
		$version = '0';
		$revision = '0';
		$fileUrl = null;
		
		//load the file, and save it to our object
		$xml = simplexml_load_file($path);
		if(!isset($xml->updateurlspurss)) return false;
		$url = (string)$xml->updateurlspurss;
		$spurss = simplexml_load_file($url);
		//there could be more than on entry 
		foreach($spurss->entry as $entry){
			$id = $entry->id;
			//break up the id into it's segments
			$segments = explode(':',$id);
			//all spurss ids should start with spurss
			if($segments[0] == 'spurss' && $segments[1] == 'ninjaforge.com' && $segments[2] == $this->getIdentifier()->type.'_'.$this->getIdentifier()->package){

				$versiontmp = explode('rev',$segments[3]);
				$version = $versiontmp[0];
				if(isset($versiontmp[1])){
					$revision = $versiontmp[1];
				}
				
				if($entry->link){
					foreach($entry->link as $link){
						if((string)$link['rel'] == 'enclosure'){
							$fileUrl = (string)$link['href'];
							//find the first enclosure and drop out
							continue;
						}
					}
				}
				//we will only take the first instance. In a well formed feed the first will be the most recent.
				continue;
			}
		}
		
		$version_compare  = version_compare((string) $xml->version, $version, '<=');
		$revision_compare = (int) $xml->revision < $revision;

		if( !(($version_compare && $revision_compare) || ($version_compare && ($revision == 0)))){
			$msg = array(
				'text' => sprintf(JText::_('%s could not be upgraded. Correct file version not found'), JText::_($this->getIdentifier()->package)),
				'update' => false
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
		
		if (!$fileUrl){
			$msg = array(
				'text' => sprintf(JText::_('%s could not be upgraded. No file attached to update information. Please download from site.'), JText::_($this->getIdentifier()->package)),
				'update' => false
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
		
		
		
		// Prepare curl
		$curl = KFactory::get('admin::com.ninja.helper.curl');
		$opt  = array(
						CURLOPT_RETURNTRANSFER => true,
						//CURLOPT_FOLLOWLOCATION => true, //this don't work if open_basedir or safe_mode is on
						CURLOPT_HTTPHEADER     => array("Expect:"),
						CURLOPT_HEADERFUNCTION => array($curl, 'readHeader')
				);
		$curl->addSession( $fileUrl, $opt );

		// Download tarball package and save it to the /tmp/ folder
		$result 	= $curl->exec();
		$info		= $curl->info();
		//disabled for testing right now, but when we sort it out for NEC which will point people to a download page and not an actual file we can restore this
		//$filename	= $info[0]['content_disposition'];
		//temporary filename from the spurss update
		$filename = basename($fileUrl);
		
		$foldername = JFile::stripExt($filename);
		JFile::write(JPATH_ROOT.'/tmp/'.$filename, $result);
		$curl->clear();

		// Unpack the tarball
		JArchive::extract(JPATH_ROOT.'/tmp/'.$filename, JPATH_ROOT.'/tmp/'.$foldername.'/');
		JFile::delete(JPATH_ROOT.'/tmp/'.$filename);

		// Install the update
		$installer = JInstaller::getInstance();
		//TODO - disabled for testing, replace later
		//$installer->install(JPATH_ROOT.'/tmp/'.$foldername.'/');

		// Cleanup
		JFolder::delete(JPATH_ROOT.'/tmp/'.$foldername.'/');

		$msg = array(
			'text' => sprintf(JText::_('%s upgraded successfully.'), JText::_($this->getIdentifier()->package)).' : '.$foldername.' : '.$filename,
			'update' => true
		);
		
		if(KRequest::type() == 'AJAX') die(json_encode($msg));
		return true;
	}

	/**
	 * Generic framework update action
	 *
	 * 		Using CURL and SourceForge to get the latest release
	 *		and runs a update procedure.
	 *
	 * @TODO		Support the SVN package later,
	 *				for increased performance
	 *				and to let us update from Assembla.
	 */
	protected function _actionUpdate()
	{
		// Import used APIs
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		jimport('joomla.installer.installer');


		$path = KFactory::get('admin::com.ninja.helper.application')->getPath('com_xml');
		
		//load the file, and save it to our object
		$xml = simplexml_load_file($path);
		if(!isset($xml->updateurl)) return false;
		$url = (string) $xml->updateurl;

		// Prepare curl
		$curl = KFactory::get('admin::com.ninja.helper.curl');
		$opt  = array(
						CURLOPT_RETURNTRANSFER => true,
						//CURLOPT_FOLLOWLOCATION => true, //this don't work if open_basedir or safe_mode is on
						CURLOPT_HTTPHEADER     => array("Expect:"),
						CURLOPT_HEADERFUNCTION => array($curl, 'readHeader')
				);
		$curl->addSession( $url, $opt );

		// Download tarball package and save it to the /tmp/ folder
		$result 	= $curl->exec();
		$info		= $curl->info();
		$filename	= $info[0]['content_disposition'];
		$foldername = JFile::stripExt($filename);
		JFile::write(JPATH_ROOT.'/tmp/'.$filename, $result);
		$curl->clear();

		// Unpack the tarball
		JArchive::extract(JPATH_ROOT.'/tmp/'.$filename, JPATH_ROOT.'/tmp/'.$foldername.'/');
		JFile::delete(JPATH_ROOT.'/tmp/'.$filename);

		// Install the update
		$installer = JInstaller::getInstance();
		$installer->install(JPATH_ROOT.'/tmp/'.$foldername.'/');

		// Cleanup
		JFolder::delete(JPATH_ROOT.'/tmp/'.$foldername.'/');

		$msg = array(
			'text' => sprintf(JText::_('%s upgraded successfully.'), JText::_($this->getIdentifier()->package)),
			'update' => true
		);
		
		if(KRequest::type() == 'AJAX') die(json_encode($msg));
		return true;
	}

	protected function _actionDisplay(KCommandContext $context)
	{
		$view = $this->getView();

		if(!$view instanceof ComNinjaViewHtml && $view instanceof KViewTemplate) {
			$view->getTemplate()->addFilters(array(KFactory::get('admin::com.ninja.template.filter.document')));
		}

		KRequest::set('get.hidemainmenu', 0);
		if(!KRequest::has('get.layout', 'cmd'))
		{
			KRequest::set('get.layout', 'admin::com.ninja.views.dashboard.basic');
			
			if(KRequest::get('get.tmpl', 'cmd') == 'component') {
				KRequest::set('get.layout', 'admin::com.ninja.views.dashboard.popup');
			}

			$view->setLayout(KRequest::get('get.layout', 'string', 'default' ));
		}
		
		return $view->display();
	}
	
	/**
	 * Display a single item
	 *
	 * @TODO Overloaded because by default KControllerBread calls $model->getItem()->isNew() when that's blank so it throws an fatal error
	 */
	protected function _actionRead(KCommandContext $context)
	{
	    $row = $this->getModel()->getItem();

		return $row;
	}
	
	/**
	 * Push the request data into the model state
	 *
	 * @TODO this is because KControllerBread have $data = null, while KControllerAbstract requires it to be KCommandContext
	 */
	public function execute($action, $data = null)
	{
		if(!is_a($data, 'KCommandContext')) $data = new KCommandContext;

		return parent::execute($action, $data);
	}

	protected function _actionCheckversionspurss()
	{
		$path = KFactory::get('admin::com.ninja.helper.application')->getPath('com_xml');
		$version = '0';
		$revision = '0';
		//load the file, and save it to our object
		$xml = simplexml_load_file($path);
		if(!isset($xml->updateurlspurss)) return false;
		$url = (string)$xml->updateurlspurss;
		$spurss = simplexml_load_file($url);
		//there could be more than on entry 
		foreach($spurss->entry as $entry){
			$id = $entry->id;
			//break up the id into it's segments
			$segments = explode(':',$id);
			//all spurss ids should start with spurss
			if($segments[0] == 'spurss' && $segments[1] == 'ninjaforge.com' && $segments[2] == $this->getIdentifier()->type.'_'.$this->getIdentifier()->package){

				$versiontmp = explode('rev',$segments[3]);
				$version = $versiontmp[0];
				if(isset($versiontmp[1])){
					$revision = $versiontmp[1];
				}
				
				//we will only take the first instance. In a well formed feed the first will be the most recent.
				continue;
			}
		}
		
		$version_compare  = version_compare((string) $xml->version, $version, '<=');
		$revision_compare = (int) $xml->revision < $revision;

		if(($version_compare && $revision_compare) || ($version_compare && ($revision == 0)))
		{
			$msg = array(
				'text' => sprintf(JText::_('%1$s %2$s rev%3$s available for download.'), JText::_($this->getIdentifier()->package), $version, $revision),
				'update' => true
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return true;
		}
		else
		{
			$msg = array(
				'text' => sprintf(JText::_('This is the newest version of %1$s.'), JText::_($this->getIdentifier()->package), $version, $revision).': '.$version.': '.$revision,
				'update' => false
			);
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
		
	}
	
	protected function _actionCheckversion()
	{
		$path = KFactory::get('admin::com.ninja.helper.application')->getPath('com_xml');

		//load the file, and save it to our object
		$xml = simplexml_load_file($path);
		if(!isset($xml->updateurl)) return false;
		$url = (string) $xml->updateurl;

		// Prepare curl
		$curl = KFactory::get('admin::com.ninja.helper.curl');
		$opt  = array(
						CURLOPT_RETURNTRANSFER => true,
						//CURLOPT_FOLLOWLOCATION => true, //this don't work if open_basedir or safe_mode is on
						CURLOPT_HTTPHEADER     => array("Expect:"),
						CURLOPT_HEADERFUNCTION => array($curl, 'readHeader')
				);
		$curl->addSession( $url, $opt );
		
		// @TODO in NEC2, let's have a api for this, so it's way faster
		$curl->exec();
		
		$info		= $curl->info();
		$filename	= $info[0]['content_disposition'];
		
		$revision = $this->extractRevisionFromFilename($filename);
		$status   = $this->extractStatusFromFilename($filename);
		$version  = $this->extractVersionFromFilename($filename);

		$version_compare  = version_compare((string) $xml->version, $version, '<');
		$revision_compare = (int) $xml->revision < $revision;

		if($version_compare || (version_compare((string) $xml->version, $version, '=') && $revision_compare))
		{
			$msg = array(
				'text' => sprintf(JText::_('%1$s %2$s %3$s rev%4$s available for download.'), JText::_($this->getIdentifier()->package), $version, JText::_($status), $revision),
				'update' => true
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return true;
		}
		else
		{
			$msg = array(
				'text' => sprintf(JText::_('This is the newest version of %1$s.'), JText::_($this->getIdentifier()->package), $version, JText::_($status), $revision),
				'update' => false
			);
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
	}
	
	private function extractVersionFromFilename($file)
	{
		$pattern = '/_v([0-9\.]+)/';
		if (preg_match($pattern, $file, $result)) {
			return $result[1];
		} else {
			return false;
		}
	}
	
	private function extractStatusFromFilename($file)
	{
		$pattern = '/_v[0-9\.]+(.*?)_rev/';
		if (preg_match($pattern, $file, $result)) {
			return $result[1];
		} else {
			return false;
		}
	}

	private function extractRevisionFromFilename($file)
	{
		$pattern = '/rev([0-9]+)\.[A-Za-z]/';
		if (preg_match($pattern, $file, $result)) {
			return $result[1];
		} else {
			return false;
		}
	}
}