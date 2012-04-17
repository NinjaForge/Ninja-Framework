<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Default Ninja Controller
 */
class NinjaControllerDefault extends ComDefaultControllerDefault
{

	/**
	 * The redirect mesage
	 *
	 * @var string
	 */
	protected $_message;

	protected $_uploadDestination = false;
	
	/**
	 * Boolean that decides wether or not to auto encode result messages to json.
	 *
	 * If using HMVC tricks, make sure to set this pass array('auto_json_result' => false) in 
	 * the controller configuration array
	 *
	 * @var boolean
	 */
	protected $_auto_json_result = true;

	/**
	 * Constructor
	 *
	 * @param array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		//Wether or not to json encode result
		$this->_auto_json_result = $config->auto_json_result;

		// Register extra actions
		$this->registerActionAlias('disable', 'enable');
		
		//Register redirect messages
		$this->registerCallback(array('after.add', 'after.apply', 'after.edit', 'after.enable', 'after.disable'), array($this, 'setMessage'));
		
		if(KRequest::type() == 'FLASH') KRequest::set('post.action', KRequest::get('get.action', 'cmd'));
	}

	/**
	 * Push the request data into the model state
	 *
	 * @todo	is this even needed anymore??
	 * @param	string		The action to execute
	 * @return	mixed|false The value returned by the called method, false in error case.
	 * @throws 	KControllerException
	 */
	public function execute($action, $data = NULL)
	{
	    //Create a context object
	    if(!($data instanceof KCommandContext))
	    {
	        $context = $this->getCommandContext();
	        $context->data   = $data;
	        $context->result = false;
	    } 
	    else $context = $data;
	
		$result = parent::execute($action, $context);

		$return_json = array(
			KRequest::type() == 'AJAX',
			KRequest::get('get.format', 'cmd') == 'json',
			!is_string($result),
			$this->_redirect_message,
			$this->_auto_json_result
		);

		if(count($return_json) === count(array_filter($return_json)))
		{
			$data = is_object($result) && method_exists($result, 'getData') ? $result->getData() : $result;
			//@TODO get rid of msg legacy, use message in the future
			$result = json_encode(array(
				'msg' => $this->_redirect_message,
				'message' => $this->_redirect_message,
				'result' => $data
			));

			//@TODO workaround for preventing 404 response headers
			//$context->status = KHttpRequest::OK;
			
			//Workaround for KDispatcherAbstract::_actionForward
			//@TODO submit patch, or similar for this limitation
			//@see KDispatcheAbstract line 43 - 45 where the forward is made if request isn't POST
			if(KRequest::method() != 'GET')
			{
				$identifier = clone $this->getIdentifier();
				$identifier->path = array();
				$identifier->name = 'dispatcher';
				$dispatcher = $this->getService($identifier)->unregisterCallback('after.dispatch', 'forward');
			}
		}

		return $result;
	}
	
	/**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'auto_json_result'	=> true,
        ));

        parent::_initialize($config);
    }
	
	/**
	 * Display the view
	 *
	 * @return void
	 */
	protected function _actionGet(KCommandContext $context)
	{
		$view = $this->getView();

		if($view instanceof KViewTemplate) {
			$view->getTemplate()->addFilter(array($this->getService('ninja:template.filter.document')));
		}
		
		return parent::_actionGet($context);
	}

