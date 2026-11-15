<?php
require_once($_SERVER['DOCUMENT_ROOT']."/admin/templates/fake_control/functions.php");

$limit = 100;
$MDB->Query("SELECT count(node_id) as c FROM dbm_nfv_idea WHERE semifinalist = 1 AND active = 1");
$res = $MDB->NextResult();
$count = $res['c'];

$totalPages = ceil($count/$limit);
$page = (isset($_GET['page']))?(int)$_GET['page']:1;
if($page<=0 || $page>$totalPages)$page=1;
$offset=($page-1)*$limit;

$MDB->Query("SELECT i.node_id as node_id FROM dbm_nfv_idea as i, dbm_nodes as n WHERE i.semifinalist = 1 AND  n.id = i.node_id AND i.active = 1 ORDER BY i.rating_semifinal DESC LIMIT $offset, $limit");
$ideas = $MDB->getRecords();

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

<script type="text/javascript" src="/js/jquery-min2.js"></script>
<style>
    /*.abon-box { padding: 30px 0 0 0 }
    .inp {width: 300px; margin: 5px 10px 5px 0; }
    select.inp {width: 304px;}
    #addPaymentForm_date img { border: 1px solid #000 }*/
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
        <td height=30 id=pad18 colspan=2 nowrap class=head><b>FAKE CONTROL</b>&nbsp;&nbsp;&nbsp;<a style="color:#247700;font-size: 0.85em;" href="/admin/templates/fake_control/members.php">учасники</a>&nbsp;&nbsp;&nbsp;<a style="color:#247700;font-size: 0.85em;" href="/admin/templates/fake_control/ideas.php">ідеї</a></td>
    </tr>
</table>

<div class="abon-box" style="padding-top:5px;">
    <p style="float:left; margin-left: 20px; font-size:1.3em;margin-top:70px;">Ідеї:</p>
    <div style="float:right; margin:0 20px 0 0;">
        <?php delayParams("/admin/templates/fake_control/ideas.php?page=$page")?>
    </div>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr class="trTop" style="height:30px;"><td><nobr><b>Назва</b></nobr></td><td><nobr><b>Тип ідеї</b></nobr></td><td><nobr><b>Кількість голосів</b></nobr></td><td><nobr><b>Сумнівні голоси</b></nobr></td><td><nobr><b>Учасник</b></nobr></td><td><nobr><b>Час подання</b></nobr></td><?php /*<td><nobr><b>Прiзвище</b></nobr></td><td><nobr><b>Активований?</b></nobr></td><td><nobr><b>IP-address</b></nobr></td><td><nobr><b>Час активацiї</b></nobr></td><td><nobr><b>Час реєстрації</b></nobr></td><td><nobr><b><span style="color:#B7412B;">Блокування</span>/<br /><span style="color:olive">Розблокування</span></b></nobr></td></tr>*/?>
<?php  foreach ($ideas as $k => $id){
     $v = Node::findById($id['node_id']);
     $member = Node::findById($v->fields['member']->value); ?>
    <tr>
        <td><a href="/admin/templates/fake_control/idea.php?idea=<?=$v->id?>"><?=$v->name?></a></td>
        <td><?=$v->tfields['idea_type']['name']?></td>
        <td><?=$v->tfields['rating_semifinal']?></td>
        <td><?=nubmerOfDisputableVotes($v->id)?></td>
        <td><a href="/admin/nodes.php?do=editnode&id=<?=$member->id?>"><?=$member->name?></a></td>
        <td><?=$v->tfields['date']['formatted_datetime']?></td>
    </tr>
<?php }?>
</table>
</td>
</tr>

</table>

<div>
    <?php  if ($totalPages > 1){ ?>
        <div style="margin: 10px 18px 10px 18px;">
            <?php for($i = 1; $i <= $totalPages; $i ++){
            if ($i == $page)
                echo "<span>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</span>";
            else echo "<span><a href='/admin/templates/fake_control/ideas.php?page=$i" . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ) . "'>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</a></span>";
            if ($i < $totalPages)
                echo ' | ';
            }?>
        </div>
      <?php  } ?>
</div>
</div>

</body>
</html>