<?php
$SQL = "SELECT count(s.email) FROM dbm_mails_subscribers as s, dbm_mails_categories as c WHERE c.id=s.category_id";
$MDB->Query($SQL);
$count = array_pop($MDB->NextResult());
$limit = 50;
//$sidx=$_GET['sidx'];
//$sord=$_GET['sord'];
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$search_str = db_escape(preg_replace(array("/'/","/’/"),array('&rsquo;','&rsquo;'),$keyword));
$search_str =  preg_replace("/[^@\.абвгдеёжзийклмнопрстуфхцчшщыъьэюяабвгґдеєжзиіїйклмнопрстуфхцчшщьюяa-z01-9\s]/ui", "", $search_str);
$sidx = 'id';
$sord = 'ASC';
if( $count >0 )
    $total_pages = ceil($count/$limit);
else $total_pages = 0;
if ($page > $total_pages) $page=$total_pages;
$offset = $limit*($page-1);
if ($offset<0) $offset = 0;
$SQL = "SELECT s.*, c.name as category_name FROM dbm_mails_subscribers as s, dbm_mails_categories as c WHERE (s.email LIKE '%$search_str%' OR s.name LIKE '%$search_str%') AND c.id=s.category_id ORDER BY s.$sidx $sord LIMIT $offset , $limit";
$MDB->Query($SQL);
$records=$MDB->getRecords();
?>
<script>
</script>
<div>
    <input id="keyword" type="text" value="<?=$keyword?>"> <input type="submit" onclick="javascript: location.href='/admin/delivery.php?selected_tab=1&keyword=' + $('#keyword').val()" value="<?=$AdminTrnsl["Delivery_search"]?>" />
    <br /><br />
</div>
<table cellpadding="5" cellspacing="0" border="1" width="100%">
    <tr>
        <th style="font-size: 11px">№</th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_id"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_name"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_email"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_category"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_active"]?></th>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_subscribe_date"]?></th>
        <?php /*<th style="font-size: 11px"><?=$AdminTrnsl["Delivery_language"]?></th>*/?>
        <th style="font-size: 11px"><?=$AdminTrnsl["Delivery_actions"]?></th>
        </tr>
    <?php  foreach($records as $k=>$v){?>
    <tr>
        <td><?=$k + $offset + 1?></td>
        <td><?=$v['id']?></td>
        <td><?=$v['name']?>&nbsp;</td>
        <td><?=$v['email']?></td>
        <td><?=$v['category_name']?></td>
        <td><?=$v['active']?$AdminTrnsl["Yes"]:$AdminTrnsl["No"]?></td>
        <td><?=$v['date']?></td>
        <?php /*<td><?=$v['lang_id']?></td>*/?>
        <td><a href="javascript:;" onclick="EditS(<?=$v['id']?>)"><?=$AdminTrnsl["Delivery_edit"]?></a>&nbsp;<a href="javascript:;" onclick="DeleteS(<?=$v['id']?>)"><?=$AdminTrnsl["Delivery_delete"]?></a></td>
    </tr>
    <?php  }?>
</table>
<?php  if ($total_pages > 1){ ?>
        <div style="margin: 10px 18px 10px 18px;">
            <?php for($i = 1; $i <= $total_pages; $i ++){
            if ($i == $page)
                echo "<span>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</span>";
            else echo "<span><a href='/admin/delivery.php?selected_tab=1&keyword=$keyword&page=$i'>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</a></span>";
            if ($i < $total_pages)
                echo ' | ';
            }?>
        </div>
<?php  } ?>
<br />
<input type="button" value="<?=$AdminTrnsl["Delivery_create_new"]?>" onclick="showCreateSubscriberDialog()">


<?php 
$MDB->Query('SELECT * FROM dbm_mails_categories ORDER BY id ASC');
$res=$MDB->getRecords();
?>
<br /><br /><br />
<form action="" enctype="multipart/form-data" method="post">
    <table cellpadding="5" border="0">
        <tr>
                <input type="hidden" name="do" value="import_s">
                <td><strong><?=$AdminTrnsl["Delivery_import_file"]?>:</strong></td>
                <td><select name="category">
                      <?php  foreach($res as $k=>$v){?>
                        <option value="<?=$v['id']?>"><?=$v['name']?></option>
                      <?php  }?>
                    </select></td>
                <td><input type="file" name="file"></td>
                <td><input type="submit" value="<?=$AdminTrnsl["Delivery_import"]?>"></td>
        </tr>
        <tr>
            <td colspan="4"><?=$AdminTrnsl["Delivery_csv_format"]?></td>
        </tr>
    </table>
</form>
    <table cellpadding="5" border="0">
        <tr>
            <td><strong><?=$AdminTrnsl["Delivery_export_file"]?>:</strong></td>
            <td>
                <select name="category" id="categories">
                    <option value="0"><?=$AdminTrnsl["Delivery_all_categories"]?></option>
                  <?php  foreach($res as $k=>$v){?>
                    <option value="<?=$v['id']?>"><?=$v['name']?></option>
                  <?php  }?>
                </select>
            </td>
            <td><input type="button" value="<?=$AdminTrnsl["Delivery_export"]?>" onclick="javascript: location.href='/admin/delivery.php?do=export&keyword=<?=$search_str?>&category=' + $('#categories').val()" value="<?=$AdminTrnsl["Delivery_search"]?>" /></td>
        </tr>
    </table>
