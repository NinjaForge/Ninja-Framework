<? /** $Id: basic.php 1950 2009-12-18 01:42:22Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? $name = KFactory::get($this->getView())->getIdentifier()->package ?>
<? $extension = KFactory::get($this->getView())->getIdentifier()->type . '_' . $name ?>
<? $path = KFactory::get('admin::com.ninja.helper.application')->getPath('com_xml') ?>
<? $xml  = simplexml_load_file($path) ?>
<? $image = KFactory::get('admin::com.ninja.helper.default')->img('/256/'.$name.'.png') ? KFactory::get('admin::com.ninja.helper.default')->img('/256/'.$name.'.png') : KFactory::get('admin::com.ninja.helper.default')->img('/256/default.png') ?>

<link rel="stylesheet" href="/admin.css" />
<link rel="stylesheet" href="/menu.css" />
<style type="text/css">
	.header.icon-48-dashboard { background-image: url(<?= KFactory::get('admin::com.ninja.helper.default')->img('/48/dashboard.png') ?>); }
	.logoimg { width: 256px; height: 256px; background: url(<?= $image ?>) no-repeat center; }

	.slogan {
		margin-top: 1px;
	}

	span.checkversion, span.update {
		display: inline-block;
		padding-right: 20px;
		min-height: 16px;
		background: transparent right center no-repeat;
	}
	span.checkversion.updating, span.update.updating {
		background-image: url(<?= KFactory::get('admin::com.ninja.helper.default')->img('/16/spinner.gif') ?>);
	}
	span.update.complete.success {
		background-image: url(<?= KFactory::get('admin::com.ninja.helper.default')->img('/16/enable.png') ?>);
	}
	span.update {
		visibility: hidden;
	}
	span.update.ready {
		visibility: visible;
	}
	
	#cpanel > div {
		float: left;
	}
	
	/* @group RTL */
	html[dir=rtl] #cpanel > div {
		float: right;
	}
</style>

<? $checking = @text('Checking for updates&hellip;') ?>
<? $updating = @text('Updating&hellip;') ?>
<? $updated  = @text('Update complete!') ?>
<? $actionPostfix  = '' ?>
<? if(isset($xml->updateurl) || isset($xml->updateurlspurss)) : ?>
	<? if(isset($xml->updateurlspurss)) : ?>
	<? $actionPostfix = 'spurss' ?>
	<? endif;?>
<script type="text/javascript">
window.addEvent('domready', function(){

	var force = false, backup = false, upgrade = function(event){
			(new Event(event)).stop();
	
			var update = $('update'), status = $('checkversion').getParent().getNext();
	
			this.getParent().addClass('updating');
	
			this
				.store('status', this.get('html'))
				.set({
					html: '<?= $updating ?>',
					disabled: true
				});
	
			var request = new Request.JSON({
				url: '?option=com_<?= KFactory::get($this->getView())->getIdentifier()->package ?>&view=<?= KFactory::get($this->getView())->getName() ?>&action=update<?= $actionPostfix ?>',
				method: 'get',
				onSuccess: function(response){
	
					if(!response) request.fireEvent('failure');
					
					status.set('text', response.text);
					
					this.set({
							text: '<?= $updated ?>'
						})
						.getParent()
						.removeClass('updating');
	
					//If the response miss the update property, something went wrong
					if(response.update) {
						update.getParent().addClass('complete success');
						//Do a silent ajax request to the current page to run cleanup procedures
						new Request().get(<?= json_encode(array(
							'option'	=> 'com_'.KFactory::get($this->getView())->getIdentifier()->package,
							'view'		=> KFactory::get($this->getView())->getName()
						)) ?>);
					} else {
						this.set({
							disabled: false
						});
					}
				}.bind(this),
				onFailure: function(xhr){
					(function(xhr){	
						var html = this.response.text || xhr.responseText;			
						status.set('html', <?= json_encode(@text('Update failed with the following error')) ?>+html);
						status.getPrevious().removeClass('loading');
					}.pass(xhr, this)).delay(10);
				}
			})
			.send();
		},
		checkversion = function(event){
			var event = new Event(event);
			event.stop();
			
			this.getParent().addClass('updating');
				
			this
				.store('status', this.get('html'))
				.set({
					html: '<?= $checking ?>',
					disabled: true
				});
				
			var status = this.getParent().getNext(), update = $('update'), request = new Request.JSON({
				url: '?option=com_<?= KFactory::get($this->getView())->getIdentifier()->package ?>&view=<?= KFactory::get($this->getView())->getName() ?>&action=checkversion<?= $actionPostfix ?>',
				method: 'get',
				onSuccess: function(response){
	
					if(!response) request.fireEvent('failure');
	
					status.set('text', response.text);
	
					this.set({
							text: this.retrieve('status')
						})
						.getParent()
						.removeClass('updating');
	
					if(response.update) {
						update.getParent().addClass('ready');
						update.addEvent('click', upgrade);
					} else {
						this.set({
							disabled: false
						});
					}
				}.bind(this),
				onFailure: function(xhr){
					(function(xhr){	
						var html = this.response.text || xhr.responseText;			
						status.set('html', <?= json_encode(@text('Update check failed with the following error')) ?>+html);
						status.getPrevious().removeClass('loading');
					}.pass(xhr, this)).delay(10);
				}
			})
			.send();
		};

	$('checkversion').addEvent('click', checkversion);

	window.addEvents({
		keydown: function(event){
			if(event.alt && !$('checkversion').match('[disabled]')) {
				force	= true;
				if(!backup) backup	= $('checkversion').get('html');
				$('checkversion')
								.set('html', <?= json_encode(@text('Force Update')) ?>)
								.removeEvent('click', checkversion)
								.addEvent('click', upgrade);
			}
		},
		keyup: function(event){
			if(force) {
				force = true;
				if(!$('checkversion').match('[disabled]')) $('checkversion').set('html', backup);
				
				$('checkversion').addEvent('click', checkversion)
								.removeEvent('click', upgrade);
			}
		}
	});
});
</script>
<? endif ?>

