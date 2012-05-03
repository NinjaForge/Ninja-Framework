<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
/**
 * Dashboard Controller
 */
class NinjaControllerDashboard extends ComDefaultControllerResource
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

        //Don't hide the mainmenu please
        KRequest::set('get.hidemainmenu', 0);

		if(KRequest::get('get.action', 'cmd') == 'update') $this->update();
		if(KRequest::get('get.action', 'cmd') == 'updatespurss') $this->execute('updatespurss');
		if(KRequest::get('get.action', 'cmd') == 'checkversion') $this->execute('checkversion');
		if(KRequest::get('get.action', 'cmd') == 'checkversionspurss') $this->execute('checkversionspurss');
	}

    /**
     * Get a behavior by identifier
     *
     * @NOTE overloaded to change the identifier of the toolbar to com_ninja's
     *
     * @return KControllerBehaviorAbstract
     */
    public function getBehavior($behavior, $config = array())
    {
       $result = parent::getBehavior($behavior, $config);

       if($behavior == 'commandable') {
           $result->setToolbar($this->getService('ninja:controller.toolbar.dashboard'));
       }

       return $result;
    }

	/**
	 * Get the view object attached to the controller
	 * 
	 * This function will check if the view folder exists. If not it will throw
	 * an exception. This is a security measure to make sure we can only explicitly
	 * get data from views the have been physically defined. 
	 *
	 * @NOTE Customized in order to do some magic trickery regarding how the view is loaded
	 *
	 * @throws  KControllerException if the view cannot be found.
	 * @return	KViewAbstract
	 *  
	 */
	public function getView()
	{
	    if(!$this->_view instanceof KViewAbstract)
		{	   
		    //Make sure we have a view identifier
		    if(!($this->_view instanceof KServiceIdentifier)) {
		        $this->setView($this->_view);
			}
			
			//Create the view
			$config = array(
			    'model'      => $this->getModel()
	    	);
	    	
			$this->_view = $this->getService('ninja:view.dashboard.html', $config);
			
			//Set the layout
			if(isset($this->_request->layout)) {
	    	    $this->_view->setLayout($this->_request->layout);
	    	} 
			
			//Make sure the view exists
		    if(!file_exists(dirname($this->_view->getIdentifier()->filepath))) {
		        throw new KControllerException('View :'.$this->_view->getName().' not found', KHttpResponse::NOT_FOUND);
		    }
		}
		
		return $this->_view;
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


		$path = $this->getService('ninja:helper.application')->getPath('com_xml');
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
				'text' => sprintf(JText::_('COM_NINJA_COULD_NOT_BE_UPGRADED_CORRECT_FILE_VERSION_NOT_FOUND'), JText::_($this->getIdentifier()->package)),
				'update' => false
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
		
		if (!$fileUrl){
			$msg = array(
				'text' => sprintf(JText::_('COM_NINJA_COULD_NOT_BE_UPGRADED_NO_FILE_ATTACHED_TO_UPDATE_INFORMATION_PLEASE_DOWNLOAD_FROM_SITE'), JText::_($this->getIdentifier()->package)),
				'update' => false
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
		
		
		
		// Prepare curl
		$curl = $this->getService('ninja:helper.curl');
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
			'text' => sprintf(JText::_('COM_NINJA_UPGRADED_SUCCESSFULLY'), JText::_($this->getIdentifier()->package)).' : '.$foldername.' : '.$filename,
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


		$path = $this->getService('ninja:helper.application')->getPath('com_xml');
		
		//load the file, and save it to our object
		$xml = simplexml_load_file($path);
		if(!isset($xml->updateurl)) return false;
		$url = (string) $xml->updateurl;

		// Prepare curl
		$curl = $this->getService('ninja:helper.curl');
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
			'text' => sprintf(JText::_('COM_NINJA_UPGRADED_SUCCESSFULLY'), JText::_($this->getIdentifier()->package)),
			'update' => true
		);
		
		if(KRequest::type() == 'AJAX') die(json_encode($msg));
		return true;
	}

    /**
     * Specialised display function.
     *
     * @param	KCommandContext	A command context object
     * @return 	string|false 	The rendered output of the view or false if something went wrong
     */
	protected function _actionGet(KCommandContext $context)
	{
		$view = $this->getView();

		if($view instanceof KViewTemplate) {
			$view->getTemplate()->addFilter(array($this->getService('ninja:template.filter.document')));
		}

		if(!KRequest::has('get.layout', 'cmd') && KRequest::get('get.tmpl', 'cmd') == 'component')
		{
			$view->setLayout('popup');
		}
		
		return $view->display();
	}
	
	/**
	 * Push the request data into the model state
	 *
	 * @TODO this is because KControllerBread have $data = null, while KControllerAbstract requires it to be KCommandContext
	 */
	public function execute($action, $data = null)
	{
		if(!($data instanceof KCommandContext)) $data = new KCommandContext;

		return parent::execute($action, $data);
	}

	protected function _actionCheckversionspurss()
	{
		$path = $this->getService('ninja:helper.application')->getPath('com_xml');
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
				'text' => sprintf(JText::_('COM_NINJA_REV_AVAILABLE_FOR_DOWNLOAD'), JText::_($this->getIdentifier()->package), $version, $revision),
				'update' => true
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return true;
		}
		else
		{
			$msg = array(
				'text' => sprintf(JText::_('COM_NINJA_THIS_IS_THE_NEWEST_VERSION_OF_'), JText::_($this->getIdentifier()->package), $version, $revision).': '.$version.': '.$revision,
				'update' => false
			);
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return false;
		}
		
	}
	
	protected function _actionCheckversion()
	{
		$path = $this->getService('ninja:helper.application')->getPath('com_xml');

		//load the file, and save it to our object
		$xml = simplexml_load_file($path);
		if(!isset($xml->updateurl)) return false;
		$url = (string) $xml->updateurl;

		// Prepare curl
		$curl = $this->getService('ninja:helper.curl');
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
				'text' => sprintf(JText::_('COM_NINJA_REV_AVAILABLE_FOR_DOWNLOAD_STATE'), JText::_($this->getIdentifier()->package), $version, JText::_($status), $revision),
				'update' => true
			);
			
			if(KRequest::type() == 'AJAX') die(json_encode($msg));
			return true;
		}
		else
		{
			$msg = array(
				'text' => sprintf(JText::_('COM_NINJA_THIS_IS_THE_NEWEST_VERSION_OF_'), JText::_($this->getIdentifier()->package), $version, JText::_($status), $revision),
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