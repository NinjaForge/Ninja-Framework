<?php
/**
 * @version		$Id: behavior.php 1372 2011-10-11 18:56:47Z stian $
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Behavior Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperBehavior extends KTemplateHelperAbstract
{
	/**
	 * Array which holds a list of loaded javascript libraries
	 *
	 * boolean
	 */
	protected static $_loaded = array();

	/**
	 * Method to load the mootools framework into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 */
	public function mootools($config = array())
	{
		$config = new KConfig($config);
		$html ='';

		// Only load once
		if (!isset(self::$_loaded['mootools'])) 
		{
			$html .= '<script src="media://lib_koowa/js/mootools.js" />';
			self::$_loaded['mootools'] = true;
		}

		return $html;
	}

	/**
	 * Render a modal box
	 *
	 * @return string	The html output
	 */
	public function modal($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => 'a.modal',
			'options'  => array('disableFx' => true)
 		));

 		$html = '';

		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['modal']))
		{
			$html .= '<script src="media://lib_koowa/js/modal.js" />';
			$html .= '<style src="media://lib_koowa/css/modal.css" />';

			self::$_loaded['modal'] = true;
		}

		$signature = md5(serialize(array($config->selector,$config->options)));
		if (!isset(self::$_loaded[$signature]))
		{
			$options = !empty($config->options) ? $config->options->toArray() : array();
			$html .= "
			<script>
				window.addEvent('domready', function() {

				SqueezeBox.initialize(".json_encode($options).");
				SqueezeBox.assign($$('".$config->selector."'), {
			        parse: 'rel'
				});
			});
			</script>";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Render a tooltip
	 *
	 * @return string	The html output
	 */
	public function tooltip($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => '.hasTip',
			'options'  => array()
 		));

 		$html = '';

		$signature = md5(serialize(array($config->selector,$config->options)));
		if (!isset(self::$_loaded[$signature]))
		{
		    //Don't pass an empty array as options
			$options = $config->options->toArray() ? ', '.$config->options : '';
			$html .= "
			<script>
				window.addEvent('domready', function(){ new Tips($$('".$config->selector."')".$options."); });
			</script>";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Render an overlay
	 *
	 * @return string	The html output
	 */
	public function overlay($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'url'  		=> '',
			'options'  	=> array(),
			'attribs'	=> array(),
		));

		$html = '';
		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['overlay']))
		{
			$html .= '<script src="media://lib_koowa/js/koowa.js" />';
			$html .= '<style src="media://lib_koowa/css/koowa.css" />';

			self::$_loaded['overlay'] = true;
		}

		$url = $this->getService('koowa:http.url', array('url' => $config->url));
		if(!isset($url->query['tmpl'])) {
		    $url->query['tmpl'] = '';
		}

		$attribs = KHelperArray::toString($config->attribs);

        $id = 'overlay'.rand();
        if($url->fragment)
        {
            //Allows multiple identical ids, legacy should be considered replaced with #$url->fragment instead
            $config->append(array(
                'options' => array(
                    'selector' => '[id='.$url->fragment.']'
                )
            ));
        }
		
		//Don't pass an empty array as options
		$options = $config->options->toArray() ? ', '.$config->options : '';
		$html .= "<script>window.addEvent('domready', function(){new Koowa.Overlay('$id'".$options.");});</script>";

		$html .= '<div data-url="'.$url.'" class="-koowa-overlay" id="'.$id.'" '.$attribs.'><div class="-koowa-overlay-status">'.JText::_('Loading...').'</div></div>';
		return $html;
	}

	/**
	 * Keep session alive
	 *
	 * This will send an ascynchronous request to the server via AJAX on an interval
	 * in miliseconds
	 *
	 * @return string	The html output
	 */
	public function keepalive($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'refresh'  => 15 * 60000, //15min
		    'url'	   => KRequest::url()
		));

		$refresh = (int) $config->refresh;

	    // Longest refresh period is one hour to prevent integer overflow.
		if ($refresh > 3600000 || $refresh <= 0) {
			$refresh = 3600000;
		}

		// Build the keepalive script.
		$html =
		"<script>
			Koowa.keepalive =  function() {
				var request = new Request({method: 'get', url: '".$config->url."'}).send();
			}

			window.addEvent('domready', function() { Koowa.keepalive.periodical('".$refresh."'); });
		</script>";

		return $html;
	}
	
	/**
	 * Loads the Forms.Validator class and connects it to Koowa.Controller
	 *
	 * This allows you to do easy, css class based forms validation-
	 * Koowa.Controller.Form works with it automatically.
	 * Requires koowa.js and mootools to be loaded in order to work.
	 *
	 * @see    http://www.mootools.net/docs/more125/more/Forms/Form.Validator
	 *
	 * @return string	The html output
	 */
	public function validator($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'selector' => '.-koowa-form',
		    'options'  => array(
		        'scrollToErrorsOnChange' => true,
		        'scrollToErrorsOnBlur'   => true
		    )
		));

		$html = '';
		// Load the necessary files if they haven't yet been loaded
		if(!isset(self::$_loaded['validator']))
		{
		    if(version_compare(JVERSION,'1.6.0','ge')) {
		        $html .= '<script src="media://lib_koowa/js/validator-1.3.js" />';
		    } else {
		        $html .= '<script src="media://lib_koowa/js/validator-1.2.js" />';
		    }
		    $html .= '<script src="media://lib_koowa/js/patch.validator.js" />';

            self::$_loaded['validator'] = true;
        }

		//Don't pass an empty array as options
		$options = $config->options->toArray() ? ', '.$config->options : '';
		$html .= "<script>
		window.addEvent('domready', function(){
		    $$('$config->selector').each(function(form){
		        new Koowa.Validator(form".$options.");
		        form.addEvent('validate', form.validate.bind(form));
		    });
		});
		</script>";

		return $html;
	}
	
	/**
	 * Loads the autocomplete behavior and attaches it to a specified element
	 *
	 * @see    http://mootools.net/forge/p/meio_autocomplete
	 * @return string	The html output
	 */
	public function autocomplete($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'identifier'    => null,
			'element'       => null,
			'filter_column' => 'name'
		));
		
		if(!is_a($config->identifier, 'KServiceIdentifier')) {
		    $config->identifier = $this->getIdentifier($config->identifier);
		}
		
		$config->append(array(
		    'url'     => JRoute::_('&option=com_'.$config->identifier->package.'&view='.$config->identifier->name.'&format=json', false)
		))->append(array(
		    'options' => array(
		        'filter'     => array(
		            'type' => 'contains',
		            'path' => $config->filter_column			        
		        ),
		        'urlOptions' => array(
		            'queryVarName' => 'search'
		        ),
		        'requestOptions' => array(
		            'method' => 'get'
		        )
		    )
		));
		
		$html = '';
		
		// Load the necessary files if they haven't yet been loaded
		if(!isset(self::$_loaded['autocomplete']))
		{
		    $html .= '<script src="media://lib_koowa/js/autocomplete.js" />';
		    $html .= '<script src="media://lib_koowa/js/patch.autocomplete.js" />';
		    $html .= '<style src="media://lib_koowa/css/autocomplete.css" />';
		}
		
		$html .= "
		<script>
			window.addEvent('domready', function(){				
				new Koowa.Autocomplete($('".$config->element."'), ".json_encode($config->url).", ".$config->options.");
			});
		</script>";

	    return $html;
	}
}