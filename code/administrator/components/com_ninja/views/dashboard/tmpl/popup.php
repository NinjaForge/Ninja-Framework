<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="/admin.css" />
<style type="text/css">
	.current { max-height: 380px; overflow: auto; }
</style>

<?= @helper('behavior.mootools') ?>
<?= @ninja('behavior.ninja') ?>

<?= @helper('tabs.startpane', array('id' => 'popup', 'options' => array('display' => 1))) ?>
	<?= @helper('tabs.startpanel', array('title' => @text('General Information'))) ?>
		<?= @template('information') ?>
	<?= @helper('tabs.endpanel') ?>
	<? /*@helper('tabs.startpanel', @text('Support')) ?>
		<?= @template('support') ?>
	<?= @helper('tabs.endpanel')*/ ?>
	<?= @helper('tabs.startpanel', array('title' => @text('Changelog and Version Information'))) ?>
		<?= @template('changelog') ?>
	<?= @helper('tabs.endpanel') ?>
<?= @helper('tabs.endpane') ?>