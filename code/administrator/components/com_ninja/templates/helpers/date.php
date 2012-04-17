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
 * Date Helper - for converting dates to/from formats
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperDate extends KTemplateHelperAbstract
{
	/**
	 * Gives twitter style, human readable but still accurate datetime presentations
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.date');
     * $helper->html(array('date' => $row->created_on));
     * $helper->html(array('date' => $row->created_on, 'html' => false));
     *
     * // Inside a template layout
     * <?= @ninja('date.html', array('date' => $row->created_on)) ?>
     * <?= @ninja('date.html', array('date' => $row->created_on, 'html' => false)) ?>
     * </code>
	 *
	 * @param	array	optional array of configuration options
     * @return	string	Html
	 */
	public function html($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'date'	=> null,
			'html'	=> true
		));
		
		if(empty($config->date)) return JText::_('COM_NINJA_NO_DATE_PROVIDED');
		
		$periods		= array('second', 'minute', 'hour', 'day');
		$lengths		= array(60, 60, 24, 7);
	
		$now			= strtotime(gmdate('Y-m-d H:i:s'));
		$unix_date		= strtotime($config->date);
		$datetime		= new DateTime($config->date, new DateTimeZone('UTC'));


		// check validity of date
		if(empty($unix_date)) return;
	
		// is it future date or past date
		if($now >= $unix_date)
		{    
			$difference	= $now - $unix_date;
			$tense		= 'ago';
		}
		else
		{
			$difference	= $unix_date - $now;
			$tense		= 'from now';
		}
		
		//If the number is equal to or over 24 hours then change to a less relative format
		if($difference < 604800 && $difference > -604800)
		{
			for($i = 0; $difference >= $lengths[$i] && $i < 3; $i++)
			{
				$difference /= $lengths[$i];
			}
			$difference = round($difference);
			
			if($difference != 1) $periods[$i].= 's';
	
			$html  = sprintf(JText::_('%s '.$periods[$i].' '.$tense), $difference);
		}
		elseif(gmdate('Y') == gmdate('Y', $unix_date))
		{
			$html = $this->format(array('date' => $config->date, 'format' => JText::_('%d %b')));
		}
		else
		{
			$html = $this->format(array('date' => $config->date, 'format' => JText::_('%d %b %y')));
		}

		if($config->html) return '<span title="'.$this->format(array('date' => $config->date)).'" data-date="'.$datetime->format('r').'">' . $html . '</span>';

		return  $html;
	}
	
	/**
	 * Formats the date according to the current locale and timezone offset
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.date');
     * $helper->format(array('date' => $row->created_on));
     * $helper->format(array('date' => $row->created_on, 'format' => JText::_('%d %b %y')));
     *
     * // Inside a template layout
     * <?= @ninja('date.format', array('date' => $row->created_on)) ?>
     * <?= @ninja('date.format', array('date' => $row->created_on, 'format' => JText::_('%d %b %y'))) ?>
     * </code>
	 *
	 * @param	array	optional array of configuration options
     * @return	string	Html
	 */
	public function format($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'date'			=> null,
			'format'		=> JText::_('COM_NINJA_DATE_FORMAT_LC2'),
			'gmt_offset'	=> JFactory::getUser()->getParameters()->get('timezone', JFactory::getConfig()->getValue('offset')),
		));

		return $this->getService('koowa:template.helper.date')->format($config);
	}
}