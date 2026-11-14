<?
$root = Node::findRoot();
$topSection = getTopSection($CurrentNode);
//$topSections = $root->getChildrenOfClasses(array("section", "section_text"));
//$l2Section = getL2pSection($CurrentNode);
//$l3Section = getL3pSection($CurrentNode);
$parents = $CurrentNode->getParentList();
array_shift($parents);
$CurrentNodeNodeClass = $CurrentNode->getNodeClass();
if (!in_array($CurrentNodeNodeClass->shortname, array("news_item", "article", "pub"))) {
	$parents[] = $CurrentNode;
}
$lastNodeInPath = array_pop($parents);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><? if (!$CurrentNode->isRoot()) { ?><?= $CurrentNode->name?> - <? } ?>Персональный сайт Виктора Андриенко</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="/main.css" >
<? if (!$CurrentNode->isRoot()) { ?><link rel="stylesheet" type="text/css" href="/second.css" ><? } ?>
<!--[if lte IE 6]>
<style media="all" type="text/css">
	@import "/ie.css";
</style>
<![endif]-->
<style>
	body {width:100%;padding:0px 0px 60px 0px;margin:0px;background:#7E7E7E url(/_img/bg_body.gif) repeat top left;}
</style>
<script src="/js.js"></script>
<script language="javascript1.5" type="text/javascript" src="/_img/sm-iepng.js" defer="defer" charset="ISO-8859-1"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" align="center">
<tr>
<td><img src="/_img/s.gif" width="10" height="300" alt="" class="shadowBodyTL"></td>
<td rowspan="2" id="widthMain" class="contentBg">
<!-- основная часть --> 
<div style="position:relative;top:57px;left:0px;">
<div style="position:absolute;top:0px;left:382px;" class="menuMain">
<table cellpadding="0" cellspacing="0" border="0" width="220">
<tr><td><a href="/about" onMouseOver="over('menu_01')" onMouseOut="out('menu_01')"><img name="menu_01" src="/_img/menu_main_01.gif" width="210" height="31" alt=""></a></td></tr> 
<tr><td><a href="/about/publications" onMouseOver="over('menu_01_01')" onMouseOut="out('menu_01_01')"><img name="menu_01_01" src="/_img/menu_main_01_01.gif" width="210" height="17" alt=""></a></td></tr>


<tr><td><a href="/about/filmography" onMouseOver="over('menu_01_02')" onMouseOut="out('menu_01_02')"><img name="menu_01_02" src="/_img/menu_main_01_02.gif" width="210" height="19" alt=""></a></td></tr>
<tr><td><a href="/about/stories" onMouseOver="over('menu_01_03')" onMouseOut="out('menu_01_03')"><img name="menu_01_03" src="/_img/menu_main_01_03.gif" width="195" height="19" alt=""></a></td></tr>
<tr><td><a href="/news" onMouseOver="over('menu_02')" onMouseOut="out('menu_02')"><img name="menu_02" src="/_img/menu_main_02.gif" width="210" height="28" alt=""></a></td></tr> 
<tr><td><a href="/friends" onMouseOver="over('menu_03')" onMouseOut="out('menu_03')"><img name="menu_03" src="/_img/menu_main_03.gif" width="210" height="24" alt=""></a></td></tr> 
<tr><td><a href="/contacts" onMouseOver="over('menu_04')" onMouseOut="out('menu_04')"><img name="menu_04" src="/_img/menu_main_04.gif" width="210" height="24" alt=""></a></td></tr> 
<tr><td><a href="/work" onMouseOver="over('menu_05')" onMouseOut="out('menu_05')"><img name="menu_05" src="/_img/menu_main_05.gif" width="210" height="29" alt=""></a></td></tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="220">
<tr><td><a href="/work/radio" onMouseOver="over('menu_05_01')" onMouseOut="out('menu_05_01')"><img name="menu_05_01" src="/_img/menu_main_05_01.gif" width="150" height="20" alt=""></a></td></tr> 
<tr><td><a href="/work/cinema" onMouseOver="over('menu_05_02')" onMouseOut="out('menu_05_02')"><img name="menu_05_02" src="/_img/menu_main_05_02.gif" width="150" height="19" alt=""></a></td></tr> 
<tr><td><a href="/work/broadcast" onMouseOver="over('menu_05_03')" onMouseOut="out('menu_05_03')"><img name="menu_05_03" src="/_img/menu_main_05_03.gif" width="150" height="18" alt=""></a></td></tr> 
<tr><td><a href="/work/animation" onMouseOver="over('menu_05_04')" onMouseOut="out('menu_05_04')"><img name="menu_05_04" src="/_img/menu_main_05_04.gif" width="150" height="18" alt=""></a></td></tr> 
<tr><td><a href="/work/stage" onMouseOver="over('menu_05_05')" onMouseOut="out('menu_05_05')"><img name="menu_05_05" src="/_img/menu_main_05_05.gif" width="150" height="23" alt=""></a></td></tr>
</table> 
</div>
</div>
<table cellpadding="0" cellspacing="0" border="0" class="tabTop" id="widthMain" align="center">
<tr>
<td class="left" id="widthLeft">
<table cellpadding="0" cellspacing="0" border="0"> 
<tr>
<td class="menu_01"><a href="/">Главная</a></td><td class="s">&nbsp;</td>
<td class="menu_02"><a href="/contacts">Контакты</a></td><td class="s">&nbsp;</td>
<!-- <td class="menu_03"><a href="/announcements">Анонсы</a></td><td class="s">&nbsp;</td> -->
</tr>
</table>
</td>
<td class="right">
<table cellpadding="0" cellspacing="0" border="0" align="right"> 
<!-- <tr><td class="menu_04"><a href="#">Спонсоры</a></td></tr> -->
</table>
</td>
</tr></table>
<table cellpadding="0" cellspacing="0" border="0" id="widthMain" class="contentTab">
<tr>
<td class="contentLeftTd">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentLeft" style="background-color:#bababa;">
<tr>
<td class="contentLeft"><a href="/"><img src="/_img/logo.gif" width="370" height="100" alt=""></a>
<? if ($CurrentNode->isRoot()) { ?>
	<br><img src="_img/img_welcom.gif" width="210" height="40" alt="">
<? } else { ?>
	<div class="menuSecond">
	<? for ($i=0; $i<sizeof($parents); $i++) {
		$p = &$parents[$i]; ?><a href="<?= $p->getHref() ?>"><?= $p->name ?></a>&nbsp;&nbsp;/&nbsp;&nbsp;<? } ?><a href="<?= $lastNodeInPath->getHref() ?>" style="text-decoration:none"><strong><?= $lastNodeInPath->name ?></strong></a>
	</div>
	<div class="contentTextHead">
	
	<? if ($CurrentNodeNodeClass->shortname == "project") { 
	   $_projectsSection = $parents[count($parents)-1];
	   $_siblingProjects = $_projectsSection->getChildren();
	   
	   if (count($_siblingProjects) && !(count($_siblingProjects)==1 && $_siblingProjects[0]->shortname==$CurrentNode->shortname)) {
	?>
		
		<div class="contentLink">
			<? for ($i=0; $i < count($_siblingProjects) ; $i++ ){
				$_siblingProject = &$_siblingProjects[$i]; ?>
				<div<? if ($i == count($_siblingProjects)-1) { ?> style="background:none"<? } ?>><?
				if ($_siblingProject->shortname == $CurrentNode->shortname) { ?>
					<strong><nobr><?= $_siblingProject->name; ?></nobr></strong>
				<? } else { ?>
					<a href="<?= $_siblingProject->getHref(); ?>"><nobr><?= $_siblingProject->name; ?></nobr></a>
				<? } ?>
				</div>
			<? } ?>
			<br style="clear:both;">
  		</div>
		
	<? }
	} ?>
	
	<h2><?= $lastNodeInPath->name ?></h2>
	
	</div>
<? } ?>
</td></tr>
<tr><td class="contentText">
<img src="/_img/s.gif" width="43" height="98" align="right">