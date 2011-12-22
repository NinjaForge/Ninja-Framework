<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="<?= @getIdentifier()->package ?>">
	<?= @render(@$msg, @$title, @$module) ?>
</div>