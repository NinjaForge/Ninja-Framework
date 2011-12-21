<? /** $Id: head.php 1352 2011-09-13 16:39:50Z richie $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="media://lib_koowa/css/koowa.css" />
<? if(JFactory::getApplication()->isAdmin()) : ?>
<link rel="stylesheet" href="/admin.css" />
<? endif ?>
<link rel="stylesheet" href="/toolbar.css" />
<link rel="stylesheet" href="/form.css" />

<?= @helper('behavior.mootools') ?>
<?= @ninja('behavior.ninja') ?>
<script type="text/javascript" src="media://lib_koowa/js/koowa.js"></script>
<?= @helper('behavior.validator') ?>
<script type="text/javascript" src="/toolbar.js"></script>
