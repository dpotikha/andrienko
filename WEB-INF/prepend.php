<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); // E_STRICT is gone since PHP 8.0 and deprecated in 8.4
ini_set("display_errors", 'Off');
ini_set("log_errors", 'On');
// log to <account home>/tmp/php-errors.log (outside the web root): the server's php-fpm log is not readable from the site account
$__errLogDir = dirname($_SERVER["DOCUMENT_ROOT"]) . "/tmp";
if (is_dir($__errLogDir) && is_writable($__errLogDir)) {
    $__errLog = $__errLogDir . "/php-errors.log";
    if (@filesize($__errLog) > 10 * 1024 * 1024) @rename($__errLog, $__errLog . ".1"); // keep at most ~20 MB
    ini_set("error_log", $__errLog);
}
unset($__errLogDir, $__errLog);

ini_set("memory_limit", "512M");
ini_set("max_execution_time", "600");
ini_set('mbstring.internal_encoding', 'UTF-8');

ini_set('php_value default_charset', 'utf-8');
ini_set('mbstring.internal_encoding', 'UTF-8');

//$DOC_ROOT = $GLOBALS["HTTP_SERVER_VARS"]["DOCUMENT_ROOT"];
$DOC_ROOT = $_SERVER["DOCUMENT_ROOT"];

if (strpos(PHP_OS, "WIN") !== false) {
    DEFINE("SYSTEM_SEPARATOR", ";");
    DEFINE("DIR_SEPARATOR", "\\");
    DEFINE("FILE_SEPARATOR", "\\");
    DEFINE("NEWLINE", "\r\n");
} else {
    DEFINE("SYSTEM_SEPARATOR", ":");
    DEFINE("DIR_SEPARATOR", "/");
    DEFINE("FILE_SEPARATOR", "/");
    DEFINE("NEWLINE", "\n");
}


ini_set("include_path", "$DOC_ROOT/WEB-INF/classes");

DEFINE("FIELD_IMGLIB_DIR", $DOC_ROOT . DIR_SEPARATOR . "/s/i");
DEFINE("FIELD_SERVERFILES_DIR", $DOC_ROOT . DIR_SEPARATOR . "/s/files");
DEFINE("FIELD_IMGLIB_THUMBNAILS_DIR", $DOC_ROOT . DIR_SEPARATOR . "/s/ithumb");
DEFINE("FIELD_IMGLIB_THUMBNAILS2_DIR", $DOC_ROOT . DIR_SEPARATOR . "/s/ithumb2");
DEFINE("FIELD_IMGLIB_THUMBNAILS3_DIR", $DOC_ROOT . DIR_SEPARATOR . "/s/ithumb3");
DEFINE("FIELD_IMGLIB_ORIGINAL_DIR", $DOC_ROOT . DIR_SEPARATOR . "/s/o");
DEFINE("FILE_FIELD_DIR", $DOC_ROOT . DIR_SEPARATOR . "_files");
$QLOCALES = array();
function is_mobile(){
    return false;
}
function getUploadFile($content_type, $container_type) {
    if (isset($_SESSION['import_files'][$content_type][$container_type]) && is_array($_SESSION['import_files'][$content_type][$container_type]) && count($_SESSION['import_files'][$content_type][$container_type]) == 1) {
        foreach ($_SESSION['import_files'][$content_type][$container_type] as $k1 => $v1) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/s/' . 'tmp/import/' . session_id() . '/' . $k1)) {
                return false;
            } else {
                return array($_SERVER['DOCUMENT_ROOT'] . '/s/' . 'tmp/import/' . session_id() . '/' . $k1, $v1, '/s/tmp/import/' . session_id() . '/' . $k1);
            }
            break;
        }
    }
    return false;
}

function jsonServerVars() {
    $s = $_SERVER;
    foreach ($s as $k => $v) {
        if (!preg_match('/^HTTP\_/iU', $k) && !in_array($k, array('REMOTE_ADDR', 'REQUEST_URI', 'REMOTE_PORT', 'REQUEST_METHOD'))) {
            unset($s[$k]);
        }
    }
    return json_encode($s);
}

function amountFormat($amount) {
    return number_format($amount, 0, '.', ' ');
}

function priceFormat($price) {
    return number_format($price, 2, '.', ' ');
}

function percentFormat($percent) {
    return number_format($percent, 2, '.', ' ') . '%';
}

function csv_parser($file) {
    $csv_lines = file($file);
    if (is_array($csv_lines)) {
        //������ csv
        $cnt = count($csv_lines);
        for ($i = 0; $i < $cnt; $i++) {
            $line = $csv_lines[$i];
            $line = trim($line);
            //��������� �� ��, ��� ����� ���� �������� ������ ������ �������
            $first_char = true;
            //����� �������
            $col_num = 0;
            $length = strlen($line);
            $skip_char = false;
            $column = '';
            for ($b = 0; $b < $length; $b++) {
                //���������� $skip_char ���������� ������������ �� ������ ������
                if ($skip_char != true) {
                    //���������� ������������/�� ������������ ������
                    ///print $line[$b];
                    $process = true;
                    //���������� ������ ��������� ������� �� ������� �������
                    if ($first_char == true) {
                        if ($line[$b] == '"') {
                            $terminator = '";';
                            $process = false;
                        }
                        else
                            $terminator = ';';
                        $first_char = false;
                    }

                    //������������� ������ �������, ��������� �� �������
                    if ($line[$b] == '"') {
                        $next_char = $line[$b + 1];
                        //��������� �������
                        if ($next_char == '"')
                            $skip_char = true;
                        //������ ����� �������
                        elseif ($next_char == ';') {
                            if ($terminator == '";') {
                                $first_char = true;
                                $process = false;
                                $skip_char = true;
                            }
                        }
                    }

                    //���������� ������� ����� � �������
                    if ($process == true) {
                        if ($line[$b] == ';') {
                            if ($terminator == ';') {

                                $first_char = true;
                                $process = false;
                            }
                        }
                    }

                    if ($process == true)
                        $column .= $line[$b];

                    if ($b == ($length - 1)) {
                        $first_char = true;
                    }

                    if ($first_char == true) {

                        $values[$i][$col_num] = $column;
                        $column = '';
                        $col_num++;
                    }
                }
                else
                    $skip_char = false;
            }
        }
    }
    return $values;
}

