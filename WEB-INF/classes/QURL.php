<?php

class QURL {

    public static function getClientIP() {
        if ($_SERVER['HTTP_CLIENT_IP']) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ($_SERVER['HTTP_X_FORWARDED']) {
            $ip = $_SERVER['HTTP_X_FORWARDED'];
        } elseif ($_SERVER['HTTP_FORWARDED_FOR']) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif ($_SERVER['HTTP_FORWARDED']) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function removeWWW($url) {
        if (strpos($url, "www.") === 0) {
            return substr($url, strlen("www."));
        }
        if (strpos($url, "www2.") === 0) {
            return substr($url, strlen("www2."));
        }
        return $url;
    }

    public static function encodeURL($uri) {
        $cb = "";
        for ($i = 0; $i < strlen($uri); $i++) {
            $ch = $uri[$i];
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

    public static function externalURL($url) {
        if (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) {
            return $url;
        } else {
            return "https://" . $url; // no scheme stored: assume https
        }
    }

    public static function externalURLNoHTTP($url) {
        if (strpos($url, "http://") === 0) {
            return substr($url, strlen("http://"));
        } else if (strpos($url, "https://") === 0) {
            return substr($url, strlen("https://"));
        } else {
            return $url;
        }
    }

    public static function redirect($url, $skip_use_lang = false) {
        if (!$skip_use_lang) {
            header('Location: ' . QURL::link($url));
        } else {
            header('Location: ' . $url);
        }
        exit();
    }

    public static function link($node, $params=array(), $lang="") {
        $param_str = "";
        foreach ($params as $k => $v) {
            $param_str.="&" . $k . "=" . $v;
        }
        $SITE_LOCALE = (strlen($lang)) ? $lang : SITE_LOCALE;
        if (MOD_REWRITE && count($params))
            $param_str = "?" . substr($param_str, 1);
        if (is_object($node)) {
            if (MOD_REWRITE) {
                if (USE_LANG) {
                    return $node->getHref() . $param_str;
                } elseif (USE_VIRTUAL_LANG) {
                    return '/' . $SITE_LOCALE . $node->getHref() . $param_str;
                } else {
                    return $node->getHref() . $param_str;
                }
            } else {
                if (USE_LANG) {
                    return "/index.php?lang=" . $SITE_LOCALE . "&node_id=" . $node->id . $param_str;
                } elseif (USE_VIRTUAL_LANG) {
                    return "/index.php?lang=" . $SITE_LOCALE . "&node_id=" . $node->id . $param_str;
                } else {
                    return "/index.php?node_id=" . $node->id . $param_str;
                }
            }
        } else {
            if (MOD_REWRITE) {
                if (USE_LANG) {
                    return "/" . $SITE_LOCALE . $node . $param_str;
                } elseif (USE_VIRTUAL_LANG) {
                    return "/" . $SITE_LOCALE . $node . $param_str;
                } else {
                    return $node . $param_str;
                }
            } else {
                $n = QNode::getInstance($node);
                if (!$n)
                    return QURL::link("/_404", $params, $lang);

                if (USE_LANG) {
                    return "/index.php?lang=" . $SITE_LOCALE . "&node_id=" . $n->id . $param_str;
                } elseif (USE_VIRTUAL_LANG) {
                    return "/index.php?lang=" . $SITE_LOCALE . "&node_id=" . $n->id . $param_str;
                } else {
                    return "/index.php?node_id=" . $n->id . $param_str;
                }
            }
        }
    }

}

//////////////////////////////////////////////////////////////////////////////////////////////
?>