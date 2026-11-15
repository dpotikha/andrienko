<?php

    $MDB->Query('SELECT * FROM dbm_mails_categories WHERE id='.$_GET['id']);
    $res=$MDB->NextResult();

?><form action="/admin/delivery.php" id="updateForm" method="post">
    <input type="hidden" name="do" value="edit_category"/>
    <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
    <table cellpadding="0" cellspacing="5" border="0" width="100%">
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_name"]?>:</strong></td>
            <td width="100%"><input type="text" name="name" id="name" value="<?=htmlspecialchars(stripslashes($res['name']))?>" style="width: 100%" /></td>
        </tr>
    </table>
</form>