function L($var) {
    global $LOCALE;
    return isset($LOCALE[$var]) ? $LOCALE[$var] : '';
}

function QLANG($lang) {
    $LOCALES = require($_SERVER['DOCUMENT_ROOT'] . '/langs/' . $lang . '.php');
    $LOCALE = array();
    foreach ($LOCALES as $section_shortname => $v) {
        foreach ($v['locale'] as $k1 => $v1) {
            if (preg_match_all('/(\[#(.*)#\]\,*)+/iU', $v1, $matches)) {
                $LOCALE[$section_shortname . '.' . $k1] = $matches[2];
            } else {
                $LOCALE[$section_shortname . '.' . $k1] = $v1;
            }
        }
    }
    return $LOCALE;
}

function class_include($c) {
    $dynamic_class_path = $_SERVER['DOCUMENT_ROOT'] . "/dynamic_classes/" . $c . ".class.php";
    if (!file_exists($dynamic_class_path)) {
        die('Dynamic class file does not exists: ' . $c);
    }
    require_once($dynamic_class_path);
}

function switchLocale($lang, $path = NULL) {
    if (is_null($path)) {
        $path = $_SERVER['REQUEST_URI'];
    }
    if ($path == '/en' || $path == '/ua')
        $path = '';
    if ($path == '')
        $path = '/';
    $res = explode('?', $path);
    if (count($res) > 1) {
        $path = $res[0];
        $q = '?' . $res[1];
    } else {
        $path = $res[0];
        $q = '';
    }
    if (!mb_strlen($path) || $path == "/")
        return "/" . $lang . "/" . $q;
    //dump($path);
    return preg_replace("/\/" . SITE_LOCALE . "\//", "/" . $lang . "/", $path) . $q;
}

///////////////////////////////////////////////////

