<? /** $Id: filter_default.php 762 2010-12-17 15:18:34Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<?= @template('filter_form') ?>
<? if($state->search) echo @template('filter_reset') ?>