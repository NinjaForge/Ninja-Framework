<? /* $Id: installing.php 48 2011-03-28 22:50:42Z stiandidriksen $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<h2 class="working">
    <?= sprintf(@text('Installing %s'), $package) ?>
    <? $b = $total + $i - 1; ?><span><?= sprintf(@text('Package %d of %d'), $i, $b) ?></span>
</h2>