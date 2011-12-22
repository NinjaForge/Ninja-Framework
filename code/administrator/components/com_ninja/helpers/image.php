<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * Manipulate images using standard methods such as resize, crop, rotate, etc.
 * This class must be re-initialized for every image you wish to manipulate.
 *
 * @package    self
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
 
 //@TODO this because Koowa changes camelCase classnames to plural, but not identifiers
 KLoader::loadIdentifier('ninja:helper.image.driver');
 
 /**
 * Improved image helper class, derived from Kohanas
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class NinjaHelperImage extends KTemplateHelperAbstract
{
	// Master Dimension
	const NONE = 1;
	const AUTO = 2;
	const HEIGHT = 3;
	const WIDTH = 4;
	// Flip Directions
	const HORIZONTAL = 5;
	const VERTICAL = 6;

	// Allowed image types
	public static $allowed_types = array
	(
		IMAGETYPE_GIF => 'gif',
		IMAGETYPE_JPEG => 'jpg',
		IMAGETYPE_PNG => 'png',
		IMAGETYPE_TIFF_II => 'tiff',
		IMAGETYPE_TIFF_MM => 'tiff',
	);

	// Driver instance
	protected $driver;

	// Driver actions
	public $actions = array();

	// Reference to the current image filename
	protected $image = '';

	/**
	 * Creates a new image editor instance.
	 *
	 * @throws  Kohana_Exception
	 * @param   string   filename of image
	 * @param   array    non-default configurations
	 * @return  void
	 */
	public function __construct($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'image'		=> null,
			'driver'	=> function_exists('exec') && is_file(exec('which convert')) ? 'imagemagick' : 'gd',
			'params'	=> array(),
		));
		$this->config = $config;
	
	    parent::__construct($config);
	
		//@TODO legacy
		$image = $config->image;
	
		static $check;

		// Make the check exactly once
		($check === NULL) and $check = function_exists('getimagesize');

		if ($check === FALSE)
			throw new KException('image getimagesize missing');

		// Check to make sure the image exists
		if ( ! is_file($image))
			throw new KException('image file not found');

		// Disable error reporting, to prevent PHP warnings
		$ER = error_reporting(0);

		// Fetch the image size and mime type
		$image_info = getimagesize($image);

		// Turn on error reporting again
		error_reporting($ER);

		// Make sure that the image is readable and valid
		if ( ! is_array($image_info) OR count($image_info) < 3)
			throw new KException('image file unreadable');

		// Check to make sure the image type is allowed
		if ( ! isset(self::$allowed_types[$image_info[2]]))
			throw new KException('image type not allowed');

		// self has been validated, load it
		$this->image = array
		(
			'file' => str_replace('\\', '/', realpath($image)),
			'width' => $image_info[0],
			'height' => $image_info[1],
			'type' => $image_info[2],
			'ext' => self::$allowed_types[$image_info[2]],
			'mime' => $image_info['mime']
		);


		// Initialize the driver
		$this->driver = $this->getService('ninja:helper.image.driver.'.$this->config['driver'], $this->config['params']);

		// Validate the driver
		if ( ! ($this->driver instanceof NinjaHelperImageDriver))
			throw new KException('image driver must be implement self_Driver class');
	}

	/**
	 * Handles retrieval of pre-save image properties
	 *
	 * @param   string  property name
	 * @return  mixed
	 */
	public function __get($property)
	{
		if (isset($this->image[$property]))
		{
			return $this->image[$property];
		}
		else
		{
			throw new KException('invalid property');
		}
	}

	/**
	 * Resize an image to a specific width and height. By default, Kohana will
	 * maintain the aspect ratio using the width as the master dimension. If you
	 * wish to use height as master dim, set $image->master_dim = self::HEIGHT
	 * This method is chainable.
	 *
	 * @throws  Kohana_Exception
	 * @param   integer  width
	 * @param   integer  height
	 * @param   integer  one of: self::NONE, self::AUTO, self::WIDTH, self::HEIGHT
	 * @return  object
	 */
	public function resize($width, $height, $master = NULL)
	{
		if ( ! $this->valid_size('width', $width))
			throw new KException('image invalid width');

		if ( ! $this->valid_size('height', $height))
			throw new KException('image invalid height');

		if (empty($width) AND empty($height))
			throw new KException('image invalid dimensions');

		if ($master === NULL)
		{
			// Maintain the aspect ratio by default
			$master = self::AUTO;
		}
		elseif ( ! $this->valid_size('master', $master))
			throw new KException('image invalid master');

		$this->actions['resize'] = array
		(
			'width'  => $width,
			'height' => $height,
			'master' => $master,
		);

		return $this;
	}

    /**
	 * Crop an image to a specific width and height. You may also set the top
	 * and left offset.
	 * This method is chainable.
	 *
	 * @throws  Kohana_Exception
	 * @param   integer  width
	 * @param   integer  height
	 * @param   integer  top offset, pixel value or one of: top, center, bottom
	 * @param   integer  left offset, pixel value or one of: left, center, right
	 * @return  object
	 */
	public function crop($width, $height, $top = 'center', $left = 'center')
	{
		if ( ! $this->valid_size('width', $width))
			throw new KException('image invalid width', $width);

		if ( ! $this->valid_size('height', $height))
			throw new KException('image invalid height', $height);

		if ( ! $this->valid_size('top', $top))
			throw new KException('image invalid top', $top);

		if ( ! $this->valid_size('left', $left))
			throw new KException('image invalid left', $left);

		if (empty($width) AND empty($height))
			throw new KException('image invalid dimensions');

		$this->actions['crop'] = array
		(
			'width'  => $width,
			'height' => $height,
			'top'    => $top,
			'left'   => $left,
		);

		return $this;
	}

    /**
	 * Allows rotation of an image by 180 degrees clockwise or counter clockwise.
	 *
	 * @param   integer  degrees
	 * @return  object
	 */
	public function rotate($degrees)
	{
		$degrees = (int) $degrees;

		if ($degrees > 180)
		{
			do
			{
				// Keep subtracting full circles until the degrees have normalized
				$degrees -= 360;
			}
			while($degrees > 180);
		}

		if ($degrees < -180)
		{
			do
			{
				// Keep adding full circles until the degrees have normalized
				$degrees += 360;
			}
			while($degrees < -180);
		}

		$this->actions['rotate'] = $degrees;

		return $this;
	}

    /**
	 * Flip an image horizontally or vertically.
	 *
	 * @throws  Kohana_Exception
	 * @param   integer  direction
	 * @return  object
	 */
	public function flip($direction)
	{
		if ($direction !== self::HORIZONTAL AND $direction !== self::VERTICAL)
			throw new KException('image invalid flip');

		$this->actions['flip'] = $direction;

		return $this;
	}

    /**
	 * Change the quality of an image.
	 *
	 * @param   integer  quality as a percentage
	 * @return  object
	 */
	public function quality($amount)
	{
		$this->actions['quality'] = max(1, min($amount, 100));

		return $this;
	}

	/**
	 * Sharpen an image.
	 *
	 * @param   integer  amount to sharpen, usually ~20 is ideal
	 * @return  object
	 */
	public function sharpen($amount)
	{
		$this->actions['sharpen'] = max(1, min($amount, 100));

		return $this;
	}

	/**
	 * Save the image to a new image or overwrite this image.
	 *
	 * @throws  Kohana_Exception
	 * @param   string   new image filename
	 * @param   integer  permissions for new image
	 * @param   boolean  keep or discard image process actions
	 * @return  object
	 */
	public function save($new_image = FALSE, $chmod = 0644, $keep_actions = FALSE)
	{
		// If no new image is defined, use the current image
		empty($new_image) and $new_image = $this->image['file'];

		// Separate the directory and filename
		$dir  = pathinfo($new_image, PATHINFO_DIRNAME);
		$file = pathinfo($new_image, PATHINFO_BASENAME);

		// Normalize the path
		$dir = str_replace('\\', '/', realpath($dir)).'/';

        //Capture output, this is because of the FTP Layer
        ob_start();
		if ($status = $this->driver->process($this->image, $this->actions, $dir, $file, true))
		{
			if ($chmod !== FALSE)
			{
				// Set permissions
				JPath::setPermissions($new_image, $chmod);
			}
		}
		$output = ob_get_contents();
		ob_end_clean();
		
		//Write the contents to the image, deleting the cached one first
		if(JFile::exists($dir.'/'.$file)) JFile::delete($dir.'/'.$file);
		JFile::write($dir.'/'.$file, $output);
		
		// Reset actions. Subsequent save() or render() will not apply previous actions.
		if ($keep_actions === FALSE)
			$this->actions = array();

		return $status;
	}
	
	/** 
	 * Output the image to the browser. 
	 * 
	 * @param   boolean  keep or discard image process actions
	 * @return	object 
	 */ 
	public function render($keep_actions = FALSE) 
	{ 
		$new_image = $this->image['file']; 
	
		// Separate the directory and filename 
		$dir  = pathinfo($new_image, PATHINFO_DIRNAME); 
		$file = pathinfo($new_image, PATHINFO_BASENAME); 
	
		// Normalize the path 
		$dir = str_replace('\\', '/', realpath($dir)).'/'; 
	
		// Process the image with the driver 
		$status = $this->driver->process($this->image, $this->actions, $dir, $file, $render = TRUE); 
		
		// Reset actions. Subsequent save() or render() will not apply previous actions.
		if ($keep_actions === FALSE)
			$this->actions = array();
		
		return $status; 
	}

	/**
	 * Sanitize a given value type.
	 *
	 * @param   string   type of property
	 * @param   mixed    property value
	 * @return  boolean
	 */
	protected function valid_size($type, & $value)
	{
		if (is_null($value))
			return TRUE;

		if ( ! is_scalar($value))
			return FALSE;

		switch ($type)
		{
			case 'width':
			case 'height':
				if (is_string($value) AND ! ctype_digit($value))
				{
					// Only numbers and percent signs
					if ( ! preg_match('/^[0-9]++%$/D', $value))
						return FALSE;
				}
				else
				{
					$value = (int) $value;
				}
			break;
			case 'top':
				if (is_string($value) AND ! ctype_digit($value))
				{
					if ( ! in_array($value, array('top', 'bottom', 'center')))
						return FALSE;
				}
				else
				{
					$value = (int) $value;
				}
			break;
			case 'left':
				if (is_string($value) AND ! ctype_digit($value))
				{
					if ( ! in_array($value, array('left', 'right', 'center')))
						return FALSE;
				}
				else
				{
					$value = (int) $value;
				}
			break;
			case 'master':
				if ($value !== self::NONE AND
				    $value !== self::AUTO AND
				    $value !== self::WIDTH AND
				    $value !== self::HEIGHT)
					return FALSE;
			break;
		}

		return TRUE;
	}
}