<? /** $Id: toolbar_title.php 762 2010-12-17 15:18:34Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="header pagetitle icon-48-generic icon-48-<?= $name ?> template-<?= KFactory::get('lib.joomla.application')->getTemplate() ?>">
	<h2>
		<?= @text($title) ?>
	</h2>
</div>