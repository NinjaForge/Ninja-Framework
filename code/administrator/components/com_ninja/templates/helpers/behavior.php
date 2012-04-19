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
 * Behavior Helper - Adds sexy javascript behaviors
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @category	Ninja
 * @package		Ninja_Template
 * @subpackage	Helper
 */
class NinjaTemplateHelperBehavior extends ComDefaultTemplateHelperBehavior
{
    /**
     * Loads an addon js to mootools with depencies used in Ninja scripts that isn't in the mootools build found in Joomla
     *
     * Examples: 
	 * <code>
	 * // Outside a template layout
     * $helper = $this->getService('ninja:template.helper.behavior');
     * $helper->ninja(array('method' => 'load'));
     *
     * // Inside a template layout
     * <?= @ninja('behavior.ninja') ?>
     * </code>
     *
     * @param	array	optional array of configuration options
     * @return	string	Html, but only if $config->method = 'render'. 
     *                  Specify as 'load' if adding directly to <head> is the desired behavior.
     */
    public function ninja($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'method' => 'render'
        ));
        
        if(!in_array($config->method, array('render', 'load'))) {
            $config->method = 'render';
        }
        
        $document = $this->getService('ninja:template.helper.document');
        $script   = version_compare(JVERSION,'1.6.0','ge') ? '/ninja-1.3.js' : '/ninja-1.2.js';

        return $document->{$config->method}($script);
    }

    /**
	 * Render a Wysiwyg bbCode editor
	 *
	 * @return string	The html output
	 */
	public function wysiwygbbcode($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'element' => 'myTextArea',
			'placeholder' => '',
			'options' => array()
 		))->append(array(
 		    'value' => $config->{$config->name}
 		));
 		
 		$helper = $this->getService('ninja:template.helper.document');

 		$html = '';

		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['wysiwygbbcode']))
		{
			$html .= $helper->render(array('/wysiwygbbcode.js', '/wysiwygbbcode.css'));

			self::$_loaded['wysiwygbbcode'] = true;
		}

		$signature = md5(serialize(array($config->element,$config->options)));
		if (!isset(self::$_loaded[$signature]))
		{
			$options = !empty($config->options) ? $config->options->toArray() : array();
			$html .= "
			<script>
			    window.addEvent('domready', function() {
                    wswgEditor.initEditor('".$config->element."', true);
			    });
			</script>";

			self::$_loaded[$signature] = true;
		}
		
		$html .= '<div class="richeditor">
				
				<div class="editbar">
					<button title="bold" onclick="wswgEditor.doClick(\'bold\');" type="button"><b>B</b></button>
					<button title="italic" onclick="wswgEditor.doClick(\'italic\');" type="button"><i>I</i></button>
					<button title="underline" onclick="wswgEditor.doClick(\'underline\');" type="button"><u>U</u></button>
					<button title="hyperlink" onclick="wswgEditor.doLink();" type="button" style="background-image: url('.$helper->img('/wysiwygbbcode/url.gif').');"></button>
					<button title="image" onclick="wswgEditor.doImage();" type="button" style="background-image:url('.$helper->img('/wysiwygbbcode/img.gif').');"></button>
					<button title="list" onclick="wswgEditor.doClick(\'InsertUnorderedList\');" type="button" style="background-image:url('.$helper->img('/wysiwygbbcode/icon_list.gif').');"></button>
					<button title="color" onclick="wswgEditor.showColorGrid2(\'none\')" type="button" style="background-image:url('.$helper->img('/wysiwygbbcode/colors.gif').');"></button><span id="colorpicker201" class="colorpicker201"></span>
					<button title="quote" onclick="wswgEditor.doQuote();" type="button" style="background-image:url('.$helper->img('/wysiwygbbcode/icon_quote.png').');"></button>
					<button title="youtube" onclick="wswgEditor.InsertYoutube();" type="button" style="background-image:url('.$helper->img('/wysiwygbbcode/icon_youtube.gif').');"></button>
					<button title="switch to source" type="button" onclick="wswgEditor.SwitchEditor()" style="background-image:url('.$helper->img('/wysiwygbbcode/icon_html.gif').');"></button>
				</div>
				
				<div class="container">
				<textarea name="'.$config->element.'" id="'.$config->element.'" style="height:150px;width:100%;" placeholder="'.$config->placeholder.'">'.$config->value.'</textarea>
				</div>
			</div>';

		return $html;
	}

	/**
	 * Renders a Textboxlist (facebook like)
	 *
	 * Examples: 
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.behavior');
	 * $helper->textboxlist(array('name' => 'to', 'id' => 'to'));
	 * $helper->textboxlist(array('name' => 'to', 'id' => 'to', 'url' => '?option=com_example&view=examples&format=json'));
	 *
	 * // Inside a template layout
	 * <?= @ninja('behavior.textboxlist', array('name' => 'to', 'id' => 'to')) ?>
	 * </code>
	 *
	 * @param	array	optional array of configuration options
	 * @return	string	Html 
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
		$html 	= $this->getService('ninja:template.helper.document')->render(array('/jquery/GrowingInput.js', '/jquery/TextboxList.js', '/jquery/TextboxList.Autocomplete.js', '/TextboxList.Autocomplete.Binary.js', '/TextboxList.css', '/TextboxList.Autocomplete.css'));
		
		$html .= "
		<script>
		ninja(function($){
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
		</script>";

		$html .= '<div class="'.$config->class.'"> 
					<input type="text" name="'.$config->name.'" value="" id="'.$config->id.'" /> 
				</div>';
		
		return $html;
	}

	/**
	 * Drag and Drop Sortables Behavior
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.behavior');
	 * $helper->sortable();
	 * <tbody class="sortable"><tr class="sortable"><td class="handle"></td></tr></tbody>
	 *
	 * // Inside a template layout
	 * <?= @ninja('behavior.sortable') ?>
	 * <tbody class="sortable"><tr class="sortable"><td class="handle"></td></tr></tbody>
	 * </code>
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string 	Html
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

		$html = $this->getService('ninja:template.helper.document')->render(array('/sortables.js', '/sortables.css'));
		
		$signature = md5(serialize(array($config->selector,$config->options)));
		if (!isset(self::$_loaded[$signature])) 
		{
			$options = !empty($config->options) ? $config->options->toArray() : array(); 
			$html .= "
				<script>
				(function(){
					var sortable = function() {
						$$('".$config->selector."').sortable(".json_encode($options).");
					};
					window.addEvents({domready: sortable, request: sortable});
				})();
				</script>
			";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Tooltip Behavior, creates a tooltip on hover from the title attribute
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.behavior');
	 * $helper->tooltip();
	 * $helper->tooltip(array('selector' => '.hasTip', 'options' => array('fixed' => true, 'closeOnClick' => true)))
	 *
	 * // Inside a template layout
	 * <?= @ninja('behavior.tooltip') ?>
	 * <?= @ninja('behavior.tooltip', array('selector' => '.hasTip', 'options' => array('fixed' => true, 'closeOnClick' => true))) ?>
	 * </code>
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string 	html
	 */
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
		$html = $this->getService('ninja:template.helper.document')->render(array('/tooltip.js', '/tooltip.css'));
		
		if(array_key_exists('showOnce', $config->options) && $config->options['showOnce'] === true) $config->options['showOnce'] = $helper->formid('tooltip');

		// Attach modal behavior to document
		$html .= "
				<script>
					window.addEvent('domready',function(){
						var tips=new NTooltip('" . $config->selector . "'," . json_encode($config->options->toArray()) . ");
						window.addEvent('domupdate', function(){ $$('" . $config->selector . "').each(tips.build, tips)});
				});
				</script>";

		return $html;
	}
	
	/**
	 * @todo deprecate/clean check to see if this is used or even working
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	void
	 */
	public function uploader($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'type' => 'single',
			'element' => 'file-upload',
			'options' => array()
		));
		
		$this->getService('ninja:template.helper.document')->load('/Fx.ProgressBar.js');
		$this->getService('ninja:template.helper.document')->load('/Swiff.Uploader.js');
		$this->getService('ninja:template.helper.document')->load('/Roar.js');
		$this->getService('ninja:template.helper.document')->load('/Roar.css');
		
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
				path: '<?php echo $this->getService('ninja:template.helper.document')->swf('/Swiff.Uploader.swf') ?>',
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
		$this->getService('ninja:template.helper.document')->load('js', ob_get_clean());
		$this->getService('ninja:template.helper.document')->load('css', "
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
	
	/**
	 * Live Title Behavior, renders the title text as the user types
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.behavior');
	 * $helper->livetitle(array('title' => $row->title));
	 * $helper->livetitle(array('title' => $row->name, 'target' => 'name', 'placeholder' => 'Custom Placeholder'));
	 *
	 * // Inside a template layout
	 * <?= @ninja('behavior.livetitle', array('title' => $row->title)) ?>
	 * <?= @ninja('behavior.livetitle', array('title' => $row->name, 'target' => 'name', 'placeholder' => 'Custom Placeholder')) ?>
	 * </code>
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string 	Html
	 */
	public function livetitle($config = array())
	{
		$config = new KConfig($config);
		
		$config->append(array(
			'doctitle'		=> JFactory::getDocument()->getTitle(),
			'placeholder'	=> JText::_(KInflector::humanize(KRequest::get('get.view', 'cmd'))),
			'title'			=> false,
			'target'		=> 'title'
		));

		$document = JFactory::getDocument();
		ob_start(); ?>
			window.addEvent('domready', function(){
				$('<?php echo $config->target ?>').set('autocomplete', 'off');
				var setTitle = function(){
					document.title=this.value ? this.value + <?php echo json_encode(' | '.$config->doctitle) ?> : <?php echo json_encode($config->doctitle) ?>;
					var header =	document.getElement('#toolbar-box .header h2') ||
									document.getElement('#toolbar-box .header') || 
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
				var header =	document.getElement('#toolbar-box .header h2') ||
								document.getElement('#toolbar-box .header') || 
								document.getElement('.header') ||
								document.getElement('#toolbar-top h3') ||
								document.getElement('#mc-title h1');
								
				if(header) header.set('text', <?php echo json_encode($config->title) ?>);
			});
		<?php endif ?>
	<?php
		$this->getService('ninja:template.helper.document')->load('js', ob_get_clean());
	}
	
	/**
	 * Autocomplete Behavior, looks up and autocompletes as the user types
	 *
	 * Examples:
	 * <code>
	 * // Outside a template layout
	 * $helper = $this->getService('ninja:template.helper.behavior');
	 * $helper->autocomplete(array(
	 *						'value' => $row->created_by, 
	 *						'name' => 'created_by',
	 *						'model' => @route('view=users&format=json&sort=gid&direction=desc', true),
	 *						'label' => 'Author',
	 *						'text' => $this->getService('com://admin/example.model.users')->id($row->created_by)->getItem()->text,
	 *						'placeholder' => 'Start typing a username, email or realname'
	 *						);
	 * // Inside a template layout
	 * <? @ninja('behavior.autocomplete', array(
	 *						'value' => $row->created_by, 
	 *						'name' => 'created_by',
	 *						'model' => @route('view=users&format=json&sort=gid&direction=desc', true),
	 *						'label' => 'Author',
	 *						'text' => $this->getService('com://admin/example.model.users')->id($row->created_by)->getItem()->text,
	 *						'placeholder' => 'Start typing a username, email or realname'
	 *						);
	 * </code>
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return	string 	Html
	 */
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
		$helper 	= $this->getService('ninja:template.helper.document');
		$instance	= $helper->formid((int)uniqid());
		
		$html = $helper->render(array('/autocomplete.js', '/autocomplete.css'));
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
		$html .= '<script>'.ob_get_clean().'</script>';
		ob_start();
	?>
	<label for="<?php echo $instance ?>" class="key"><?php echo JText::_($config->label) ?></label>
	<input type="text" id="<?php echo $instance ?>" placeholder="<?php echo $config->placeholder ?>" class="inputbox required value" value="<?php echo $config->text ?>"/>
	<input type="hidden" name="<?php echo $config->name ?>" id="<?php echo $config->target ?>" value="<?php echo $config->value ?>" />
	<?php
		return $html.ob_get_clean();
	}
}