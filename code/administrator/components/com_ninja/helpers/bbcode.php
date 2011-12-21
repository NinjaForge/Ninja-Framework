<?php defined( 'KOOWA' ) or die( 'Restricted access' );

require_once dirname(__FILE__) . '/bbcode/stringparser_bbcode.class.php';

/**
 * Shortcut to bbcode
 *
 * @author Stian Didriksen
 */


// Unify line breaks of different operating systems
function convertlinebreaks ($text) {
    return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}

// Remove everything but the newline charachter
function bbcode_stripcontents ($text) {
    return preg_replace ("/[^\n]/", '', $text);
}

// Function to include images
function do_bbcode_img ($action, $attributes, $content, $params, $node_object) {
    if ($action == 'validate') {
        if (substr ($content, 0, 5) == 'data:' || substr ($content, 0, 5) == 'file:'
          || substr ($content, 0, 11) == 'javascript:' || substr ($content, 0, 4) == 'jar:') {
            return false;
        }
        return true;
    }
    return '<img src="'.htmlspecialchars($content).'" alt="">';
}

class NinjaHelperBbcode extends StringParser_BBCode implements KTemplateHelperInterface, KObjectHandlable, KObjectServiceable
{
	/**
	 * The service identifier
	 *
	 * @var KServiceIdentifier
	 */
	private $__service_identifier;
	
	/**
	 * The service container
	 *
	 * @var KService
	 */
	private $__service_container;
	
	/**
	 * Cache instance arrays using cache_group as keys
	 *
	 * @var JCache
	 */
	protected $_cache = array();

