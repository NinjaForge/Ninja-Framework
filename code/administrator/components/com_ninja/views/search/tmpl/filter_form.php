<? /** $Id: filter_form.php 532 2010-10-17 23:29:30Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route() ?>" method="get" style="display:inline;" id="<?= @id('search') ?>">
	<label for="search"><?= @text('Filter:') ?></label>
	<?= @helper('admin::com.ninja.helper.paginator.search', array(htmlspecialchars(@$state->search))) ?>
	<!--&#160;&#160;-->
	<input type="hidden" name="option" value="com_<?= $this->getIdentifier()->package ?>" />
	<input type="hidden" name="view" value="<?= KFactory::get($this->getView())->getName() ?>" />
</form>