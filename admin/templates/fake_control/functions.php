<?php
require_once($_SERVER['DOCUMENT_ROOT']."/admin/prepend.php");
//$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
//if (!$currentLoggedUser)
//    die('Permission forbidden');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 'On');

//if (!isset($_SESSION['fake_control_delay_time']))
//    $_SESSION['fake_control_delay_time'] = 20;
//if (!isset($_SESSION['fake_control_reg_delay']))
//    $_SESSION['fake_control_reg_delay'] = 40;
//if (isset($_POST['delay_time']))
//    $_SESSION['fake_control_delay_time'] = (int)$_POST['delay_time'];
//if (isset($_POST['reg_delay']))
//    $_SESSION['fake_control_reg_delay'] = (int)$_POST['reg_delay'];


//$MDB->Query("CREATE TABLE dbm_banned_ips (ip varchar(20) NOT NULL, PRIMARY KEY (ip))");
//if (isset($_GET['do']) && $_GET['do'] == 'banip' && isset($_GET['bip']))
//    setIpBan($_GET['bip'], 1);
//if (isset($_GET['do']) && $_GET['do'] == 'unbanip' && isset($_GET['bip']))
//    setIpBan($_GET['bip'], 0);


//function isAllowedIp($ipString){
//    $ips = explode(',', $ipString);
//    global $MDB;
//    foreach ($ips as $v) {
//        $v = trim($v);
//        if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/iU', $v)){
//           $MDB->Query("SELECT count(*) as c FROM dbm_banned_ips WHERE ip = '$v'");
//           $res = $MDB->NextResult();
//           if ($res['c'])
//               return false;
//        }
//    }
//    return true;
//}
function numberOfVotesByUser($id){
    global $MDB;
    $MDB->Query("SELECT count(*) as c FROM dbm_nfv_vote_semifinal WHERE member = $id");
    $res = $MDB->NextResult();
    if ($res['c'])
        return $res['c'];
    return 0;
}
function numberOfCommentsByUser($id){
    global $MDB;
    $MDB->Query("SELECT count(*) as c FROM dbm_nfv_comment WHERE member = $id");
    $res = $MDB->NextResult();
    if ($res['c'])
        return $res['c'];
    return 0;
}
//function changeVoteStatus($votes, $status){
//    $status = (int)$status;
//    if (!($status >= 0 && $status <= 2))
//        return;
//    global $MDB;
//    $voteCondition = 'node_id = ' . implode(' OR node_id = ', $votes);
//    $MDB->Query("UPDATE dbm_nfv_vote_semifinal SET confirmed = $status WHERE $voteCondition");
//    updateIdeasRating();
//}
function getQuality($vote, $ideaId){
    $delay_time = $_SESSION['fake_control_delay_time'];
    $reg_delay = $_SESSION['fake_control_reg_delay'];
    if ($vote->tfields['delay_time'] < $delay_time || $vote->tfields['reg_delay'] < $reg_delay || $vote->tfields['country'] == '' || $vote->tfields['country'] == 'PROXY')
        return "<b style='color:red'>Сумнівний</b>";
    global $MDB;
    $MDB->Query("SELECT count(*) as c FROM dbm_nfv_vote_semifinal as vote, dbm_nodes as nvote
            WHERE vote.node_id = nvote.id AND nvote.parent_id = $ideaId
            AND (vote.ip = '" . $vote->tfields['ip'] . "' OR vote.session_id = '" . $vote->tfields['session_id'] . "')");
    $res = $MDB->NextResult();
    if ($res['c'] > 1)
        return "<b style='color:red'>Сумнівний</b>";
    return "<span style='color:green'>Добрий</span>";
}

function nubmerOfDisputableVotes($ideaId){
    $delay_time = $_SESSION['fake_control_delay_time'];
    $reg_delay = $_SESSION['fake_control_reg_delay'];
    $idea = QNode::getInstance($ideaId);
    global $MDB;
    $MDB->Query("SELECT count(DISTINCT ip) as c 
            FROM (SELECT ip FROM dbm_nfv_vote_semifinal as vote, dbm_nodes as nvote
            WHERE vote.confirmed != 2 AND vote.node_id = nvote.id AND nvote.parent_id = $ideaId
            AND vote.delay_time > $delay_time AND vote.reg_delay > $reg_delay AND vote.country != 'PROXY' 
            AND vote.country != '' GROUP BY session_id) as temp");
//    $MDB->Query("SELECT count(DISTINCT ip) as c 
//            FROM (SELECT ip FROM dbm_nfv_idea as idea, dbm_nfv_vote_semifinal as vote, dbm_nodes as nvote
//            WHERE vote.node_id = nvote.id AND nvote.parent_id = idea.node_id AND idea.node_id = $ideaId
//            AND vote.delay_time > $delay_time AND vote.reg_delay > $reg_delay AND vote.country != 'PROXY'
//            AND vote.country != '' GROUP BY session_id) as temp");

//    $MDB->Query("SELECT count(DISTINCT session_id) as c, idea.rating as rating FROM dbm_nfv_idea as idea, dbm_nfv_vote_semifinal as vote, dbm_nodes as nvote
//        WHERE vote.node_id = nvote.id AND nvote.parent_id = idea.node_id AND idea.node_id = $ideaId
//            AND vote.delay_time >= $delay_time AND vote.reg_delay >= $reg_delay AND vote.country != 'PROXY' AND vote.country != ''");
    $res = $MDB->NextResult();
    if ($res['c']) 
        return $idea->tfields['rating_semifinal'] - $res['c'];
    return $idea->tfields['rating_semifinal'];
}
function updateIdeasRating(){
    global $MDB;
    $MDB->Query("UPDATE dbm_nfv_idea SET rating_semifinal = 0");
    $MDB->Query("UPDATE dbm_nfv_idea,
        (SELECT distinct(n.parent_id) as ideaid, count(v.node_id) as votecount
        FROM dbm_nfv_vote_semifinal as v, dbm_nodes as n
        WHERE (v.confirmed = 0 OR v.confirmed = 1) AND v.node_id = n.id GROUP BY n.parent_id) as votesofidea
        SET dbm_nfv_idea.rating_semifinal = votesofidea.votecount
        WHERE dbm_nfv_idea.node_id = votesofidea.ideaid");
}

//function setUserBan($userId, $banValue){ // $banValue == 1  - banned
//    $userId = (int)$userId;
//    $user = Node::findById($userId);
//    if ($user && $user->fields['firstname']->value == '')
//        QNode::updateNode($user, array('ban' => $banValue));
//    if ($banValue == 1)
//        $confirmed = 2;
//    else $confirmed = 0;
//    global $MDB;
//    $MDB->Query("UPDATE dbm_nfv_vote_semifinal SET confirmed = $confirmed WHERE member = $userId");
//    //updateIdeasRating();
//}
function setUserBan($userIds, $banValue){ // $banValue == 1  - banned
    global $MDB;
    $memberCondition = 'node_id = ' . implode(' OR node_id = ', $userIds);
    $voteCondition = 'member = ' . implode(' OR member = ', $userIds);
    $MDB->Query("UPDATE dbm_nfv_member SET ban = $banValue WHERE firstname = '' AND ($memberCondition)");
    if ($banValue == 1)
        $confirmed = 2;
    else $confirmed = 0;
    $MDB->Query("UPDATE dbm_nfv_vote_semifinal SET confirmed = $confirmed WHERE $voteCondition");
    updateIdeasRating();
}

function setIpBan($ipString, $banValue){ // $banValue == 1  - banned
    $banValue = (int)$banValue;
    $ips = explode(',', $ipString);
    $ipCondition = '';
    global $MDB;
    foreach ($ips as $v) {
        $v = trim($v);
        if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/iU', $v)){
           $ipCondition .= " ip LIKE '%$v%' OR ";
           if ($banValue == 1)
               $MDB->Query("INSERT IGNORE INTO dbm_banned_ips SET ip = '$v'");
           else $MDB->Query("DELETE FROM dbm_banned_ips WHERE ip = '$v'");
        }
    }
    if ($ipCondition){
        $ipCondition = '(' . mb_substr($ipCondition, 0, mb_strlen($ipCondition) - 4) . ')';
        if ($banValue == 1)
            $confirmed = 2;
        else $confirmed = 0;
        global $MDB;
        $MDB->Query("UPDATE dbm_nfv_vote_semifinal SET confirmed = $confirmed WHERE $ipCondition");
        $MDB->Query("UPDATE dbm_nfv_member SET ban = $banValue WHERE $ipCondition AND firstname = ''");
        updateIdeasRating();
    }
}

function getIpLinks($ipString, $href = '', $sign = '?'){
    $ips = explode(',', $ipString);
    $s = '';
    foreach ($ips as $v) {
        $v = trim($v);
        if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/iU', $v)){
            if ($href)
                $s .= '<a href="' . $href . $sign . 'ip=' . $v . '">' . $v . '</a>&nbsp;&nbsp;';
            else $s .= $v . '&nbsp;&nbsp;';
        }
    }
    if (!$s) return '&nbsp;';
    return $s;
}

function headTitle($where, $text, $value = '', $href = ''){
    ?><td><nobr><b><?=$text?></b></nobr></td><?php  return;

    if ($_SESSION['fake_control_sortby_' . $where] == $value){
        if ($_SESSION["fake_control_sortby_$where"."_direction"] == 'ASC'){?><td class="actDown"><nobr><b><a href="<?=$href?>&sort_by=<?=$value?>"><?=$text?></a></b></nobr></td><?php }
        else {?><td class="actUp"><nobr><b><a href="<?=$href?>&sort_by=<?=$value?>"><?=$text?></a></b></nobr></td><?php }
        return;
    }
    if ($value != ''){?><td><nobr><b><a href="<?=$href?>&sort_by=<?=$value?>"><?=$text?></a></b></nobr></td><?php }
    else{?><td><nobr><b><?=$text?></b></nobr></td><?php }
}
function getSortOrder($where, $thisUrl){
    if (!isset($_SESSION['fake_control_sortby_'.$where])){
        if ($where == 'members'){
            $_SESSION['fake_control_sortby_'.$where] = 'n.name';
            $_SESSION["fake_control_sortby_$where"."_direction"] = 'ASC';
        } else {
            $_SESSION['fake_control_sortby_'.$where] = 'v.date';
            $_SESSION["fake_control_sortby_$where"."_direction"] = 'DESC';
        }
    }
    if (isset($_GET['sort_by'])){
        if ($_GET['sort_by'] == $_SESSION['fake_control_sortby_'.$where])
            $_SESSION["fake_control_sortby_$where"."_direction"] = $_SESSION["fake_control_sortby_$where"."_direction"] == 'ASC' ? 'DESC' : 'ASC';
        else {
            $_SESSION['fake_control_sortby_'.$where] = db_escape($_GET['sort_by']);
            $_SESSION["fake_control_sortby_$where"."_direction"] = 'ASC';
        }
        QURL::redirect($thisUrl);
    }
    return $_SESSION['fake_control_sortby_'.$where] . ' ' .  $_SESSION["fake_control_sortby_$where"."_direction"];
}
function delayParams($url){
    ?><form method="post" action="<?=$url?>">
            <table>
                <tr><td colspan="3"><b>Параметри оцінки сумнівності голосів</b></td></tr>
                <tr><td>Час на голос </td><td><input size="4" type="text" value="<?=$_SESSION['fake_control_delay_time']?>" name="delay_time" /></td><td>секунд</td></tr>
                <tr><td>Час від реєстрації </td><td><input size="4" type="text" value="<?=$_SESSION['fake_control_reg_delay']?>" name="reg_delay" /></td><td>секунд</td></tr>
                <tr><td colspan="3" align="right"><input type="submit" value="Зберегти" /></td></tr>
            </table>
    </form><?php 
}
?>
