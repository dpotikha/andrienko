<?php  tinclude("_head") ?>
<?= $CurrentNode->tfields["text"]?>
<?php 
$works = $CurrentNode->getChildren();
?>
<div class="newsMain" id="bgNo">
<?php  for ($i=0; $i<sizeof($works); $i++) { 
	$w =& $works[$i];
?>
	 <h3 class="work"><a href="<?= $w->getHref() ?>"><?= $w->name ?></a></h3>
<?php  } ?>
</div>
<?php  tinclude("_foot") ?>