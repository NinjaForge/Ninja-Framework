<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

	<? @ninja('behavior.tooltip', array('selector' => '[title].hasTip')) ?>

	<? @$fieldsets = array() ?>
	<? $col = 0 ?>
	<? foreach(@$setting->xml->children() as $fieldset) : ?>
	<? if(count($fieldset->children()) < 1 || \@$fieldset['hide']) continue ?>
	<? ob_start() ?>
	<fieldset class="adminform ninja-form <?= $fieldset['class'] ?>">	
		<legend title="<?= \@$fieldset['title'] ?>"><?= \@$fieldset['legend'] ? \@$fieldset['legend'] : KInflector::humanize($fieldset['name']) ?></legend>
		<? foreach($this->getService('ninja:form.default')->importXml($fieldset) as $element) : ?>
			<? if(!$element->getName()) : ?>
				<div class="element">&nbsp;</div>
				<? continue ?>
			<? endif ?>
			<? if(isset(@$setting->params[(string)$fieldset['name']][$element->getName()])) : ?>
				<? $element->setValue(@$setting->params[(string)$fieldset['name']][$element->getName()]) ?>
			<? endif ?>
			<? $element->setName('params['.$fieldset['name'].']['.$element->getName().']') ?>
			<div class="element">
			<?= $element->renderHtmlLabel() ?>
			<?= $element->renderHtmlElement() ?>
			</div>
		<? endforeach ?>
	</fieldset>
	<? @$fieldsets[(++$col)%2][] = ob_get_clean() ?>
	<? endforeach ?>
	
	<? foreach(@$fieldsets[0] as $fieldset) : ?>
		<?= $fieldset ?>
	<? endforeach ?>
</div>
<div class="col width-50 validation-advice-align-left">
	<? foreach(@$fieldsets[1] as $fieldset) : ?>
		<?= $fieldset ?>
	<? endforeach ?>