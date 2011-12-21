<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: abstract.php 913 2011-03-17 18:19:44Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

abstract class ComNinjaElementAbstract extends KObject implements KObjectIdentifiable
{	
	/**
	 * Reference to the object that instantiated the element
	 *
	 * @var ComNinjaFormParameter | [ComNinjaElement instance]
	 */
	protected $_parent;
	
	/**
	 * The element type/name
	 *
	 * This is set based on the identifier during construct
	 *
	 * @var string 
	 */
	protected $_name;

	/**
	 * The element node
	 *
	 * @var SimpleXMLElement 
	 */
	protected $_node;

	/**
	 * The object identifier
	 *
	 * @var KIdentifierInterface 
	 */
	protected $_identifier;
	
	/**
	 * Constructor
	 *
	 * @param	array An optional associative array of configuration settings.
	 */
	public function __construct(KConfig $options)
	{
		// Set the objects identifier first to allow to use it in the initialization
	    $this->_identifier = $options->identifier;
	    
	    // Set the objects node second to allow to use it in the initialization
	    $this->node = $options->node;
		
		parent::__construct($options);
		
		/**
		 * For legacy support, we add a reference to the parent here.
		 * @TODO make getParent() method for this
		 */
		$this->_parent = $options->parent;
		
		$this->_name = $this->_identifier->name;
		
		$this->set($options->toArray());
	}
	
	/**
	 * Initializes the options for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   array   Options
	 * @return  array   Options
	 */
	protected function _initialize(KConfig $options)
	{
		$name		 = isset($options->name) ? $options->name : (string) $this->node['name'];
		$id			 = isset($options->id) 
						? $options->id 
						: isset($this->node['id'])
							? (string) $this->node['id']
							: $name;

		$label		 = isset($this->node['label']) ? (string) $this->node['label'] : false;
		$label  	 = $label ? $label : KInflector::humanize((string) $this->node['name']);
		
		$options->append(array(
	        'parent'		=> false,
			'identifier'	=> null,
			'group'			=> false,
			'fetchTooltip'	=> true,
			'name'			=> $name,
			'id'			=> $id,
			'label'			=> $label
	   	));
	   	
	    parent::_initialize($options);
	}

	public function render()
	{
		$name	= $this->name;
		$label	= $this->label;
		$descr	= isset($this->node['description']) ? (string) $this->node['description'] : false;

		$result[0] = $this->fetchTooltip ? $this->fetchTooltip($label, $descr, $this->node, $this->field, $name) : false;
		$result[1] = $this->fetchElement($name, $this->value, $this->node, $this->field);
		$result[2] = $descr;
		$result[3] = $label;
		$result[4] = $this->value;
		$result[5] = $name;

		return $result;
	}
	
	public function before($string = null)
	{
		return $string;
	}
	
	public function after($string = null)
	{
		return $string;
	}
	
	public function toString()
	{
		$el = $this->render($this->node, $this->value, $this->field);
		
		if(!$el[0] && !$el[1]) return false;

		if($el[0]) $html[] = $el[0];
		
		if($el[1]) $html[] = $el[1];
		
		return implode($html);
	}
	
	/**
	 * To string magic method
	 *
	 * Since the magic method here can't deal with exceptions,
	 * we have a separate method named toString() that we can call directly
	 * for debugging purposes.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	public function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='')
	{
		$output = '<label id="'.$this->id.'-lbl" for="'.$this->id.'"';
		if ($description) {
			//$output .= ' class="hasTip key" title="'.JText::_($label).'::'.JText::_($description).'">';
			//removed label from the tooltip as it looks silly in our new tooltip javascript
			$output .= ' class="hasTip key" title="'.JText::_($description).'">';
		} else {
			$output .= ' class="key">';
		}
		$output .= JText::_( $label ).'</label>';

		return $output;
	}

	public function fetchElement($name, $value, &$xmlElement, $control_name) {
		return;
	}
	
	/**
	 * Get the element name
	 *
	 * @return 	string
	 * @see 	ComNinjaElementAbstract::__construct
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Get the identifier
	 *
	 * @return 	KIdentifierInterface
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	}
	
	/**
	 * Gets a list of rows based on a specified model and optional states
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @return array|KDatabaseRowsetInterface
	 */
	public function getList()
	{
		$model = KFactory::tmp((string)$this->node['get'])->limit(0);
		
		if(isset($this->node['set']))
		{
			$json 	= '{"'.str_replace(array(';', ':'), array('","', '":"'), (string)$this->node['set']).'"}';
			$states = json_decode(str_replace('",""}', '"}', $json));
			$model->set($states);
		}
		
		return $model->getList();
	}
}