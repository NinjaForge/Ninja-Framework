<? /** $Id: search_form.php 2310 2010-07-01 22:20:54Z johanjanssens $ */ ?>
<? defined('KOOWA') or die('Restricted access'); ?>

<input name="search" id="search" value="<?= $state->search;?>" />
<button onclick="this.form.submit();"><?= @text('Go')?></button>
<button onclick="document.getElementById('search').value='';this.form.submit();"><?= @text('Reset'); ?></button>