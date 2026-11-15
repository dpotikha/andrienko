<?php

function getProductName($node, $return_articles = true, $return_cat = true) {
    $article = $node->name;

    $cat = getLocale(QNode::getInstance($node->parent_id), 'name_single');
    $collectionNode = QNode::getInstance($node->fields['collection']->value);
    $collection = $collectionNode ? getLocale($collectionNode) : '';

    if ($return_cat) {
        $name = trim($cat . ' ' . $collection);
    } else {
        $name = trim($collection);
    }
    if ($return_articles) {
        $name = trim($name) . ' ' . $article;
    }
    return trim($name);
}

function parsePhotoGalls($text) {

    $pg = preg_match_all('/div +id\="([0-9]+)" +class\=\"photoGalleryBlock\"/iU', $text, $pgMatches);
    if (count($pgMatches[1])) {
        foreach ($pgMatches[1] as $id) {
            $galleryNode = QNode::getInstance($id);
            if ($galleryNode) {
                $photos = $galleryNode->getChildren();
                ob_start();
                ?>
                <button id="cnt-photo-prev" class="content__prev"></button>
                <button id="cnt-photo-next" class="content__next"></button>
                <ul class="cycle-slideshow" data-cycle-fx="carousel" data-cycle-timeout="0" data-cycle-slides="> li" data-cycle-prev="#cnt-photo-prev" data-cycle-next="#cnt-photo-next" data-cycle-carousel-visible="6" data-allow-wrap="false">
                    <?php
                    foreach ($photos as $k => $v) {
                        $thumb = str_replace('/ithumb/', '/ithumb2/', $v->tfields['photo']['thumburl']);
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $thumb)) {
                            resizePhoto($_SERVER['DOCUMENT_ROOT'] . $v->tfields['photo']['url'], $_SERVER['DOCUMENT_ROOT'] . $thumb, 140, 130, 'crop');
                        }
                        $original_file = str_replace('/ithumb/', '/o/', $v->tfields['photo']['thumburl']);
                        ?>
                        <li><a class="js-cb" href="<?= $v->tfields['photo']['url'] ?>" <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . $original_file)) { ?>source-link="<?= $v->getHref() ?>"<?php } ?> title="<?= htmlspecialchars($v->tfields['title']) ?>" data-head="<?= htmlspecialchars($v->tfields['title']) ?>" data-link="#"><img src="<?= $thumb ?>" width="140" height="130" alt=""></a></li>
                <?php } ?>
                </ul>
                <?php
                $gal = ob_get_contents();
                ob_end_clean();
            } else {
                $gal = '';
            }
            $text = str_replace('<div id="' . $id . '" class="photoGalleryBlock"><img src="/_img/link_photogallery.jpg" alt="" /></div>', $gal, $text);
        }
    }
    return $text;
}

function getFormattedDate($time) {
//15 марта 2011      15 січня 2011      15 March 2011      15 märts 2011
    switch (SITE_LOCALE) {
        case 'ru':
            $months_genitive = array(
                "января",
                "февраля",
                "марта",
                "апреля",
                "мая",
                "июня",
                "июля",
                "августа",
                "сентября",
                "октября",
                "ноября",
                "декабря"
            );
            return date('j', $time) . ' ' . $months_genitive[date('m', $time) - 1] . ' ' . date('Y', $time);
            break;
        case 'ua':
            $months_genitive = array(
                "січня",
                "лютого",
                "березня",
                "квітня",
                "травня",
                "червня",
                "липня",
                "серпня",
                "вересня",
                "жовтня",
                "листопада",
                "грудня"
            );
            return date('j', $time) . ' ' . $months_genitive[date('m', $time) - 1] . ' ' . date('Y', $time);
            break;
        case 'en':
            return date('j F Y', $time);
            break;
        case 'ee':
            $months_genitive = array(
                "jaanuar",
                "veebruar",
                "märts",
                "aprill",
                "mai",
                "juuni",
                "juuli",
                "august",
                "sentember",
                "oktoober",
                "november",
                "detsember"
            );
            return date('j', $time) . ' ' . $months_genitive[date('m', $time) - 1] . ' ' . date('Y', $time);
            break;
    }
}

function getNumberOfComments($node_id) {
    global $MDB;
    $MDB->Query("SELECT count(id) as count FROM dbm_comments WHERE node_id = $node_id");
    $res = $MDB->NextResult();
    return $res['count'];
}

