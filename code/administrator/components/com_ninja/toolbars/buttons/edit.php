<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
* @version      $Id: edit.php 897 2011-02-23 14:09:23Z betweenbrain $
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Edit button class for a toolbar
 * 
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
class ComNinjaToolbarButtonEdit extends ComNinjaToolbarButtonAbstract
{

	/**
	 * Gives the button 'list' status.
	 *
	 * When the view is plural, and the list has no items, this button wont render
	 *
	 * @var boolean true
	 */
	public $list = true;

	public function __construct(KConfig $options)
	{
		$this->setMethod('edit');
		
		parent::__construct($options);
		
		$this->attribs->class .= ' invalid';
		
		$url = clone KRequest::url();
		$query = $url->getQuery(1);
		if(empty($query['view'])) return;
		$query['view'] = KInflector::singularize($query['view']);
		$url->setQuery($query);
		$this->attribs->href = (string)$url;
	}
}