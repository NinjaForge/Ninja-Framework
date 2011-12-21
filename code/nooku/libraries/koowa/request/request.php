<?php
/**
 * @version    	$Id: request.php 2924 2011-03-17 21:04:16Z johanjanssens $
 * @category	Koowa
 * @package    	Koowa_Request
 * @copyright  	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license    	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link 		http://www.nooku.org
 */

//Instantiate the request singleton
KRequest::instantiate();

/**
 * Request class
 *
 * Allows to get input from GET, POST, REQUEST, COOKIE, ENV, SERVER, SESSION, FILES
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Koowa
 * @package     Koowa_Request
 * @uses        KFilter
 * @uses        KInflector
 * @uses        KFactory
 * @static
 */
class KRequest
{
    /**
     * Accepted request hashes
     *
     * @var array
     */
    protected static $_hashes = array('COOKIE', 'ENV', 'FILES', 'GET', 'POST', 'PUT', 'DELETE', 'SERVER', 'REQUEST', 'SESSION');

    /**
     * Accepted request methods
     *
     * @var array
     */
    protected static $_methods = array('GET', 'POST', 'PUT', 'DELETE' /*,'HEAD', 'OPTIONS'*/ );


    /**
     * URL of the request regardless of the server
     *
     * @var KHttpUri
     */
    protected static $_uri = null;

    /**
     * Base path of the request.
     *
     * @var KHttpUri
     */
    protected static $_base = null;
    
    /**
     * Root path of the request.
     *
     * @var KHttpUri
     */
    protected static $_root = null;

    /**
     * Referrer of the request
     *
     * @var KHttpUri
     */
    protected static $_referrer = null;
    
    /**
     * The raw post or put content information
     *
     * @var array
     */
    protected static $_content = null;
    
    /**
     * The request accepts information
     *
     * @var array
     */
    protected static $_accept = null;
    
    
    /**
     * Constructor
     *
     * Prevent creating instances of this class by making the contructor private
     */
    final private function __construct(KConfig $config) 
    {
        $content = self::content();
        
        if(self::type() == 'HTTP')
        {
            $authorization = KRequest::get('server.HTTP_AUTHORIZATION', 'url');
	        if (strstr($authorization,"Basic")) 
	        {
	            $parts = explode(':',base64_decode(substr($authorization, 6)));
			
	            if (count($parts) == 2) 
			    {
				    KRequest::set('server.PHP_AUTH_USER', $parts[0]); 
				    KRequest::set('server.PHP_AUTH_PW'  , $parts[1]); 
			    }
		    }
        }
        
        if(!empty($content['data']))
        {
            if($content['type'] == 'application/x-www-form-urlencoded') 
            {
                if (in_array(self::method(), array('PUT', 'DELETE'))) 
                {
                    parse_str($content['data'], $GLOBALS['_'.self::method()]);
                    $GLOBALS['_REQUEST'] = array_merge($GLOBALS['_REQUEST'],  $GLOBALS['_'.self::method()]); 
                }
            }
            
            if($content['type'] == 'application/json') 
            {
                if(in_array(self::method(), array('POST', 'PUT', 'DELETE'))) 
                {
                    $GLOBALS['_'.self::method()] = json_decode($content['data'], true);
                    $GLOBALS['_REQUEST'] = array_merge($GLOBALS['_REQUEST'],  $GLOBALS['_'.self::method()]); 
                }
            }   
        }
     }
    
    /**
     * Clone 
     *
     * Prevent creating clones of this class
     */
    final private function __clone() { }
    
    /**
     * Force creation of a singleton
     *
     * @return void
     */
    public static function instantiate($config = array())
    {
        static $instance;
        
        if ($instance === NULL) 
        {
            if(!$config instanceof KConfig) {
                $config = new KConfig($config);
            }
            
            $instance = new self($config);
        }
        
        return $instance;
    }