function getCommentsStatusStringBlog(&$node) {
    global $MDB;
    $MDB->Query("SELECT count(id) as count FROM dbm_comments WHERE node_id = $node->id");
    $res = $MDB->NextResult();
    $total = $res['count'];
    if (isset($_SESSION['USER'])) {
        if ($node->fields['user']->value == $_SESSION['USER']->id || $node->fields['member']->value == $_SESSION['USER']->id) {
            $MDB->Query("SELECT count(id) as count FROM dbm_comments WHERE is_read = 0 AND node_id = $node->id");
            $res = $MDB->NextResult();
            $unread = $res['count'];
        }
    }
    ?><span class="commentsAmount"><a href="<?= QURL::link($node) ?>#comments"><?= $total ?> <?= formattedRusCount($total, "РєРѕРјРµРЅС‚Р°СЂiРІ", "РєРѕРјРµРЅС‚Р°СЂ", "РєРѕРјРµРЅС‚Р°СЂСЏ") ?></a>
        <?php  if (isset($unread)) { ?>
        / <a href="<?= QURL::link($node) ?>#comments"><span><?= $unread ?></span> <?= formattedRusCount($unread, "РЅРѕРІРёС…", "РЅРѕРІРёР№", "РЅРѕРІРёС…") ?></a>
        <?php  } ?>
    </span>     <?php
}

function getCommentsStatusStringIdea(&$idea) {
    global $MDB;
    $MDB->Query("SELECT count(id) as count FROM dbm_comments WHERE node_id = {$idea['ideaId']}");
    $res = $MDB->NextResult();
    $total = $res['count'];
    if (isset($_SESSION['USER'])) {
        if ($idea['userId'] == $_SESSION['USER']->id) {
            $MDB->Query("SELECT count(id) as count FROM dbm_comments WHERE is_read = 0 AND node_id = {$idea['ideaId']}");
            $res = $MDB->NextResult();
            $unread = $res['count'];
        }
    }
    ?><span class="comments"><?= $total ?> <?= formattedRusCount($total, "РєРѕРјРµРЅС‚Р°СЂiРІ", "РєРѕРјРµРЅС‚Р°СЂ", "РєРѕРјРµРЅС‚Р°СЂСЏ") ?>
        <?php  if (isset($unread)) { ?>
        <br><span><?= $unread ?></span> <?= formattedRusCount($unread, "РЅРѕРІРёС…", "РЅРѕРІРёР№", "РЅРѕРІРёС…") ?>
        <?php  } ?>
    </span><?php
}

function getCommentPage($id, $node_id = 0, $commentsPerPage = SITE_COMMENTS_PER_PAGE) {
    if (!$id)
        return '';
    $finalComments = buildComments($node_id);
    $k = 0;
    if (sizeof($finalComments))
        foreach ($finalComments as $k => $v)
            if ($v['id'] == $id)
                break;
    if ($k + 1 > $commentsPerPage)
        return ceil(($k + 1) / $commentsPerPage);
    return '1';
}

function showAddBlock($id, $parentId = 0) {
    $parentId = $id;
    ?><table id='addBlock<?= $id ?>' class="addBlock">
        <tr>
            <td>Автор:</td>
            <td><input class="nameInput" type='text' id='name<?= $id ?>'></td>
        </tr>
        <tr>
            <td>Комментарий:</td>
            <td><textarea class="textInput" id='text<?= $id ?>'></textarea></td>
            <td class="saveTd">
                <input type="hidden" id="parent<?= $id ?>" value="<?= $parentId ?>" />
                <?php /*<input type="hidden" id="user_id<?= $id ?>" value="0" />*/?>
                <a href="javascript:;" class="submitComment" rel="<?= $id ?>"><img border="0" src="/admin/_img/ico/save.png" tooltip="Сохранить"></a>
                <a href="javascript:;" class="cancelSubmitComment" rel="<?= $id ?>"><img border="0" src="/admin/_img/ico/stop.png" tooltip="Отмена"></a>
            </td>

        </tr>
    </table><?php
}

