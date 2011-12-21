<? /** $Id: unauthorized.php 202 2010-03-10 10:18:34Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="<?= @getIdentifier()->package ?>">
	<?= @render(@$msg, @$title, @$module) ?>
</div>