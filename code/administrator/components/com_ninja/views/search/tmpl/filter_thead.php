<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if(@$length > 0) : ?>
	<table class="adminlist ninja-list">
		<thead> 
			<tr>
				<th nowrap="nowrap" style="text-align: left;">
					<?= @template('filter_default') ?>
				</th>
			</tr>
		</thead>
	</table>
<? endif ?>