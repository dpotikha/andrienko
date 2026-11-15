<?php

require_once("prepend.php");
$MDB->QIns('dbm_log', array(
                'user_id' => $_SESSION['CurrentAdminUser']->id,
                'activity' => 1,
                'ip' => QURL::getClientIP(),
                'server' => jsonServerVars(),
                'date' => date('Y-m-d H:i:s')
            ));
$session->removeAttribute(SESSION_LOGGED_USER_ATTR);
Header("Location: /admin");

?>