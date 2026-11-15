<?php

function getmicrotime1() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/WEB-INF/prepend.php");
require_once(__DIR__ . "/upload_guard.php");


$node_id = (int) $request->getParameter("node_id");
$n=0;
if (isset($_SESSION['multiphoto'][$node_id])) {
    ?>
<table border="1" cellpadding="5">
        <?php  foreach ($_SESSION['multiphoto'][$node_id] as $k => $v) { $n++;?>
            <tr>
                <td><?=$n?></td>
                <td><a title="<?=$v?>" href="/s/tmp/multiphoto/<?= session_id() ?>/<?= $node_id ?>/crop_<?= $k ?>" rel="prettyPhoto[img]"><img src="/s/tmp/multiphoto/<?= session_id() ?>/<?= $node_id ?>/thumb_<?= $k ?>"  /></a></td>
                <td><a href="javascript:delPhoto('<?=$k?>')">Удалить</a></td>
            </tr>
        <?php  } ?>
    </table>

<table><tr><?php  if($_POST['type']==24){?><td>Добавить водяные знаки:<input type="checkbox" name="use_watermak" id="use_watermak" value="1" checked ></td><?php  }else{?><td><input type="hidden" name="use_watermak" id="use_watermak" value="0" ><?php  }?></td><td><input type="button" value="Сохранить фото-пакет" onclick="javascript: savePhoto()" /></td></tr></table>
    <?php 
}
?>