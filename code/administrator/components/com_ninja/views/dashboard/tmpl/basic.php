<? /** $Id: basic.php 1950 2009-12-18 01:42:22Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<script type="text/javascript" src="/koowa.js"></script>
<?= @helper('behavior.modal') ?>

<? $name      = $this->getView()->getIdentifier()->package ?>
<? $path = @service('ninja:helper.application')->getPath('com_xml') ?>
<? $xml  = simplexml_load_file($path) ?>
<? $image = @ninja('document.img', '/256/'.$name.'.png') ? @ninja('document.img', '/256/'.$name.'.png') : @ninja('document.img', '/256/default.png') ?>

<link rel="stylesheet" href="/admin.css" />
<link rel="stylesheet" href="/menu.css" />
<style type="text/css">
	.header.icon-48-dashboard { background-image: url(<?= @ninja('document.img', '/48/dashboard.png') ?>); }
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
		background-image: url(<?= @ninja('document.img', '/16/spinner.gif') ?>);
	}
	span.update.complete.success {
		background-image: url(<?= @ninja('document.img', '/16/enable.png') ?>);
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
				url: '?option=com_<?= $this->getView()->getIdentifier()->package ?>&view=<?= $this->getView()->getName() ?>&action=update<?= $actionPostfix ?>',
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
							'option'	=> 'com_'.$this->getView()->getIdentifier()->package,
							'view'		=> $this->getView()->getName()
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
				url: '?option=com_<?= $this->getView()->getIdentifier()->package ?>&view=<?= $this->getView()->getName() ?>&action=checkversion<?= $actionPostfix ?>',
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
	#<?= @ninja('document.formid', 'adminform') ?> {
		margin: 0 0 10px 0;	
	}
</style>

<table class="adminform" id="<?= @ninja('document.formid', 'adminform') ?>">
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
					<?= @ninja('manifest.buttons') ?>
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
	#<?= @ninja('document.formid', 'tabs') ?>, #<?= @ninja('document.formid', 'accordions') ?> {
		float: left;
	}
	
	#<?= @ninja('document.formid', 'tabs') ?> {
		width: 55%;
	}
	#<?= @ninja('document.formid', 'tabs') ?> dl.tabs {
		margin-top: 1px;
	}
	
	#<?= @ninja('document.formid', 'accordions') ?> {
		width: 44%;
		margin-left: 1%;
	}
</style>
<? if(count($this->getService('ninja:helper.module')->render($this->getView()->getIdentifier()->package . '-dashboard-tabs')) > 0) : ?>
<div id="<?= @ninja('document.formid','tabs') ?>">
	<?= @helper('tabs.startpane', array('id' => @ninja('document.formid', 'tabs'))) ?>
	<? foreach ($this->getService('ninja:helper.module')->render($this->getView()->getIdentifier()->package . '-dashboard-tabs') as $title => $content) : ?>
		<?= @helper('tabs.startpanel', array('title' => @text($title))) ?>
			<?= $content ?>
		<?= @helper('tabs.endpanel') ?>
	<? endforeach ?>
	<?= @helper('tabs.endpane') ?>
</div>
<? endif ?>

<? if(count($this->getService('ninja:helper.module')->render($this->getView()->getIdentifier()->package . '-dashboard-accordions')) > 0) : ?>
<?= @helper('accordion.startpane', array('id' => @ninja('document.formid', 'accordions'), 'options' => array('display' => 0, 'alwaysHide' => false))) ?>
<? foreach ($this->getService('ninja:helper.module')->render($this->getView()->getIdentifier()->package . '-dashboard-accordions') as $title => $content) : ?>
	<?= @helper('accordion.startpanel', array('title' => @text($title))) ?>
		<?= $content ?>
	<?= @helper('accordion.endpanel') ?>
<? endforeach ?>
<?= @helper('accordion.endpane') ?>
<? endif ?>