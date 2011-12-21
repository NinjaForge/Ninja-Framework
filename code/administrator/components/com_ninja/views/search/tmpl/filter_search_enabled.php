<? /** $Id: filter_search_enabled.php 1399 2011-11-01 14:22:48Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<table class="adminlist ninja-list">
	<thead> 
		<tr>
			<th nowrap="nowrap" style="text-align: left;" width="100%">
				<?= @template('ninja:view.search.filter_default') ?>
			</th>
			<th nowrap="nowrap" style="text-align: right" width="0">
				&#160;<?= @ninja('grid.filter', array('state' => array('enabled' => $state->enabled))) ?>&#160;
			</th>
		</tr>
	</thead>
</table>