<style type="text/css">
	#<?= KFactory::get('admin::com.ninja.helper.default')->formid('adminform') ?> {
		margin: 0 0 10px 0;	
	}
</style>

<table class="adminform" id="<?= KFactory::get('admin::com.ninja.helper.default')->formid('adminform') ?>">
	<tfoot>
		<tr>
			<td colspan="2" style="text-align: center">
				<?= @text($xml->name) ?>
				<span class="version" title="<?= @text('Version') ?>">
					<?= $xml->version ?> <span style="color:<?= \@$xml->version['color'] ?>"><?= \@$xml->version['status'] ?></span>
				</span>
				~
				<span class="revision">
					<?= @text('Revision') ?> <?= $xml->revision ?>
				</span>
			</td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<td width="55%" valign="top">
				<div id="cpanel">
					<?= KFactory::get('admin::com.ninja.helper.manifest')->buttons() ?>
				</div>
			</td>
			<td width="45%" valign="top">
				<table style="width:100%;" class="nf-extension">
					<tbody>
						<tr valign="middle">
							<td width="256" align="center" style="text-align:center">
								<div class="logoimg"></div>
							</td>
							<td style="white-space: nowrap;vertical-align: middle;">
								<h1 class="extension"><?= @text($xml->name) ?></h1>
								<h2 class="slogan"><?= @text($xml->description) ?></h2>
								<? if(isset($xml->updateurl) || isset($xml->updateurlspurss)) : ?>
								<span class="checkversion">
									<button id="checkversion"><?= sprintf(@text('Check for Updates&hellip;')) ?></button>
								</span>
								<p>&nbsp;</p>
								<span class="update">
									<button id="update"><?= @text('Install Update') ?></button>
								</span>
								<? endif ?>
							<td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<style type="text/css">
	#<?= KFactory::get('admin::com.ninja.helper.default')->formid('tabs') ?>, #<?= KFactory::get('admin::com.ninja.helper.default')->formid('accordions') ?> {
		float: left;
	}
	
	#<?= KFactory::get('admin::com.ninja.helper.default')->formid('tabs') ?> {
		width: 55%;
	}
	#<?= KFactory::get('admin::com.ninja.helper.default')->formid('tabs') ?> dl.tabs {
		margin-top: 1px;
	}
	
	#<?= KFactory::get('admin::com.ninja.helper.default')->formid('accordions') ?> {
		width: 44%;
		margin-left: 1%;
	}
</style>
<? if(count(KFactory::get('admin::com.ninja.helper.module')->render(KFactory::get($this->getView())->getIdentifier()->package . '-dashboard-tabs')) > 0) : ?>
<div id="<?= KFactory::get('admin::com.ninja.helper.default')->formid('tabs') ?>">
	<?= KFactory::get('admin::com.ninja.helper.tabs')->startpane(array('id' => KFactory::get('admin::com.ninja.helper.default')->formid('tabs'))) ?>
	<? foreach (KFactory::get('admin::com.ninja.helper.module')->render(KFactory::get($this->getView())->getIdentifier()->package . '-dashboard-tabs') as $title => $content) : ?>
		<?= KFactory::get('admin::com.ninja.helper.tabs')->startpanel(array('title' => @text($title))) ?>
			<?= $content ?>
		<?= @ninja('tabs.endpanel') ?>
	<? endforeach ?>
	<?= @ninja('tabs.endpane') ?>
</div>
<? endif ?>
<? if(count(KFactory::get('admin::com.ninja.helper.module')->render(KFactory::get($this->getView())->getIdentifier()->package . '-dashboard-accordions')) > 0) : ?>
<?= @ninja('accordions.startpane', array('id' => KFactory::get('admin::com.ninja.helper.default')->formid('accordions'), 'options' => array('display' => 0, 'alwaysHide' => false))) ?>
<? foreach (KFactory::get('admin::com.ninja.helper.module')->render(KFactory::get($this->getView())->getIdentifier()->package . '-dashboard-accordions') as $title => $content) : ?>
	<?= KFactory::get('admin::com.ninja.helper.accordions')->startpanel(array('title' => @text($title))) ?>
		<?= $content ?>
	<?= KFactory::get('admin::com.ninja.helper.accordions')->endpanel() ?>
<? endforeach ?>
<?= KFactory::get('admin::com.ninja.helper.accordions')->endpane() ?>
<? endif ?>