<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: image.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * View for rendering images, applying effects and such
 *
 * @package Ninja
 */
class NinjaViewImage extends KViewFile
{
	/**
	 * Default mimetype
	 *
	 * @var string
	 */
	public $mimetype = 'image/png';
	
	/**
	 * Disposition header
	 *
	 * @var string
	 */
	public $disposition = 'inline';
	
	/**
	 * Cache expire
	 *
	 * @var int seconds
	 */
	public $expires;
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'disposition' => 'inline'
       	));
       	
       	parent::_initialize($config);
    }

	public function display()
	{
		//Prepare MediaHelper
		JLoader::register('MediaHelper', JPATH_ROOT.'/components/com_media/helpers/media.php');
	
		//Set the cache expire, which is 3 months in seconds
		$this->expires = 7889231;
	
		$item	= $this->getModel()->getItem();
		$image	= $this->getModel()->getImage();
		if(is_a($image, 'NinjaHelperImage')) {
			$path = $image->file;
		} else {
			$path	= $image;
			$image	= $this->getService('ninja:helper.image', array('image' => $path));
		}

		//$this->mimetype = 'image/'.MediaHelper::getTypeIcon($image->file);
		$this->mimetype = $image->mime;
		
		$identifier = $this->getIdentifier();
		$cache  = JPATH_ROOT.'/cache/com_'.$identifier->package.'/'.KInflector::pluralize($this->getName());
		$cache .= '/'.$item->id.'/'.$identifier->name.'&'.urldecode(http_build_query($image->actions)).'.'.$image->ext;
		if(!JFile::exists($cache)) {
			//To avoid "image directory unwritable" messages
			JFile::write($cache, '');
			
			$image->save($cache);
			JPath::setPermissions($cache);
		} else {
			//Time since created, in seconds
			$mtime	 = filemtime($cache);
			$created = time()-date('U', $mtime);
			
			//Set modified since header
			//header('Last-Modified: '.gmdate("D, d M Y H:i:s", $mtime).' GMT');
			
			if($created > $this->expires) {
				//To avoid permission errors on some systems, delete the image first instead of overwriting
				JFile::delete($cache);
				
				$image->save($cache);
				JPath::setPermissions($cache);
			}
		}
		
		$this->disposition = 'inline';
		$this->output = JFile::read($cache);
		$this->filename = basename($path);

		return parent::display();
	}
	
	/**
	 * Nooku sets the header disposition headers, we set the caching headers
		 *
	 * @return KViewFile
	 */
	protected function _setDisposition()
	{
		header('Cache-Control: private, max-age=10800, pre-check=10800');
		header("Pragma: private");
		header("Expires: " . date("D, d M Y H:i:s", strtotime('+'.$this->expires.' second')).' GMT');
	
		return parent::_setDisposition();
	}
}