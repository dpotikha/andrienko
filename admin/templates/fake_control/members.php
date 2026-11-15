<?php
require_once($_SERVER['DOCUMENT_ROOT']."/admin/templates/fake_control/functions.php");
//QURL::redirect($_SERVER['DOCUMENT_ROOT']."/admin/templates/fake_control/ideas.php");
if (isset($_GET['do']) && $_GET['do'] == 'banuser' && isset($_GET['userid']))
    setUserBan(array((int)$_GET['userid']), 1);
if (isset($_GET['do']) && $_GET['do'] == 'unbanuser' && isset($_GET['userid']))
    setUserBan(array((int)$_GET['userid']), 0);

if (isset($_POST['do']) && $_POST['do'] == 'blockMembers' && isset($_POST['items'])){
    setUserBan($_POST['items'], 1);
    QURL::redirect($_POST['referer']);
}
if (isset($_POST['do']) && $_POST['do'] == 'unblockMembers' &&  isset($_POST['items'])){
    setUserBan($_POST['items'], 0);
    QURL::redirect($_POST['referer']);
}
if (isset($_POST['do']) && $_POST['do'] == 'blockMembersIp' &&  isset($_POST['items'])){
    foreach ($_POST['items'] as $id){
        $user = Node::findById($id);
        $ipString[] = $user->tfields['ip'];
    }
    setIpBan(implode(',', $ipString), 1);
    QURL::redirect($_POST['referer']);
}
if (isset($_POST['do']) && $_POST['do'] == 'unblockMembersIp' &&  isset($_POST['items'])){
    foreach ($_POST['items'] as $id){
        $user = Node::findById($id);
        $ipString[] = $user->tfields['ip'];
    }
    setIpBan(implode(',', $ipString), 0);
    QURL::redirect($_POST['referer']);
}

if (isset($_GET['ip']))
    $ip = '%' . db_escape($_GET['ip']) . '%';
else $ip = '%';

//dump($_SESSION['fake_control_sortby_members']);
//dump($_SESSION['fake_control_sortby_members_direction']);
//dump($sortOrder);
//exit;

$limit = 100;
$MDB->Query("SELECT count(node_id) as c FROM dbm_nfv_member WHERE active = 1 AND ip LIKE '$ip'");
$res = $MDB->NextResult();
$count = $res['c'];

$totalPages = ceil($count/$limit);
$page = (isset($_GET['page']))?(int)$_GET['page']:1;
$sortOrder = getSortOrder('members', "/admin/templates/fake_control/members.php?page=1". (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : ''));
if($page<=0 || $page>$totalPages)$page=1;
$offset=($page-1)*$limit;

//$MDB->Query("ALTER TABLE dbm_nfv_vote_semifinal ADD INDEX ( member ) ");
//$MDB->Query("ALTER TABLE dbm_nfv_comment ADD INDEX ( member ) ");
//$MDB->Query("ALTER TABLE dbm_nfv_member ADD INDEX ( active ) ");
//$MDB->Query("ALTER TABLE dbm_nfv_member ADD INDEX ( ban ) ");
//$MDB->Query("DESCRIBE dbm_nfv_vote_semifinal");
//$childs = $MDB->getRecords();
//dump($childs);
//exit;

                   
//$MDB->Query("SELECT m.node_id as node_id, votesByUser.votesCount as votesByUser  FROM dbm_nfv_member as m, dbm_nodes as n,
//        (SELECT m.node_id as member_id, count(v.node_id) as votesCount
//            FROM  dbm_nfv_member as m
//            LEFT JOIN dbm_nfv_vote_semifinal as v
//            ON v.member = m.node_id
//            GROUP BY  m.node_id) as votesByUser
//
//        WHERE m.active = 1 AND n.id = m.node_id AND m.ip LIKE '$ip'    AND votesByUser.member_id = m.node_id
//
//        ORDER BY $sortOrder LIMIT $offset, $limit");////BY ip DESC, date_confirm DESC

