<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? foreach (@$templates as $i => $template) : ?>
<tr>
	<?= @ninja('grid.count', array('total' => @$total)) ?>
	<td><?= @ninja('grid.id', array('value' => $template->id)) ?></td>
	<td><?= $template->name ?></td>
	<td align="center"><?= $template->version ?></td>
	<td><?= @date(array('date' => $template->creationdate)) ?></td>
	<td><a href="http://<?= $template->authorurl ?>"><?= $template->author ?></a></td>
</tr>
<? endforeach ?>
<?= @ninja('grid.placeholders', array('total' => $total, 'colspan' => 6)) ?>