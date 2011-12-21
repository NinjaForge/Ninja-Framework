<? /** $Id: fieldsets.php 1954 2009-12-19 14:08:04Z stian $ */ ?>
<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>

<? $fieldsets = array() ?>
<? foreach($setting->xml->children() as $fieldset) : ?>
	
	<? if(count($fieldset->children()) < 1 || isset($fieldset['hide'])) continue ?>
	
	<?= KFactory::tmp('admin::com.ninja.form.parameter', array(
	  		'data' 	   => $setting->params,
	  		'xml'  	   => $setting->xml,
	  		'render'   => 'fieldset',
	  		'group'	   => (string)$fieldset['group'],
	  		//'groups'   => false,
	  		'grouptag' => 'fieldset',
	  		'name'	   => 'params'
	  ))->render() ?>

<? endforeach ?>