function resizeAvatar($file_avatar, $pNode, $_tw = 51, $_th = 51, $_w = 100, $_h = 100, $tstrategy = 'crop', $strategy = 'crop', $save_original = false) {
    $path = _resizeAvatar($file_avatar, $pNode, $_w, $_h, FIELD_IMGLIB_DIR, $strategy);
    $path = _resizeAvatar($file_avatar, $pNode, $_tw, $_th, FIELD_IMGLIB_THUMBNAILS_DIR, $tstrategy);
    if ($save_original) {
        $exp = explode('.', strtolower($file_avatar['name']));
        $imgPath = QNode::getImgPath($pNode);
        FileWorks::mkdir_recursive(FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/", 0777);
        copy($file_avatar['tmp_name'], FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
    }
    return $path;
}
function resizeAvatarThumb2($file_avatar, $pNode, $_tw = 51, $_th = 51, $_tw2 = 51, $_th2 = 51, $_w = 100, $_h = 100, $tstrategy = 'crop',$ts2trategy = 'crop', $strategy = 'crop', $save_original = false) {
    $path = _resizeAvatar($file_avatar, $pNode, $_w, $_h, FIELD_IMGLIB_DIR, $strategy);
    $path = _resizeAvatar($file_avatar, $pNode, $_tw, $_th, FIELD_IMGLIB_THUMBNAILS_DIR, $tstrategy);
    $path = _resizeAvatar($file_avatar, $pNode, $_tw2, $_th2, FIELD_IMGLIB_THUMBNAILS2_DIR, $ts2trategy);
    //edump($path,FIELD_IMGLIB_THUMBNAILS2_DIR);
    if ($save_original) {
        $exp = explode('.', strtolower($file_avatar['name']));
        $imgPath = QNode::getImgPath($pNode);
        FileWorks::mkdir_recursive(FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/", 0777);
        copy($file_avatar['tmp_name'], FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
    }
    return $path;
}
function resizeAvatarThumb3($file_avatar, $pNode, $_tw = 51, $_th = 51, $_tw2 = 51, $_th2 = 51,$_tw3 = 51, $_th3 = 51, $_w = 100, $_h = 100, $tstrategy = 'crop',$ts2trategy = 'crop',$ts3trategy = 'crop', $strategy = 'crop', $save_original = false) {
    $path = _resizeAvatar($file_avatar, $pNode, $_w, $_h, FIELD_IMGLIB_DIR, $strategy);
    $path = _resizeAvatar($file_avatar, $pNode, $_tw, $_th, FIELD_IMGLIB_THUMBNAILS_DIR, $tstrategy);
    $path = _resizeAvatar($file_avatar, $pNode, $_tw2, $_th2, FIELD_IMGLIB_THUMBNAILS2_DIR, $ts2trategy);
    $path = _resizeAvatar($file_avatar, $pNode, $_tw3, $_th3, FIELD_IMGLIB_THUMBNAILS3_DIR, $ts3trategy);
    if ($save_original) {
        $exp = explode('.', strtolower($file_avatar['name']));
        $imgPath = QNode::getImgPath($pNode);
        FileWorks::mkdir_recursive(FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/", 0777);
        copy($file_avatar['tmp_name'], FIELD_IMGLIB_ORIGINAL_DIR . $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
    }
    return $path;
}
function _resizeAvatar($file_avatar, $pNode, $_w = 100, $_h = 100, $FIELD_IMGLIB_DIR = FIELD_IMGLIB_DIR, $strategy = 'crop') {

    list($w, $h) = getimagesize($file_avatar['tmp_name']);
    $imgPath = QNode::getImgPath($pNode);

    FileWorks::mkdir_recursive(FIELD_IMGLIB_DIR . $imgPath . "/", 0777);
    FileWorks::mkdir_recursive(FIELD_IMGLIB_THUMBNAILS_DIR . $imgPath . "/", 0777);
    if($FIELD_IMGLIB_DIR==FIELD_IMGLIB_THUMBNAILS2_DIR){
        FileWorks::mkdir_recursive(FIELD_IMGLIB_THUMBNAILS2_DIR . $imgPath . "/", 0777);
    }
    if($FIELD_IMGLIB_DIR==FIELD_IMGLIB_THUMBNAILS3_DIR){
        FileWorks::mkdir_recursive(FIELD_IMGLIB_THUMBNAILS2_DIR . $imgPath . "/", 0777);
        FileWorks::mkdir_recursive(FIELD_IMGLIB_THUMBNAILS3_DIR . $imgPath . "/", 0777);
    }

    $exp = explode('.', strtolower($file_avatar['name']));

    if (($w <= $_w && $h <= $_h) || ($strategy == 'width' && $w <= $_w) || ($strategy == 'height' && $h <= $_h) || ($strategy == 'larger_side' && $w >= $h && $w <= $_w) || ($strategy == 'larger_side' && $h >= $w && $h <= $_h)) {
        @copy($file_avatar['tmp_name'], $FIELD_IMGLIB_DIR . $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
        return preg_replace('/\/+/', '/', $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
    }

    $thumb = PhpThumbFactory::create($file_avatar['tmp_name']);

//    $img->setSourceFile($file_avatar['tmp_name']);
    //   $img->setTargetFile($FIELD_IMGLIB_DIR."/s/tmp/".$pNode->id.'/'.name2shortname($exp[0]).'.'.$exp[1]);
    //  $img->setJpegOutputQuality(90);

    if ($strategy == 'crop') {
        if ($w * $_h > $h * $_w) {
            $nh = min($_h, $h);
            $thumb->resize(0, $nh);
            $nw = $w * $nh / $h;
        } else {
            $nw = min($_w, $w);
            $thumb->resize($nw, 0);
            $nh = $h * $nw / $w;
        }
        $thumb->crop(0, 0, min($_w, $nw), min($_h, $nh));
        //$img->setSourceFile($FIELD_IMGLIB_DIR."/s/tmp/".$pNode->id.'/'.name2shortname($exp[0]).'.'.$exp[1]);
        //$img->setTargetFile($FIELD_IMGLIB_DIR."/s/tmp/".$pNode->id.'/'.name2shortname($exp[0]).'.'.$exp[1]);
        //$img->crop(0,0,min($_w,$nw),min($_h,$nh));
    } elseif ($strategy == 'width') {
        $nw = min($_w, $w);
        $thumb->resize($nw, 0);
        $nh = $h * $nw / $w;
    } elseif ($strategy == 'height') {
        $nh = min($_h, $h);
        $thumb->resize(0, $nh);
        $nw = $w * $nh / $h;
    } elseif ($strategy == 'larger_side') {
        if ($w > $h) {
            $nw = min($_w, $w);
            $thumb->resize($nw, 0);
            $nh = $h * $nw / $w;
        } else {
            $nh = min($_h, $h);
            $thumb->resize(0, $nh);
            $nw = $w * $nh / $h;
        }
    } else {
        if ($w < $h) {
            $nh = min($_h, $h);
            $thumb->resize(0, $nh);
            $nw = $w * $nh / $h;
        } else {
            $nw = min($_w, $w);
            $thumb->resize($nw, 0);
            $nh = $h * $nw / $w;
        }
    }

    FileWorks::mkdir_recursive($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $pNode->id . '/', 0777);


    $thumb->save($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $pNode->id . '/' . name2shortname($exp[0]) . '.' . $exp[1]);
    @copy($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $pNode->id . '/' . name2shortname($exp[0]) . '.' . $exp[1], $FIELD_IMGLIB_DIR . $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
    @unlink($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $pNode->id . '/' . name2shortname($exp[0]) . '.' . $exp[1]);
    //@rmdir($FIELD_IMGLIB_DIR."/s/tmp/".$pNode->id.'/');

    FileWorks::rmdir_recursive($_SERVER['DOCUMENT_ROOT'] . "/s/tmp/" . $pNode->id . '/', true);
    return preg_replace('/\/+/', '/', $imgPath . "/" . name2shortname($exp[0]) . '.' . $exp[1]);
}

///////////////////////////////////////////////////

function resizePhoto($file_photo, $target_photo = '', $_w = 140, $_h = 140, $strategy = 'crop') {

    $path_parts = pathinfo($file_photo);
    if ($target_photo == '')
        $target_photo = $path_parts['dirname'] . '/' . 'tmp_' . $path_parts['basename'];
    $target_parts = pathinfo($target_photo);
    FileWorks::mkdir_recursive($path_parts['dirname'], 0777);
    FileWorks::mkdir_recursive($target_parts['dirname'], 0777);

    list($w, $h) = getimagesize($file_photo);

    if (($w <= $_w && $h <= $_h) || ($strategy == 'width' && $w <= $_w) || ($strategy == 'height' && $h <= $_h) || ($strategy == 'larger_side' && $w >= $h && $w <= $_w) || ($strategy == 'larger_side' && $h >= $w && $h <= $_h)) {
        @copy($file_photo, $target_photo);
        return;
    }

    $img = new imageClass();
    $img->setSourceFile($file_photo);
    $img->setTargetFile($target_photo);
    $img->setJpegOutputQuality(90);

    if ($strategy == 'crop') {
        if ($w * $_h > $h * $_w) {
            $nh = min($_h, $h);
            $img->resizeToHeight($nh);
            $nw = $w * $nh / $h;
        } else {
            $nw = min($_w, $w);
            $img->resizeToWidth($nw);
            $nh = $h * $nw / $w;
        }
        $img->setSourceFile($target_photo);
        $img->setTargetFile($target_photo);
        $img->crop(0, 0, min($_w, $nw), min($_h, $nh));
    } elseif ($strategy == 'width') {
        $nw = min($_w, $w);
        $img->resizeToWidth($nw);
        $nh = $h * $nw / $w;
    } elseif ($strategy == 'height') {
        $nh = min($_h, $h);
        $img->resizeToHeight($nh);
        $nw = $w * $nh / $h;
    } elseif ($strategy == 'larger_side') {
        if ($w > $h) {
            $nw = min($_w, $w);
            $img->resizeToWidth($nw);
            $nh = $h * $nw / $w;
        } else {
            $nh = min($_h, $h);
            $img->resizeToHeight($nh);
            $nw = $w * $nh / $h;
        }
    } else {
        if ($w < $h) {
            $nh = min($_h, $h);
            $img->resizeToHeight($nh);
            $nw = $w * $nh / $h;
        } else {
            $nw = min($_w, $w);
            $img->resizeToWidth($nw);
            $nh = $h * $nw / $w;
        }
    }
}

///////////////////////////////////////////////////


function makeLink($node, $params = array(), $lang = "") {
    $param_str = "";
    foreach ($params as $k => $v) {
        $param_str.="&" . $k . "=" . $v;
    }
    $SITE_LOCALE = (mb_strlen($lang)) ? $lang : SITE_LOCALE;
    if (MOD_REWRITE && count($params))
        $param_str = "?" . mb_substr($param_str, 1);
    if (is_object($node)) {
        if (MOD_REWRITE)
            return (USE_LANG) ? ("/" . $SITE_LOCALE . $node->getHref() . $param_str) : ($node->getHref() . $param_str);
        else
            return (USE_LANG) ? ("/index.php?lang=" . $SITE_LOCALE . "&node_id=" . $node->id . $param_str) : ("/index.php?node_id=" . $node->id . $param_str);
    }else {
        if (MOD_REWRITE)
            return (USE_LANG) ? ("/" . $SITE_LOCALE . $node . $param_str) : $node . $param_str;
        else {
            $n = QNode::getInstance($node);
            if (!$n)
                return makeLink("/_404", $params, $lang);
            return (USE_LANG) ? ("/index.php?lang=" . $SITE_LOCALE . "&node_id=" . $n->id . $param_str) : ("/index.php?node_id=" . $n->id . $param_str);
        }
    }
}

/////////////////////////////////////////////////////////
function rucfirst($str) {
    $e = "UTF-8";
    $fc = mb_strtoupper(mb_substr($str, 0, 1, $e), $e);
    return $fc . mb_substr($str, 1, mb_strlen($str, $e), $e);
}

function getNewsDate($datetime, $use_year = true, $field = 'date') {
    if (is_object($datetime)) {
        $datetime = $datetime->fields[$field]->value->time;
    }
    if ($use_year) {
        if (SITE_LOCALE == "en")
            return date("jS F Y", $datetime);

        return date("j ", $datetime) . getMonthsGenitive(date("m", $datetime) - 1) . ((date(" Y", $datetime) == date('Y')) ? "" : date(" Y", $datetime));
    }else {
        if (SITE_LOCALE == "en")
            return date("jS F", $datetime);

        return date("j ", $datetime) . getMonthsGenitive(date("m", $datetime) - 1);
    }
}

function getEventsDate($datetime, $return_time = false, $field = 'date') {
    if (is_object($datetime)) {
        $datetime = $datetime->fields[$field]->value->time;
    }
    $time = $return_time ? date(', H:i', $datetime) : '';
    return date("d ", $datetime) . getMonthsGenitive(date("m", $datetime) - 1) . date(" Y", $datetime) . $time;
}

function encodeURL($uri) {
    $cb = "";
    for ($i = 0; $i < mb_strlen($uri); $i++) {
        $ch = mb_substr($uri, $i, 1);
        switch ($ch) {
            case '?' :
            case '&' :
            case '=' :
            case ' ' :
            case '\'':
            case '"' :
            case '%' :
            case '<' :
            case '>' :
            case '+' :
            case '^' :
            case '~' :
            case '|' :
                $cb .= '%';
                $cb.= encodeHex(ord($ch) >> 4);
                $cb .= encodeHex(ord($ch));
                break;
            default :
                $cb .= $ch;
                break;
        }
    }
    return $cb;
}

function getPeriod($start, $end) {

    return getNewsDate($start) . " - " . getNewsDate($end);

//October 6th 2007 - December 30th 2007
}

function getLocale($node, $field = 'name') {

    if ($field == "name" && !isset($node->tfields[$field . "_" . SITE_LOCALE])) {
        return $node->name;
    }
    if (isset($node->tfields[$field . "_" . SITE_LOCALE])) {

        /*if ($field == 'text' && isset($node->tfields[$field . "_" . SITE_LOCALE])) {

            switch (SITE_LOCALE) {
                case 'en':
                    if (strlen($node->tfields[$field . "_ua"]) && !strlen($node->tfields[$field . "_en"])) {
                        return '<p>This page is available only in <a href="/ua' . $node->getHref() . '">Ukrainian</a> version</p>';
                    }
                    break;
                case 'ua':
                    if (strlen($node->tfields[$field . "_en"]) && !strlen($node->tfields[$field . "_ua"])) {
                        return '<p>Ця сторінка доступна лише в <a href="/en' . $node->getHref() . '">англійській</a> версіії</p>';
                    }
                    break;
            }
        }*/
        if (SITE_LOCALE == 'ua' && !strlen($node->tfields[$field . "_" . SITE_LOCALE])) {
            if ($field == "name") {
                return $node->name;
            } else {
                return $node->tfields[$field . "_en"];
            }
        }
        return $node->tfields[$field . "_" . SITE_LOCALE];
    }
    if (isset($node->tfields[$field])) {
        return $node->tfields[$field];
    }
    return '';
}

function getAlt($node, $field = 'name') {


    return htmlspecialchars(getLocale($node, $field));
}

function utf8_substr($str, $start, $end) {
    preg_match_all("/./su", $str, $ar);
    if (!count($ar[0]))
        return substr($str, $start, $end);
    if (func_num_args() >= 3) {
        $end = func_get_arg(2);
        return join("", array_slice($ar[0], $start, $end));
    } else {
        return join("", array_slice($ar[0], $start));
    }
}

function str_transliterate_ru2en($str) {
    $google_transliterate = array(
        'ый' => 'iy',
        'ье' => 'je',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'ґ' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'є' => 'ye',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'і' => 'i',
        'ї' => 'yi',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ў' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shch',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya'
    );
    return str_replace(array_keys($google_transliterate), $google_transliterate, $str);
}

function remove_illegal_characters($shortname) {
    $replacement = array(
        '/ /' => '-',
        '/[\.,;:]/' => '_',
        '/[^0-9a-z_-]/' => '',
        '/_\-/' => '-',
        '/\-_/' => '-',
        '/_\-/' => '-',
        '/\-_/' => '-',
        '/[_]+/' => '_',
        '/[-]+/' => '-',
        '/[_\-]+$/' => '',
        '/^[_\-]+/' => '',
    );
    return preg_replace(array_keys($replacement), $replacement, $shortname);
}

function name2shortname($name) {
    $shortname = str_transliterate_ru2en(trim(mb_strtolower($name)));
    $shortname = remove_illegal_characters($shortname);
    return $shortname;
}

function tget_class($class) {
    return strtolower(get_class($class));
}

function getmicrotime() {

    list($usec, $sec) = explode(" ", microtime());

    return ((float) $usec + (float) $sec);
}

function _log($str) {

    global $___logFile;

//$___logFile = fopen($_SERVER["DOCUMENT_ROOT"]."/sql.log", "a+");
//fwrite($___logFile, date("Y-m-d h:i:s") . "\t" . $str . "\r\n");
//fclose($___logFile);
}

function _log2($str) {

    global $___logFile;

//$___logFile = fopen($_SERVER["DOCUMENT_ROOT"]."/sql.log", "a+");
//fwrite($___logFile, date("Y-m-d h:i:s") . "\t" . $str . "\r\n");
//fclose($___logFile);
}

$____st = getmicrotime();

$____pt = getmicrotime();



_log("\r\n\r\nScript start - " . $_SERVER['REQUEST_URI']);

function _logTimeFromStart($key = "") {

    global $____st, $____pt;

    $ct = getmicrotime();

    _log("Time from : prev. pt. - " . sprintf("%01.5f", ($ct - $____pt)) .
            ",  script start - " . sprintf("%01.5f", ($ct - $____st)) .
            (mb_strlen($key) == 0 ? "" : ", " . $key));

    $____pt = getmicrotime();
}

function _dump($v) {
    echo "<pre>";
    var_dump($v);
    echo "</pre>";
}

function import($package_name) {
    _logTimeFromStart("import start");
    $packs = explode(".", $package_name);
    $cn = array_pop($packs);
    $dir = implode("/", $packs);
    if ($cn == "*") {
        $include_path = ini_get("include_path");
        $incs = explode(SYSTEM_SEPARATOR, $include_path);
        $find = false;
        foreach ($incs as $inc) {
            if (@is_dir("$inc/$dir")) {
                $dir = "$inc/$dir";
                $find = true;
                break;
            }
        }
        if (!$find) {
            die("FATAL ERROR in import(): can't find path '$dir' in any include dirs('$include_path')");
        }
        $files = array();
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            if (substr($entry, -3) == "php") {
                $files[] = $dir . "/" . $entry;
            }
        }
        $d->close();
    } else {
        $files = array($dir . "/" . $cn . ".php");
    }
    foreach ($files as $file) {
        $d = trim("$file");
        if (!@is_dir($d)) {
            _logTimeFromStart("start require $d");
            include_once($d);
            _logTimeFromStart("end require $d");
        }
    }
    _logTimeFromStart("import end");
}

function _stripslashes($str) {
    $str = str_replace("\\\\", "\\", $str);
    $str = str_replace("\\\"", "\"", $str);
    $str = str_replace("\\'", "'", $str);
    return $str;
}

function quoteString($str, $maxlen) {
    $str = str_replace("\n", " ", $str);
    $str = str_replace("\r", " ", $str);
    $str = str_replace("\t", " ", $str);
    $_words = explode(" ", $str);
    $res = "";
    $c = 0;
    for ($i = 0; $i < sizeof($_words); $i++) {
        $w = $_words[$i];
        if (mb_strlen($w) == 0) {
            continue;
        }
        if (mb_strlen($res) + mb_strlen($w) > $maxlen) {
            break;
        }
        if ($c != 0) {
            $res .= " ";
        }
        $res .= $w;
        $c++;
    }
    return $res;
}

/**

 *  convert special chars to XML style

 */
function prepareStringForXML($str) {
    $str = (string) $str;
    $out = "";
    for ($i = 0; $i < mb_strlen($str); $i++) {
        $c = mb_substr($str, $i, 1);
        switch ($c) {
            case '<':
                $out .= "&lt;";
                break;
            case '>':
                $out .= "&gt;";
                break;
            case '&':
                $out .= "&amp;";
                break;
            case '"':
                $out .= "&#034;";
                break;
            case '\'':
                $out .= "&#039;";
                break;
            default:
                $out .= $c;
        }
    }
    return $out;
}

/**

 *  convert special chars to HTML style

 */
function prepareStringForHTML($str) {

    $out = "";

    for ($i = 0; $i < mb_strlen($str); $i++) {

        $c = mb_substr($str, $i, 1);

        switch ($c) {

            case '<':

                $out .= "&lt;";

                break;

            case '>':

                $out .= "&gt;";

                break;

            case '&':

                $out .= "&amp;";

                break;

            case '"':

                $out .= "&quot;";

                break;

            case '\'':

                $out .= "&#039;";

                break;

            default:

                $out .= $c;
        }
    }

    return $out;
}

function strReplaceButNotInQuotes($patt, $replacement, $s, $QUOTE_CHARACTER = "'") {

    $charbuffer = "";

    for ($j = 0; $j < mb_strlen($s); $j++) {

        $c = mb_substr($s, $j, 1);

        if ($c == $QUOTE_CHARACTER) {

            $charbuffer .= $QUOTE_CHARACTER;

            for ($j++; $j < mb_strlen($s) && mb_substr($s, $j, 1) != "'"; $j++)
                $charbuffer .= mb_substr($s, $j, 1);

            $charbuffer .= $QUOTE_CHARACTER;
        } else {

            $k = $j;

            for (; $k < mb_strlen($s) && $k - $j < mb_strlen($patt) && mb_substr($s, $k, 1) == mb_substr($patt, $k - $j, 1); $k++) {
                
            }

            if (($k - $j) == mb_strlen($patt)) {

                $j = $k - 1;

                $charbuffer .= $replacement;
            } else {

                $charbuffer .= $c;
            }
        }
    }
    return $charbuffer;
}

function byteToHex($b) {

    $cb = "";

    $cb .= encodeHex(($b & 0xf0) >> 4);

    $cb .= encodeHex($b & 0xf);

    return $cb;
}

function encodeHex($ch) {

    $ch &= 0xf;

    if ($ch < 10)
        return strtoupper(chr($ch + 48));
    else
        return strtoupper(chr(($ch + 97) - 10));
}

function _array_remove_filter($var) {

    return ($var != null);
}

function array_remove($array, $key) {
    $array[$key] = null;
    $array = array_filter($array, "_array_remove_filter");
    return $array;
}

function tinclude($t, $params = array()) {

    global $request, $session;
    $dynamic_template_path = $_SERVER['DOCUMENT_ROOT'] . "/dynamic_templates/" . $t . ".php";

    if (!file_exists($dynamic_template_path)) {
        die('Dynamic template file does not exists: ' . $t);
    }
    extract($request->attributes);
    extract($params);
    include($dynamic_template_path);
}

function finclude($t, $form = '', $params = array()) {
    if ($form == '')
        $form = $t;
    global $request;
    tinclude('forms/frm_' . $t, $params);
    $f = $request->getAttribute($form . 'Form');
    if ($f instanceof QForm)
        return $f;
}

define('META_MAX_WORDS', 20);

function _getMetaKeywords($str) {

    $str = strip_tags($str);

    $str = str_replace("\n", " ", $str);

    $str = str_replace("\r", " ", $str);



    $str = str_replace(".", " ", $str);

    $str = str_replace(",", " ", $str);

    $str = str_replace('"', " ", $str);



    $_keywords = explode(" ", $str);

    $keywords = array();

    for ($i = 0; $i < sizeof($_keywords) && $i < META_MAX_WORDS; $i++) {

        $k = $_keywords[$i];

        if (strlen($k) < 4)
            continue;

        $keywords [] = $k;
    }

    return $keywords;
}

function getFileExt($filename) {
    return array_pop(explode(".", $filename));
}

function prepareMetaDescription($str) {

    $keywords = _getMetaKeywords($str);

    return implode(" ", $keywords);
}

function prepareMetaKeywords($str) {

    $keywords = _getMetaKeywords($str);

    return implode(",", $keywords);
}

_logTimeFromStart("point1");

/* * *********************************************************************************************** */



DEFINE("_CACHE_DIR_", $_SERVER["DOCUMENT_ROOT"] . "/s/cache");

function _ftok($path, $proj) {

    return mb_strlen($path) + 0xff3;
}

function _write_cache($varname) {

//*
//if ($GLOBALS[$varname]) {

    $cacheFile = fopen(_CACHE_DIR_ . "/" . $varname, "w+");
    if ($cacheFile === false) {
        error_log("_write_cache: cannot write " . _CACHE_DIR_ . "/" . $varname);
        return;
    }

    fwrite($cacheFile, serialize($GLOBALS[$varname]));

    fclose($cacheFile);

//}
//*/

    /*



      if ($GLOBALS[$varname]) {

      $cache_key = _ftok ($varname, "dbm");



      $serializedValue = serialize($GLOBALS[$varname]);



      $shm_id = @shmop_open($cache_key, "a", 0, 0);

      if ($shm_id) {

      shmop_delete($shm_id);

      shmop_close($shm_id);

      }

      $shm_id = shmop_open($cache_key, "c", 0644, strlen($serializedValue));

      if ($shm_id) {

      shmop_write($shm_id, $serializedValue, 0);

      shmop_close($shm_id);

      }

      }

      // */
}

function _read_cache($varname) {

//*

    $cacheFileStr = @file_get_contents(_CACHE_DIR_ . "/" . $varname);



    if ($cacheFileStr) {

        $GLOBALS[$varname] = unserialize($cacheFileStr);
    } else {

        $GLOBALS[$varname] = null;
    }

//*/

    /*

      //$cache_key = ftok ($_SERVER["SERVER_NAME"] . "/" . "NID");

      $cache_key = _ftok ($varname, "dbm");

      $shm_id = @shmop_open($cache_key, "a", 0, 0);

      if ($shm_id) {

      $cacheFileStr = shmop_read($shm_id, 0, shmop_size($shm_id));

      shmop_close($shm_id);

      }

      //_dump($cacheFileStr);

      // */
}

require_once ("dbm/DBUtils.php");

function ___shutdown_function() {

//global $___logFile;
//	return false;
//fclose($___logFile);

    DBUtils::rollback(true);



    _write_cache("_NODE_CLASS_CACHE_BY_ID");

    _write_cache("_NODE_CLASS_CACHE_BY_SHORTNAME");

    _write_cache("_NODES_CACHE_BY_ID");

    _write_cache("_NODES_CACHE_BY_PATH");

    _write_cache("USERS_CACHE");

    _write_cache("USER_GROUPS_CACHE");
}

register_shutdown_function("___shutdown_function");

/* * *********************************************************************************************** */

function list_dir_contents($path) {

    $files = array();

    if ($dir = @opendir($path)) {

        while (($file = readdir($dir)) !== false) {

            if ($file == '.' || $file == '..') {

                continue;
            }

            $file = $path . '/' . $file;

            $files[] = $file;
        }

        closedir($dir);
    }

    return $files;
}

function list_dir_contents_only_dirs($path) {
    $files = array();
    if ($dir = @opendir($path)) {
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file = $path . '/' . $file;
            if (is_dir($file)) {
                $files[] = $file;
            }
        }
        closedir($dir);
    }
    return $files;
}

function list_dir_contents_only_files($path) {
    $files = array();
    if ($dir = @opendir($path)) {
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file = $path . '/' . $file;
            if (!is_dir($file)) {
                $files[] = $file;
            }
        }
        closedir($dir);
    }
    return $files;
}

//simple object

class PHPObject {

    function __construct() {
        
    }

}

class Date {

    var $time = 0;

    function __construct($o = null) {
        if (is_string($o)) {
            if (preg_match("~([0-9]{1,2})\\.([0-9]{1,2})\\.([0-9]{4})~", $o)) {
                list($d, $m, $y) = explode(".", $o);
                $this->time = mktime(0, 0, 0, (int)$m, (int)$d, (int)$y); // (int): PHP 5 read "2026 12:00" as 2026, PHP 8 throws
            } else {
                $this->time = strtotime($o);
            }
        } elseif (is_integer($o)) {
            $this->time = $o;
        } else {
            $this->time = time();
        }
    }

    function format($format = "Y-m-d") {
        return date($format, $this->time);
    }

    function getDay1() {
        return $this->format("j");
    }

    function getDay() {
        return $this->format("d");
    }

    function getMonth() {
        return $this->format("m");
    }

    function getYear() {
        return $this->format("Y");
    }

    function getHour() {
        return $this->format("H");
    }

    function getMinute() {
        return $this->format("i");
    }

    function getSecond() {
        return $this->format("s");
    }

}

/* * *********************************************************************************************** */

function useEmailTemplate($template, $patterns, $values) {
    $text = $template;
    for ($i = 0; $i < sizeof($patterns); $i++) {
        $text = str_replace($patterns[$i], $values[$i], $text);
    }
    return $text;
}

function formatCurrency($number) {
    return sprintf("%01.2f", $number);
}

/* * *********************************************************************************************** */

_logTimeFromStart("point2");
require_once ("dbm/User.php");
_logTimeFromStart("point2.1");
require_once ("dbm/UserGroup.php");
_logTimeFromStart("point2.2");
require_once ("dbm/NodeClass.php");
_logTimeFromStart("point2.3");
require_once ("base/HTTP.php");
_logTimeFromStart("point2.5");
require_once ("base/Validator.php");
_logTimeFromStart("point2.6");
require_once ("base/HTMLWriter.php");
_logTimeFromStart("point2.7");
require_once ("sql/mysql/Connection.php");
_logTimeFromStart("point2.8");
_logTimeFromStart("point2.9");
require_once($DOC_ROOT . "/_settings.php");
_logTimeFromStart("point2.10");
require_once ("dbm/messages_" . LOCALE . ".properties");
_logTimeFromStart("point2.11");
_logTimeFromStart("point3");
require_once ("PHPMailer/Exception.php");
require_once ("PHPMailer/PHPMailer.php");
require_once ("PHPMailer/SMTP.php");
require_once ("CPaginator.php");
require_once ("my_func.php");
require_once ("check_node.php");
require_once ("CClearText.php");
require_once ("CProtect.php");
require_once ("QNode.php");
require_once ("QMail.php");
require_once ("QDelivery.php");
require_once ("QURL.php");
require_once ("qform/QLang.php");
require_once ("qform/QForm.php");
require_once ("qform/QFormElement.php");
require_once ("qform/QFormValidationRule.php");
require_once ("qform/types/QFormText.php");
require_once ("qform/types/QFormPassword.php");
require_once ("qform/types/QFormTextArea.php");
require_once ("qform/types/QFormHidden.php");
require_once ("qform/types/QFormRadioBox.php");
require_once ("qform/types/QFormCheckBox.php");
require_once ("qform/types/QFormSelectBox.php");
require_once ("qform/types/QFormFile.php");
$ccText = &CClearText::getInstance();


$connection = new Connection($connect_params["url"], $connect_params["params"]);

require_once ("sql/mysql/mysql.class.php");


$MDB->Query("SELECT shortname, value FROM dbm_settings");
$settings = $MDB->getRecords();
if ($settings)
    foreach ($settings as $k => $v) {
        define($v['shortname'], $v['value']);
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

DBUtils::execUpdate("set character_set_client = utf8");
DBUtils::execUpdate("SET CHARACTER SET '" . DB_CHARSET . "'");
DBUtils::execUpdate("SET NAMES '" . DB_CHARSET . "'");

$_USE_NODES_CACHE_ = true;

NodeClass::buildNodeClassCache();
UserGroup::buildUserGroupCache();
User::buildUsersCache();
Node::readCache();

_logTimeFromStart("point4");

function mailto($email, $safe_mode = '', $title = '', $attributes = '') {
    if ($safe_mode == '') {
        $safe_mode = USE_SAFE_EMAIL;
    }
    if ($safe_mode) {
        return safe_mailto($email, $title = '', $attributes = '');
    }
    $title = (string) $title;
    if ($title == "") {
        $title = $email;
    }
    $attributes = _parse_attributes($attributes);
    return '<a href="mailto:' . $email . '"' . $attributes . '>' . $title . '</a>';
}

function safe_mailto($email, $title = '', $attributes = '') {
    $title = (string) $title;
    if ($title == "") {
        $title = $email;
    }
    for ($i = 0; $i < 16; $i++) {
        $x[] = substr('<a href="mailto:', $i, 1);
    }
    for ($i = 0; $i < mb_strlen($email); $i++) {
        $x[] = "|" . ord(mb_substr($email, $i, 1));
    }
    $x[] = '"';
    if ($attributes != '') {
        if (is_array($attributes)) {
            foreach ($attributes as $key => $val) {
                $x[] = ' ' . $key . '="';
                for ($i = 0; $i < mb_strlen($val); $i++) {
                    $x[] = "|" . ord(mb_substr($val, $i, 1));
                }
                $x[] = '"';
            }
        } else {
            for ($i = 0; $i < mb_strlen($attributes); $i++) {
                $x[] = mb_substr($attributes, $i, 1);
            }
        }
    }
    $x[] = '>';
    $temp = array();
    for ($i = 0; $i < mb_strlen($title); $i++) {
        $ordinal = ord($title[$i]);

        if ($ordinal < 128) {
            $x[] = "|" . $ordinal;
        } else {
            if (count($temp) == 0) {
                $count = ($ordinal < 224) ? 2 : 3;
            }
            $temp[] = $ordinal;
            if (count($temp) == $count) {
                $number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);
                $x[] = "|" . $number;
                $count = 1;
                $temp = array();
            }
        }
    }

    $x[] = '<';
    $x[] = '/';
    $x[] = 'a';
    $x[] = '>';

    $x = array_reverse($x);
    ob_start();
    ?><script type="text/javascript">
            //<![CDATA[
            var l = new Array();
    <?php
    $i = 0;
    foreach ($x as $val) {
        ?>l[<?php echo $i++; ?>] = '<?php echo $val; ?>';<?php } ?>
            for (var i = l.length - 1; i >= 0; i = i - 1) {
                if (l[i].substring(0, 1) == '|')
                    document.write("&#" + unescape(l[i].substring(1)) + ";");
                else
                    document.write(unescape(l[i]));
            }
            //]]>
    </script><?php
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
}

function _parse_attributes($attributes, $javascript = FALSE) {
    if (is_string($attributes)) {
        return ($attributes != '') ? ' ' . $attributes : '';
    }

    $att = '';
    foreach ($attributes as $key => $val) {
        if ($javascript == TRUE) {
            $att .= $key . '=' . $val . ',';
        } else {
            $att .= ' ' . $key . '="' . $val . '"';
        }
    }

    if ($javascript == TRUE AND $att != '') {
        $att = substr($att, 0, -1);
    }

    return $att;
}

?>