<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: paginator.php 861 2011-01-29 12:39:58Z richie $
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
class ComNinjaHelperPaginator extends KTemplateHelperPaginator
{
	protected $_ajax = false;
	
	protected $_ajax_layout = 'default_items';
	
	/**
	 * Constructor
	 *
	 * @param array Associative array of values
	 */
	public function __construct(KConfig $options)
	{
		//Load koowa javascript
		//@TODO get rid of JHTML depencies
		if(KFactory::get('admin::com.ninja.helper.default')->framework() !='jquery') KTemplateHelper::factory('behavior')->mootools();
		
		$this->set($options->append(array(
			'name' => KRequest::get('get.view', 'cmd', 'items')
		))->toArray());
	}
	
	/**
	 * Render item pagination
	 *
	 * @param	array $config	Configuration array
	 * @return	string	Html
	 * @see  	http://developer.yahoo.com/ypatterns/navigation/pagination/
	 */
	public function pagination($config = array())
	{
		$config = new KConfig($config);

		$config->append(array(
			'total'		=> 0,
			'display'	=> 5,
			'ajax'		=> false,
			'name'		=> $this->name
		));

		$this->_ajax = (bool) $config->ajax;
		if(is_string($config->ajax)) $this->_ajax_layout = $config->ajax;
		KFactory::get('admin::com.ninja.helper.default')->css('/pagination.css');

		// Paginator object
		$paginator = KFactory::tmp('lib.koowa.model.paginator')->setData(array(
			'total'  => $config->total,
			'offset' => $config->offset,
			'limit'  => $config->limit,
			'dispay' => $config->display
		));

		$view = $config->name;
		$items = (int) $config->total === 1 ? KInflector::singularize($view) : $view;
		if($config->total <= 10) return '<div class="pagination"><div class="limit">'.sprintf(JText::_('Listing %s ' . KInflector::humanize($items)), $config->total ).'</div></div>';

		// Get the paginator data
		$list = $paginator->getList();
		$limitlist = $config->total > 10 ? $this->limit($config->toArray()) : $config->total;
		
		$html  = '<div class="pagination">';
		$html .= '<div class="limit">'.sprintf(JText::_('Listing %s ' . KInflector::humanize($items)), $limitlist ).'</div>';
		$html .=  $this->pages($list);
		$html .= '<div class="count"> '.JText::_('Pages').' '.$paginator->current.' '.JText::_('of').' '.$paginator->count.'</div>';
		$html .= '</div>';
		
		if($this->_ajax)
		{	
			jimport('joomla.environment.browser');
			$uagent			= JBrowser::getInstance()->getAgentString();
			$windoze		= strpos($uagent, 'Windows') ? true : false;
			$url			= clone KRequest::url();
			$url->fragment	= 'offset=@{offset}';
			$formid			= KFactory::tmp('admin::com.ninja.helper.default')->formid();
			$cookie			= KRequest::get('cookie.' . $formid, 'string', false);
			$states			= array( 'total'  => $total, 'offset' => $offset, 'limit'  => $limit, 'display' => $display );
			if($cookie)
			{
				$merge = KHelperArray::merge(json_decode($cookie, true), $states);
				KRequest::set('cookie.' . $formid, json_encode($merge), 'string');
			}
			//Temp fix
			$cookie = false;
			$states			= $cookie ? array() : array('state' => $states);
			KFactory::get('admin::com.ninja.helper.default')->js('/pagination.js');
			KFactory::get('admin::com.ninja.helper.default')->js('window.addEvent(\'domready\', function(){ $$(\'div.pagination\')[0].paginator(' . json_encode(array_merge(array(
				'identificator' => $formid,
				'text' => array(	
					'count'		=> sprintf(JText::_('Pages %s of %s'), '@{current}', '@{total}'),
					'first'		=> sprintf(JText::_('%s First'), $windoze ? '<<' : '&#10094;&#10094;'),
					'previous'	=> sprintf(JText::_('%s Previous'), $windoze ? '<' : '&#10094;'),
					'next'		=> sprintf(JText::_('Next %s'), $windoze ? '>' : '&#10095;'),
					'last'		=> sprintf(JText::_('Last %s'), $windoze ? '>>' : '&#10095;&#10095;')
				)
			),
				$states
			)) . '); });');
		}
		
		return $html;
	}
	
	/**
	 * Render a list of pages links
	 *
	 * @param	araay 	An array of page data
	 * @return	string	Html
	 */
	public function pages($pages)
	{
		//We are forced to do this as the folks at Redmond continue to amaze with their shitty software
		jimport('joomla.environment.browser');
		$uagent = JBrowser::getInstance()->getAgentString();
		$windoze = strpos($uagent, 'Windows') ? true : false;
		
		$html = '<ul class="pages">';
		
		$html .= '<li class="first first-child">'.$this->link($pages['first'], '%s First', $windoze ? '<<' : '&#10094;&#10094;').'</li>';
		$html .= '<li class="previous last-child">'.$this->link($pages['previous'], '%s Previous', $windoze ? '<' : '&#10094;').'</li>';
		
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
		$html .= '<li class="next first-child">'.$this->link($pages['next'], 'Next %s', $windoze ? '>' : '&#10095;').'</li>';
		$html .= '<li class="last last-child">'.$this->link($pages['last'], 'Last %s', $windoze ? '>>' : '&#10095;&#10095;').'</li>';

		$html .= '</ul>';
		return $html;
	}
	
	/**
	 * Render a page link
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	Html
	 */
	public function link($page, $title, $symbol = null, $tmpl = false)
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
	
	public function createLink($page)
	{
		$url   = clone KRequest::url();
		$query = $url->getQuery(true);
		
		$query['limit']  = $page->limit;	
		$query['offset'] = $page->offset;
		
		return JRoute::_($this->cleanLink((string) $url->setQuery($query)));
	}
	
	public function cleanLink($url = null)
	{
		return str_replace(array('%3D', '%26', '%40', '%7B', '%7D'), array('=', '&', '@', '{', '}'), $url);
	}

	/**
	 * Render a search box for a grid
	 *
	 * @param array $config		Helper configuration
	 */
	public function search($config = array())
	{
		$config = new KConfig($config);
	
		// Set defaults
		$config->append(array(
			'text'		=> JText::_('Find '.KInflector::singularize(KRequest::get('get.view', 'cmd')).'&hellip;'),
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
	
	
	/*
	 * Renders a <tfoot> element for those lazy, table-abusing, backend list views.
	 *
	 * @param	array $config 	configuration object
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
				'<tr class="'.KFactory::get('admin::com.ninja.helper.grid')->zebra().'">'.
					'<td colspan="'.$config->colspan.'" align="center">'.
						'<h2 class="ninja-empty-list">'.
							sprintf(JText::_('No %s found'), JText::_($this->name)).
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
	
	public function usergroup($value = null, $name = 'usergroup', $options = array(), $control_name = 'f')
	{
		if(JVersion::isCompatible('1.6.0'))
		{
			return JHtml::_('access.usergroup', $name, $value[$name], 'onchange="this.form.submit()"');
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
	 * Render a select box with limit values
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html select box
	 */
	public function limit($config = array())
	{
		$url = KRequest::url();
		$url->query['limit'] = '${limit}';
		$config['attribs']['onchange'] = 'window.location.href = \''.$url.'\'.replace(\'%24%7Blimit%7D\', this.value)';

		return parent::limit($config);
	}
}