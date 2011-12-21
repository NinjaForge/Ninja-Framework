<? /* $Id: installing.php 917 2011-09-19 12:15:44Z stiandidriksen $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<h2 class="working">
    <?= sprintf(@text('Installing %s'), $package) ?>
    <? $b = $total + $i - 1; ?><span><?= sprintf(@text('Package %d of %d'), $i, $b) ?></span>
</h2>