    /**
     * Template object
     *
     * @var	object
     */
    protected $_template;

	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct( KConfig $config = null) 
	{ 
		//Set the service container
		if(isset($config->service_container)) {
		    $this->__service_container = $config->service_container;
		}
		
		//Set the service identifier
		if(isset($config->service_identifier)) {
		    $this->__service_identifier = $config->service_identifier;
		}
		
		//Initialise the object
		if($config) {
		    $this->_initialize($config);
		}
		
		// Set the view indentifier
		$this->_template = $config->template;
		

		$this->addFilter(STRINGPARSER_FILTER_PRE, 'convertlinebreaks');
		
		$this->addParser(array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
		$this->addParser(array ('block', 'inline', 'link', 'listitem'), 'nl2br');
		$this->addParser('list', 'bbcode_stripcontents');
		
		//Tables
		$this->addCode('td', 'simple_replace', null, array('start_tag' => '<td>', 'end_tag' => '</td>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
		$this->addCode('th', 'simple_replace', null, array('start_tag' => '<th>', 'end_tag' => '</th>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
		$this->addCode('tr', 'simple_replace', null, array('start_tag' => '<tr>', 'end_tag' => '</tr>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
		$this->addCode('table', 'simple_replace', null, array('start_tag' => '<table>', 'end_tag' => '</table>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
		
		$this->addCode('b', 'simple_replace', null, array('start_tag' => '<strong>', 'end_tag' => '</strong>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
		$this->addCode('u', 'simple_replace', null, array ('start_tag' => '<span class="underline" style="text-decoration: underline;">', 'end_tag' => '</span>'), 'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->addCode('i', 'simple_replace', null, array ('start_tag' => '<em>', 'end_tag' => '</em>'), 'inline', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->addCode('url', 'usecontent?', array($this, 'replaceURL'), array('usecontent_param' => 'default'), 'link', array('listitem', 'block', 'inline'), array ('link'));
		$this->addCode('link', 'callback_replace_single', array($this, 'replaceURL'), array(), 'link', array('listitem', 'block', 'inline'), array('link'));
		$this->addCode('color', 'usecontent?', array($this, 'replaceColor'), array('usecontent_param' => 'default'), 'link', array ('listitem', 'block', 'inline'), array('color'));
		$this->addCode('size', 'usecontent?', array($this, 'replaceSize'), array('usecontent_param' => 'default'), 'size', array ('listitem', 'block', 'inline'), array('size'));
		$this->addCode('img', 'usecontent', 'do_bbcode_img', array (), 'image', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->addCode('bild', 'usecontent', 'do_bbcode_img', array (), 'image', array ('listitem', 'block', 'inline', 'link'), array ());
		$this->setOccurrenceType('img', 'image');
		$this->setOccurrenceType('bild', 'image');
		$this->addCode('quote', 'usecontent?', array($this, 'quote'), array('usecontent_param' => 'default'), 'block', array('listitem', 'block', 'inline', 'link'), array());
		$this->addCode('code', 'simple_replace', null, array('start_tag' => '<pre>', 'end_tag' => '</pre>'), 'inline', array('listitem', 'block', 'inline', 'link'), array());
		//$this->setMaxOccurrences ('image', 2);
		$this->addCode(
			'list',
			'callback_replace?',
			array($this, 'replaceStart'),
			array(),
			'list',
			array('block', 'listitem'),
			array()
		);
		//$this->addCode('list=', 'simple_replace', null, array ('start_tag' => '<ol>', 'end_tag' => '</ol>'), 'list', array ('block', 'listitem'), array ());
		$this->addCode(
			'list',
			'simple_replace',
			null,
			array('start_tag' => '<ul>', 'end_tag' => '</ul>'),
			'list',
			array('block', 'listitem'),
			array()
		);
		$this->addCode('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'), 'listitem', array ('list'), array ());
		$this->setCodeFlag('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
		$this->setCodeFlag('*', 'paragraphs', true);
		$this->setCodeFlag('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
		$this->setCodeFlag('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
		$this->setCodeFlag('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
		$this->setRootParagraphHandling(true);
		$this->setCodeFlag('quote', 'paragraph_type', BBCODE_PARAGRAPH_ALLOW_INSIDE);
		$this->setCodeFlag('code', 'paragraph_type', BBCODE_PARAGRAPH_ALLOW_INSIDE);
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 * @return 	void
	 */
	protected function _initialize(KConfig $config)
	{
		//do nothing
	}
	
	/**
	 * Get a handle for this object
	 *
	 * This function returns an unique identifier for the object. This id can be used as
	 * a hash key for storing objects or for identifying an object
	 *
	 * @return string A string that is unique
	 */
	public function getHandle()
	{
	    return spl_object_hash( $this );
	}
	
	/**
	 * Get an instance of a class based on a class identifier only creating it
	 * if it doesn't exist yet.
	 *
	 * @param	string|object	The class identifier or identifier object
	 * @param	array  			An optional associative array of configuration settings.
	 * @throws	KServiceServiceException
	 * @return	object  		Return object on success, throws exception on failure
	 * @see 	KObjectServiceable
	 */
	final public function getService($identifier, array $config = array())
	{
	    return $this->__service_container->get($identifier, $config);
	}
	
	/**
	 * Gets the service identifier.
	 *
	 * @return	KServiceIdentifier
	 * @see 	KObjectServiceable
	 */
	final public function getIdentifier($identifier = null)
	{
		if(isset($identifier)) {
		    $result = $this->__service_container->getIdentifier($identifier);
		} else {
		    $result = $this->__service_identifier; 
		}
	    
	    return $result;
	}
	
	/**
	 * Get the template object
	 *
	 * @return  object	The template object
	 */
	public function getTemplate()
	{
	    return $this->_template;
	}
	
	public function replaceStart($action, $attributes, $content, $params, $node_object)
	{
		if(!isset($attributes['default'])) return '<ul>' . $content . '</ul>';
	    return	'<ol'.(
	    			$attributes['default'] != 1 ?
	    			' start="'.(int)$attributes['default'].'"' :
	    			false
	    		).'>' . $content . '</ol>';
	}
	
	public function replaceURL($action, $attributes, $content, $params, $node_object)
	{
	    if (!isset ($attributes['default'])) {
	        $url = $content;
	        $text = $content;
	    } else {
	        $url = $attributes['default'];
	        $text = $content;
	    }
	    if ($action == 'validate') {
	        if (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) == 'file:'
	          || substr ($url, 0, 11) == 'javascript:' || substr ($url, 0, 4) == 'jar:') {
	            return false;
	        }
	        return true;
	    }
	    //Fix urls that don't have a protocol
	    if(!preg_match('/^[a-z][\w-]+:/', $url)) {
	    	$url = 'http://'.$url;
	    }

	    return '<a href="'.$url.'">'.$text.'</a>';
	}
	
	public function replaceSize($action, $attributes, $content, $params, $node_object)
	{
		if(!isset($attributes['default'])) return false;
		
		return '<span style="font-size:'.intval($attributes['default']).'%">'.$content.'</span>'; 
	}
	
	public function replaceColor($action, $attributes, $content, $params, $node_object)
	{
	    if (!isset($attributes['default'])) {
	        $color = $content;
	        $text = htmlspecialchars ($content);
	    } else {
	        $color = $attributes['default'];
	        $text = $content;
	    }
	    return '<span style="color:'.htmlspecialchars ($color).'">'.$text.'</span>';
	}
	
	/**
	 * A wrapper arond the parser function so it works with the koowa helpers
	 *
	 * Use the options cache_group and cache_key to control the cache behavior.
	 * @TIP set your own group and key, as the way keys are generated isn't optimal when using md5 other than
	 *      you don't need to purge cache when the text changes, as that also changes the key
	 *
	 * @author Stian Didriksen
	 */
	public function parse($config = array())
	{
	    //If no text, no need to run all this code
	    if(empty($config['text'])) return;
	
	    if(!isset($config['cache_group'])) $config['cache_group'] = 'com.ninja.helper.bbcode.parsed';
	    if(!isset($config['cache_key']))   $config['cache_key'] = md5($config['text']);
	    
	    if(!isset($this->_cache[$config['cache_group']])) {
	        $this->_cache[$config['cache_group']] = JFactory::getCache($config['cache_group'], 'output');
	    }
	    $cache = $this->_cache[$config['cache_group']];
	
	    //If it's cached, no need to parse so return the cached text
	    if($cached_output = $cache->get($config['cache_key'])) return $cached_output;

        //For debugging
        //$before = microtime(true);

		//This bbCode parser does not pick up [list=], but treats it as [list]
		$config['text'] = str_replace('[list=]', '[list=1]', $config['text']);

		$config['text'] = preg_replace('/\[([a-z0-9=#]+)\:([a-z0-9:]+)\]/m', '[\1]', $config['text']); 
		$config['text'] = preg_replace('/\[\/([a-z0-9]+)\:([a-z0-9:]+)\]/m', '[/\1]', $config['text']); 
		$config['text'] = preg_replace('/\[\*\:([a-z0-9:]+)\]/m', '[*]', $config['text']); 
		$config['text'] = preg_replace('/\[\/\*\:([a-z0-9:]+)\]/m', '[/*]', $config['text']); 
		
		///*
		//Preprocessing of links
		//@TODO this fix should be optimized, preferrably by using cache
		//Modified with a handle for dealing with this kind of problem: http://example.com[/quote] causing unclosed tags
		$config['text'] = preg_replace_callback('#(^|[ \t\r\n"“\'])(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#ui', array($this, '_pregUrlReplaceCallback'), $config['text']);

		//*/
		
		//Preprocessing of links
		//$config['text'] = preg_replace('#(^|[ \t\r\n"“\'])(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#ui', '$1[url]$2[/url]', $config['text']);
		

		//Parse the bbcode
		$config['text'] = parent::parse($config['text']);
		
		//Strip out any [url] tags that failed validation (like javascript:alert(1) style urls)
		$config['text'] = str_replace(array('[url]', '[/url]'), '', $config['text']);
		
		//This interferes with some tags
		//$config['text'] = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)', '<a rel="nofollow" href="\\1">\\1</a>', $config['text']);

        $cache->store($config['text'], $config['cache_key']);

        //For debugging
        //error_log('Missed cache and that cost you '.(microtime(true)-$before).' seconds');

		return $config['text'];
	}
	
	/**
	 * Callback used when detecting URIs
	 *
	 * @param  $matches    array over matches
	 * @return string
	 */
	private function _pregUrlReplaceCallback($matches)
	{
	    $pattern = '#(\[\/\w+)$#ui';
	    $url     = preg_replace($pattern, '$2', $matches[2]);
	    $code    = str_replace($url, '', $matches[2]);
	    return $matches[1].'[url]'.$url.'[/url]'.$code;
	}
	
	/*
	
	 * New quote parser

	 * @author CaptainHook

	 */

	

	public function quote($action, $attributes, $content, $params, $node_object)

	{

	    if (!isset($attributes['default'])) {

	        $text = $content;

			return '<blockquote>'.$content.'</blockquote>';

	    } else {

	        $author = $attributes['default'];

	        $text = $content;

			return '<div class="blockquote"><p class="citation">'.$author.' said:</p>'.$content.'</div>';

	    }

	    

	}
}