<? /** $Id: button_default.php 780 2010-12-29 15:30:05Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? if($img = KFactory::get('admin::com.ninja.helper.default')->img('/32/'.@$name.'.png')) : ?>

	<? KFactory::get('admin::com.ninja.helper.default')->css('.toolbar .icon-32-'.@$name.' { background-image: url('.$img.'); }') ?>
	
<? endif ?>

<td class="button <? in_array($name, array('new', 'save', 'apply')) ? print 'special' : '' ?>" id="<?= @$id ?>">
	<a <?= KHelperArray::toString(@$attribs)  ?>>
		<span class="icon-32-<?= @$name ?>"></span>
		<?= @text(@$text) ?>
	</a>
</td>