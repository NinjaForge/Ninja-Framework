<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @package		Ninja
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 
 /**
 * Loads com_ninja language file and the english based language file for the component
 * also Adds untranslated strings (orphans) to the component language file
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 */
class NinjaHelperLanguage extends KObject
{	
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$lang = JFactory::getLanguage();

		// work out the path
		$path = JFactory::getApplication()->isSite() ? JPATH_SITE : JPATH_ADMINISTRATOR;

		// load the com_ninja english language file
		$lang->load('com_ninja', JPATH_ADMINISTRATOR, 'en-GB', true);

		// load the foriegn language file for com_ninja
		$lang->load('com_ninja', JPATH_ADMINISTRATOR, $lang->getDefault(), true);

		// load the extensions english language file overring strings in com_ninja
		$lang->load($config->option, $path, 'en-GB', true);

		// load the foriegn language file for the extension and override teh strings from the english one
		$lang->load($config->option, $path, $lang->getDefault(), true);

		$lang->load($config->option, $path, null, true);

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
					$guesses[] = array('file' => $file, 'keys' => strtoupper( $key ).'="'.$guess.'"');
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
						. "\n\n; ".KInflector::humanize(KRequest::get('get.view', 'cmd'))
						. "\n; @file     " . $guess['file']
						. "\n; @url      " . KRequest::url()
						. "\n; @referrer " . KRequest::referrer()
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