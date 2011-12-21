<? /** $Id: default.php 1930 2009-12-16 02:09:58Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th width="1px"><?= @ninja('grid.checkall') ?></th>
				<th><?= @text('Name') ?></th>
				<th><?= @text('Version') ?></th>
				<th><?= @text('Date') ?></th>
				<th><?= @text('Author') ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 6)) ?>
		<tbody>
			<?= @template('admin::com.ninja.view.templates.default_items') ?>
		</tbody>
	</table>
</form>