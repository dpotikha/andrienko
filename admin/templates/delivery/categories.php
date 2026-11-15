<?php

$MDB->Query('SELECT * FROM dbm_mails_categories ORDER BY id ASC');
$res=$MDB->getRecords();
?>
<table cellpadding="5" cellspacing="0" border="1" width="100%">
    <tr>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_id"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_title"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_create_date"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_actions"]?></th>
        </tr>
    <?php  foreach($res as $k=>$v){?>
    <tr>
        <td><?=$v['id']?></td>
        <td><?=$v['name']?></td>
        <td><?=$v['date']?></td>
        <td>&nbsp;<a href="javascript:;" onclick="EditC(<?=$v['id']?>)"><?=$AdminTrnsl["Delivery_edit"]?></a>&nbsp;<a href="javascript:;" onclick="DeleteC(<?=$v['id']?>)"><?=$AdminTrnsl["Delivery_delete"]?></a></td>
    </tr>
    <?php  }?>
    </table>
<br />
<input type="button" value="<?=$AdminTrnsl["Delivery_create_new"]?>" onclick="showCreateCatDialog()">