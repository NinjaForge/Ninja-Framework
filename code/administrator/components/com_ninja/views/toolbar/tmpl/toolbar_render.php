<? /** $Id: toolbar_render.php 762 2010-12-17 15:18:34Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div id="toolbar" class="toolbar template-<?= KFactory::get('lib.joomla.application')->getTemplate() ?>">
	<table id="toolbar-<?= KFactory::get($this->getView())->getName() ?>" class="toolbar">
		<tr>
			<? foreach($buttons as $button) : ?>
				<?= $button->render() ?>
			<? endforeach ?>
		</tr>
	</table>
</div>