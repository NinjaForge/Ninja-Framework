<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: behavior.php 1028 2011-04-20 23:42:44Z stian $
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2010 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.koowa.org
 */

/**
 * Template Behavior Helper
 *
 * @author		Mathias Verraes <mathias@koowa.org>
 * @category	Koowa
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class ComNinjaHelperBehavior extends KTemplateHelperBehavior
{
	/**
	 * Textboxlist helper, facebook like
	 *
	 * @author Stian Didrikse <stian@ninjaforge.com>
	 * @param  KConfig $config
	 * @return html
	 */
	public function textboxlist($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'id'  => 'users',
			'url' => '?option='.KRequest::get('get.option', 'cmd').'&view=users&format=json&groups=1&me=0'
		))->append(array(
			'name' => $config->id,
			'class' => $config->id
		));
		
		$helper = KFactory::get('admin::com.ninja.helper.default');
		$helper->js('/GrowingInput.js');
		$helper->js('/TextboxList.js');
		$helper->js('/TextboxList.Autocomplete.js');
		$helper->js('/TextboxList.Autocomplete.Binary.js');
		
		$helper->js("
		jQuery(function($){
			// Autocomplete with poll the server as you type
			new $.TextboxList('#".$config->id."', ".json_encode(array(
				'unique' => true,
				'plugins' => array(
					'autocomplete' => array(
						'minLength' => 2,
						'queryRemote' => true,
						'remote' => array(
							'url' => $config->url,
							'extraParams' => array(
								'is_autocomplete' => true
							)
						)
					)
				)
			)).");
		});
		");
		
		$helper->css('/TextboxList.css');
		$helper->css('/TextboxList.Autocomplete.css');
		
		return '<div class="'.$config->class.'"> 
					<input type="text" name="'.$config->name.'" value="" id="'.$config->id.'" /> 
				</div>';
	}

	/**
	 * Sortables
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string	html
	 */
	public function sortable($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'option'	=> KRequest::get('get.option', 'cmd'),
			'view'		=> KInflector::singularize(KRequest::get('get.view', 'cmd')),
			'selector'	=> 'table tbody.sortable'
		))->append(array(
			'options'	=> array(
				'handle'	=> 'td.handle',
				'numcolumn'	=> '.grid-count',
				'adapter'	=> array(
					'type'		=> 'koowa',
					'options'	=> array(
						'url'		=> '?option='.$config->option.'&view='.$config->view.'&format=json',
						'data'	=> array(
							'_token'	=> JUtility::getToken(),
							'action'	=> 'edit'
						),
						'key'		=> 'order',
						'offset'	=> 'relative'
					)
				)
			)
		));

		// Load the necessary files if they haven't yet been loaded
		if (!isset($this->_loaded['sortable'])) 
		{
			KFactory::get('admin::com.ninja.helper.default')->js('/sortables.js');
			KFactory::get('admin::com.ninja.helper.default')->css('/sortables.css');
			
			$this->_loaded['sortable'] = true;
		}
		
		$signature = md5(serialize(array($config->selector,$config->options)));
		if (!isset($this->_loaded[$signature])) 
		{
			$options = !empty($config->options) ? $config->options->toArray() : array(); 
			KFactory::get('admin::com.ninja.helper.default')->js("
				(function(){
					var sortable = function() {
						$$('".$config->selector."').sortable(".json_encode($options).");
					};
					window.addEvents({domready: sortable, request: sortable});
				})();
			");

			$this->_loaded[$signature] = true;
		}
	}

	public function tooltip($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'selector'	=> '.hasHint',
			'options'	=> array(
				'fixed' => false,
				'closeOnClick' => false,
				'showDelay' => 0
			)
		));

		// Load the javascript and css
		KFactory::get('admin::com.ninja.helper.default')->js('/tooltip.js');
		KFactory::get('admin::com.ninja.helper.default')->css('/tooltip.css');
		
		if(array_key_exists('showOnce', $config->options) && $config->options['showOnce'] === true) $config->options['showOnce'] = KFactory::get('admin::com.ninja.helper.default')->formid('tooltip');

		// Attach modal behavior to document
		KFactory::get('admin::com.ninja.helper.default')->js("window.addEvent('domready',function(){var tips=new NTooltip('" . $config->selector . "'," . json_encode($config->options->toArray()) . ");window.addEvent('domupdate', function(){ $$('" . $config->selector . "').each(tips.build, tips)});});");
	}
	
	public function uploader($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'type' => 'single',
			'element' => 'file-upload',
			'options' => array()
		));
		
		KFactory::get('admin::com.ninja.helper.default')->js('/Fx.ProgressBar.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/Swiff.Uploader.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/Roar.js');
		KFactory::get('admin::com.ninja.helper.default')->js('/Roar.css');
		
		$url = clone KRequest::url();
		$url->query['_token'] = JUtility::getToken();
		$url->query['action'] = 'upload';
		$url->query['format'] = 'json';
		$data = json_encode(
			array(
				'_token' => JUtility::getToken(),
				'action' => 'upload',
				'format' => 'json',
				JUtility::getToken() => '1'
			)
		);
		ob_start(); ?>
		
		window.addEvent('domready', function() {
		
			// One Roar instance for our notofications, positioned in the top-right corner of our demo.
			var log = new Roar();
			
			var link = $('select-0');
			var linkIdle = link.get('html');
			
			function linkUpdate() {
				if (!swf.uploading) return;
				var size = Swiff.Uploader.formatUnit(swf.size, 'b');
				link.set('html', '<span class="small">' + swf.percentLoaded + '% of ' + size + '</span>');
			}
		
			// Uploader instance
			var swf = new Swiff.Uploader({
				path: '<?php echo KFactory::get('admin::com.ninja.helper.default')->swf('/Swiff.Uploader.swf') ?>',
				url: <?php echo json_encode((string)$url) ?>,
				data: <?php echo $data ?>,
				mergeData: true,
				verbose: true,
				queued: false,
				multiple: false,
				target: link,
				instantStart: true,
				typeFilter: {
					'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
				},
				fileSizeMax: 2 * 1024 * 1024,
				onSelectSuccess: function(files) {
					if (Browser.Platform.linux) window.alert('Warning: Due to a misbehaviour of Adobe Flash Player on Linux,\nthe browser will probably freeze during the upload process.\nSince you are prepared now, the upload will start right away ...');
					log.alert('Starting Upload', 'Uploading <em>' + files[0].name + '</em> (' + Swiff.Uploader.formatUnit(files[0].size, 'b') + ')');
					this.setEnabled(false);
				},
				onSelectFail: function(files) {
					log.alert('<em>' + files[0].name + '</em> was not added!', 'Please select an image smaller than 2 Mb. (Error: #' + files[0].validationError + ')');
				},
				appendCookieData: true,
				onQueue: linkUpdate,
				onFileComplete: function(file) {
					console.log(file, this, file.response.text);
					// We *don't* save the uploaded images, we only take the md5 value and create a monsterid ;)
					if (file.response.error) {
						log.alert('Failed Upload', 'Uploading <em>' + this.fileList[0].name + '</em> failed, please try again. (Error: #' + this.fileList[0].response.code + ' ' + this.fileList[0].response.error + ')');
					} else {
						var src = JSON.decode(file.response.text, true);
						
						var img = $('demo-portrait');
						img.setStyles({
							'background-image': 'url('+src.uploaded+')',
							'height': src.height,
							'width': src.width
						});
						img.highlight();
					}
					
					file.remove();
					this.setEnabled(true);
				},
				onComplete: function() {
					link.set('html', linkIdle);
				}
			});

			// Button state
			link.addEvents({
				click: function() {
					return false;
				},
				mouseenter: function() {
					this.addClass('hover');
					swf.reposition();
				},
				mouseleave: function() {
					this.removeClass('hover');
					this.blur();
				},
				mousedown: function() {
					this.focus();
				}
			});
		
		});
		
	<?php
		KFactory::get('admin::com.ninja.helper.default')->js(ob_get_clean());
		KFactory::get('admin::com.ninja.helper.default')->css("
			/* Basic layout */
			
			h4 {
				margin-top: 1.25em;
			}
			
			a {
				padding: 1px;
			}
			
			a:hover, a.hover {
				color: red;
			}
			
			/* demo elements */
			
			#demo-portrait {
				position: relative;
				width: 130px;
				height: 153px;
				margin-bottom: 30px;
				border: 1px solid #eee;
				background-position: 1px 1px;
				background-repeat: no-repeat;
			}
			
			#demo-portrait a {
				position: absolute;
				left: 1px;
				right: 1px;
				bottom: -30px;
				padding: 0;
				line-height: 22px;
				display: block;
				text-align: center;
			}
		");
		return;
	}
	
	public function livetitle($config = array())//$doctitle, $placeholder = false, $set = false)
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'doctitle'		=> KFactory::get('lib.joomla.document')->getTitle(),
			'placeholder'	=> JText::_(KInflector::humanize(KRequest::get('get.view', 'cmd'))),
			'title'			=> false,
			'target'		=> 'title'
		));

		$document = KFactory::get('lib.joomla.document');
		ob_start(); ?>
			window.addEvent('domready', function(){
				$('<?php echo $config->target ?>').set('autocomplete', 'off');
				var setTitle = function(){
					document.title=this.value ? this.value + <?php echo json_encode(' | '.$config->doctitle) ?> : <?php echo json_encode($config->doctitle) ?>;
					var header =	document.getElement('#toolbar-box .header') || 
									document.getElement('.header') ||
									document.getElement('#toolbar-top h3') ||
									document.getElement('#mc-title h1');

					if(header) header.set('text', this.value || <?php echo json_encode($config->placeholder) ?>);
				};
				$('<?php echo $config->target ?>').addEvents({'keyup': setTitle, 'keydown': setTitle,'change': setTitle});
				
			});
		
		<?php if($config->title) : ?>
			<?php $document->setTitle($config->title . ' | ' . $config->doctitle) ?>
			window.addEvent('domready', function(){
				var header =	document.getElement('#toolbar-box .header') || 
								document.getElement('.header') ||
								document.getElement('#toolbar-top h3') ||
								document.getElement('#mc-title h1');
								
				if(header) header.set('text', <?php echo json_encode($config->title) ?>);
			});
		<?php endif ?>
	<?php
		KFactory::get('admin::com.ninja.helper.default')->js(ob_get_clean());
		return;
	}
	
	public function autocomplete($config = array())//$value, $name, $model, $label = false, $placeholder = false, $id = false)
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'value' => null,
			'name'	=> null,
			'model'	=> null,
			'label'	=> false,
			'placeholder' => false,
			'text'	=> ''
		))->append(array(
			'target' => $config->name
		));
		
		
		if(is_string($config->model)) {
			$data = str_replace('&amp;', '&', $config->model);
		} else {
			$data = array();
			foreach($config->model as $item)
			{
				$data[] = array('value' => $item->id, 'text' => $item->text);
				if($item->id == $config->value) $config->text = $item->text;
			}
		}
		$instance 	 = KFactory::get('admin::com.ninja.helper.default')->formid((int)uniqid());
		
		KFactory::get('admin::com.ninja.helper.default')->js('/autocomplete.js');
		KFactory::get('admin::com.ninja.helper.default')->css('/autocomplete.css');
		ob_start(); ?>
			window.addEvent('domready', function(){				
				var data = <?php echo json_encode($data) ?>;
				
				new Meio.Autocomplete.Select($('<?php echo $instance ?>'), data, {
					valueField: '<?php echo $config->target ?>',
				    valueFilter: function(data){
				        return data.value;
				    },
					onNoItemToList: function(elements){
						var colors = elements.field.node.getStyles('color');

						elements.field.node.get('morph').start({color: '#f00'}).chain(function(){
							this.morph(colors);
						}.bind(elements.field.node));
					},
					filter: {
						type: 'contains',
						path: 'text'
					},
					urlOptions: {
						queryVarName: 'search'
					},
					requestOptions: {
						method: 'get',
						formatResponse: function(response){
							var data = [];
							new Hash(response).each(function(item, id){
								this.include({value: id, text: item.text});
							}, data);
							return data;
						}
					}
				});
			});
	<?php
		KFactory::get('admin::com.ninja.helper.default')->js(ob_get_clean());
		ob_start();
	?>
	<label for="<?php echo $instance ?>" class="key"><?php echo JText::_($config->label) ?></label>
	<input type="text" id="<?php echo $instance ?>" placeholder="<?php echo $config->placeholder ?>" class="inputbox required value" value="<?php echo $config->text ?>"/>
	<input type="hidden" name="<?php echo $config->name ?>" id="<?php echo $config->target ?>" value="<?php echo $config->value ?>" />
	<?php
		return ob_get_clean();
	}
}