function addComment($node_id, $parent_id, $name, $text, $user_id = 0, $is_read = 0) {
    global $MDB;
    $name = CProtect::string($name, 100);
    $text = CProtect::string($text, MAX_COMMENT_SYMBOLS);
    if (!$text)
        die('false');
    $MDB->QIns("dbm_comments", array('node_id' => $node_id, 'parent_id' => $parent_id, 'name' => $name, 'text' => $text, 'date' => date('Y/m/d H:i:s'), 'ip' => QURL::getClientIP(), 'user' => $user_id, 'session_id' => session_id()));
    $commentId = $MDB->LastId();
    $node = QNode::getInstance($node_id);
    if ($node->nodeClassId == 340)
        $MDB->QUpdate("dbm_nfv_blog_item", array('comments_count' => getNumberOfComments($node_id)), "node_id = $node_id");
    return $commentId;
}

function getMetaKeywords() {
    global $request;
    $rootNode = $request->getAttribute('rootNode');
    $CurrentNode = $request->getAttribute('CurrentNode');
    $topSection = $request->getAttribute('topSection');
    $top2Section = $request->getAttribute('top2Section');
    $top3Section = $request->getAttribute('top3Section');
    $top4Section = $request->getAttribute('top4Section');

    if ($CurrentNode && isset($CurrentNode->tfields['meta_keywords']) && strlen($CurrentNode->tfields['meta_keywords'])) {
        return CProtect::string($CurrentNode->tfields['meta_keywords']);
    }
    if ($top4Section && isset($top4Section->tfields['meta_keywords']) && strlen($top4Section->tfields['meta_keywords'])) {
        return CProtect::string($top4Section->tfields['meta_keywords']);
    }
    if ($top3Section && isset($top3Section->tfields['meta_keywords']) && strlen($top3Section->tfields['meta_keywords'])) {
        return CProtect::string($top3Section->tfields['meta_keywords']);
    }
    if ($top2Section && isset($top2Section->tfields['meta_keywords']) && strlen($top2Section->tfields['meta_keywords'])) {
        return CProtect::string($top2Section->tfields['meta_keywords']);
    }
    if ($topSection && isset($topSection->tfields['meta_keywords']) && strlen($topSection->tfields['meta_keywords'])) {
        return CProtect::string($topSection->tfields['meta_keywords']);
    }
    if ($rootNode && isset($rootNode->tfields['meta_keywords']) && strlen($rootNode->tfields['meta_keywords'])) {
        return CProtect::string($rootNode->tfields['meta_keywords']);
    }
    return '';
}

function getMetaDescription() {
    global $request;
    $rootNode = $request->getAttribute('rootNode');
    $CurrentNode = $request->getAttribute('CurrentNode');
    $topSection = $request->getAttribute('topSection');
    $top2Section = $request->getAttribute('top2Section');
    $top3Section = $request->getAttribute('top3Section');
    $top4Section = $request->getAttribute('top4Section');

    if ($CurrentNode && isset($CurrentNode->tfields['meta_description']) && strlen($CurrentNode->tfields['meta_description'])) {
        return CProtect::string($CurrentNode->tfields['meta_description']);
    }
    if ($top4Section && isset($top4Section->tfields['meta_description']) && strlen($top4Section->tfields['meta_description'])) {
        return CProtect::string($top4Section->tfields['meta_description']);
    }
    if ($top3Section && isset($top3Section->tfields['meta_description']) && strlen($top3Section->tfields['meta_description'])) {
        return CProtect::string($top3Section->tfields['meta_description']);
    }
    if ($top2Section && isset($top2Section->tfields['meta_description']) && strlen($top2Section->tfields['meta_description'])) {
        return CProtect::string($top2Section->tfields['meta_description']);
    }
    if ($topSection && isset($topSection->tfields['meta_description']) && strlen($topSection->tfields['meta_description'])) {
        return CProtect::string($topSection->tfields['meta_description']);
    }
    if ($rootNode && isset($rootNode->tfields['meta_description']) && strlen($rootNode->tfields['meta_description'])) {
        return CProtect::string($rootNode->tfields['meta_description']);
    }
    return '';
}

/* * ******************************************************** */

function set_access_page($owner_id) {
    if (isset($_SESSION['user']) && $_SESSION['user']->id == $owner_id) {
        define('PAGE_ACCESS', 2);
    } elseif (isset($_SESSION['user'])) {
        define('PAGE_ACCESS', 1);
    } else {
        define('PAGE_ACCESS', 0);
    }
}

/* * ******************************************************** */

function makeCategoriesLinks($categories) {
    if (!is_array($categories))
        $categories = explode(',', $categories);
    $cats = array();
    foreach ($categories as $k => $v) {
        $cat = QNode::getInstance($v);
        if ($cat) {
            $cats[] = '<a href="' . QURL::link($cat) . '" class="d-blue">' . getLocale($cat, 'name') . '</a>';
        }
    }
    return implode(', ', $cats);
}

