<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
<? @$xml = simplexml_load_file($this->getService('ninja:helper.application')->getPath('com_xml')) ?>

<style type="text/css">
	.info dt,
	.info dd {
		float: left;
		display: inline-block;
	}
	
	.info dt {
		clear: both;
	}
	.info dd {
		padding-left: 0.6em !important;
	}
	
	/* @group RTL */
	html[dir=rtl] .info dt,
	html[dir=rtl] .info dd {
		float: right;
	}
	html[dir=rtl] .info dd {
		padding-right: 0.6em !important;
	}
	/* @end */
</style>

<? $list = array(
	'author'		=> 'Created By',
	'license'		=> 'Code License',
	'csslicense'	=> 'CSS License',
	'jslicense'		=> 'JS License',
	'copyright'		=> 'Copyright',
) ?>

<dl class="info">
<? foreach($list as $definition => $title) : ?>

	<? if(!isset($xml->$definition)) continue ?>

	<dt>
		<strong>
			<?= @text($title) ?>
		</strong>
	</dt>
	<dd>
		<?= $xml->$definition ?>
	</dd>
	
<? endforeach ?>
</dl>

<div style="clear: both"></div>

<dl class="info">
<? if(isset(@$xml->support)) : ?>
	<dt>
		<strong>
			<?= @text('COM_NINJA_SUPPORT') ?>
		</strong>
	</dt>
	<dd>
		<a href="<?= @$xml->support['href'] ?>" target="_blank"><?= @text(@$xml->support) ?></a>
	</dd>
<? endif ?>
<? if(isset(@$xml->rate)) : ?>
	<dt>
		<strong>
			<?= @text('COM_NINJA_IF_YOU_LIKE_THIS_EXTENSION_CONSIDER_GIVING_IT_AN_HONEST_REVIEW_AT_THE') ?>
		</strong>
	</dt>
	<dd>
		<a href="<?= @$xml->rate['href'] ?>" target="_blank"><?= @text(@$xml->rate) ?></a>
	</dd>
<? endif ?>
</dl>
<!--<p><strong><?= @text(@$xml->name.' - ') ?></strong>	<?= @text(@$xml->description) ?></p>-->
<? if(isset(@$xml->credits)) : ?>
	<hr />
	<p>
	<h3><?= @text('COM_NINJA_CREDITS') ?></h3>
	<style type="text/css">
		hr {
			opacity: 0.2;
		}
		.credits ul {
			list-style:none;
			padding: 0;
		}
		.credits li {
			background-image: none;
			list-style:none;
			text-height: 16px;
			font-size: 14px;
			vertical-align: middle;
		}
	</style>
	<ul class="credits">
	<? foreach(@$xml->credits->children() as $a => $credit) : ?>
	<li>
		<? $attr = current($credit->attributes()) ?>
		<? if($attr) $attr = ' '.KHelperArray::toString($attr) ?>
		
		<a<?= $attr ?>>
			<?= $credit ?>
		</a>
	</li>
	<? endforeach ?>
	</ul>
	</p>
<? endif ?>