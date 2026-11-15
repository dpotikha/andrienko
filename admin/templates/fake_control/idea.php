<?php
require_once($_SERVER['DOCUMENT_ROOT']."/admin/templates/fake_control/functions.php");

if (isset($_POST['do']) && $_POST['do'] == 'setVoteStatus' && isset($_POST['items']) && isset($_POST['status'])){
    changeVoteStatus($_POST['items'], (int)$_POST['status']);
    QURL::redirect($_POST['referer']);
}


$confirmedStyles = array('', 'style="background-color: lightgreen;"', 'style="background-color: rgb(246, 220, 142);"');
$confirmedNames = array('Не розглянено', 'Підтверджено', 'Заблоковано');

if (!isset($_GET['idea']))
    QURL::redirect('/admin/templates/fake_control/ideas.php');
$ideaId = (int)$_GET['idea'];
$idea = Node::findById($ideaId);
if (!$idea || $idea->dynamicTemplate != 'idea_item')
    QURL::redirect('/admin/templates/fake_control/ideas.php');

if (isset($_GET['changeVoteStatus']) && isset($_GET['voteid']))
    changeVoteStatus(array((int)$_GET['voteid']), (int)$_GET['changeVoteStatus']);

if (isset($_GET['ip']))
    $ip = '%' . db_escape($_GET['ip']) . '%';
else $ip = '%';
if (isset($_GET['session_id']))
    $session_id = '%' . db_escape($_GET['session_id']) . '%';
else $session_id = '%';

$limit = 100;
$MDB->Query("SELECT count(v.node_id) as c FROM dbm_nfv_vote_semifinal as v, dbm_nodes as n WHERE v.node_id = n.id AND n.parent_id = $ideaId AND v.ip LIKE '$ip' AND v.session_id LIKE '$session_id'");
$res = $MDB->NextResult();
$count = $res['c'];

$totalPages = ceil($count/$limit);
$page = (isset($_GET['page']))?(int)$_GET['page']:1;
$sortOrder = getSortOrder('idea', "/admin/templates/fake_control/idea.php?idea=$ideaId&page=1" . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '') . (isset($_GET['session_id']) ? '&session_id='.$_GET['session_id'] : '' ));
if($page<=0 || $page>$totalPages)$page=1;
$offset=($page-1)*$limit;

