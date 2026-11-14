<? tinclude("_head") ?>
<?= $CurrentNode->tfields["text"]?>
<?
$works = $CurrentNode->getChildren();
?>
<div class="newsMain" id="bgNo">
<? for ($i=0; $i<sizeof($works); $i++) { 
	$w =& $works[$i];
?>
	 <h3 class="work"><a href="<?= $w->getHref() ?>"><?= $w->name ?></a></h3>
<? } ?>
</div>
<? tinclude("_foot") ?>