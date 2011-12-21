<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: access.php 1424 2011-11-22 10:27:20Z stian $
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Access Helper
 *
 * @author      Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class NinjaTemplateHelperAccess extends KTemplateHelperAbstract
{

	/**
	 * Array over controller actions
	 *
	 * @param array Associative array of actions
	 */
	protected $_actions = array();
	
	/**
	 * Array over permission levels
	 *
	 * @param array
	 */
	protected $_levels = array('No Access', 'Has Access', 'and Can Create', 'and Can Manage');
	
	/**
	 * Array over asset rules
	 *
	 * @param array Associative array of rules
	 */
	protected $_assetrules = array();
	
	/**
	 * Constructor
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $options)
	{
		$option	= KRequest::get('get.option', 'cmd');
		$view	= KRequest::get('get.view', 'cmd');
		if(KInflector::isPlural($view)) $view = KInflector::singularize($view);
		$id		= KRequest::get('get.id', 'int', false);
		$name	= $id ? $option.'.'.$view.'.'.$id : $option.'.'.$view;
		
		$app	 = JFactory::getApplication()->isAdmin() ? 'admin' : 'site';
		$package = str_replace('com_', null, $option);

		//Override default settings
		$this->set($options->append(array(
			'name'	 => $name,
			'models' => (object) array(
				'assets' 		=> 'com://'.$app.'/'.$package.'.model.assets',
				'user_groups'	=> 'com://'.$app.'/'.$package.'.model.user_groups'
			),
			'inputName' => 'access',
			'inputId'   => 'access',
			'id'	    => 'access',
			'render'	=> 'permissions',
			'objects'	=> array(),
			'default'	=> false
		))->toArray());

		return parent::__construct($options);
		// @TODO this shouldn't be needed anymore
		$actions = isset($options->actions) ? $options->actions : 'ninja:controller.view';
		foreach((array)$actions as $action)
		{
			$this->setActions($this->getService($action)->getActions());
		}
	}
	
	public function setActions($actions)
	{
		$this->_actions = array();
		natsort($actions);
		foreach($actions as $action)
		{
			
			//This is for translation purposes.
			// Example:
			// in language file: ACTIONADD = Create Item
			// will change 'add' => Create Item.
			// We're doing this to have controller actions specific translatable strings,
			// but avoid seeing 'actionadd' when a string is untranslated
			$title			 = 'action'.$action;
			$translatedTitle = JText::_($title);
			$descr			 = 'action'.$action.' desc';
			$translatedDescr = JText::_($descr);
			
			$this->_actions[$action] = (object) array(
				'title'  		=> $title == $translatedTitle ? JText::_($action) : $translatedTitle,
				'description'	=> $descr == $translatedDescr ? false : $translatedDescr
			);
		}
	} 
	
	public function getActions()
	{
		return $this->_actions;
	}
	
	public function setLevels($levels)
	{
		$this->_levels = $levels;
		return $this;
	} 
	
	public function getLevels()
	{
		return $this->_levels;
	}
	
	/**
	 * Translate an access level into an explanatory string
	 *
	 * @param	$level		The access level [0-3]
	 * @return	string
	 */
	public function getLevel($level)
	{
		if($level < 1) return $this->_levels[$level];
	
		$levels = array();
		foreach (range(1, $level) as $i)
		{
			$levels[] = $this->_levels[$i];
		}
	
		return implode(' ', $levels);
	}
	
	public function setAssetRules()
	{
		$rules = $this->getService($this->models->assets)->limit(0)->name($this->name)->getList();
		foreach($rules as $rule)
		{
			$name = end(explode('.', $rule->name));
			$this->_assetrules[$name] = $rule->level;
		}
	}
	
	public function getAssetRules()
	{
		if(!$this->_assetrules) $this->setAssetRules();
		return $this->_assetrules;
	}
	
	public function permissionslist()
	{
		
		// Get the actions for the asset.
		$actions = $this->getActions();

		$rules	 = $this->getAssetRules();
		
		// Get the available user groups.
		$groups = $this->getService($this->models->user_groups)->limit(0)->getList();

		// Build the form control.
		$html = array();

		// Open the table.
		$html[] = '<div id="'.$this->id.'" class="permissions"><table class="permissionlist">';

		// The table heading.
		$html[] = '	<thead>';
		$html[] = '	<tr>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action">'.JText::_('User Group').'</span>';
		$html[] = '		</th>';
		foreach ($actions as $name => $action)
		{
			$html[] = '		<th>';
			$html[] = '			<span class="acl-action '.$name.'" title="'.JText::_($action->description).'">'.JText::_($action->title).'</span>';
			$html[] = '		</th>';
		}
		$html[] = '	</tr>';
		$html[] = '	</thead>';

		// The table body.
		$html[] = '	<tbody>';
		foreach ($groups as $group)
		{
			$html[] = '	<tr>';
			$html[] = '		<th class="acl-groups">';
			$html[] = '			'.$group->title;
			$html[] = '		</th>';
			foreach ($actions as $action => $i)
			{
				$html[] = '		<td>';
				
				$html[] = $this->_createRuleSelect($rules, $action, $group->id);
	
				$html[] = '		</td>';
			}
			$html[] = '	</tr>';
		}
		$html[] = '	</tbody>';

		// Close the table.
		$html[] = '</table></div>';

		return implode($html);
	}
	
	public function render()
	{
		return $this->render == 'usergroups' ? $this->_renderUsergroups() : $this->_renderPermissions(); 
	}
	
	protected function _renderUsergroups()
		{
			$levels = $this->getLevels();
	
			$rules	 = $this->getAssetRules();
			
			// Get the available user groups.
			$groups = $this->objects;
	
			// Build the form control.
			$html = array();
	
			// Open the table.
			$html[] = '<div id="'.$this->id.'" class="permissions"><table class="permissionlist">';
	
			// The table heading.
			$html[] = '	<thead>';
			$html[] = '	<tr>';
			$html[] = '		<th>';
			$html[] = '			<span class="acl-action">'.JText::_('Object').'</span>';
			$html[] = '		</th>';
			$html[] = '		<th colspan="'.count($levels).'" class="permission-level">';
			$html[] = '			<span class="acl-action">'.JText::_('Permissions Level').'</span>';
			$html[] = '		</th>';
			$html[] = '	</tr>';
			$html[] = '	</thead>';
	
			// The table body.
			$html[] = '	<tbody>';

			foreach ($groups as $group)
			{
				$html[] = '	<tr data-object="'.$group.'">';
				$html[] = '		<th class="acl-groups">';
				$html[] = '			'.JText::_(KInflector::humanize($group));
				$html[] = '		</th>';
				
				$active = isset($rules[$group]) ? $rules[$group] : 1;
				foreach ($levels as $i => $level)
				{
					$id 	 = $this->inputId.'-'.$group.'-'.$i;
					$checked = $active == $i ? ' checked="checked"' : ''; 
					
					$html[] = '	<td class="permissions-level level-'.$i.'">';
					$html[] = '		<input type="radio" name="'.$this->inputName.'['.$group.']" id="'.$id.'" value="'.$i.'" '.$checked.' />';
					$html[] = ' 	<label for="'.$id.'">';
					$html[] = 			JText::_($level);
					$html[] = '</label>';
					$html[] = ' </td>';
				}
				$html[] = '	</tr>';
			}
			$html[] = '	</tbody>';
	
			// Close the table.
			$html[] = '</table></div>';
	
			return implode($html);
		}
	
	protected function _renderPermissions()
	{
		$levels = $this->getLevels();

		$rules	 = $this->getAssetRules();
		
		// Get the available user groups.
		$groups = $this->getService($this->models->user_groups)->limit(0)->getList();

		// Build the form control.
		$html = array();

		// Open the table.
		$html[] = '<div id="'.$this->id.'" class="permissions"><table class="permissionlist">';

		// The table heading.
		$html[] = '	<thead>';
		$html[] = '	<tr>';
		$html[] = '		<th>';
		$html[] = '			<span class="acl-action">'.JText::_('User Group').'</span>';
		$html[] = '		</th>';
		$html[] = '		<th colspan="'.count($levels).'" class="permission-level">';
		$html[] = '			<span class="acl-action">'.JText::_('Permissions Level').'</span>';
		$html[] = '		</th>';
		$html[] = '	</tr>';
		$html[] = '	</thead>';

		// The table body.
		$html[] = '	<tbody>';
		foreach ($groups as $group)
		{
			$html[] = '	<tr>';
			$html[] = '		<th class="acl-groups">';
			$html[] = '			'.$group->title;
			$html[] = '		</th>';
			
			$active = isset($rules[$group->id]) ? $rules[$group->id] : 1;
			foreach ($levels as $i => $level)
			{
				$id 	 = $this->inputId.'-'.$group->id.'-'.$i;
				$checked = $active == $i ? ' checked="checked"' : ''; 
				
				$html[] = '	<td class="permissions-level level-'.$i.'">';
				$html[] = '		<input type="radio" name="'.$this->inputName.'['.$group->id.']" id="'.$id.'" value="'.$i.'" '.$checked.' />';
				$html[] = ' 	<label for="'.$id.'">';
				$html[] = 		$level;
				$html[] = '</label>';
				$html[] = ' </td>';
			}
			$html[] = '	</tr>';
		}
		$html[] = '	</tbody>';

		// Close the table.
		$html[] = '</table></div>';

		return implode($html);
	}
	
	protected function _createRuleSelect($rules, $action, $group)
	{
		if(!array_key_exists($action, $rules))
		{
			$value = null;
			if(in_array($action, array('read', 'browse')) && $this->default) $value = 1;
		}
		elseif(array_key_exists($group, $rules[$action]))
		{
			$value = $rules[$action][$group];
		}
		
		$options = array(
			array(
				'value' => null,
				'text'	=> '&hellip;'
			),
			array(
				'value' => '0',
				'text'	=> 'Deny'
			),
			array(
				'value' => 1,
				'text'	=> 'Allow'
			)
		);

		if($this->default) unset($options[0]);
		
		//add, cancel, browse
		$noOwner = array('add', 'cancel', 'browse');
		if(!in_array($action, $noOwner))
		{
			$options[] = array(
				'value' => 2,
				'text'	=> 'Owner Allowed'
			);
		}
		
		return JHTML::_('select.genericlist', $options, $this->inputName.'['.$action.']['.$group.']', null, 'value', 'text', $value, $this->inputId.'-'.$action.'-'.$group, true);
	}
}