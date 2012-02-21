<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @package		Joomla
* @subpackage	Articles
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Renders a author element
 *
 * @package 	Joomla
 * @subpackage	Articles
 * @since		1.5
 */

class NinjaFormParameter extends KObject
{

	/**
	 * The xml object
	 *
	 * @var SimpleXMLElement 
	 */
	protected $_xml;
	
	/**
	 * The array containing the form data
	 *
	 * @var array
	 */
	protected $_data = false;
	
	/**
	 * Array of groups with array of child element objects
	 *
	 * @var array
	 */
	protected $_elements = array();
	
	/**
	 * The default group to render
	 *
	 * @var string
	 */
	protected $_group;
	
	/**
	 * The default form element name
	 *
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
		parent::__construct($options);

		/**
		 * Set the default group to render and the default name for the elements, 
		 * usually that's the <params> element with no "group" attribute and "params"
		 */
		$this->_group  = $options->group;
		$this->_name   = $options->name;
		$this->_render = $options->render;
		$this->grouptag = $options->grouptag;
		
		$this->setXml($options->xml);

		$this->setData($options->data);
		
		$this->groups   = $options->groups;
		
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   array   Options
	 * @return  array   Options
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
	        'group'      => false,
	        'groups'	 => true,
	        'name'		 => 'params',
	        'data'		 => null,
	        'xml'		 => null,
			'render'	 => 'fieldset',
			'grouptag'	 => 'params'
	   	));
	   	
	    parent::_initialize($config);
	}
	
	public function setXml($xml)
	{
		if(is_a($xml, 'SimpleXMLElement'))
		{
			$this->_xml = $xml;
		}
		
		if(file_exists($xml))
		{
			$this->_xml = simplexml_load_file($xml);
			$this->_xml->addAttribute('src', $xml);
		}
		
		else if(is_string($xml))
		{
			$this->_xml = simplexml_load_string($xml);
		}
		
		if($this->_xml)
		{
			$data = array();
			foreach ($this->_xml->{$this->grouptag} as $params)
			{
				if(!isset($params['group'])) continue;
				$group = (string) $params['group'];
				foreach ($params->children() as $param)
				{
					if(!isset($param['default'])) continue;
					$data[$group][(string)$param['name']] = (string) $param['default'];
				}
			}
			if(!$this->_data)	$this->_data = $data;
			else 				$this->_data = array_merge($data, $this->_data);
		}
		
		return $this;
	}
	
	public function getXml()
	{
		return $this->_xml;
	}
	
	public function setGroup($group)
	{
		$this->_group = $group;
		return $this;
	}
	
	public function getGroup()
	{
		return $this->_group;
	}
	
	public function setData($data)
	{
		if(is_array($data))
		{
			$this->_mergeData($data);
			return $this;
		}
		
		if(is_object($data))
		{
			if(is_a($data, 'KConfig')) $data = $data->toArray();
			settype($data, 'array');
			$this->_mergeData($data);
			return $this;
		}
	
		$decoded = json_decode($data, true);
		if(!is_array($decoded))
		{
			$decoded = (array)$this->_decodeINI($data);
		}
		
		$this->_mergeData($decoded);
		return $this;
	}
	
	public function reset()
	{
		$this->_data = false;
		return $this;
	}
	
	protected function _mergeData(Array $data)
	{
		if(!$this->_data)	$this->_data = $data;
		else 				$this->_data = array_merge($this->_data, $data);
	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	protected function _decodeINI($data)
	{
		$rows = explode("\n", $data);
		$data = array();
		
		foreach($rows as $row)
		{
			$parts = explode('=', $row);
			$data[$parts[0]] = isset($parts[1]) ? $parts[1] : false;
		}
		
		return $data;
	}

	/**
	 * Render a parameter type
	 *
	 * @param	object	A param tag node
	 * @param	string	The control name
	 * @return	array	Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
	public function getElement($node)
	{
		if(isset($this->_elements[$this->_group]) && isset($this->_elements[$this->_group][(string) $node['name']]))
		{
			return $this->_elements[$this->_group][(string) $node['name']];
		}
	
		//get the type of the parameter
		$type = (string) $node['type'];
		try
		{
			$identifier = new KServiceIdentifier($type);
		}
		catch(KException $e)
		{
			$identifier = 'ninja:element.'.$type;
		}
		
		$name = $this->_name.'['.$this->_group.']['.(string) $node['name'].']';
		$id   = $this->_name.$this->_group.(string) $node['name'];
		
		if(!$this->groups)
		{
			$name = $this->_name.'['.(string) $node['name'].']';
			$id   = $this->_name.(string)$node['name'];
		}

		$element = $this->getService($identifier, array(
								'parent' => $this,
								'node'	 => $node,
								'value'	 => $this->get((string) $node['name']),
								'field'	 => $this->_name,
								'name'	 => $name,
								'id'	 => $id,
								'group'	 => $this->_group
				  			));

		// error happened
		if ($element === false)
		{
			$result = array();
			$result[0] = (string) $node['name'];
			$result[1] = JText::_('Element not defined for type').' = '.$type;
			$result[5] = (string) $result[0];
			return $result;
		}
		
		return $this->_elements[$this->_group][(string) $node['name']] = $element;
	}
	
	
	/**
	 * Set a value
	 *
	 * @access	public
	 * @param	string The name of the param
	 * @param	string The value of the parameter
	 * @return	string The set value
	 * @since	1.5
	 */
	public function set($key, $value = '')
	{
		$this->_data[$this->_group][$key] = $value;
		
		return $this;	
	}

	/**
	 * Get a value
	 *
	 * @access	public
	 * @param	string The name of the param
	 * @param	mixed The default value if not found
	 * @return	string
	 * @since	1.5
	 */
	public function get($key)
	{
		$value = false;
		
		if(!$this->groups)
		{
			if(isset($this->_data[$key])) return $this->_data[$key];
		}
		
		if(!isset($this->_data[$this->_group]))       return $value;
		if(!isset($this->_data[$this->_group][$key])) return $value;
		
		return $this->_data[$this->_group][$key];
	}
	
	public function __toString()
	{
		return $this->render();
	}

	public function render()
	{
		$html = array();
		$backup = $this->_group;
		$closeHTML = null;//just initialize here, it will get overwritten later
		
		$i = 0;
		foreach($this->toArray() as $name => $params)
		{
			$this->_group = $name;
			$group  = current($this->getParams());
			$class  = @$group['class'];
			$title  = @$group['title'];
			$legend = KInflector::humanize((string)$group['group']);
		  
			switch ($this->_render){
          case 'fieldset':
          		$switch = isset($group['toggle']) ? ' toggle' : null;
          		if(isset($group['togglehide']) && $group['togglehide'] = 'yes'){
          			$class .=" toggleHide";
          		}    		
          		
              $html[] = '<fieldset class="adminform ninja-form '.$class.$switch.'">';
      				$html[] = '<legend title="'.$title.'">';
      				$html[] = isset($group['legend']) ? JText::_($group['legend']) : $legend;
      				$html[] = '</legend>';
      				if(isset($group['toggle']))
      				{
      					$html[] = '<div class="wrapper switch">';
      					
      					$toggle = simplexml_load_string('<param name="enabled" type="text" default="forum" />');
      					$name  = $this->_name.'['.$this->_group.']['.(string) $toggle['name'].']';
      					$id    = $this->_name.$this->_group.(string) $toggle['name'];
      					$value = $this->get((string) $toggle['name']);
      					
      					
      					//DC - changed to type match to a string to differentiate between a "new" parameter with a default of "enabled" and a toggle that has been saved as off
      					if ($value !== '0' && $group['toggle'] == 'enabled'){
      						$value = 1;      						
      					}
      					$checked = $value ? ' checked="checked"' : null;
      					
      					$this->getService('ninja:template.helper.document')->load('/elements/toggle/touch.js');
      					$this->getService('ninja:template.helper.document')->load('/switch.js');
      					
      					//DC Nov 2010 - moved css into a file as it was being repeated whenever there was more than one toggle on a page
      					$this->getService('ninja:template.helper.document')->load("/toggle.css");					
      					
      					
      					//DC Nov 2010 - temporarily removed the jtext because the buttons don't resize when the text is translated
      					//todo - make the buttons resize and put back translation 
      					$on = 'ON';  //JText::_('on');
      					$off = 'OFF'; //JText::_('off');
      					$access = $this->getService('ninja:template.helper.document')->formid('forum');
      					$this->getService('ninja:template.helper.document')->load('js', "
      						window.addEvent('domready', function() {
      							var toggle = new Switch('".$id."', {
      								focus: true, 
      								onChange: function(state) {
      									var value = (state) ? 1 : 0;
      									this.container.getPrevious().value = value;
      									if(state == 1){
      										this.container.addClass('enabled').removeClass('disabled');
      										$('".$id."').getParent().getParent().getParent().getParent().getParent().removeClass('disabled').fade(1).getElements('input, select, textarea').set('disabled', false);
      										if($('".$id."').getParent().getParent().getParent().getParent().getParent().hasClass('toggleHide')){
      											$('".$id."').getParent().getParent().getParent().getParent().getParent().getChildren('.element').reveal();
      										}
      									} else {
      										this.container.addClass('disabled').removeClass('enabled');
      										$('".$id."').getParent().getParent().getParent().getParent().getParent().addClass('disabled').fade(0.6).getElements('input, select, textarea').set('disabled', 'disabled');
      										$('".$id."').getParent().getParent().getParent().getParent().getElements('.hiddentoggle').set('disabled', false);
      										if($('".$id."').getParent().getParent().getParent().getParent().getParent().hasClass('toggleHide')){
      											$('".$id."').getParent().getParent().getParent().getParent().getParent().getChildren('.element').dissolve();
      										}
      									}
      								}
      							});
      							$('$id').getParent().getNext().set('html', '<span class=\"on\">$on</span><span class=\"off\">$off</span>');
      						});
      					");
      					
      					$html[] = '	<input name="'.$name.'" value="'.$value.'" type="hidden" class="hiddentoggle"/>';
      					$html[] = '	<input type="checkbox" class="toggle inclToggle" id="'.$id.'"'.$checked.' />';
      					$html[] = '</div>';
      				}
      				//closing statement for the above HTML. It is appended later
      				$closeHTML =  '</fieldset>';
              break;
          case 'accordion':
              jimport('joomla.html.pane');
        	    $panel = JPane::getInstance('sliders', array('allowAllClose'=>'true'));
        	  	
        			$html[] = $panel->startPane($class); 			  
        		  $html[] = $panel->startPanel(isset($group['legend']) ? $group['legend'] : $legend, $title);          
        
              $closeHTML = $panel->endPanel().$panel->endPane();
              break;
          case 'tabs':
              jimport('joomla.html.pane');
        	    $panel = JPane::getInstance('tabs', array('allowAllClose'=>'true'));
        	  	//TODO - D: there is a good chance this won't work because the panels should all be in the same pane
        	  	//do soem testing and expand this to opening and closing the pane outside the loop if needed. 
        		$html[] = $panel->startPane($class); 			  
        		$html[] = $panel->startPanel(isset($group['legend']) ? $group['legend'] : $legend, $title);          
        
              $closeHTML = $panel->endPanel().$panel->endPane();
              break;
          default:
              $html[] = '<div class="adminform ninja-form '.$class.'">';
              //closing statement for the above HTML. It is appended later
      		  $closeHTML =  '</div>';
              break;
      
      }
			foreach($params as $param)
			{
				$html[] = $param['element']->before('<div class="element">');
				
				  /**
				   * Here we're using toString for debugging.
				   *
				   * @TODO: Change to (string) $param['element']; when tested stable.
				   *
				   */
				$html[] = $param['element']->toString();
				
				$html[] = $param['element']->after('</div>');
			}
			$group  = $this->_group;
			
			//add our closing statement
      $html[] = $closeHTML;
			
      $i++;
		}              		
		
		return implode($html);
	}

	/**
	 * Render all parameters to an array
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	array	Array of all parameters, each as array Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
	public function toArray()
	{
		$groups = $this->getParams();
		$backup = $this->_group;

		$results = array();
		foreach($groups as $group)
		{
			$this->_group = (string) $group['group'];
			foreach($group->children() as $param)
			{
				if(isset($param['ninja']) && (string)$param['ninja'] == 'void') continue;
				$name = (string) $param['name'];
				$element = $this->getElement($param);
				//$results[$this->_group][$name] = $element->render($param, $this->get($name), $this->_name);
				$results[$this->_group][$name]['element'] = $element;
			}
		}
		
		$this_group = $backup;
		
		return $results;
	}
	
	public function getParams()
	{
		if($this->_group !== false)	return $this->_xml->xpath('//'.$this->grouptag.'[@group="'.$this->_group.'"]');

		$params = (array) $this->_xml->children();

		$result = array();
		$array  = is_array($params[$this->grouptag]) ? $params[$this->grouptag] : array($params[$this->grouptag]);
		foreach ($array as $param)
		{
	
			if(!isset($param['group'])) continue;
			
			$result[] = $param;
		}

		return $result;
	}
}