$MDB->Query("SELECT v.node_id as node_id FROM dbm_nfv_vote_semifinal as v, dbm_nodes as n WHERE v.node_id = n.id AND n.parent_id = $ideaId AND v.ip LIKE '$ip' AND v.session_id LIKE '$session_id' ORDER BY $sortOrder LIMIT $offset, $limit");
$votes = $MDB->getRecords();
$thisUrl = "/admin/templates/fake_control/idea.php?idea=$ideaId&page=$page" . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '') . (isset($_GET['session_id']) ? '&session_id='.$_GET['session_id'] : '' );
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
       $("input[name=setVoteStatus0]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте встановити статут "Нерозглянено" для вибраних голосів?')){
               $('input[name=status]').val('0');
               $('#wholesale').submit();
           }
       });
      $("input[name=setVoteStatus1]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте встановити статут "Підтверджено" для вибраних голосів?')){
               $('input[name=status]').val('1');
               $('#wholesale').submit();
           }
       });
      $("input[name=setVoteStatus2]").click(function(event){
           event.preventDefault();
           if (confirm('Ви дiйсно бажаєте встановити статут "Заблоковано" для вибраних голосів?')){
               $('input[name=status]').val('2');
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
    <div style="float:left;">
        <a style="margin-left: 20px;" href="/admin/templates/fake_control/ideas.php">Всі ідеї</a>
        <?= isset($_GET['ip']) || isset($_GET['session_id']) ? '<a style="margin-left: 20px;" href="/admin/templates/fake_control/idea.php?idea='.$ideaId.'">Повний список голосів цієї ідеї</a>' : ''?>
        <div style="margin-left: 20px; font-size:1.3em;margin-bottom:5px;">
            <p style="margin-bottom: 0px;">Ідея "<a href="/admin/nodes.php?do=editnode&id=<?=$idea->id?>"><?=$idea->name?></a>"</p>
            <p style="font-size:0.9em;margin: 4px 15px;">Кількість голосів: <?=$idea->tfields['rating_semifinal']?></p>
            <p style="font-size:0.9em;margin: 4px 15px;">Кількість сумнівних голосів: <?=nubmerOfDisputableVotes($ideaId)?></p>
            <p style="font-size:0.9em;margin: 4px 15px;">Тип: <?=$idea->tfields['idea_type']['name']?></p>
        </div>
        <p style="margin-left: 20px; font-size:1.3em;margin-bottom:5px;">Голоси <?= isset($_GET['ip']) ? ' з ip ' . $_GET['ip'] : ''?><?= isset($_GET['session_id']) ? ' з session_id ' . $_GET['session_id'] : ''?>:</p>
    </div>
    <div style="float:right; margin:0 20px 0 0;">
        <?php delayParams($thisUrl)?>
    </div>
<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr>
<td id=pad18 class=back2>
<form id="wholesale" method="post" action="/admin/templates/fake_control/idea.php">
    <input type="hidden" name="referer" value="/admin/templates/fake_control/idea.php?page=<?=$page?>&idea=<?=$ideaId?><?=isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ?><?= isset($_GET['session_id']) ? ' з session_id ' . $_GET['session_id'] : ''?>" />
    <input type="hidden" name="status" value="10" />
    <input type="hidden" name="do" value="setVoteStatus" />
<table width=100% border=0 cellpadding=0 cellspacing=0 class=backtab3 id=back2>
<tr class="trTop" style="height:30px;"><td><nobr><b><input type="checkbox" id="mainCheckbox" /></b></nobr></td><?php headTitle('idea','Статус', 'v.confirmed', $thisUrl)?><td><nobr><b>Оцінка</b></nobr></td><td><nobr><b>Учасник</b></nobr></td><?php headTitle('idea','IP-address', 'v.ip', $thisUrl)?><?php headTitle('idea','Час на голос', 'v.delay_time', $thisUrl)?><?php headTitle('idea','Час від реєстрації', 'v.reg_delay', $thisUrl)?><?php headTitle('idea','SESSION_ID', 'v.session_id', $thisUrl)?><?php headTitle('idea','Країна', 'v.country', $thisUrl)?><?php headTitle('idea','Час голосу', 'v.date', $thisUrl)?><td><nobr><b>Змінити статус</b></nobr></td>
<?php  foreach ($votes as $k => $id){
     $v = Node::findById($id['node_id']);
     $member = Node::findById($v->fields['member']->value); ?>
    <tr <?= $confirmedStyles[$v->fields['confirmed']->value]?>>
        <td><nobr><b><input class="checkBoxItem" type="checkbox" name="items[]" value="<?=$v->id?>" /></b></nobr></td>
        <td><?=$confirmedNames[$v->fields['confirmed']->value]?></td>
        <td><?=getQuality($v, $ideaId)?></td>
        <td><?php  if ($member){?><a href="/admin/nodes.php?do=editnode&id=<?=$member->id?>"><?=$member->name?></a><?php }else{?>&nbsp;<?php }?></td>
        <td><?=getIpLinks($v->tfields['ip'], "/admin/templates/fake_control/idea.php?idea=$ideaId", '&')?></td>
        <td><?=$v->tfields['delay_time']?></td>
        <td><?=$v->tfields['reg_delay']?></td>
        <td><?= $v->tfields['session_id'] ? "<a href='/admin/templates/fake_control/idea.php?idea=$ideaId&session_id=".$v->tfields['session_id']."'>". $v->tfields['session_id'] . "</a>" : ''?></td>
        <td><?=$v->tfields['country']?></td>
        <td><?=$v->tfields['date']['formatted_datetime']?></td>
        <td>
            <?php  if ($v->fields['confirmed']->value == 0){?>не розглянено<?php }else{?><a href="<?="/admin/templates/fake_control/idea.php?idea=$ideaId&page=$page&changeVoteStatus=0&voteid=" . $v->id . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ) . (isset($_GET['session_id']) ? '&session_id='.$_GET['session_id'] : '' )?>">не розглянено</a><?php }?>&nbsp;
            <?php  if ($v->fields['confirmed']->value == 1){?>підтверджено<?php }else{?><a href="<?="/admin/templates/fake_control/idea.php?idea=$ideaId&page=$page&changeVoteStatus=1&voteid=" . $v->id . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ) . (isset($_GET['session_id']) ? '&session_id='.$_GET['session_id'] : '' )?>">підтверджено</a><?php }?>&nbsp;
            <?php  if ($v->fields['confirmed']->value == 2){?>заблоковано<?php }else{?><a href="<?="/admin/templates/fake_control/idea.php?idea=$ideaId&page=$page&changeVoteStatus=2&voteid=" . $v->id . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ) . (isset($_GET['session_id']) ? '&session_id='.$_GET['session_id'] : '' )?>">заблоковано</a><?php }?>&nbsp;
        </td>
    </tr>
<?php }?>
</table>
     <input style="margin:10px 0 0 0px;" name="setVoteStatus0" type="submit" value="Не розглянено" class="button1">
     <input style="margin:10px 0 0 0px;" name="setVoteStatus1" type="submit" value="Підтверджено" class="button1">
     <input style="margin:10px 0 0 0px;" name="setVoteStatus2" type="submit" value="Заблоковано" class="button1">
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
            else echo "<span><a href='/admin/templates/fake_control/idea.php?idea=$ideaId&page=$i" . (isset($_GET['ip']) ? '&ip='.$_GET['ip'] : '' ) . (isset($_GET['session_id']) ? '&session_id='.$_GET['session_id'] : '' ) . "'>" . (($i - 1) * $limit + 1) . '..' . min($i * $limit, $count) . "</a></span>";
            if ($i < $totalPages)
                echo ' | ';
            }?>
        </div>
      <?php  } ?>
</div>
</div>

</body>
</html>