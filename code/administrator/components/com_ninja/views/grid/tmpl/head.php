<? /** $Id: head.php 1387 2011-10-12 00:01:56Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<link rel="stylesheet" href="media://lib_koowa/css/koowa.css" />
<link rel="stylesheet" href="/pagination.css" />
<? if(JFactory::getApplication()->isAdmin()) : ?>
<link rel="stylesheet" href="/admin.css" />
<? endif ?>
<link rel="stylesheet" href="/toolbar.css" />
<link rel="stylesheet" href="/grid.css" />

<?= @helper('behavior.mootools') ?>
<?= @ninja('behavior.ninja') ?>
<script type="text/javascript" src="media://lib_koowa/js/koowa.js"></script>
<script type="text/javascript" src="/toolbar.js"></script>