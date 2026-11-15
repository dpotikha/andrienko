<?php
   

?><form action="/admin/delivery.php" id="addForm" method="post">
    <input type="hidden" name="do" value="add_category"/>
    <table cellpadding="0" cellspacing="5" border="0" width="100%">
        <tr>
            <td valign="top"><strong><?=$AdminTrnsl["Delivery_name"]?>:</strong></td>
            <td width="100%"><input type="text" name="name" id="name" value="" style="width: 100%" /></td>
        </tr>
    </table>
</form>