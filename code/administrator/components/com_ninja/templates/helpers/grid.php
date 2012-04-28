<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Grid Helper
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperGrid extends KTemplateHelperGrid
{	
	/**
	 * The grid offset, used in the count method.
	 *
	 * @var int
	 */
	public static $offset = null;
	
	/**
	 * Constructor
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$this->getService('ninja:template.helper.document')->load('/grid.css');
	}
	
	/**
	 * Method for rendering a edit item link
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.grid');
	 * $helper->edit(array('row' => $row));
	 * $helper->edit(array('row' => $row, 'column' => 'description', 'link' => '&view=anotherview&id='.$row->id, 'escape' = false));
	 *
	 * // Inside a template layout
	 * <?= @ninja('grid.edit', array('row' => $row)) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html 
	 */
	public function edit($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'row'		=> null,
			'column'	=> 'title',
			'id'		=> 'id',
			'filter'	=> false,
			'escape'	=> true,
		))->append(array(
			'link'		=> 'view='.  KInflector::singularize(KRequest::get('get.view', 'cmd')) .'&id='.$config->row->{$config->id}

		));
		
		$view = $this->getTemplate()->getView();
		
		$text = $config->escape ? $view->escape($config->row->{$config->column}) : $config->row->{$config->column};
		
		if($config->filter) $text = $config->filter->sanitize($text);
		
		return ($config->row->isLockable() && $config->row->locked()) ? '<span style="cursor:default;">' . $config->row->{$config->column} . '</span>' : '<a href="'.$view->createRoute($config->link).'">'. $text . '</a>';
	}
	
	/**
	 * Method for rendering an Ajax state toggler
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.grid');
	 * $helper->toggle(array('enabled' => $row->enabled));
	 * $helper->toggle(array('enabled' => $row->featured, 'toggle' => 'featured', 'text' => 'optional link title'));
	 *
	 * // Inside a template layout
	 * <?= @ninja('grid.toggle', array('enabled' => $row->enabled)) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html 
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

		$change = $config->{$config->toggle} ? 0 : 1;
		$href = '
		<a class="icon-toggle icon-toggle-' . $config->toggle . '-' . json_encode((bool)$config->{$config->toggle}) . ' toggle-state" rel="' . str_replace('"', "'", json_encode(array('toggle' => $config->toggle, $config->toggle => (int)$config->{$config->toggle}, 'id' => $config->id))) . '" title="' . $config->text . '" data-view="'.$config->view.'"></a>'
		;

		$href .= $this->getService('ninja:template.helper.document')->render('/toggle.js');

		return $href;
	}
	
	/**
	 * Method for rendering the item count of list views
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.grid');
	 * $helper->count(array('total' => $total));
	 *
	 * // Inside a template layout
	 * <?= @ninja('grid.count', array('total' => $total)) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	boolean|string	if the total is less than 10 boolean false or Html
	 */
	public function count($config = array())
	{
		$config = new KConfig($config);
		
		if(!self::$offset) self::$offset = $config->offset;
		if(!$config->total && !$config->title)	return ++self::$offset;
		
		if($config->total <= 10)	return false;
		elseif($config->title)		return '<th class="grid-count">' . JText::_('COM_NINJA_NUM') . '</th>';
		else				return '<td class="grid-count">' . ++self::$offset . '</td>';
	}
	
	/**
	 * Method for rendering placeholder (empty) table rows
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.grid');
	 * $helper->placeholders(array('total' => $total, 'colspan' => 9));
	 *
	 * // Inside a template layout
	 * <?= @ninja('grid.placeholder', array('total' => $total, 'colspan' => 9) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	boolean|string	if the total is less than 10 boolean false or Html
	 */
	public function placeholders($config = array())
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
			$html[] = '<tr>';
			foreach(range(1, $config->colspan - (int)$config->toggle) as $column)
			{
				$html[] = '<td>&nbsp;</td>';
			}
			$html[] = '</tr>';
		}
		return implode($html);
	}
	
	/**
	 * Method for rendering a radio list of filters for filtering the item list
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.grid');
	 * $helper->filter(array('state' => array('enabled' => $state->enabled)));
	 *
	 * // Inside a template layout
	 * <?= @ninja('grid.filter', array('state' => array('enabled' => $state->enabled))) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	boolean|string	if the total is less than 10 boolean false or Html
	 */
	public function filter($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'name'		=> 'enabled',
		))->append(array(
			'selected'	=> $config->state->{$config->name},
			'list'	=> array(
				(object) array('id' => '', 'title' => JText::_('COM_NINJA_ALL')),
				(object) array('id' => '1', 'title' => JText::_('COM_NINJA_ENABLED')),
				(object) array('id' => '0', 'title' => JText::_('COM_NINJA_DISABLED'))
			)
		));
		
		$this->getService('ninja:template.helper.document')->load('/select.css');
		
		$url = clone KRequest::url();
		$url->query[$config->name] = '';
		JFactory::getDocument()->addScriptDeclaration("
window.addEvent('domready', function(){
	$$('[name=".$config->name."][checked]').getNext().addClass('selected');
	new Element('label', {'class': 'divider'}).inject($('".$config->name."').getNext(), 'after');
	$$('[name=".$config->name."]').addEvent('change', function(){
		this.getSiblings('label').removeClass('selected');
		this.getNext().addClass('selected');
		window.location.href = '$url'.replace('&".$config->name."=', '&".$config->name."='+this.get('value'));
	});		
});");

		return '<div class="ninja-filter '.$config->name.'">'.$this->getService('koowa:template.helper.select')->radiolist($config).'</div>';
	}

	/**
     * Method for generating ordering arrows for nest item sets
     *
     * @param   KConfig  $config  Nooku configuration data
     * @return  string   HTML-Code for output
     */
    public function nestedorder($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'row' => null,
            'rows' => null,
            'id' => 'id',
            'lft' => 'lft',
            'rgt' => 'rgt',
            'level' => 'level',
            'ordering' => 'ordering',
            'data' => array('order' => 0)
        ));

        $up = 'media://lib_koowa/images/arrow_up.png';
        $down = 'media://lib_koowa/images/arrow_down.png';

        $row = $config->row;
        $rows = clone $config->rows;

        $cid = $config->id;
        $clft = $config->lft;
        $crgt = $config->rgt;
        $clevel = $config->level;
        $ordering = $row->{$config->ordering};

        $rowIsFirst = false;
        $rowIsLast = false;
        $rowHasPrev = false;
        $rowHasNext = false;

        $showUp = false;
        $showDown = false;
        $showLeft = false;
        $showRight = false;

        $first = true;
        $lastId = -1;
        $prevRow = true;
        $prevRowData = null;

        foreach ($rows as $r)
        {
            if ($r->$cid == $row->$cid)
            {
                $prevRow = false;
                continue;
            }

            if ($r->$clevel == $row->$clevel && ($r->$clft - 1 == $row->$crgt || $r->$crgt + 1 == $row->$clft))
            {
                if ($prevRow)
                    $showUp = true;
                else
                    $showDown = true;
            }
        }


        $config->data->order = -1;
        $updata = str_replace('"', '&quot;', $config->data);

        $config->data->order = +1;
        $downdata = str_replace('"', '&quot;', $config->data);

        $html = '';
        if ($showUp)
            $html .= '<span><img src="' . $up . '" border="0" alt="' . JText::_('Move up') .
                '" data-action="edit" data-data="' . $updata . '" /></span>';
        else
            $html .= '';

        if ($showDown)
            $html .= '<span><img src="' . $down . '" border="0" alt="' . JText::_('Move down') .
                '" data-action="edit" data-data="' . $downdata . '"/></span>';
        else
            $html .= '';

        return $html;
    }
}