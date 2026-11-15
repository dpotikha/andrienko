<?php $cname=$CM_CONTROL_NAME?>
<script type="text/javascript">if(typeof(configMCE)=='undefined'){document.write('<script src="/admin/tiny_mce/tiny_mce.js" type="text/javascript"><\/script>');<?php  require_once($_SERVER["DOCUMENT_ROOT"].'/admin/templates/tiny_mce/custom_editor2.php') ?><?php /*document.write('<script src="/admin/templates/tiny_mce/custom_editor2.php?<?=rand()?>" type="text/javascript"><\/script>');*/?>}</script>
<iframe id="fr_<?=$cname?>" src="/admin/tiny_mce/not_setup.php";  style="width: 100%; height: 300px;"  frameborder="0" onload="mce_iframe_onload('<?=$cname?>')"></iframe>
<textarea id="<?=$cname?>" name="<?=$cname?>" class="mceAdvanced<?=$cname?>" style="width:100%;height: 300px; display:none"><?=prepareStringForXML($CM_PARAMS["strvalue"])?></textarea>
<a id="a_<?=$cname?>" href="javascript:setupMCE('<?=$cname?>');">Enable editor</a>
		

