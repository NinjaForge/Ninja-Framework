<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: date.php 1014 2011-04-11 16:57:04Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Date helper, gives you things like facebook style '2 hours ago'
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaHelperDate extends KTemplateHelperAbstract
{
	/**
	 * Gives twitter style, more human readable but still accurate datetime presentations
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param $date in datetime format
	 * @return html string
	 */
	public function html($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'date'	=> null,
			'html'	=> true
		));
		
		if(empty($config->date)) return JText::_('No date provided');
		
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
			$html = $this->format($config->date, JText::_('%d %b'));
		}
		else
		{
			$html = $this->format($config->date, JText::_('%d %b %y'));
		}

		if($config->html) return '<span title="'.$this->format($config->date, JText::_('DATE_FORMAT_LC2')).'" data-date="'.$datetime->format('r').'">' . $html . '</span>';

		return  $html;
	}


	/**
	 * Gives facebook style, more human readable datetime presentations
	 *
	 * Past example: 2 days ago
	 *
	 * Future example: 35 seconds from now
	 *
	 * @TODO This is now a legacy call, use date.html from now on instead of date.beautiful
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param $date in datetime format
	 * @return string
	 */
	public function beautiful($config = array())//$date, $title = true)
	{
		return self::html($config);
	}
	
	/**
	 * Formats the date according to the current locale and timezone offset
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param $date in datetime format
	 * @return string
	 */
	public function format($date, $format = false, $offset = false)
	{
		if(!$offset)
		{
			$user	= KFactory::get('lib.joomla.user');
			$offset	= $user->getParameters()->get('timezone', KFactory::get('lib.joomla.config')->getValue('offset'));
		}
		
		if(!$format) $format = JText::_('DATE_FORMAT_LC2');
		
		return KFactory::get('lib.koowa.template.helper.date')->format(array(
			'date'		=>	$date,
			'format'	=>	$format,
			'offset'	=>	$offset
		));
	}
}