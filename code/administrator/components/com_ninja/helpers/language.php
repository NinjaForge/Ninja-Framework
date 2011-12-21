<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: language.php 794 2011-01-10 18:44:32Z stian $
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Adds untranslated strings (orphans) to the component language file
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaHelperLanguage extends KObject
{	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$lang = &JFactory::getLanguage();
		$orphans = $lang->getOrphans();
		if ($orphans)
		{
			ksort( $orphans, SORT_STRING );
			$guesses = array();
			foreach ($orphans as $key => $occurance) {
				if (is_array( $occurance ) AND isset( $occurance[0] )) {
					$info = &$occurance[0];
					$file = @$info['step']['file'];

					$guess = str_replace( '_', ' ', $info['string'] );
					// Integers isn't translatable
					if(is_numeric($key) || strpos($key, '??') === 0 || strpos($guess, '&bull;') === 0) continue;
					$guesses[] = array('file' => $file, 'keys' => strtoupper( $key ).'='.$guess);
				}
			}

			$append = false;
			foreach ($guesses as $guess) {
				if(!$guess['file'] || ( strpos($guess['file'], '/components/'.$config->option.'/') === false && strpos($guess['file'], '/components/com_ninja/') === false )) continue;
				
				$append .= "\n".$guess['keys'];
			}
			if(!$append) return;
			$langfile	= key($lang->getPaths($config->option));
			$readfile	= JFile::read($langfile);
			$text		= $readfile 
						. "\n\n# ".KInflector::humanize(KRequest::get('get.view', 'cmd'))
						. "\n# @file     " . $guess['file']
						. "\n# @url      " . KRequest::url()
						. "\n# @referrer " . KRequest::referrer()
						."\n" 
						. $append;
			JFile::write($langfile, $text);
			//echo $readfile;
			//die('<pre>'.var_export($langfile, true).'</pre>');
		}
		//die('<script type="text/javascript">console.log('.json_encode($orphans).')</script>');
	}
	
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'option' => KRequest::get('get.option', 'cmd')
		));
		
		return parent::_initialize($config);
	}
}