<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/admin.css" />
<style type="text/css">
	.current { max-height: 380px; overflow: auto; }
	.info dt { min-width: 75px; margin-bottom: 5px;}
</style>

<?= @helper('behavior.mootools') ?>
<?= @ninja('behavior.ninja') ?>

<?= @helper('tabs.startpane', array('id' => 'popup', 'options' => array('display' => 1))) ?>
	<?= @helper('tabs.startpanel', array('title' => @text('COM_NINJA_GENERAL_INFORMATION'))) ?>
		<?= @template('information') ?>
	<?= @helper('tabs.endpanel') ?>
	<? /*@helper('tabs.startpanel', @text('COM_NINJA_SUPPORT')) ?>
		<?= @template('support') ?>
	<?= @helper('tabs.endpanel')*/ ?>
	<?= @helper('tabs.startpanel', array('title' => @text('COM_NINJA_CHANGELOG_AND_VERSION_INFORMATION'))) ?>
		<?= @template('changelog') ?>
	<?= @helper('tabs.endpanel') ?>
<?= @helper('tabs.endpane') ?>