/* * ******************************************************** */


/* * ******************************************************** */




/* * ******************************************************** */

function is_leapyear($year = 2004) {
    if ($year % 400 == 0)
        return 1;
    if ($year % 4 == 0 && $year % 100 != 0)
        return 1;
    return 0;
}

function formattedDateTime($date, $today = 'сегодня', $yesterday = 'вчера') {
    if (date('d-m-Y') == date('d-m-Y', $date)) {
        return $today . ', ' . date('H:i', $date);
    }
    if (date('d-m-Y') == (date('d-m-Y', $date + 24 * 3600))) {
        return $yesterday . ', ' . date('H:i', $date);
    }

    return date('d.m.Y, H:i', $date);
}

function formattedRusCount($count, $loc1 = 'значений', $loc2 = 'значение', $loc3 = 'значения') {
    $count = (string) $count;
    $len = strlen($count);
    $last1 = $count[$len - 1];
    $last2 = ($count > 1) ? ($count[$len - 2] . $count[$len - 1]) : $last1;

    if (($last2 >= 11 && $last2 <= 14) || in_array($last1, array(0, 5, 6, 7, 8, 9)))
        return sprintf($loc1, $count);
    if ($last1 == 1)
        return sprintf($loc2, $count);
    return sprintf($loc3, $count);
}

function getAge($date) {
    $is_leap_year = is_leapyear($date['year']);
    $age = 0;
    $years = date('Y') - $date['year'] - 1;
    $y = $is_leap_year ? 2004 : 2003;
    $cur_day = date('j');
    $cur_month = date('n');
    $time1 = mktime(0, 0, 0, $date['month'], $date['day'], $y);
    $time2 = mktime(0, 0, 0, $cur_month, $cur_day, $y);
    $age = $years + (($time2 >= $time1) ? 1 : 0);
    return $age;
}

function output_file($file, $name = '') {
//do something on download abort/finish
//register_shutdown_function( 'function_name'  );
    if ($name == "") {
        $info = pathinfo($file);
        $name = $info['basename'];
    }
    if (!file_exists($file))
        die('file not exist!');
    $size = filesize($file);
    $name = rawurldecode($name);

    if (preg_match('~Opera(/| )([0-9].[0-9]{1,2})~', $_SERVER['HTTP_USER_AGENT']))
        $UserBrowser = "Opera";
    elseif (preg_match('~MSIE ([0-9].[0-9]{1,2})~', $_SERVER['HTTP_USER_AGENT']))
        $UserBrowser = "IE";
    else
        $UserBrowser = '';

/// important for download im most browser
    $mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?
            'application/force-download' : 'application/octet-stream';
    @ob_end_clean(); /// decrease cpu usage extreme
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header('Accept-Ranges: bytes');
    header("Cache-control: private");
    header('Pragma: private');

/////  multipart-download and resume-download
    // "Range: bytes=START-[END]"; anything else (or out of bounds) gets the whole file
    $range = 0;
    $end = $size - 1;
    if (isset($_SERVER['HTTP_RANGE']) && preg_match('/^bytes=(\d+)-(\d*)$/', trim($_SERVER['HTTP_RANGE']), $m)
            && (int)$m[1] < $size) {
        $range = (int)$m[1];
        if ($m[2] !== '' && (int)$m[2] >= $range && (int)$m[2] < $size)
            $end = (int)$m[2];
        $new_length = $end - $range + 1;
        header("HTTP/1.1 206 Partial Content");
        header("Content-Length: $new_length");
        header("Content-Range: bytes $range-$end/$size");
    } else {
        header("Content-Length: " . $size);
    }
    $chunksize = 1 * (1024 * 1024);
    $bytes_send = 0;
    $bytes_total = $end - $range + 1;
    if ($file = fopen($file, 'r')) {
        if ($range > 0)
            fseek($file, $range);
        while ($bytes_send < $bytes_total && !feof($file) and ( connection_status() == 0)) {
            $buffer = fread($file, min($chunksize, $bytes_total - $bytes_send));
            print($buffer); //echo($buffer); // is also possible
            flush();
            $bytes_send += strlen($buffer);
//sleep(1);//// decrease download speed
        }
        fclose($file);
    } else
        die('error can not open file');
    if (isset($new_length))
        $size = $new_length;
    die();
}

