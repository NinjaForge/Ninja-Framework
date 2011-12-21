<? /* $Id: installing.php 1305 2011-09-01 12:09:51Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<h2 class="working">
    <?= sprintf(@text('Installing %s'), $package) ?>
    <? $b = $total + $i - 1; ?><span><?= sprintf(@text('Package %d of %d'), $i, $b) ?></span>
</h2>