<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: link.php 794 2011-01-10 18:44:32Z stian $
 * @category	NinjaForge Plugin Manager
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Install button class for a toolbar
 * 
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Napi
 * @package		Napi_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonLink extends KToolbarButtonDefault
{
	/**
	 * If link is modal or not
	 *
	 * @var boolean
	 */
	public $modal = false;

	public function __construct(KConfig $options)
	{
		$options->option	= !empty($options->option) ? $options->option : KRequest::get('get.option', 'cmd');
		parent::__construct($options);
	}
	
	public function getLink($options = array())
	{
		$option = $this->_options['option'];
		$view	= KRequest::get('get.view', 'cmd');
		// modify url
		$url = clone KRequest::url();
		$query = new KObject;
		$query->set($url->getquery(1));
		//$query['view']	= 'settings';
		$query->order		= null;
		$query->direction	= null;
		$query->limit		= null;
		$query->offset		= null;
		if ( isset($query->tmpl) ) JTML::_('behavior.modal'); $this->modal = true;
		//$query['layout']= 'default';
		$query->set($options);
		$url->setQuery($query->get());
		return $url;
	}
	
	public function render()
	{
		$name = $this->getName();
		$img = KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.img', '/32/'.$name.'.png');
		if($img)
		{
			KTemplateAbstract::loadHelper('admin::com.ninja.helper.default.css', '.toolbar .icon-32-'.$name.' { background-image: url('.$img.'); }');
		}
	
		$text	= JText::_($this->_options['text']);
		
		$view	= KRequest::get('get.view', 'cmd');
		$link	= ' href="'. JRoute::_($this->getLink()) .'"';
		
		$html 	= array ();
		
		
		// Sanitize the url since we can't trust the server var
		$url = KFactory::get('lib.koowa.filter.url')->sanitize($this->getLink());

		// Create the URI object
		$uri = KFactory::tmp('lib.koowa.http.uri', array('uri' => $url));
		$query = $uri->getQuery(1);
			
		$html[]	= '<td class="button" id="'.$this->getId().'">';
		$active = $view == KInflector::variablize(KInflector::pluralize($query['view'])) || $view == KInflector::variablize(KInflector::singularize($query['view']));
		$hide   = !KInflector::isPlural($view);
		if (($active || $hide) || !$this->modal ) {
			$html[]	= '<a class="toolbar inactive">';
		} else {
			$html[]	= '<a'.$link.' onclick="'. $this->getOnClick().'" class="toolbar">';
		}
			$html[]	= '<span class="'.$this->getClass().'" title="'.$text.'">';
			$html[]	= '</span>';
			$html[]	= $text;
		if ((!$active && !$hide) || $this->modal) {
			$html[]	= '</a>';
		} else {
			$html[]	= '</a>';
		}
		
		$html[]	= '</td>';

		return implode(PHP_EOL, $html);
	}
}