///////////////////////////////////////////////////////////////////////////
function clearTmpDir($tmpDir, $time_limit) {
    $subdirs = list_dir_contents_only_dirs($tmpDir);
    print_r($subdirs);
    foreach ($subdirs as $dir) {
        $files = list_dir_contents_only_files($dir);
        foreach ($files as $file) {
            $update_time = filemtime($file);
            echo $update_time . "<br>";
            if ($update_time < $time_limit) {
//echo $file.", ".date("d.m.Y h:i",$update_time)."<br>";
                if (@unlink($file)) {
//echo "deleted $file<br>";
                }
            }
        }

        $update_time = filemtime($dir);
        if ($update_time < $time_limit) {
//echo $dir.",        ".date("d.m.Y h:i",$update_time)."<br>";
            if (@rmdir($dir)) {
//echo "deleted $dir<br>";
            }
        }
    }
}

///////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
function uniqNumber() {
    return md5(uniqid(rand(), true));
}

function hexbin($temp) {
    $len = strlen($temp);
    for ($i = 0; $i < $len; $i+=2)
        $data.=chr(hexdec(substr($temp, $i, 2)));
    return $data;
}

//////////////////////////////////////////////////////////////////////////////////////////////////


function getHash() {
    $hash = "";
    $alphabet = "abcdefghijklmnopqrstuvwxyz0123456789";
    for ($i = 0; $i <= 20; $i++) {
        $r = rand(0, 35);
        $hash.=substr($alphabet, $r, 1);
    }
    return $hash;
}

function tformatDate($date) {
    $arr = explode(".", $date);
//<!--Пятница, 27 апреля
    return UppernameRusDayFromEngDay(date("l", mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]))) . ", " . $arr[0] . " " . getLowerMonthsGenitive($arr[1] - 1);
}

function getMonthsGenitive($id) {
    return getLowerMonthsGenitive($id);
    global $LOCALE;
    return $LOCALE['_main.gmonth_' . sprintf('%02d', $id)];
}

//////////////////////////////////////////////////////////////////////////////////////////////////
function getLowerMonthsGenitive($id) {
    if (SITE_LOCALE == 'ru') {

        $months_genitive = array(
            "января",
            "февраля",
            "марта",
            "апреля",
            "мая",
            "июня",
            "июля",
            "августа",
            "сентября",
            "октября",
            "ноября",
            "декабря"
        );
    } else {
        $months_genitive = array(
            "січня",
            "лютого",
            "березня",
            "квітня",
            "травня",
            "червня",
            "липня",
            "серпня",
            "вересня",
            "жовтня",
            "листопада",
            "грудня"
        );
    }
    return $months_genitive[$id];
}

//////////////////////////////////////////////////////////////////////////////////////////////////
function getUpperMonthsGenitive($id) {
    $months_genitive = array(
        "Січень",
        "Лютий",
        "Березень",
        "Квітень",
        "Травень",
        "Червень",
        "Липень",
        "Серпень",
        "Вересень",
        "Жовтень",
        "Листопад",
        "Грудень"
    );
    return $months_genitive[$id];
}

//////////////////////////////////////////////////////////////////////////////////////////////////
function nameRusDayFromEngDay($eng_day) {
    switch ($eng_day) {
        case "Sunday": return "воскресенье";
        case "Monday": return "понедельник";
        case "Tuesday": return "вторник";
        case "Wednesday": return "среда";
        case "Thursday": return "четверг";
        case "Friday": return "пятница";
        case "Saturday": return "суббота";
    }
    return "";
}

function UppernameRusDayFromEngDay($eng_day) {
    switch ($eng_day) {
        case "Sunday": return "Воскресенье";
        case "Monday": return "Понедельник";
        case "Tuesday": return "Вторник";
        case "Wednesday": return "Среда";
        case "Thursday": return "Четверг";
        case "Friday": return "Пятница";
        case "Saturday": return "Суббота";
    }
    return "";
}

//////////////////////////////////////////////////////////////////////////////////////////////////
function GetNavigatorPath($CurrentNode, $max = -1, $is_include = 1) {
    $parent = $CurrentNode->getParentList();
    $parent = array_reverse($parent);
    for ($i = 0; $i < $max; $i++) {
        unset($parent[$i]);
    }
    foreach ($parent as $k => $v) {

        $navigatorPath = '<a href="' . $v->absolutePath . '">' . $v->name . '</a>&nbsp;&nbsp;/&nbsp;&nbsp;' . $navigatorPath;
    }
    if ($is_include == 1)
        $navigatorPath.='<strong>' . $CurrentNode->name . '</strong>';
    return $navigatorPath;
}