	/**
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @access	protected
	 * @param	array	$files 	The object to process
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	protected function parseSQLFiles($files, $path = null, $driver = 'mysql', $charset = 'utf8')
	{
		// Initialize variables
		$files = (array) $files;
		if ( !$path ) $path = dirname(JFactory::getApplication()->getpath('admin')) . DS . 'sql';
		$db = JFactory::getDBO();
		$dbDriver = strtolower($db->get('name'));
		if ($dbDriver == 'mysqli') {
			$dbDriver = 'mysql';
		}
		$dbCharset = ($db->hasUTF()) ? 'utf8' : '';

		// Get the name of the sql file to process
		foreach ($files as $file)
		{
			if( $charset == $dbCharset && $driver == $dbDriver) {
				// Check that sql files exists before reading. Otherwise raise error for rollback
				if ( !file_exists( $path.DS.$file ) ) {
					return false;
				}
				$buffer = file_get_contents($path.DS.$file);
				
				// Graceful exit and rollback if read not successful
				if ( $buffer === false ) {
					return false;
				}

				// Create an array of queries from the sql file
				jimport('joomla.installer.helper');
				$queries = JInstallerHelper::splitSql($buffer);
				if (count($queries) == 0) {
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						
						if (!$db->query()) {
							JError::raiseWarning(1, JText::_('COM_NINJA_SQL_ERROR')." ".$db->stderr(true));
							return false;
						}
					}
				}
			}
		}

		return (int) count($queries);
	}
	
	/**
	 * Filter that gets the redirect URL from the sesison and sets it in the
	 * controller
	 *
	 * @return void
	 */
	public function setMessage(KCommandContext $context)
	{
		$defaults = array('url' => null, 'message' => null);
		$redirect = array_merge($defaults, $this->getRedirect());
		if(!$redirect['message'])
		{
			$message = new KObject;
			$message->count = count((array) KRequest::get('post.id', 'int', 1));
			$message->action=  ' ' . $this->getService('ninja:template.helper.inflector')->verbalize(KRequest::get('post.action', 'cmd', $context->action)) . '.';
			$message->name	= $this->getService($this->getModel())->getIdentifier()->name;

			$message->singular = KInflector::humanize(KInflector::singularize($message->name)) . $message->action;
			$message->plural   = KInflector::humanize(KInflector::pluralize($message->name)) . $message->action;
			
			$redirect['message'] = sprintf(JText::_($message->count > 1 ? '%s ' . $message->plural : $message->singular), $message->count);
			$this->_redirect_message = $redirect['message'];
		}
	}
	
	
	public function setPermissions(ArrayObject $args)
	{
		$identifier = $this->getIdentifier();
		$prefix		= $identifier->type.'_'.$identifier->package.'.'.$identifier->name.'.';
		$data		= $args['result']->getData();
		
		$data = array(
			'name' => $prefix.$data['id'],
			'title' => $data['title'],
			'rules'=> json_encode($data['access'])
		);

		$assets	= $this->getService('ninja:template.helper.access')->models->assets; 
		$model	= $this->getService($assets);
		$table  = $this->getService($model->getTable());
		
		$query = $this->getService('koowa:database.adapter.mysqli')->getQuery()->where('name', '=', $data['name']);
		$table
			->fetchRow($query)
			->setData($data)
			->save();
		
		
	}
	
	public function getUploadDestination()
	{
		return false;
	}
	
	protected function _actionUpload()
	{
		if(!$destination = $this->getUploadDestination()) return;
		jimport('joomla.filesystem.file');

		$result = array();
		
		$result['time'] = date('r');
		$result['addr'] = substr_replace(gethostbyaddr($_SERVER['REMOTE_ADDR']), '******', 0, 6);
		$result['agent'] = $_SERVER['HTTP_USER_AGENT'];
		
		if (count($_GET)) {
			$result['get'] = $_GET;
		}
		if (count($_POST)) {
			$result['post'] = $_POST;
		}
		if (count($_FILES)) {
			$result['files'] = $_FILES;
		}
		
		// Validation
		
		$error = false;
		
		if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$error = 'Invalid Upload';
		}
		
		
		// Processing
		
		/**
		 * Its a demo, you would move or process the file like:
		 *
		 * move_uploaded_file($_FILES['Filedata']['tmp_name'], '../uploads/' . $_FILES['Filedata']['name']);
		 * $return['src'] = '/uploads/' . $_FILES['Filedata']['name'];
		 *
		 */

		$upload = $destination.$_FILES['Filedata']['name'];
		$uploaddir = dirname(JPATH_ROOT.$destination.$_FILES['Filedata']['name']);
		JFile::upload($_FILES['Filedata']['tmp_name'], $uploaddir.'/avatar.png');
		KRequest::set('files.uploaded', dirname($upload).'/avatar.png', 'string');

		if ($error) {
		
			$return = array(
				'status' => '0',
				'error' => $error
			);
		
		} else {
		
			$return = array(
				'status' => '1',
				'name' => $_FILES['Filedata']['name'],
				'tmp_name' => $_FILES['Filedata']['tmp_name'],
				'destination' => $destination,
				'uploaded' => KRequest::root().KRequest::get('files.uploaded', 'string'),
				'test' => JPATH_ROOT.KRequest::get('files.uploaded', 'string')
			);
		
			// Our processing, we get a hash value from the file
			$return['hash'] = @md5_file(realpath(JPATH_ROOT.$destination.$_FILES['Filedata']['name']));
			
			// ... and if available, we get image data
			$info = @getimagesize(JPATH_ROOT.KRequest::get('files.uploaded', 'string'));
		
			if ($info) {
				$return['width'] = $info[0];
				$return['height'] = $info[1];
				$return['mime'] = $info['mime'];
			}
		
		}
		
		
		// Output
		
		/**
		 * Again, a demo case. We can switch here, for different showcases
		 * between different formats. You can also return plain data, like an URL
		 * or whatever you want.
		 *
		 * The Content-type headers are uncommented, since Flash doesn't care for them
		 * anyway. This way also the IFrame-based uploader sees the content.
		 */
		
		if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml') {
			// header('Content-type: text/xml');
		
