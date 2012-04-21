<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<form action="<?= @route() ?>" method="post" id="<?= @id() ?>">
	<?= @$placeholder() ?>
	<table class="adminlist ninja-list">
		<thead>
			<tr>
				<?= @ninja('grid.count', array('total' => @$total, 'title' => true)) ?>
				<th width="1px"><?= @helper('grid.checkall') ?></th>
				<th><?= @text('COM_NINJA_NAME') ?></th>
				<th><?= @text('COM_NINJA_VERSION') ?></th>
				<th><?= @text('COM_NINJA_DATE') ?></th>
				<th><?= @text('COM_NINJA_AUTHOR') ?></th>
			</tr>
		</thead>
		<?= @ninja('paginator.tfoot', array('total' => @$total, 'colspan' => 6)) ?>
		<tbody>
			<?= @template('ninja:view.templates.default_items') ?>
		</tbody>
	</table>
</form>