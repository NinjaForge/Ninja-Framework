<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: grid.php 1002 2011-04-07 19:02:17Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Template Pagination Helper
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Template
 * @subpackage	Helper
 */
class ComNinjaHelperGrid extends KTemplateHelperGrid
{
	/**
	 * Boolean for grid sort to check if sortables is active
	 *
	 * @var boolean
	 */
	public $sortables = null;
	
	/**
	 * Boolean telling us the color of the zebra stripe, true = white & false = black
	 *
	 * @var boolean
	 */
	public $zebra = false;
	
	/**
	 * The grid offset, used in the count method.
	 *
	 * @var int
	 */
	public $offset = null;
	
	protected $_toggle = false;
	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		if(KFactory::get('admin::com.ninja.helper.default')->framework()!='jquery') JHTML::_('behavior.mootools');
		KFactory::get('admin::com.ninja.helper.default')->js('/grid.js');
		KFactory::get('admin::com.ninja.helper.default')->css('/grid.css');
	}

	/**
	 * Render a select box with limit values for a grid
	 *
	 * @param 	int		Currenct limit
	 * @return 	string	Html select box
	 */
	public function checkall($config = array())//$formid = null, $name = "toggle")
	{
		$config = new KConfig($config);

		$config->append(array(
			'form'	=> KFactory::get('admin::com.ninja.helper.default')->formid(),
			'name'	=> 'toggle'
		));

		$config->append(array(
			'element'	=> implode('-', array($config->form, $config->element))
		));
		
		KFactory::get('admin::com.ninja.helper.default')->js('/grid.js');
		if(KFactory::get('admin::com.ninja.helper.default')->framework() == 'jquery') {
			KFactory::get('lib.joomla.document')->addScriptDeclaration("jQuery(function($){
				$('#" . $config->form . "').click(function(event){
					if($(event.target).is('[type=checkbox].id')) $(event.target).count();
				}).selectables();
				
				$('#" . $config->element . "').click(function(event) {
					$('#" . $config->element . "').checkall();
				});
			});");
		} else {
			KFactory::get('lib.joomla.document')->addScriptDeclaration('window.addEvent(\'domready\', function(){
				$(\'' . $config->form . '\').addEvent(\'click\', function(event){
					if($(event.target).getProperty(\'type\') == \'checkbox\' && $(event.target).hasClass(\'id\')) $(event.target).count();
				}).selectables();

				$(\'' . $config->element . '\').addEvent(\'click\', function(event) {
					$(\'' . $config->element . '\').checkall();
				});
			});');
		}

		return '<input type="checkbox" id="' . $config->element . '" class="toggle" value="" />';
	}
	
	/**
	* @param int The row index
	* @param int The record id
	* @param boolean
	* @param string The name of the form element
	* @return string
	*/
	public function id($config = array())//$id, $locked = false, $name = 'id', $label = false, $checked = false )
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'value'			=> false,
			'locked'		=> false,
			'name'			=> 'id',
			'label'			=> KInflector::humanize(KInflector::singularize(KRequest::get('get.view', 'cmd'))),
			'checked'		=> false
		));
		
		if($config->checked) $config->checked = ' checked="checked"';

		if ( $config->locked ) {
			return '';
		}
		
		return '<input type="checkbox" name="'.$config->name.'[]" class="id validate-reqchk-byname label:\''.$config->label.'\'" value="'.$config->value.'"'.$config->checked.' />';
	}

	public function checkedOut($config = array())// $row, $i, $identifier = 'id' )
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'row'		=> null,
			'column'	=> 'id'
		));
		
		$user   = KFactory::get('lib.joomla.user');
		$userid = $user->get('id');

		$result = false;
		if(is_a($config->row, 'JTable')) {
			$result = $config->row->isCheckedOut($userid);
		} else {
			$result = JTable::isCheckedOut($userid, $config->row->checked_out);
		}

		$checked = '';
		if ( $result ) {
			$checked = self::_checkedOut( $config->row );
		} else {
			$checked = self::id(array('value' => $config->row->{$config->column}));
		}

		return $checked;
	}

	protected function _checkedOut( $row, $overlib = 1 )
	{
		$hover = '';
		if ( $overlib )
		{
			$db = KFactory::get('lib.koowa.database.adapter.mysqli');
			$query = $db->getQuery()
				->select('name')
        		->from('users')
				->where('id', '=', $row->checked_out);	

			$text = addslashes(htmlspecialchars($db->select($query, KDatabase::FETCH_OBJECT)->name));

			$hover = '<span class="editlinktip hasTip" title="'. JText::_( 'Checked Out' ) .'::'. JText::sprintf('By %s', $text) .'">';
		}
		$image = KRequest::root().'/media/com_ninja/images/16/lock.png';
		$checked = $hover .'<img src="'.$image.'"/><input type="hidden" name="id[]" value="'.$row->id.'" disabled="disabled" /></span>';

		return $checked;
	}
	
	public function sortby($title, $model, $order = null)
	{
		$img = '';
		if (!$order) $order = KInflector::underscore($title);
		if ($order == 'id') $order = $model->getModel()->getTable()->getPrimaryKey();
		if($sortables = $this->sortables) $sortables = 'ordering';
		
		//$model = $model->getModel()->getState();
		
		// cleanup
		$direction = $model->direction ? $model->direction : 'asc';
		$direction	= strtolower($direction);
		$direction 	= in_array($direction, array('asc', 'desc')) ? $direction : 'asc';
		
		$state = $model->getState();
		$state = (array) $state['order'];
		$selected = $model->selected ? $model->selected : 0;
		// only for the current sorting
		if($state['value'] === $order)
		{
			$img = KTemplateAbstract::loadHelper('image.template',   'sort_'.$direction.'.png', 'images/', NULL, NULL);
			if($direction == 'desc') $order = $sortables;
			$direction =  $direction == 'desc' ? 'asc' : 'desc'; // toggle
			
		}

		// modify url
		$url = clone KRequest::url();
		$query = $url->getQuery(1);
		$query['order'] 	 = $order;
		$query['direction'] = $direction;
		$url->setQuery($query);

		// render html
		$html  = '<a href="'.JRoute::_($url).'" title="'.JText::_('Click to sort by this column').'">';
		$html .= JText::_($title).$img;
		$html .= '</a>';

		return $html;
	}
	
	public function sort($config = array())
	{
		$config = new KConfig($config);
		
		//Set defaults
		$config->append(array(
			'title' => 'Title'
		));
		
		//Set more defaults
		$config->append(array(
			'order' => KInflector::underscore($config->title),
			'icon'	=> JHTML::_('image.administrator',   'sort_'.$config->direction.'.png', 'images/', NULL, NULL)
		));
		
		$img = '';
		if($sortables = $this->sortables) $sortables = 'ordering';
		
		//$model = $model->getModel()->getState();
		
		//$selected = $model->selected ? $model->selected : 0;
		// only for the current sorting
		if($config->sort === $config->order)
		{
			$img = $config->icon;
			$config->direction =  $config->direction == 'desc' ? 'asc' : 'desc'; // toggle
		}

		// modify url
		$url = clone KRequest::url();
		$query = $url->getQuery(1);
		$query['sort'] 	 = $config->order;
		$query['direction'] = $config->direction;
		$url->setQuery($query);

		// render html
		$html  = '<a href="'.JRoute::_($url).'" title="'.JText::_('Click to sort by this column').'" class="grid-sort">';
		$html .= JText::_($config->title).$img;
		$html .= '</a>';

		return $html;
	}
	
	/**
	 * Ajax toggler
	 *
	 * @author 		Stian Didriksen <stian@ninjaforge.com>
	 */
	public function toggle($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'view'		=> KInflector::singularize(KRequest::get('get.view', 'cmd')),
			'toggle'	=> 'enabled',
			'enabled'	=> true,
			'text'		=> '',
			//ID is optional, useful if togglers are used outside .adminlist tables
			'id'		=> false
		));

		if(!$this->_toggle) {
			KFactory::get('admin::com.ninja.helper.default')->js('/toggle.js');
			$this->_toggle = true;
		}
		$change = $config->{$config->toggle} ? 0 : 1;
		$href = '
		<a class="icon-toggle icon-toggle-' . $config->toggle . '-' . json_encode((bool)$config->{$config->toggle}) . ' toggle-state" rel="' . str_replace('"', "'", json_encode(array('toggle' => $config->toggle, $config->toggle => (int)$config->{$config->toggle}, 'id' => $config->id))) . '" title="' . $config->text . '" data-view="'.$config->view.'"></a>'
		;

		return $href;
	}
	
	/**
	 * function description
	 *
	 * @author 		Stian Didriksen <stian@ninjaforge.com>
	 * @return 		returns an image nested in an anchor element.
	 */
	public function enable( $item, $enabled = 1, $id = null, $imgY = 'enable.png', $imgX = 'disable.png')
	{
				
		if (property_exists((object)$item->getData(), 'enabled')) $enable = $item->enabled;
		else if (property_exists((object)$item->getData(), 'published')) $enable = $item->published;
		$id		= $item->id ? $item->id : $id;
	
		$img 	= $enable ? $imgY : $imgX;
		$alt 	= $enable ? JText::_( 'Enabled' ) : JText::_( 'Disabled' );
		$text 	= $enable ? JText::_( 'Disable Item' ) : JText::_( 'Enable Item' );
		$action = $enable ? 'disable' : 'enable';

		$href = '
		<a href="#" onclick="Koowa.Form.addField(\''.$action.'\', \'cb'. $id .'\');Koowa.Form.addField(\'action\', \''.$action.'\');Koowa.Form.submit(\'post\')" title="'. $text .'">
		<img src="'.JURI::root().'media/com_ninja/images/16/'. $img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}
	
	/**
	 * function description
	 *
	 * @author 		Stian Didriksen <stian@ninjaforge.com>
	 * @return 		returns an image nested in an anchor element.
	 */
	public function required( $enable, $id, $imgY = 'enable.png', $imgX = 'disable.png')
	{
		//Load koowa javascript
		KTemplateAbstract::loadHelper('script', KRequest::root().'/media/plg_koowa/js/koowa.js');

		$img 	= $enable ? $imgY : $imgX;
		$alt 	= $enable ? JText::_( 'Required' ) : JText::_( 'Optional' );
		$text 	= $enable ? JText::_( 'Make item optional' ) : JText::_( 'Require Item' );
		$action = $enable ? 'optional' : 'required';

		$href = '
		<a href="#" onclick="Koowa.Form.addField(\''.$action.'\', \'cb'. $id .'\');Koowa.Form.addField(\'action\', \''.$action.'\');Koowa.Form.submit(\'post\')" title="'. $text .'">
		<img src="'.JURI::root().'media/com_ninja/images/16/'. $img .'" border="0" alt="'. $alt .'" />
		</a>'
		;

		return $href;
	}
	
	public function sortables($option = array())
	{
		$this->sortables = true;
		$document = JFactory::getDocument();
		
		KFactory::get('admin::com.ninja.helper.default')->js('/sortables.js');
		KFactory::get('admin::com.ninja.helper.default')->css('/sortables.css');
		
		$options 			= new KObject;
		$options->form		= KFactory::get('admin::com.ninja.helper.default')->formid();
		$options->list 		= 'tbody.sortable';
		$options->handles 	= 'td.handle';
		$options->set((array)$option);
		
		$script = 'window.addEvent(\'domready\', function(){ new NSortables(' . json_encode($options) . '); });';
		$document->addScriptDeclaration($script);
	}
	
	public function zebra($config = array())//$white = 'row0', $black = 'row1')
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'even'	=> 'row0',
			'odd'	=> 'row1'
		));
		
		//This is a workaround since KTemplateHelper::factory calls KFactory::tmp
		$helper = KFactory::get($this->getIdentifier());
		
		$helper->zebra = $helper->zebra ? false : true;
		
		return $helper->zebra ? $config->even : $config->odd;
	}
	
	public function count($config = array())
	{
		$config = new KConfig($config);
		
		//This is a workaround since KTemplateHelper::factory calls KFactory::tmp
		$helper = KFactory::get($this->getIdentifier()); 
		
		if(!$helper->offset) $helper->offset = $config->offset;
		if(!$config->total && !$config->title)	return ++$helper->offset;
		
		if($config->total <= 10)	return false;
		elseif($config->title)		return '<th class="grid-count">' . JText::_('NUM') . '</th>';
		else				return '<td class="grid-count">' . ++$helper->offset . '</td>';
	}
	
	public function placeholders($config = array())//$rows = 0, $columns = 3, $toggle = false)
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'total'		=> 0,
			'colspan'	=> 3,
			'offset'	=> $config->offset,
			'toggle'	=> $config->total < 10
		));
		
		$config->total -= $config->offset;
		$config->range  = $config->total > 0 ? 1 : 2;

		if($config->total >= 10) return false;
		$html = array();
		foreach(range($config->range, (10 - $config->total), 1) as $row)
		{
			$html[] = '<tr class="' . self::zebra() . '">';
			foreach(range(1, $config->colspan - (int)$config->toggle) as $column)
			{
				$html[] = '<td>&nbsp;</td>';
			}
			$html[] = '</tr>';
		}
		return implode($html);
	}
	
	public function isCheckedOut( $row, $i, $identifier = 'id' )
	{
		if(!property_exists($row, 'checked_out')) return false;
		$user   = KFactory::get('lib.joomla.user');
		$userid = $user->get('id');

		$result = false;
		if(is_a($row, 'JTable')) {
			$result = $row->isCheckedOut($userid);
		} else {
			$result = JTable::isCheckedOut($userid, $row->checked_out);
		}

		return $result;
	}
	
	/**
	 * Display a radio list for filtering the list based on specified filters
	 *
	 */
	public function filter($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'name'		=> 'enabled',
		))->append(array(
			'selected'	=> $config->state->{$config->name},
			'list'	=> array(
				(object) array('id' => '', 'title' => JText::_('All')),
				(object) array('id' => '1', 'title' => JText::_('Enabled')),
				(object) array('id' => '0', 'title' => JText::_('Disabled'))
			)
		));
		
		KFactory::get('admin::com.ninja.helper.default')->css('/select.css');
		
		$url = clone KRequest::url();
		$url->query[$config->name] = '';
		KFactory::get('lib.joomla.document')->addScriptDeclaration("
window.addEvent('domready', function(){
	$$('[name=".$config->name."][checked]').getNext().addClass('selected');
	new Element('label', {'class': 'divider'}).inject($('".$config->name."').getNext(), 'after');
	$$('[name=".$config->name."]').addEvent('change', function(){
		this.getSiblings('label').removeClass('selected');
		this.getNext().addClass('selected');
		window.location.href = '$url'.replace('&".$config->name."=', '&".$config->name."='+this.get('value'));
	});		
});");

		return '<div class="ninja-filter '.$config->name.'">'.KTemplateHelperSelect::radiolist($config).'</div>';
	}
}