$MDB->Query("SELECT m.node_id as node_id FROM dbm_nfv_member as m, dbm_nodes as n
        WHERE m.active = 1 AND n.id = m.node_id AND m.ip LIKE '$ip'
        ORDER BY $sortOrder LIMIT $offset, $limit");
$members = $MDB->getRecords();

$thisUrl = "/admin/templates/fake_control/members.php?page=$page". (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '');
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
       $("#mainCheckbox").change(function(){
             if ($('#mainCheckbox').is(':checked'))
                 $('.checkBoxItem').attr('checked', 'checked')
             else $('.checkBoxItem').attr('checked', '')
       });
       $("input[name=blockMembers]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте заблокувати вибраних учасників (крiм тих, що пройшли повну реєстрацію) та всi їх голоси?')){
               $('input[name=do]').val('blockMembers');
               $('#wholesale').submit();
           }
       });
      $("input[name=unblockMembers]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте розблокувати вибраних учасників та всi їх голоси?')){
               $('input[name=do]').val('unblockMembers');
               $('#wholesale').submit();
           }
       });
      $("input[name=blockMembersIp]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте заблокувати всiх учасникiв (крiм тих, що пройшли повну реєстрацію) з вибраними ip та їх голоси?')){
               $('input[name=do]').val('blockMembersIp');
               $('#wholesale').submit();
           }
       });
      $("input[name=unblockMembersIp]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте розблокувати всiх учасникiв з вибраними ip та їх голоси?')){
               $('input[name=do]').val('unblockMembersIp');
               $('#wholesale').submit();
           }
       });
     });
