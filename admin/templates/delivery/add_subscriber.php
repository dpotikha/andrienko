<?php
$MDB->Query('SELECT * FROM dbm_mails_categories ORDER BY id ASC');
$res=$MDB->getRecords();
?><form action="/admin/delivery.php" id="addForm" method="post">
    <input type="hidden" name="do" value="add_subscriber"/>
    <table cellpadding="0" cellspacing="5" border="0" width="100%">
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_name"]?>:</strong></td>
            <td width="100%"><input type="text" name="name" id="name" value="" style="width: 100%" /></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_email"]?>:</strong></td>
            <td width="100%"><input type="text" name="email" id="email" value="" style="width: 100%" /></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_category"]?>:</strong></td>
            <td>
                <select name="category">
                      <?php  foreach($res as $k=>$v){?>
                        <option value="<?=$v['id']?>"><?=$v['name']?></option>
                      <?php  }?>
                </select>
            </td>
        </tr>
    </table>
</form>