    /**
     * Get sanitized data from the request.
     *
     * @param   string              Variable identifier, prefixed by hash name eg post.foo.bar
     * @param   mixed               Filter(s), can be a KFilter object, a filter name, an array of filter names or a filter identifier
     * @param   mixed               Default value when the variable doesn't exist
     * @throws  KRequestException   When an invalid filter was passed
     * @return  mixed               The sanitized data
     */
    public static function get($identifier, $filter, $default = null)
    {
        list($hash, $keys) = self::_parseIdentifier($identifier);
        
        $result = null;
        if(isset($GLOBALS['_'.$hash]))
        {
            $result = $GLOBALS['_'.$hash];
            foreach($keys as $key)
            {
                if(array_key_exists($key, $result)) {
                    $result = $result[$key];
                } else {
                    $result = null;
                    break;
                }
            }
        }
       
        
        // If the value is null return the default
        if(is_null($result)) {
            return $default;
        }
    
        // Handle magic quotes compatability
        if (get_magic_quotes_gpc() && !in_array($hash, array('FILES', 'SESSION'))) {
            $result = self::_stripSlashes( $result );
        }

        if(!($filter instanceof KFilterInterface)) {
            $filter = KFilter::factory($filter);
        }

        return $filter->sanitize($result);
    }

    /**
     * Set a variable in the request. Cookies and session data are stored persistently.
     *
     * @param   mixed   Variable identifier, prefixed by hash name eg post.foo.bar
     * @param   mixed   Variable value
     */
    public static function set($identifier, $value)
    {
        list($hash, $keys) = self::_parseIdentifier($identifier);

        // Add to _REQUEST hash if original hash is get, post, or cookies 
        if(in_array($hash, array('GET', 'POST', 'COOKIE'))) {
            self::set('request.'.implode('.', $keys), $value);
        }

        // Store cookies persistently
        if($hash == 'COOKIE')
        {
            // rewrite the $keys as foo[bar][bar]
            $ckeys = $keys; // get a copy
            $name = array_shift($ckeys);
            foreach($ckeys as $ckey) {
                $name .= '['.$ckey.']';
            }

            if(!setcookie($name, $value)) {
                throw new KRequestException("Couldn't set cookie, headers already sent.");
            }
        }
        
        // Store in $GLOBALS
        foreach(array_reverse($keys, true) as $key) {
            $value = array($key => $value);
        }
        
        $GLOBALS['_'.$hash] = KHelperArray::merge($GLOBALS['_'.$hash], $value);
    }

    /**
     * Check if a variable exists based on an identifier
     *
     * @param   string  Variable identifier, prefixed by hash name eg post.foo.bar
     * @return  boolean
     */
    public static function has($identifier)
    {
        list($hash, $keys) = self::_parseIdentifier($identifier);

        // find $var in the hashe
        foreach($keys as $key)
        {
            if(array_key_exists($key, $GLOBALS['_'.$hash])) {
                return true;;
            }
        }

        return false;
    }
    
    /**
     * Get the POST or PUT raw content information
     * 
     * The raw post data is not available with enctype="multipart/form-data".
     *  
     * @param   string  The content data to return. Can be 'type' or 'data'. 
     *                  If not set, all the data will be returned.
     * @return  array   An associative array with the content data. Valid keys are
     *                  'type' and 'data'
     */
    public static function content($key = null)
    {
        $result = '';
        
        if (!isset(self::$_content) && isset($_SERVER['CONTENT_TYPE'])) 
        {             
            $type = $_SERVER['CONTENT_TYPE']; 
                        
            // strip parameters from content-type like "; charset=UTF-8"             
            if (is_string($type)) 
            {                  
                if (preg_match('/^([^,\;]*)/', $type, $matches)) {                    
                    $type = $matches[1];                 
                }             
            }             
                
            self::$_content['type'] = $type;  
            
            
            $data = '';
            if (isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) 
            {   
                $input = fopen('php://input', 'r');
                while ($chunk = fread($input, 1024)) {
                    $data .= $chunk;
                }

                fclose($input);
            }
            
            self::$_content['data'] = $data;
        }
            
        return isset($key) ? self::$_content[$key] : self::$_content;
    }
    