</script>
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
    <?= isset($_GET['ip']) ? '<a style="margin-left: 20px;" href="/admin/templates/fake_control/members.php">Повний список учасникiв</a>' : ''?>
    <?php /*<div style="float:right; margin-right:20px;">
        <table>
            <tr><td>Фільтри:&nbsp;&nbsp;&nbsp;</td><?headTitle('members','Логін', 'n.name', $thisUrl)?><?headTitle('members','Логін', 'n.name', $thisUrl)?><?headTitle('members','Логін', 'n.name', $thisUrl)?></tr>
        </table>
    </div>*/?>
    <p style="margin-left: 20px; font-size:1.3em;margin-bottom:5px;">Учасники<?= isset($_GET['ip']) ? ' з ip ' . $_GET['ip'] : ''?>:</p>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<form id="wholesale" method="post" action="/admin/templates/fake_control/members.php">
    <input type="hidden" name="referer" value="/admin/templates/fake_control/members.php?page=<?=$page?><?=isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ?>" />
    <input type="hidden" name="do" value="1" />
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr class="trTop" style="height:30px;"><td><nobr><b><input type="checkbox" id="mainCheckbox" /></b></nobr></td><?php headTitle('members','Логін', 'n.name', $thisUrl)?><?php headTitle('members','Заблоковано/<br />Живий', 'm.ban', $thisUrl)?><?php headTitle('members','Голосів');//, 'votesByUser.votesCount', $thisUrl)?><td><nobr><b>Коментарів</b></nobr></td><?php headTitle('members','Email', 'm.email', $thisUrl)?><?php headTitle('members','Ім\'я', 'm.firstname', $thisUrl)?><?php headTitle('members','Прізвище', 'm.lastname', $thisUrl)?><?php /*<td><nobr><b>Активований?</b></nobr></td>*/?><?php headTitle('members','IP-address', 'm.ip', $thisUrl)?><?php headTitle('members','Час активацiї', 'm.date_confirm', $thisUrl)?><?php headTitle('members','Час реєстрації', 'm.date', $thisUrl)?><td><nobr><b><span style="color:#B7412B;">Блокування</span>/<br /><span style="color:olive">Розблокування</span></b></nobr></td></tr>
<?php  foreach ($members as $k => $id){
     $v = Node::findById($id['node_id']);?>
    <tr <?= $v->fields['ban']->value == 1 ? 'style="background-color: rgb(246, 220, 142);"' : ''?>>
        <td><nobr><b><input class="checkBoxItem" type="checkbox" name="items[]" value="<?=$v->id?>" /></b></nobr></td>
        <td><a href="/admin/nodes.php?do=editnode&id=<?=$v->id?>"><?=$v->name?></a></td>
        <td><?=$v->tfields['ban'] == 1 ? 'Заблоковано' : 'Живий'?></td>
        <td><?=numberOfVotesByUser($v->id);?><?php //=$id['votesByUser']?></td>
        <td><?=numberOfCommentsByUser($v->id);?><?php //=$id['commentsByUser']?></td>
        <td><?=$v->tfields['email']?></td>
        <td><?=$v->tfields['firstname']?>&nbsp;</td>
        <td><?=$v->tfields['lastname']?>&nbsp;</td>
        <?php /*<td><?=$v->tfields['active'] ? 'Так' : 'Нi'?></td>*/?>
        <td><?=getIpLinks($v->tfields['ip'], '/admin/templates/fake_control/members.php')?></td>
        <td><?=$v->tfields['date_confirm']['formatted_datetime']?></td>
        <td><?=$v->tfields['date']['formatted_datetime']?></td>
        <td>
            <?php  if ($v->fields['ban']->value == 0){?>
                <?php  if ($v->fields['firstname']->value == ''){?><a style="color:#B7412B;" href="javascript:if(confirm('Ви дiйсно бажаєте заблокувати учасника <?=$v->name?> та всi його голоси?')) location.href='/admin/templates/fake_control/members.php?do=banuser&userid=<?=$v->id?>&page=<?=$page?>'">Учасник</a>&nbsp;&nbsp;&nbsp;&nbsp;<?php }?>
            <?php } else {?>
                <a style="color:olive;" href="javascript:if(confirm('Ви дiйсно бажаєте розблокувати учасника <?=$v->name?> та всi його голоси?')) location.href='/admin/templates/fake_control/members.php?do=unbanuser&userid=<?=$v->id?>&page=<?=$page?>'">Учасник</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php }?>
            <?php  if (isAllowedIp($v->tfields['ip'])){?>
                <a style="color:#B7412B;" href="javascript:if(confirm('Ви дiйсно бажаєте заблокувати всiх учасникiв (крiм тих, що пройшли повну реєстрацію) з ip <?=getIpLinks($v->tfields['ip'])?> та їх голоси?')) location.href='/admin/templates/fake_control/members.php?do=banip&bip=<?=$v->tfields['ip']?>&page=<?=$page?>'"href="/admin/templates/fake_control/members.php?do=banip&id=<?=$v->id?>">IP</a>
            <?php } else {?>
                <a style="color:olive;" href="javascript:if(confirm('Ви дiйсно бажаєте розблокувати всiх учасникiв з ip <?=getIpLinks($v->tfields['ip'])?> та їх голоси?')) location.href='/admin/templates/fake_control/members.php?do=unbanip&bip=<?=$v->tfields['ip']?>&page=<?=$page?>'"href="/admin/templates/fake_control/members.php?do=banip&id=<?=$v->id?>">IP</a>
            <?php }?>
        </td>
    </tr>
<?php }?>
</table>
     <input style="margin:10px 0 0 0px;" name="blockMembers" type="submit" value="Блокувати учасників" class="button1">
     <input style="margin:10px 0 0 0px;" name="unblockMembers" type="submit" value="Розблокувати учасників" class="button1">
     <input style="margin:10px 0 0 0px;" name="blockMembersIp" type="submit" value="Блокувати ip" class="button1">
     <input style="margin:10px 0 0 0px;" name="unblockMembersIp" type="submit" value="Розблокувати ip" class="button1">
</form>

</td>
</tr>

</table>
    
<div>
    <?php  if ($totalPages > 1){ ?>
        <div style="margin: 10px 18px 10px 18px;">
            <?php for($i = 1; $i <= $totalPages; $i ++){
            if ($i == $page)
                echo "<span>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</span>";
            else echo "<span><a href='/admin/templates/fake_control/members.php?page=$i" . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ) . "'>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</a></span>";
            if ($i < $totalPages)
                echo ' | ';
            }?>
        </div>
      <?php  } ?>
</div>
</div>

</body>
</html>