<? /* $Id: default.php 48 2011-03-28 22:50:42Z stiandidriksen $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<style src="media://com_extensions/css/extensions.css" />
<script src="media://com_extensions/js/updater.css" />

<div class="extensions-outer">
    <div class="extensions-inner">
        <? if (version_compare($latest, Koowa::getVersion())) : ?>
        <h2><?= sprintf(@text("You're currently using the latest Nooku Framework %s"), Koowa::getVersion()) ?></h2>
        <p><?= @text('Congratulations, you have nothing to update.') ?></p>
        <? else : ?>
        <h2><?= sprintf(@text("There is a new version of the Nooku Framework available: %s"), $latest) ?></h2>
        <form action="<?= @route('action=update') ?>" method="get" id="update">
            <input type="hidden" name="option" value="com_extensions" />
            <input type="hidden" name="view" value="dashboard" />
            <input type="hidden" name="action" value="update" />
            <button><?= sprintf(@text('Update Framework to %s'), $latest) ?></button>
            <span class="progress"></span>
            <span class="status"></span>
        </form>
        <? endif; ?>
    
    </div>
</div>