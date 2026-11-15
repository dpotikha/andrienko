<?php
require_once($_SERVER['DOCUMENT_ROOT']."/admin/templates/fake_control/functions.php");
define('LOGS_PER_PAGE',100);
$paginator = new CPaginatorByQuery("SELECT count(id) as count FROM dbm_log",
        "SELECT * FROM dbm_log ORDER BY id DESC LIMIT {#offset#}, {#limit#}", LOGS_PER_PAGE);
$acts=array(
    0=>$AdminTrnsl["Log_Act_Login"],
    1=>$AdminTrnsl["Log_Act_Logout"],
    2=>$AdminTrnsl["Log_Act_Change"],
    3=>$AdminTrnsl["Log_Act_Create"],
    4=>$AdminTrnsl["Log_Act_Delete"]
);

        //"SELECT n.name, l.* FROM dbm_log as l LEFT JOIN dbm_nodes as n ON l.user_id = n.id ORDER BY l.id DESC LIMIT {#offset#}, {#limit#}", 200);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=utf-8">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<link href=/admin/admin_main.css type=text/css rel=stylesheet>

<script type="text/javascript" src="/admin/js/jquery.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.showServer').click(function(event){
            $('#server' + $(this).attr('rel')).show();
            $(this).hide();
        });
        $('.hideServer').click(function(event){
            $('#server' + $(this).attr('rel')).hide();
            $('#showServer' + $(this).attr('rel')).show();
        });
    });
</script>
<style>
    a { color:#0272BA; }
    td.actDown, td.actUp {background:none no-repeat scroll 0 0 #BCBCBC;}
    .actDown a {
        background:url("/admin/_img/arr_checkD.gif") no-repeat scroll 100% 9px; /* #BCBCBC;*/
        color:#015489;
        float:left;
        margin:-4px 0 -5px -5px;
        padding:4px 11px 5px 5px;
    }
    .actUp a {
        background:url("/admin/_img/arr_check.gif") no-repeat scroll 100% 9px; /* #BCBCBC;*/
        color:#015489;
        float:left;
        margin:-4px 0 -5px -5px;
        padding:4px 11px 5px 5px;
    }
    .trTop td {border-bottom:3px solid #BCBCBC;}
</style>
</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=4 cellspacing=0>
    <tr class=top41>
        <td height=30 id=pad18 colspan=2 nowrap class=head><b><?=$AdminTrnsl["Logger_Title"]?></b></td>
    </tr>
</table>

<div class="abon-box" style="padding-top:5px;">
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr class="trTop" style="height:30px;">
    <td><nobr><b><?=$AdminTrnsl["Logger_User"]?></b></nobr></td>
    <td><nobr><b><?=$AdminTrnsl["Type"]?></b></nobr></td>
    <td><nobr><b><?=$AdminTrnsl["Logger_Activity"]?></b></nobr></td>
    <td><nobr><b><?=$AdminTrnsl["Logger_ParentPage"]?></b></nobr></td>
    <td><nobr><b><?=$AdminTrnsl["Logger_Page"]?></b></nobr></td>
    <td><nobr><b><?=$AdminTrnsl["Date"]?></b></nobr></td>
    <td><nobr><b>Ip</b></nobr></td>
    <td><nobr><b>Server</b></nobr></td>
</tr>
    
<?php  if ($paginator->values)
        foreach ($paginator->values as $k => $v){  
        $user=User::findById($v['user_id']);
        //$node=QNode::getInstance($v['node_id']);
        //sdump();
    ?>
            <tr>
                <td><?=$user->login?></td>
                <td><?= $user->group->name ?></td>
                <td><?= $acts[$v['activity']] ?></td>
                <td><?php  if($v['parent_id']>0){?><a href="/admin/nodes.php?do=main&id=<?=$v['parent_id']?>" target="_blank"><?= $v['parent_name'] ?></a><?php  }?></td>
                <td><?php  if($v['node_id']>0){?><a href="/admin/nodes.php?do=main&id=<?=$v['node_id']?>" target="_blank"><?= $v['node_name'] ?></a><?php  }?></td>
                <td><?= $v['date'] ?></td>
                <td><?= $v['ip'] ?></td>
                <td>
                    <a href="javascript:;" id="showServer<?= $k ?>" class="showServer" rel="<?= $k ?>"><?=$AdminTrnsl["Generic_Show"]?></a>
                    <span style="display:none;" id="server<?= $k ?>"><a href="javascript:;" class="hideServer" rel="<?= $k ?>"><?=$AdminTrnsl["Generic_Hide"]?></a><br /><?= $v['server'] ?></span>
                </td>
            </tr>
<?php }?>
</table>
</td>
</tr>

</table>

<div>
<?php  if ($paginator->totalPages > 1){ ?>
        <div style="margin: 10px 18px 10px 18px;">
            <?php for($i = 1; $i <= $paginator->endPage; $i ++){
            if ($i == $paginator->currentPage)
                echo "<span>" . (($i - 1) * LOGS_PER_PAGE + 1) . '..' . min($i * LOGS_PER_PAGE, $paginator->totalRecords) . "</span> &nbsp;";
            else echo "<span><a href='/admin/logger.php?page=$i'>" . (($i - 1) * LOGS_PER_PAGE + 1) . '..' . min($i * LOGS_PER_PAGE, $paginator->totalRecords) . "</a></span>&nbsp;";
            if ($i < $totalPages)
                echo ' | ';
            }?>
        </div>
      <?php  } ?>
</div>
</div>

</body>
</html>