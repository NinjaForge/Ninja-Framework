<? /** $Id: search_letters.php 2310 2010-07-01 22:20:54Z johanjanssens $ */ ?>
<? defined('KOOWA') or die('Restricted access'); ?>

<ul class="search_letters">	
	<span><?= @text('Name'); ?>: </span>
	<? foreach($letters_name as $letter) : ?>
	<? $class = ($state->letter_name == $letter) ? 'class="active" ' : ''; ?>
	<li>
		<a href="<?= @route('letter_name='.$letter) ?>" <?= $class ?>>
			<?= $letter; ?>
		</a>
	</li>
	<? endforeach; ?>
	<li>
		<a href="<?= @route('letter_name=') ?>">
			<?= @text('Reset'); ?>
		</a>
	</li>
</ul>