//////////////////////////////////////////////////////////////////////////////////////////////////
function getLevelByPath($path) {
    $level = count(explode("/", $path));
    return $level;
}

//////////////////////////////////////////////////////////////////////////////////////////////////
function edump($var, $info = FALSE, $max_level_recursion = 4) {
    sdump($var, $info, $max_level_recursion);
    exit();
}

/* * *****************************************************************************
 *
 *
 *
 *
 *
 *
 * **************************************************************************** */

function sdump($var, $info = FALSE, $max_level_recursion = 3) {
    dump($var, $info, $max_level_recursion);
}

/* * *****************************************************************************
 *
 *
 *
 *
 *
 *
 * **************************************************************************** */

function dump(&$var, $info = FALSE, $max_level_recursion = 4) {
    mb_internal_encoding('UTF-8');
    $scope = false;
    $prefix = 'unique';
    $suffix = 'value';

    if ($scope)
        $vals = $scope;
    else
        $vals = $GLOBALS;

    $old = $var;
    $var = $new = $prefix . rand() . $suffix;
    $vname = FALSE;
    foreach ($vals as $key => $val)
        if ($val === $new)
            $vname = $key;
    $var = $old;

    echo "<pre style='margin: 0px 0px 10px 0px; display: block; background: #999; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px; font-weight:bold;'>";
    if ($info != FALSE)
        echo "<b style='color: red;'>$info:</b><br>";
    do_dump($var, '$' . $vname, NULL, NULL, $max_level_recursion);
    echo "</pre>";
}

/* * *****************************************************************************
 *
 *
 *
 *
 *
 *
 * **************************************************************************** */

function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL, $max_level_recursion = 3, $level_recursion = 1) {

    if ($level_recursion > $max_level_recursion) {
        echo "$indent$var_name <span style='color:#a2a2a2'></span> => <span style='color:#ff0000;'>" . 'Maximum level of recursion: [' . $max_level_recursion . ']' . "</span><br>";
        return;
    }
    $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference . $var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme';
    $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar])) {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    } else {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if ($type == "String")
            $type_color = "<span style='color:green'>";
        elseif ($type == "Integer")
            $type_color = "<span style='color:red'>";
        elseif ($type == "Double") {
            $type_color = "<span style='color:#3ff'>";
            $type = "Float";
        } elseif ($type == "Boolean")
            $type_color = "<span style='color:#92008d'>";
        elseif ($type == "NULL")
            $type_color = "<span style='color:black'>";

        if (is_array($avar)) {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => " : "") . "<span style='color:#FF0'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach ($keys as $name) {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent . $do_dump_indent, $reference, $max_level_recursion, $level_recursion + 1);
            }
            echo "$indent)<br>";
        } elseif (is_object($avar)) {
            echo "$indent$var_name <span style='color:#FF0'>$type</span><br>$indent(<br>";
            foreach ($avar as $name => $value)
                do_dump($value, "$name", $indent . $do_dump_indent, $reference, $max_level_recursion, $level_recursion + 1);
            echo "$indent)<br>";
        } elseif (is_int($avar))
            echo "$indent$var_name = <span style='color:#FF0'>$type(" . mb_strlen($avar) . ")</span> $type_color$avar</span><br>";
        elseif (is_string($avar))
            echo "$indent$var_name = <span style='color:#FF0'>$type(" . mb_strlen($avar) . ")</span> $type_color\"$avar\"</span><br>";
        elseif (is_float($avar))
            echo "$indent$var_name = <span style='color:#FF0'>$type(" . mb_strlen($avar) . ")</span> $type_color$avar</span><br>";
        elseif (is_bool($avar))
            echo "$indent$var_name = <span style='color:#FF0'>$type(" . mb_strlen($avar) . ")</span> $type_color" . ($avar == 1 ? "TRUE" : "FALSE") . "</span><br>";
        elseif (is_null($avar))
            echo "$indent$var_name = <span style='color:#FF0'>$type(" . mb_strlen($avar) . ")</span> {$type_color}NULL</span><br>";
        else
            echo "$indent$var_name = <span style='color:#FF0'>$type(" . (($type != 'Resource') ? mb_strlen($avar) : strlen($avar)) . ")</span> $avar<br>";

        $var = $var[$keyvar];
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////
?>