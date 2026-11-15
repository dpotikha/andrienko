<?php $cname=$CM_CONTROL_NAME?>
<script type="text/javascript">if(typeof(configMCE)=='undefined'){document.write('<script src="/admin/tiny_mce/tiny_mce.js" type="text/javascript"><\/script>');document.write('<script src="/admin/js/editor2.js?<?=rand()?>" type="text/javascript"><\/script>');}</script>
<iframe id="fr_<?=$cname?>" src="/admin/tiny_mce/not_setup.php"  style="width: 100%; height: 200px;"  frameborder="0" onload="mce_iframe_onload('<?=$cname?>',true)"></iframe>
<textarea id="<?=$cname?>" name="<?=$cname?>" class="mceAdvanced<?=$cname?>" style="width:100%;height: 200px; display:none"><?=prepareStringForXML($CM_PARAMS["strvalue"])?></textarea>
<a id="a_<?=$cname?>" href="javascript:setupMCE('<?=$cname?>',true);"><?=$AdminTrnsl["Editor_Enable"]?></a>
<script type="text/javascript">
if (root1)
{ 
  if (root1.addEventListener) root1.addEventListener("load", function(){setTimeout("setupMCE('<?=$cname?>',true,true,true);", 100); }, false);
  else 
  if (root1.attachEvent) root1.attachEvent("onload", function(){setTimeout("setupMCE('<?=$cname?>',true,true,true);", 100); });
  

}
</script>
