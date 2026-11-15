<?php
$MDB->Query('SELECT * FROM dbm_mails_subscribers WHERE id='.$_GET['id']);
$s=$MDB->NextResult();
$MDB->Query('SELECT * FROM dbm_mails_categories ORDER BY id ASC');
$res=$MDB->getRecords();
?><form action="/admin/delivery.php" id="updateForm" method="post">
    <input type="hidden" name="do" value="edit_subscriber"/>
    <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
    <table cellpadding="0" cellspacing="5" border="0" width="100%">
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_name"]?>:</strong></td>
            <td width="100%"><input type="text" name="name" id="name" value="<?=$s['name']?>" style="width: 100%" /></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_email"]?>:</strong></td>
            <td width="100%"><input type="text" name="email" id="email" value="<?=$s['email']?>" style="width: 100%" /></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_category"]?>:</strong></td>
            <td>
                <select name="category">
                      <?php  foreach($res as $k=>$v){?>
                      <?php if ($v['id'] == $s['category_id']){?>
                        <option selected="selected" value="<?=$v['id']?>"><?=$v['name']?></option>
                      <?php } else {?>
                        <option value="<?=$v['id']?>"><?=$v['name']?></option>
                      <?php }}?>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_date"]?>:</strong></td>
            <td width="100%"><input type="text" name="date" id="date" value="<?=$s['date']?>" style="width: 100%" /></td>
        </tr>
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_active"]?>:</strong></td>
            <td width="100%" align="left"><input type="checkbox" name="active" <?=$s['active'] ? ' checked="checked"' : ''?>  /></td>
        </tr>
    </table>
</form>