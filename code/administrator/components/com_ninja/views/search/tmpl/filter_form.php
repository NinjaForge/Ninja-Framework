<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route() ?>" method="get" style="display:inline;" id="<?= @id('search') ?>">
	<label for="search"><?= @text('Filter:') ?></label>
	<?= @helper('ninja:template.helper.paginator.search', array(htmlspecialchars(@$state->search))) ?>
	<!--&#160;&#160;-->
	<input type="hidden" name="option" value="com_<?= $this->getIdentifier()->package ?>" />
	<input type="hidden" name="view" value="<?= $this->getService($this->getView())->getName() ?>" />
</form>