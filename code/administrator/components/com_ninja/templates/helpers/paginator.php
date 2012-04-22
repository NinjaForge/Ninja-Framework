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
 * Pagination Helper
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperPaginator extends KTemplateHelperPaginator
{	
	/**
	 * Constructor
	 *
	 * @param   object  An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $options)
	{
	    parent::__construct($options);
		
		$this->set($options->append(array(
			'name' => KRequest::get('get.view', 'cmd', 'items')
		))->toArray());
	}
	
	/**
	 * Method for rendering the item pagination
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.paginator');
	 * $helper->pagination(array('total' => $total));
	 *
	 * // Inside a template layout
	 * <?= @ninja('paginator.pagination', array('total' => $total)) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html
	 * @link  	http://developer.yahoo.com/ypatterns/navigation/pagination/
	 */
	public function pagination($config = array())
	{
	    $isNookuServer = create_function('$var', 'return strpos($var, "Nooku-Server") !== false;');
	    if(array_filter(headers_list(), $isNookuServer) && JFolder::exists($nooku))
	    {
	    	return parent::pagination($config);
	    }
	
		$config = new KConfig($config);
		$config->append(array(
			'total'		 => 0,
			'display'	 => 5,
			'name'       => KRequest::get('get.view', 'cmd', 'items'),
			'offset'     => 0,
			'limit'	     => 0,
			'attribs'    => array('onchange' => 'this.form.submit();'),
			'show_limit' => true,
			'show_count' => true
		));

        $this->_initialize($config);

		$this->getService('ninja:template.helper.document')->load('/pagination.css');

		$view = $config->name;
		$option = KRequest::get('get.option', 'string');
		$items = (int) $config->total === 1 ? KInflector::singularize($view) : $view;
		if($config->total <= 10) return '<div class="pagination"><div class="limit">'.sprintf(JText::_($option.'_LISTING_' . KInflector::humanize($items)), $config->total ).'</div></div>';

		// Get the paginator data
		$list = $this->_items($config);
		$limitlist = $config->total > 10 ? $this->limit($config->toArray()) : $config->total;
		
		$html  = '<div class="pagination">';
		$html .= '<div class="limit">'.sprintf(JText::_($option.'_LISTING_' . KInflector::humanize($items)), $limitlist ).'</div>';
		$html .=  $this->pages($list);
		$html .= '<div class="count"> '.JText::_('COM_NINJA_PAGES').' '.$config->current.' '.JText::_('COM_NINJA_OF').' '.$config->count.'</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * Method for rendering a list of pagination page links
	 *
	 * Examples: 
	 * <code>
	 * $list = $this->getService('koowa:model.paginator')->setData(array(
	 *		'total'  => $total,
	 *		'offset' => $offset,
	 *		'limit'  => $limit,
	 *		'dispay' => 5
	 *	))->getList();
	 *
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.paginator');
	 * $helper->pages($list);
	 *
	 * // Inside a template layout
	 * <?= @ninja('paginator.page', $list) ?>
	 * </code>
	 *
	 * @param	array	$pages an array of page data
	 * @return	string	Html
	 */
	public function pages($pages)
	{
	    $isNookuServer = create_function('$var', 'return strpos($var, "Nooku-Server") !== false;');
	    if(array_filter(headers_list(), $isNookuServer) && JFolder::exists($nooku))
	    {
	    	return parent::pages($pages);
	    }
	
		//We are forced to do this as the folks at Redmond continue to amaze with their shitty software
		jimport('joomla.environment.browser');
		$uagent = JBrowser::getInstance()->getAgentString();
		$windoze = strpos($uagent, 'Windows') ? true : false;
		
		$html = '<ul class="pages">';
		
		$html .= '<li class="first first-child">'.$this->link($pages['first'], 'COM_NINJA_FIRST', $windoze ? '<<' : '&#10094;&#10094;').'</li>';
		$html .= '<li class="previous last-child">'.$this->link($pages['previous'], 'COM_NINJA_PREVIOUS', $windoze ? '<' : '&#10094;').'</li>';
		
		$html .= '</ul>';
		$html .= '<ul class="pages">';
		$count = count($pages['pages']) - 1;
		$i	   = 0;
		$class = null;
		foreach($pages['pages'] as $page) {
			$active = $page->current ? ' active' : '';
			if($i === 0) $class .= ' first-child';
			if($i === $count) $class .= ' last-child';
			$html .= '<li class="page' . $active . $class . '">'.$this->link($page, $page->page).'</li>';
			$class = null;
			$i++;
		}
		
		$html .= '</ul>';
		$html .= '<ul class="pages">';
		$html .= '<li class="next first-child">'.$this->link($pages['next'], 'COM_NINJA_NEXT', $windoze ? '>' : '&#10095;').'</li>';
		$html .= '<li class="last last-child">'.$this->link($pages['last'], 'COM_NINJA_LAST', $windoze ? '>>' : '&#10095;&#10095;').'</li>';

		$html .= '</ul>';
		return $html;
	}
	
	/**
	 * Helper to render a page link
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.paginator');
	 * $helper->link($page['next'], 'Next %s', '>');
	 *
	 * // Inside a template layout
	 * <?= @ninja('paginator.link', $page['next'], 'Next %s', '>') ?>
	 * </code>
	 *
	 * @param 	object 	$pages 	The page object
	 * @param 	string 	$title 	The page link title
	 * @param 	string 	$symbol Optional page symbol 
	 * @return	string	Html
	 */
	public function link($page, $title, $symbol = null)
	{
		$class = $page->current ? 'class="active"' : '';
		
		$title = $symbol ? JText::sprintf($title, $symbol) : JText::_($title);
		if($page->active && !$page->current && $page->offset >= 0) {
			$html = '<a href="'.$this->createLink($page).'" '.$class.'>' . $title . '</a>';
		} else {
			$html = '<span '.$class.'>' . $title . '</span>';
		}
		
		return $html;
	}
	
	/** 
	 * Helper to generate a page link
	 *
	 * @see 	link()
	 * @param 	object 	$page The page object
	 */
	public function createLink($page)
	{
		$url   = clone KRequest::url();
		$query = $url->getQuery(true);
		
		$query['limit']  = $page->limit;	
		$query['offset'] = $page->offset;
		
		return JRoute::_($this->cleanLink((string) $url->setQuery($query)));
	}
	
	/**
	 * Helper to clean a generated link
	 *
	 * @see 	createLink()
	 * @param 	string $url The url to clean
	 */
	public function cleanLink($url = null)
	{
		return str_replace(array('%3D', '%26', '%40', '%7B', '%7D'), array('=', '&', '@', '{', '}'), $url);
	}

	/**
	 * Helper to render a search box
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.paginator');
	 * $helper->search();
	 *
	 * // Inside a template layout
	 * <?= @ninja('paginator.search') ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html
	 */
	public function search($config = array())
	{
		$config = new KConfig($config);
	
		// Set defaults
		$config->append(array(
			'text'		=> JText::_(''.KRequest::get('get.option', 'string').'_FIND_'.KInflector::singularize(KRequest::get('get.view', 'cmd')).''),
			'autosave'	=> KRequest::get('get.option', 'string') . '.' . KRequest::get('get.view', 'string'),
			'size'		=> 50,
			'type'		=> 'search',
			'id'		=> 'search',
			'name'		=> 'search',
			'class'		=> 'inputbox autoredirect',
			'results'	=> 5,
		));
				
		return '<input ' . KHelperArray::toString(array_filter(array(
			'type'			=> $config->type,
			'id'			=> $config->id,
			'name'			=> $config->name,
			'value' 		=> htmlspecialchars($config->search),
			'class'			=> $config->class,
			'placeholder'	=> $config->text,
			'size'			=> $config->size,
			'results'		=> $config->results,
			'autosave'		=> $config->autosave
		))) . '/>';
		
		return '<input type="search" id="search" name="search" value="' . $config->search . '" class="inputbox autoredirect" placeholder="'. $config->text .'" size="'.$config->size.'" results="5" autosave="'.$config->autosave.'" />';
	}
	
	
	/**
	 * Helper to render a <tfoot> element with pagination for those lazy, table-abusing, backend list views.
	 *
	 * Examples: 
	 * <code>
	 * // Inside a template layout
	 * <?= @ninja('paginator.tfoot', array('total' => $total, 'colspan' => 4)) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html
	 */
	public function tfoot($config = array())
	{
		$config = new KConfig($config);

		$config->append(array(
			'total'		=> 0,
			'colspan'	=> 10
		));
		
		if(!$config->total) return (
			'<tbody>'.
				'<tr>'.
					'<td colspan="'.$config->colspan.'" align="center">'.
						'<h2 class="ninja-empty-list">'.
							sprintf(JText::_('COM_NINJA_NO_FOUND'), JText::_($this->name)).
						'</h2>'.
					'</td>'.
				'</tr>'.
			'</tbody>'
		);
		
		$html[] = '<tfoot><tr><td class="pagination-footer" colspan="'.$config->colspan.'">';
		$html[] = self::pagination($config->toArray());
		$html[] = '</td></tr></tfoot>';
		return implode(PHP_EOL, $html);
	}
	
	/**
	 * No Idea what this is, appears to be a generic list of usergroups, this should either be removed or moved to a list helper
	 *
	 * @todo deprecate/clean  see if this can be removed or moved
	 */
	public function usergroup($value = null, $name = 'usergroup', $options = array(), $control_name = 'f')
	{
		if(version_compare(JVERSION,'1.6.0','ge'))
		{
			return JHtml::_('access.usergroup', $name, @$value[$name], 'onchange="this.form.submit()"');
		}
    
    	$node = new KObject;
    	$node->size 	= null;
    	$node->class 	= null;
    	$node->multiple = null;
    	$node->set($options);
    	
    	// modify url
		$url = clone KRequest::url();
		$query = $url->getquery(1);

    	$acl		=& JFactory::getACL();
        $acltree    = $acl->get_group_children_tree( null, 'USERS', false );
        $query['usergroup']	= 0;
 		$reset = (string) $url->setQuery($query);
        $gtree[] 	= JHTML::_('select.option', '', 'by usergroup');
        foreach($acltree as $i => $tree)
        {
        	$tree->text = str_replace(array('&nbsp;', '.', '-'), '', $tree->text);
        	$query['usergroup']	= $tree->value;
        	if(!in_array($tree->value, array(29, 30)))
        	{
        		$gtree[] = $tree;
        	} else {
        		$gtree[] = JHTML::_('select.optgroup', $tree->text);
        	}
        }
        $ctrl    =  $name;
 		
        $attribs    = ' onchange="this.form.submit()" ';
        if ($v = $node->size) {
            $attribs    .= 'size="'.$v.'"';
        }
        if ($v = $node->class) {
            $attribs    .= 'class="'.$v.'"';
        } else {
            $attribs    .= 'class="inputbox autoredirect"';
        }
        if ($m = $node->multiple)
        {
            $attribs    .= 'multiple="multiple"';
            $ctrl        .= '[]';
        }

        return JHTML::_('select.genericlist',   $gtree, $ctrl, $attribs, 'value', 'text', @$value['usergroup'], $name );
    }

	/**
	 * Method to render a select box with limit values
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.paginator');
	 * $helper->limit(array('limit' => 5, 'attribs' => array('class' => 'my-class')));
	 *
	 * // Inside a template layout
	 * <?= @ninja('paginator.limit', array('limit' => 5, 'attribs' => array('class' => 'my-class')));  ?>
	 * </code>
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html 
	 */
	public function limit($config = array())
	{
		$url = KRequest::url();
		$url->query['limit'] = '${limit}';
		$config['attribs']['onchange'] = 'window.location.href = \''.$url.'\'.replace(\'%24%7Blimit%7D\', this.value)';

		return parent::limit($config);
	}
}