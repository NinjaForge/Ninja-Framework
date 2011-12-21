<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dashboard.php 1399 2011-11-01 14:22:48Z stian $
 * @category	Napi
 * @package		Napi_Parameter
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class NinjaElementDashboard extends NinjaElementAbstract
{
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$params['selector'] = 'div.dashboard';
		NinjaHtmlPane::getInstance('tabs', $params);
		jimport('joomla.filesystem.file');
		$doc = & JFactory::getDocument();
		$uri = JFactory::getURI();
		
		//Get path to manifestfile, and get required data
		$xml = & JFactory::getXMLParser('Simple');
		if( $node['path'] )
		{
			$explode = explode('::', $node['path']);
			$path = JApplicationHelper::getPath(current($explode), next($explode));
		} else if($this->_parent->_path) {
			$path = $this->_parent->_path;
		}
		$xml->loadFile($path);
		$xml = $xml->document;
		
    	if (!defined('NAPI_DASHBOARD'))
    	{
		//return '<pre>'.print_r($xml,true).'</pre>';
		define('NAPI_DASHBOARD', 1);
		}
		
		jimport('joomla.language.helper');
        $locale = JLanguageHelper::detectLanguage();
        $instructions = JText::_('NINSTRUCTIONS');
        //$instructions = JFile::read(JURI::root().'modules/'.$modname.'/dashboard/'.$locale.'/instructions.html');
        if ( !$instructions ) {
        	//$instructions = JFile::read(JURI::root().'modules/'.$modname.'/dashboard/en-GB/instructions.html');
        }
		$changelog = JText::_($node['changelog']);
        if ( !$changelog ) {
        	$changelog = 'No changelog here yet.';
        }
        
        $imgpath = $node['imgpath'] ? $node['imgpath'] : 'media/napi/img/dashboard/';
        
		$support = '<a href="http://ninjaforge.com/index.php?option=com_fireboard&Itemid=111"> NinjaForge forums</a>';
		$rate = '<a href="http://extensions.joomla.org/extensions/style-&-design/tabs-&-tabss/5726/details"> Joomla.org</a>';
			
		$return  = '';
		$script = "
		function runonce()
		{
			$('tablink1').getParent().addClass('tabactive');
			$('tabcontent1').addClass('runonce');
			$$('ul.menu-nav a').each(function(e){
				e.addEvent('click', function(){ 
					if(!$"."defined(runonce))
					{
						$('tabcontent1').removeClass('runonce');
						var runonce = true;
					}
				});
			});
		}
		window.addEvent('domready', function(){
			window.location.hash ? $('tablink'+window.location.hash.substring(window.location.hash.length - 1, window.location.hash.length)).getParent().addClass('tabactive') : runonce();
			var dashboard = $$('ul.menu-nav a');
			dashboard.each(function(e){
				e.addEvent('click', (function(event){ 
					this.addClass('tabactive');
					index = dashboard.indexOf(this);
					dashboard.each(function(e, i){ 
						if(i!=index) e.removeClass('tabactive'); 
					}); 
				}).bindWithEvent(e));
			});
		});";
//		$filter = ' {prefix: \'tab-\', invert: \'ui-filter-select\', parent: \'.ui-corner-all.ui-filter\', items: \'.nftab\', onfilter: function(){ '.$control_name.$name.'(this)} }';
		$filter = '';
		//$return .= $panel->endPanel();
		if(!$this->_parent->get('dashboardStyle')) {
			$return .= '<style type="text/css"> 

  
    #'.$control_name.$name.'{ border:1px solid #dddddd; }
    .nftab .inner{}
    .nftab{}
  </style>';
		} else {
		$return .= '<div id="'.$control_name.$name.'" class="ui-corner-all ui-filter">';
		}
		$doc->addStyleDeclaration('
		#paramsdashboard-lbl     {
			display: none;
		} 
		
    body{font-family:Helvetica,Arial,sans-serif; }
    #'.$control_name.$name.'{background: #ffffff 0 0; background-repeat: repeat-x; background-position: center -63px; 	border:0px solid #dddddd; position:relative; margin:0;overflow:hidden; }
    #'.$control_name.$name.'.content .ui-corner-top { -webkit-border-top-left-radius: 0px;
-webkit-border-top-right-radius: 0px; -moz-border-radius-topLeft: 0px;
-moz-border-radius-topRight: 0px; }
    
    .nftab {
    	display:none;
    }
    
    .nftab:target {
    	display:block;
    }
    
    .menu{ display:block;
background-position: 50% 100%; position:relative; }

    .menu img.dashbg{ width:100%; max-width:692px; display:block;margin: 0px auto 0px 0px auto; background-color: #6A84AB; }
    
    .menu ul{display: block; margin-left:auto; margin-right:auto; padding:0px; list-style:none; text-align:center; position: absolute; top:0px; width:100%;}
    .menu li{background:none; float:left; margin:0px 0px 0px 0px; padding:0px; display:inline-block; overflow:hidden;}
    .menu li img { float:left; cursor:pointer;visibility:hidden; position: relative; width: 100%; }
    .menu li#tablink0{max-width: 135px; width:19.5086705202312%;cursor:default; }
    .menu li#tablink1{max-width: 183px; width:26.4450867052023%}
    .menu li#tablink2{max-width:191px; width:27.6011560693642%;}
    .menu li#tablink3{max-width:183px; width:26.4450867052023%; }
    .menu li#tablink3 img{ cursor:default; }
    .menu a.tabactive li#tablink1,{backgroud:url(\''.JURI::root().$imgpath.'bg.png\') no-repeat 0 0; }
    .menu a.tabactive li#tablink2,{backgrond:url(\''.JURI::root().$imgpath.'bg.png\') no-repeat -139px 0; }
    .menu a.tabactive li#tablink3,.menu li#tablink3:hover{backgroud:url(\''.JURI::root().$imgpath.'bg.png\') no-repeat -277px 0; }
    .menu a.tabactive li img, .menu li:hover img {
    	/*display:inline;*/
    	visibility: visible;
    }
    .menu .menu-nav li:hover {
    	opacity:0.1;
    }
    
    .menu .menu-nav a.tabactive li, .menu .menu-nav a.tabactive:active li {
    	opacity:1;
    }
    
    .tabtxt{padding:0 0 0 0px; float:none; display:none; }
    
    .nftab{ width:auto; text-align:left; padding:0px 10px 6px 10px; font-size:12px; margin-bottom:5px;
background-position: 50% -67px;
background-repeat: repeat-x;
background-color: transparent;
background-image: url('.JURI::root().$imgpath.'dashbg.png); }
    .nftab .inner{ overflow-y:auto; overflow-x:hidden; width:100%; }
    .nftab h2,.nftab h1{font-weight:bold; text-align:center; padding: 10px 0px; }
    .nftab h1{color:#fff; margin:0px 0px 15px; padding: 1px 0px 0px 0px; }
    .nftab strong{font-weight:normal; }
    .nftab dt strong,.nftab h3 strong{font-weight:bold !important; }
    .nftab dt{font-weight:bold !important; padding:5px 0pt 2px; }
.buttons{border:none; padding:3px; }
.nftab .boxshot {
	text-align:center;
	margin:0px auto;
}
.nftab .boxshot img {
	max-width: '.($node['boxshotwidth'] ? $node['boxshotwidth'] : '233px' ).';
	width: 100%;
}
.runonce {
	display:block!important;
}
.nftab .nftab-content {
	display: inline-block;
	width: 50%;
	float:left;
}
.nftab .nftab-content p {
	margin-left: 10px;
}
.nftab ol {
	counter-reset: steps;
}
.nftab ol li {
	counter-increment: steps;
}
.nftab ol li:before {
	content: counter(steps)". "; 
}
.nftab.tab-changelog .inner div {
	float:left;
	width:100%;
	display:block;
	clear:both;
	margin-bottom: 40px;
}
.dashboard-footer {
	text-align:center;
	display:inline-block;
}');

//die('<pre>'.JURI::base(true).print_r(parse_url(JRequest::getURI(), PHP_URL_QUERY), true).'</pre>');
  $return .= '
  <!--[if lte IE 6]>     
    <style>
  	 .nftab h1 {margin:0px 0px 15px 0px !important;}
     .nftab {padding:0px 0px 6px !important;}           
    </style>
	<![endif]--> 
<div class="menu ui-corner-top">
	<img src="'.JURI::root().$imgpath.'bg.png" class="dashbg ui-corner-top" />
	<ul class="menu-nav ui-corner-top">
	<li title="" id="tablink0" class="ui-corner-tl"><img src="'.JURI::root().$imgpath.'dashtabbg_0.png" class="ui-corner-tl" /></li>
    	<a href="#tabcontent1"><li title="" id="tablink1" class="tab ui-filter-trigger'.$filter.'"><img src="'.JURI::root().$imgpath.'dashtabbg_1.png" /><span id="info" class="tabtxt">General Info</span></li></a>
    	<a href="#tabcontent2"><li title="" id="tablink2" class="tab ui-filter-trigger'.$filter.'"><img src="'.JURI::root().$imgpath.'dashtabbg_2.png" /><span id="inst" class="tabtxt">Instructions</span></li></a>
    	<a href="#tabcontent3"><li title="" id="tablink3" class="tab ui-filter-trigger'.$filter.'"><img src="'.JURI::root().$imgpath.'dashtabbg_3.png" /><span id="change" class="tabtxt">Changelog</span></li></a>
	</ul>
</div>
		<div id="tabcontent1" class="nftab tab-general-info filter-general-info ui-filter-item ui-filter-select ">
	    	<h1>'.JText::_(current($xml->name)->data()).'</h1>
		    <div class="inner">
			    <h2>'.JText::_(current($xml->slogan)->data()).'</h2>
			    <div class="boxshot nftab-content"><img class="ui-widget-content" src="'.JURI::root().'media/'.basename($path, '.xml').'/img/box.png" alt="'.JText::_(current($xml->name)->data()).'" title="'.JText::_(current($xml->name)->data()).'"></div>
			    <div class="description nftab-content">
				    <p><b>'.JText::_('EXTCREATEDBY').'</b> ';
				    $i = 0;
				    $count = count($xml->author);
				    $separator = '&nbsp;';
				    foreach($xml->author as $author):
				    	 $separator = '&nbsp;';
				    	 $i++;
				    	if ($count!=1&&$i!=$count)
				    	{
				    	$separator = ( ($i+1) == $count ? '&nbsp;&amp;' : ( ($i!=0) ? ',' : '' ) );
				    	}
				    	//$separator = $i.$count;
				    	$email = ($author['email'] ? $author['email'] : $xml['authorEmail'] );
				    	$return .= '&nbsp;<a href="mailto:'.$email.'">'.$author->data().'</a>'.$separator;
				    endforeach;
				    
				    $creditsxml = current($xml->credits);
				    $credits = array();
				    $i = 0;
				    foreach($creditsxml->children() as $credit)
				    {
				    	$credits[$i] = '<p>';
				    	$credits[$i] .= '<b>'.JText::_($credit['what']).'</b> ';
				    	$credits[$i] .= '<a href="'.JText::_($credit['where']).'">'.JText::_($credit['who']).'</a>';
				    	$credits[$i] .= '</p>';
				    	
				    	$i++;
				    }
				    $credits = implode("\n", $credits);
				    $return .= '</p>
				    '.$credits.'
				    <p><b>'.JText::_('EXTPHPLIC').'</b>  <a href="http://creativecommons.org/licenses/LGPL/2.1/"> CC-GNU LGPL</a></p>
				    <p><b>'.JText::_('EXTJSLIC').'</b> <a rel="license" href="http://www.opensource.org/licenses/mit-license.php"> MIT </a></p>
				    <p><b>'.JText::_('EXTCSSIMGCOP').'</b> '.JText::sprintf('EXTCSSIMGCOPTXT', '2008', date('Y')).' <a target="_blank" href="http://'.current($xml->authorUrl)->data().'" class="hasTip"  title="Click me!::Get more extensions at '.JText::_(current($xml->copyright)->data()).'">'.JText::_(current($xml->copyright)->data()).'</a></p>
				    <p><b>'.JText::_('EXTSUP').'</b> '.JText::sprintf('EXTSUPTXT', $support).'</p>
				    <p><b>'.JText::_('EXTRATE').'</b> '.JText::sprintf('EXTRATETXT', $rate).'</p>
				</div>
			</div>
		</div>
		<div id="tabcontent2" class="nftab tab-instructions filter-instructions ui-helper-hidden ui-filter-select  ui-filter-item">
		    <h1>'.JText::_('INSTRUCTIONS').'</h1>  
		    <div class="inner">   
		    	'.$instructions.'
			</div>
		</div>
		<div id="tabcontent3" class="nftab tab-changelog ui-helper-hidden ui-filter-select">
			<h1>'.JText::_('CHANGELOG').'</h1> 
			<div class="inner">
			';
			    $i = 0;
			    $changelog = current($xml->changelog);
			    $count = count($changelog->children());
			    $count = 2;
			    foreach($changelog->children() as $log):
			    	 $i++;
			
			    	//$separator = $i.$count;
			    	//$children = $log[$i];
			    	//die(print_r($log, true));
			    	//$children = $children->children();
			    	//$test = print_r($children->children(), true);
			    	$version = ($log['version'] ? '<p><b>'.JText::_('Version:').'</b> '.$log['version'].'</p>' : '<p><b>'.JText::_('Version:').'</b> '.$xml['version'].'</p>' );
			    	$date = ($log['date'] ? '<p><b>'.JText::_('Date:').'</b> '.$log['date'].'</p>' : '' );
			    	$state = ($log['state'] ? '<p><b>'.JText::_('State:').'</b> '.$log['state'].'</p>' : '' );
			    	$changes = '<ul>';
			    	foreach($log->children() as $child)
			    	{
			    		$changes .= '<li>'.$child->data().'</li>';
			    	}
			    	$changes .= '</ul>';
			    	$return .= '<div>'.$version.$date.$state.$changes.'</div>';
			    	unset($email);
			    endforeach;
			    unset($i);
			    unset($separator);
			    $return .= '
			</div>
		</div>
	<span class="dashboard-footer">
		<a href="http://www.ninjaforge.com" target="_self"><img  class="buttons" src="'.JURI::root().$imgpath.'ninjaforge.png" alt="Visit NinjaForge" title="Visit Ninjaforge"/></a>
		<a href="http://jcd-a.org" target="_self"><img class="buttons" src="'.JURI::root().$imgpath.'jcda.png" alt="NinjaForge is a JCDA Member" title="NinjaForge is a JCDA Member"/></a>
		<a href="http://www.mozilla-europe.org/en/firefox/" target="_self"><img class="buttons" src="'.JURI::root().$imgpath.'firefox3.png" alt="Get Firefox for a better internet experience" title="Get Firefox for a better internet experience"/></a>
		<a href="http://getfirebug.com/" target="_self"><img class="buttons" src="'.JURI::root().$imgpath.'firebug.png" alt="Ninjas use and recommend Firebug" title="Ninjas use and recommend Firebug"/></a>
		<a href="http://validator.w3.org/check?uri=referer" target="_self"><img  class="buttons" src="'.JURI::root().$imgpath.'validation_xhtml.png" alt="Valid XHTML Transitional" title="Valid XHTML Transitional"/></a>
		<a href="http://jigsaw.w3.org/css-validator/check/referer" target="_self"><img  class="buttons" src="'.JURI::root().$imgpath.'validation_css.png" alt="Valid CSS" title="Valid CSS"/></a>
		<a href="http://creativecommons.org/licenses/by-nc-sa/3.0/us/"><img class="buttons" alt="Creative Commons License" src="'.JURI::root().$imgpath.'byncsa.png" /></a>
		<a href="http://creativecommons.org/licenses/LGPL/2.1/"><img  class="buttons" alt="CC-GNU GPL" src="'.JURI::root().$imgpath.'gnugpl.png" /></a>
	</span>';
		if($this->_parent->get('dashboardStyle')) {	
			$return .= '</div>';
		}
		JRequest::getCmd('format', 'html')=='script'?$doc->addScriptDeclaration($script, '', true) : $uri->setVar('format', 'script');$uri->setVar('strict', 'true'); $doc->addScript($uri->toString()); $uri->delVar('format');$uri->delVar('strict');
		//filter shortcut
        
		return $this->filter($return);
	}
	
	function filter($filterme)
	{
		return JFilterOutput::linkXHTMLSafe($filterme);
	}
	
	function fetchToolTip()
	{
		return false;
	}
}