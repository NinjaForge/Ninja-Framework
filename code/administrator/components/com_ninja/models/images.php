<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaModelImages extends KModelAbstract
{

	/**
	 * Array of the images
	 *
	 * @var array
	 */
	protected $_images = array();

	public function __construct(KConfig $options)
    {
    	parent::__construct($options);
    	
    	//lets get our states
       	$this->_state
       	     ->insert('folder', 'dirname', JPATH_ROOT.'/images/stories/')
       	     ->insert('optgroup', 'boolean', true)
       	     ->insert('limit', 'int', 0);
    }
        
    public function getItem()
    {
    	return $this->_item;
    }
    
    public function getList()
    {
    	//Removed by DC Oct 2010 to allow for multiple image pickers on one page
    	//if(!isset($this->_list))
    	//{
    		jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );
	
			// path to images directory
			$identifier = $this->getIdentifier();
			$path		= $this->_state->folder;
			$filter		= '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
			$uripath	= str_replace(JPATH_ROOT, KRequest::root(), $path);

			//Added by DC Oct 2010 to allow for multiple image pickers on one page
			$this->_images = array();

			$root 		=  basename($path);
			if(!JFolder::exists($path)) JFolder::create($path);
			$files		= JFolder::files($path, $filter, true, true);
			$this->_list = array ();
			$optgroup = $root;
			if ( is_array($files) )
			{
				foreach ($files as $file)
				{
					if (($f = basename(dirname($file))) !== $optgroup)
					{
						if($this->_state->optroup) $this->_list[] = JHTML::_('select.optgroup', $f);
					}
					$filepath = str_replace($root.'/', '', $f.'/');
					$filename = basename($file);
					$this->_list[$filename]   = JHTML::_('select.option', $filepath . $filename, $filename);
					$this->_images[$filename] = $uripath . '/' . $filepath . $filename;
					if ($f !== $optgroup) $optgroup = $f;
				}
			}
			
			ksort($this->_images);
			ksort($this->_list);
		//}
    
    	return $this->_list;
    }
    
    public function getImages()
    {
    	//Removed by DC Oct 2010 to allow for multiple image pickers on one page
    	//if(!isset($this->_images)) 
    	
    	parent::getList();

		return $this->_images;
    }
}