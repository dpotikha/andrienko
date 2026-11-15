<?php 
$node_id = (int) $request->getParameter("node_id");
$MDB->Query("SELECT * FROM dbm_seo WHERE node_id = $node_id AND lang='".$_GET['lang']."'");
$seo=$MDB->NextResult();

?>
<form action="/admin/nodes.php?do=seo&node_id=<?=$node_id?>&do1=update&lang=<?=$_GET['lang']?>" method="post" >
    <input type="hidden" name="seo_keywords" value="<?php  if($seo){?><?=htmlspecialchars($seo['seo_keywords'])?><?php  }?>">
<table  style="width: 100%" border=0 cellpadding=0 cellspacing=10>
    <tr>
        <td id=pad18><strong>SEO Title</strong></td>
        <td id=pad18  style="width: 100%"><input type="text" style="width: 100%" name="seo_title" value="<?php  if($seo){?><?=$seo['seo_title']?><?php  }?>" /></td>
    </tr>
    <?php /*<tr>
        <td id=pad18 valign="top"><strong>SEO Meta Keywords</strong></td>
        <td id=pad18><textarea style="width: 100%" rows="10" name="seo_keywords"><? if($seo){?><?=$seo['seo_keywords']?><? }?></textarea></td>
    </tr>*/?>
    <tr>
        <td id=pad18 valign="top"><strong>SEO Meta Decription</strong></td>
        <td id=pad18><textarea style="width: 100%" rows="10" name="seo_description"><?php  if($seo){?><?=stripslashes($seo['seo_description'])?><?php  }?></textarea></td>
    </tr>
    <tr>
        <td id=pad18 valign="top"><strong>Google Sitemap</strong></td>
        <td id=pad18><input type="radio" name="seo_google" value="0" <?php  if($seo && $seo['seo_google']==0 ){?>checked<?php  }?>>NO <input type="radio" name="seo_google" value="1" <?php  if(!$seo || $seo['seo_google']==1 ){?>checked<?php  }?>>YES </td>
    </tr>
    <tr>
        <td id=pad18 valign="top"><strong>Google Sitemap for children</strong></td>
        <td id=pad18><input type="radio" name="seo_google_childs" value="0" <?php  if($seo && $seo['seo_google_childs']==0 ){?>checked<?php  }?>>NO <input type="radio" name="seo_google_childs" value="1" <?php  if(!$seo || $seo['seo_google_childs']==1 ){?>checked<?php  }?>>YES </td>
    </tr>
    <tr>
        <td  colspan="2" align="center"><input type="submit" value="SAVE" /></td>
       
    </tr>
</table>
    </form>