    /**
     * Get the accept request information 
     *
     * @param   string  The accept data to return. Can be 'format', 'encoding' or 'language'. 
     *                  If not set, all the accept data will be returned.
     * @return  array   An associative array with the content data. Valid keys are
     *                  'format', 'encoding' and 'language'
     */
    public static function accept($type = null)
    {
        if (!isset(self::$_accept) && isset($_SERVER['HTTP_ACCEPT'])) 
        {             
            $accept = KRequest::get('server.HTTP_ACCEPT', 'string');
            self::$_accept['format'] = self::_parseAccept($accept);
                            
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) 
            {   
                $accept = KRequest::get('server.HTTP_ACCEPT_ENCODING', 'string');
                self::$_accept['encoding'] = self::_parseAccept($accept);   
            }
            
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) 
            {   
                $accept = KRequest::get('server.HTTP_ACCEPT_LANGUAGE', 'string');
                self::$_accept['language'] = self::_parseAccept($accept);   
            }   
        }
            
        return $type ? self::$_accept[$type] : self::$_accept;
    }
    
    /**
     * Returns the HTTP referrer.
     * 
     * 'referer' a commonly used misspelling word for 'referrer'
     * @see     http://en.wikipedia.org/wiki/HTTP_referrer
     *
     * @param   boolean     Only allow internal url's
     * @return  KHttpUri    A KHttpUri object
     */
    public static function referrer($isInternal = true)
    {
        if(!isset(self::$_referrer))
        {
            if($referrer = KRequest::get('server.HTTP_REFERER', 'url')) 
            {
                self::$_referrer = KFactory::get('lib.koowa.http.uri', array('uri' => $referrer));
                
                if($isInternal)
                {
                    if(!KFactory::get('lib.koowa.filter.internalurl')->validate((string)self::$_referrer)) {
                        return null;
                    }
                }
            }
        }

        return self::$_referrer;
    }

    /**
     * Return the URI of the request regardless of the server
     *
     * @return  KHttpUri    A KHttpUri object
     */
    public static function url()
    {
        if(!isset(self::$_uri))
        {
            /*
             * Since we are assigning the URI from the server variables, we first need
             * to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
             * are present, we will assume we are running on apache.
             */
            if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI']))
            {
                /*
                 * To build the entire URI we need to prepend the protocol, and the http host
                 * to the URI string.
                 */
                $url = self::protocol().'://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                /*
                 * Since we do not have REQUEST_URI to work with, we will assume we are
                 * running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
                 * QUERY_STRING environment variables.
                 */
            }
            else
            {
                // IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable
                $url = self::protocol().'://'. $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

                // If the query string exists append it to the URI string
                if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                    $url .= '?' . $_SERVER['QUERY_STRING'];
                }
            }
            
            // Sanitize the url since we can't trust the server var
            $url = KFactory::get('lib.koowa.filter.url')->sanitize($url);

            // Create the URI object
            self::$_uri = KFactory::tmp('lib.koowa.http.uri', array('uri' => $url));

        }

        return self::$_uri;
    }

    /**
     * Returns the base path of the request.
     *
     * @return  object  A KHttpUri object
     */
    public static function base()
    {
        if(!isset(self::$_base))
        {
            // Get the base request path
            if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI'])) {
                //Apache CGI
                $path =  rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            } else {
                //Others
                $path =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            }

            // Sanitize the url since we can't trust the server var         
            $path = KFactory::get('lib.koowa.filter.url')->sanitize($path);
                
            self::$_base = KFactory::tmp('lib.koowa.http.uri', array('uri' => $path));
        }
        
        return self::$_base;
    }
    
    /**
     * Returns the root path of the request.
     * 
     * In most case this value will be the same as KRequest::base however it can be
     * changed by pushing in a different value
     *
     * @return  object  A KHttpUri object
     */
    public static function root($path = null)
    {
        if(!is_null($path)) 
        {
            if(!$path instanceof KhttpUri) {
                $path = KFactory::tmp('lib.koowa.http.uri', array('uri' => $path));
            }
            
            self::$_root = $path;
        }
        
        if(is_null(self::$_root)) {
            self::$_root = self::$_base;
        }
        
        return self::$_root;
    }

    /**
     * Returns the current request protocol, based on $_SERVER['https']. In CLI
     * mode, NULL will be returned.
     *
     * @return  string
     */
    public static function protocol()
    {
        if (PHP_SAPI === 'cli') {
            return NULL;
        }

        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
            return 'https';
        } else {
            return 'http';
        }
    }

    /**
     * Returns current request method.
     *
     * @throws  KRequestException Wgen the method could not be found
     * @return  string
     */
    public static function method()
    {
        if(PHP_SAPI != 'cli')
        {
            $method  =  strtoupper($_SERVER['REQUEST_METHOD']);

            if($method == 'POST')
            {
                if(isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
                }
                
                if(self::has('post._method')) {
                    $method = strtoupper(self::get('post._method', 'cmd'));
                }
            }
        } 

        if ( ! in_array($method, self::$_methods)) {
            throw new KRequestException('Unknown method : '.$method);
        }

        return $method;
    }

    /**
     * Return the current request transport type.
     *
     * @return  string
     */
    public static function type()
    {
        $type = 'HTTP';

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $type = 'AJAX';
        }

        if( isset($_SERVER['HTTP_X_FLASH_VERSION'])) {
            $type = 'FLASH';
        }

        return $type;
    }
    
    /**
     * Return the request token
     *
     * @return  string  The request token or NULL if no token could be found
     */
    public static function token()
    {
        $token = null;
        
        if(self::has('server.HTTP_X_TOKEN')) {
            $token = self::get('server.HTTP_X_TOKEN', 'md5');
        }
        
        if(self::has('request._token')) {
            $token = self::get('request._token', 'md5');
        }
        
        return $token;
    }
    
    /**
     * Return the request format
     * 
     * This function tries to find the format by inspecting the accept header, 
     * only if one accept type is specified the format will be parsed from it, 
     * otherwise the path extension or the 'format' request variable is used.
     *
     * @return  string  The request format or NULL if no format could be found
     */
    public static function format()
    {
        $format = null;
        
        if(count(self::accept('format')) == 1) 
        {
            $mime   = explode('/', key(self::accept('format')));
            $format = $mime[1];
            
            if($pos = strpos($format, '+')) {
                $format = substr($format, 0, $pos);
            }
            
            //Format cannot be *
            if($format == '*') { 
                $format = null; 
            } 
        }
        
        if(!empty(self::url()->format) && self::url()->format != 'php') {
            $format = self::url()->format;
        }
        
        if(self::has('request.format')) {
            $format = self::get('request.format', 'word');
        }
        
        return $format;
    }

    /**
     * Parse the variable identifier
     *
     * @param   string  Variable identifier
     * @throws  KRequestException   When the hash could not be found
     * @return  array   0 => hash, 1 => parts
     */
    protected static function _parseIdentifier($identifier)
    {
        $parts = array();
        $hash  = $identifier;

        // Validate the variable format
        if(strpos($identifier, '.') !== false)
        {
            // Split the variable name into it's parts
            $parts = explode('.', $identifier);

            // Validate the hash name
            $hash   = array_shift($parts);
        }

        $hash = strtoupper($hash);

        if(!in_array($hash, self::$_hashes)) {
            throw new KRequestException("Unknown hash '$hash' in '$identifier'");
        }

        return array($hash, $parts);
    }

    /**
     * Parses an accept header and returns an array (type => quality) of the
     * accepted types, ordered by quality.
     *
     * @param string    header to parse
     * @param array     default values
     * @return array
     */
    protected static function _parseAccept( $accept, array $defaults = NULL)
    {
        if (!empty($accept))
        {
            // Get all of the types
            $types = explode(',', $accept);

            foreach ($types as $type)
            {
                // Split the type into parts
                $parts = explode(';', $type);

                // Make the type only the MIME
                $type = trim(array_shift($parts));

                // Default quality is 1.0
                $quality = 1.0;

                foreach ($parts as $part)
                {
                    // Prevent undefined $value notice below
                    if (strpos($part, '=') === FALSE)
                    continue;

                    // Separate the key and value
                    list ($key, $value) = explode('=', trim($part));

                    if ($key === 'q')
                    {
                        // There is a quality for this type
                        $quality = (float) trim($value);
                    }
                }

                // Add the accept type and quality
                $defaults[$type] = $quality;
            }
        }

        // Make sure that accepts is an array
        $accepts = (array) $defaults;

        // Order by quality
        arsort($accepts);

        return $accepts;
    }
    
    /**
     * Strips slashes recursively on an array
     *
     * @param   array   Array of (nested arrays of) strings
     * @return  array   The input array with stripshlashes applied to it
     */
    protected static function _stripSlashes( $value )
    {
        if(!is_object($value)) {
            $value = is_array( $value ) ? array_map( array( 'KRequest', '_stripSlashes' ), $value ) : stripslashes( $value );
        }
        
        return $value;
    }
}