			// Really dirty, use DOM and CDATA section!
			echo '<response>';
			foreach ($return as $key => $value) {
				echo "<$key><![CDATA[$value]]></$key>";
			}
			echo '</response>';
		} else {
			// header('Content-type: application/json');
			echo json_encode($return);
		}
		
		//JFactory::getApplication()->close();		
	}
	
	/**
	 * Get the real action that is was/will be performed relevant for acl checks.
	 *
	 * @return	 string Action name
	 */
	public function getRealAction()
	{
		$action = $this->getAction();
		if(empty($action))
		{
			switch(KRequest::method())
			{
				case 'GET'    :
				{
					//Determine if the action is browse or read based on the view information
					$view   = KRequest::get('get.view', 'cmd');
					$action = KInflector::isPlural($view) ? 'browse' : 'read';	
				} break; 
				
				case 'POST'   :
				{
					//If an action override exists in the post request use it
					if(!$action = KRequest::get('post.action', 'cmd')) {
						$action = 'add';
					}	
				} break;
				
				case 'PUT'    : $action = 'edit'; break;
				case 'DELETE' : $action = 'delete';	break;
			}
		}
		if($action == 'apply') $action = 'save';
		if($action == 'save')  $action = (bool) KRequest::get('get.id', 'int') ? 'edit' : 'add';

		return $action;
	}

	/**
	 * Enable/Disable action
	 *
	 * @TODO this is legacy, refactor toolbar to support the new RESTful structure in koowa controllers
	 */
	protected function _actionEnable($data)
	{
		$data['enabled'] = $this->getAction() == 'enable';

		return $this->execute('edit', $data);
	}
	
	public function clearCache(KCommandContext $context)
	{
		JLoader::register('JFolder', JPATH_LIBRARIES.'/joomla/filesystem/folder.php');
	
		$package	= $this->getIdentifier()->package;
		$name		= KInflector::pluralize($this->getIdentifier()->name);
		$path		= JPATH_ROOT.'/cache/com_'.$package.'/'.$name.'/';
		$ids		= $context->result->id;
		foreach((array)$ids as $id)
		{
			if(JFolder::exists($path.$id)) JFolder::delete($path.$id);
		}
	}
	
	/**
	 * Helper method for uploading a file
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param  array $config		Configuration array
	 *					->name		Where to find the file object in $_FILES
	 *					->to		Where the file upload destination
	 *					->rename	If given a string, that will be the new name, false to keep the current name
	 *					->randomize	Wether to create a random name for the uploaded file or not
	 *					->image		Set to true if an additional image validation is needed
	 *					->root		The root of the move operation, change this if you need to go up the root
	 * @return array				Result of the operation
	 */
	protected function _upload(array $config)
	{
		$config		= new KConfig($config);
		$identifier	= $this->getIdentifier();
		$package	= $identifier->package;
		$folder		= KInflector::pluralize($identifier->name);
		
		$config->append(array(
			'name'		=> 'image',
			'to'		=> '/images/stories/com_'.$package.'/'.$folder.'/',
			'rename'	=> false,
			'randomize'	=> false,
			'image'		=> false,
			'root'		=> JPATH_ROOT
		));
		
		//Prepare MediaHelper
		JLoader::register('MediaHelper', JPATH_ROOT.'/components/com_media/helpers/media.php');

		$error			= null;
		
		$file = KRequest::get('files.'.$config->name, 'raw');
		if(!MediaHelper::canUpload($file, $error)) {
			$message = JText::_("%s failed to upload because %s");
			JError::raiseWarning(21, sprintf($message, $file['name'], lcfirst($error)));
			return array();
		}
		if($config->image && !MediaHelper::isImage($file['name'])) {
			$message = JText::_("%s failed to upload because it's not an image.");
			JError::raiseWarning(21, sprintf($message, $file['name']));
			return array();
		}			

		$name		= $config->rename ? $config->rename : $file['name'];
		$upload 	= JFile::makeSafe($config->randomize ? uniqid(time()).'.'.JFile::getExt($name) : $name);
		$relative	= $config->to.$upload;
		$absolute	= $config->root.$relative;
		JFile::upload($file['tmp_name'], $absolute);

		return array(
			'filename'	=> $upload,
			'filepath'	=> array(
				'relative'	=> $relative,
				'absolute'	=> $absolute
			)
		);
	}
	
	/**
	 * Generic Save & New action
	 *
	 * @param	mixed 	Either a scalar, an associative array, an object
	 * 					or a KDatabaseRow
	 * @return KDatabaseRow 	A row object containing the saved data
	 */
	protected function _actionSavenew(KCommandContext $context)
	{
	    //@TODO calling parent could cause trouble with specialized save actions not being called on deriving controllers
		$result = parent::_actionSave($context);
		
		$identifier = $this->getIdentifier();
				
		//redirect to this view again
		$this->_redirect = 'index.php?option='.$identifier->type.'_'.$identifier->package.'&view='.$this->getIdentifier()->name;
		
		return $result;
	}
}