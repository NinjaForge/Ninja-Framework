<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('filter_form') ?>
<? if($state->search) echo @template('filter_reset') ?>