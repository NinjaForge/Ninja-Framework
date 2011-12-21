<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* OO cURL Class
* Object oriented wrapper for the cURL library.
* @author David Hopkins (semlabs.co.uk)
* @version 0.3
*/

/**
 * @version		$Id: curl.php 794 2011-01-10 18:44:32Z stian $
 * @category	Ninja
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
class ComNinjaHelperCurl extends KTemplateHelperAbstract
{
	
	public $sessions 				=	array();
	public $key						=   0;
	public $retry					=	0;
	public $info					= 	array();
	
	/**
	 * Construct method, add a session if options are passed
	 *
	 * @author	Stian Didriksen <stian@ninjaforge.com>
	 * @param	$config		KConfig
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		$config = (object) $config;
		if(isset($config->retry)) $this->retry = $config->retry;
	}
	
	/**
	* Adds a cURL session to stack
	* @param $url string, session's URL
	* @param $opts array, optional array of cURL options and values
	*/
	public function addSession( $url, $opts = false )
	{
		$this->sessions[] = curl_init( $url );
		if( $opts != false )
		{
			$key = count( $this->sessions ) - 1;
			$this->setOpts( $opts, $key );
		}
	}
	
	/**
	* Sets an option to a cURL session
	* @param $option constant, cURL option
	* @param $value mixed, value of option
	* @param $key int, session key to set option for
	*/
	public function setOpt( $option, $value, $key = 0 )
	{
		curl_setopt( $this->sessions[$key], $option, $value );
	}
	
	/**
	* Sets an array of options to a cURL session
	* @param $options array, array of cURL options and values
	* @param $key int, session key to set option for
	*/
	public function setOpts( $options, $key = 0 )
	{
		curl_setopt_array( $this->sessions[$key], $options );
	}
	
	/**
	* Executes as cURL session
	* @param $key int, optional argument if you only want to execute one session
	*/
	public function exec( $key = false )
	{
		$no = count( $this->sessions );
		
		if( $no == 1 )
			$res = $this->execSingle();
		elseif( $no > 1 ) {
			if( $key === false )
				$res = $this->execMulti();	
			else
				$res = $this->execSingle( $key );
		}
		
		if( $res )
			return $res;
	}
	
	/**
	* Executes a single cURL session
	* @param $key int, id of session to execute
	* @return array of content if CURLOPT_RETURNTRANSFER is set
	*/
	public function execSingle( $key = 0 )
	{
		if( $this->retry > 0 )
		{
			$retry = $this->retry;
			$code = 0;
			while( $retry >= 0 && ( $code[0] == 0 || $code[0] >= 400 ) )
			{
				$res = curl_exec( $this->sessions[$key] );
				$code = $this->info( $key, CURLINFO_HTTP_CODE );
				
				$retry--;
			}
		}
		else
			$res = curl_exec( $this->sessions[$key] );
		
		return $res;
	}
	
	/**
	* Executes a stack of sessions
	* @return array of content if CURLOPT_RETURNTRANSFER is set
	*/
	public function execMulti()
	{
		$mh = curl_multi_init();
		
		#Add all sessions to multi handle
		foreach ( $this->sessions as $i => $url )
			curl_multi_add_handle( $mh, $this->sessions[$i] );
		
		do
			$mrc = curl_multi_exec( $mh, $active );
		while ( $mrc == CURLM_CALL_MULTI_PERFORM );
		
		while ( $active && $mrc == CURLM_OK )
		{
			if ( curl_multi_select( $mh ) != -1 )
			{
				do
					$mrc = curl_multi_exec( $mh, $active );
				while ( $mrc == CURLM_CALL_MULTI_PERFORM );
			}
		}

		if ( $mrc != CURLM_OK )
			echo "Curl multi read error $mrc\n";
		
		#Get content foreach session, retry if applied
		foreach ( $this->sessions as $i => $url )
		{
			$code = $this->info( $i, CURLINFO_HTTP_CODE );
			if( $code[0] > 0 && $code[0] < 400 )
				$res[] = curl_multi_getcontent( $this->sessions[$i] );
			else
			{
				if( $this->retry > 0 )
				{
					$retry = $this->retry;
					$this->retry -= 1;
					$eRes = $this->execSingle( $i );
					
					if( $eRes )
						$res[] = $eRes;
					else
						$res[] = false;
						
					$this->retry = $retry;
					echo '1';
				}
				else
					$res[] = false;
			}

			curl_multi_remove_handle( $mh, $this->sessions[$i] );
		}

		curl_multi_close( $mh );
		
		return $res;
	}
	
	/**
	* Closes cURL sessions
	* @param $key int, optional session to close
	*/
	public function close( $key = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $session )
				curl_close( $session );
		}
		else
			curl_close( $this->sessions[$key] );
	}
	
	/**
	* Remove all cURL sessions
	*/
	public function clear()
	{
		foreach( $this->sessions as $session )
			curl_close( $session );
		unset( $this->sessions );
	}
	
	/**
	* Returns an array of session information
	* @param $key int, optional session key to return info on
	* @param $opt constant, optional option to return
	*/
	public function info( $key = false, $opt = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $this->key => $session )
			{
				if(!isset($this->info[$this->key])) $this->info[$this->key] = array();
				
				if( $opt )
					$getinfo = curl_getinfo( $this->sessions[$this->key], $opt );
				else
					$getinfo = curl_getinfo( $this->sessions[$this->key] );
				$info[] = array_merge($getinfo, $this->info[$this->key]);
			}
		}
		else
		{
			$this->key = $key;
			if(!isset($this->info[$this->key])) $this->info[$this->key] = array();

			if( $opt )
				$getinfo = curl_getinfo( $this->sessions[$this->key], $opt );
			else
				$getinfo = curl_getinfo( $this->sessions[$this->key] );
			$info[] = array_merge($getinfo, $this->info[$this->key]);
		}
		
		return $info;
	}
	
	/**
	 * CURL callback function for reading and processing headers
	 * Override this for your needs
	 * 
	 * @param object $ch
	 * @param string $header
	 * @return integer
	 */
	public function readHeader($ch, $header) {
		//extracting example data: filename from header field Content-Disposition
		$filename = $this->extractCustomHeader('Content-Disposition: attachment; filename=', '\n', $header);
		if ($filename) {
			$this->info[$this->key]['content_disposition'] = str_replace(array('"', ';'), '', trim($filename));
		}
		return strlen($header);
	}

	public function extractCustomHeader($start,$end,$header) {
		$pattern = '/'. $start .'(.*?)'. $end .'/i';
		if (preg_match($pattern, $header, $result)) {
			return $result[1];
		} else {
			return false;
		}
	}
	
	/**
	* Returns an array of errors
	* @param $key int, optional session key to retun error on
	* @return array of error messages
	*/
	public function error( $key = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $session )
				$errors[] = curl_error( $session );
		}
		else
			$errors[] = curl_error( $this->sessions[$key] );
			
		return $errors;
	}
	
	/**
	* Returns an array of session error numbers
	* @param $key int, optional session key to retun error on
	* @return array of error codes
	*/
	public function errorNo( $key = false )
	{
		if( $key === false )
		{
			foreach( $this->sessions as $session )
				$errors[] = curl_errno( $session );
		}
		else
			$errors[] = curl_errno( $this->sessions[$key] );
			
		return $errors;
	}
	
}