<? /* $Id: default.php 1305 2011-09-01 12:09:51Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<h2 class="working">
    <?= $total === 1 ? @text('Found 1 install package') : sprintf(@text('Found %d packages'), $total) ?>
</h2>