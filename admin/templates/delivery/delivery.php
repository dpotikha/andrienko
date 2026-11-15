<?php

$MDB->Query('SELECT * FROM dbm_mails_nodes ORDER BY id DESC');
$res=$MDB->getRecords();
?>
<table cellpadding="5" cellspacing="0" border="1" width="100%">
    <tr>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_id"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_subject"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_delivery_date"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_create_date"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_is_sent"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_actions"]?></th>
        </tr>
    <?php  foreach($res as $k=>$v){?>
    <tr>
        <td><?=$v['id']?></td>
        <td><?=$v['subject']?></td>
        <td><?=$v['date_send']?></td>
        <td><?=$v['date']?></td>
        <td><?=$v['is_send']?$AdminTrnsl["Yes"]:$AdminTrnsl["No"]?></td>
        <td><a href="javascript:;" onclick="EditD(<?=$v['id']?>)"><?=$AdminTrnsl["Delivery_edit"]?></a>&nbsp;<a href="javascript:;" onclick="DeleteD(<?=$v['id']?>)"><?=$AdminTrnsl["Delivery_delete"]?></a></td>
    </tr>
    <?php  }?>
</table>
<br />
<input type="button" value="<?=$AdminTrnsl["Delivery_create_new"]?>" onclick="showCreateDialog()">