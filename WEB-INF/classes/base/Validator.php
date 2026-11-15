<?php

/**
 * �������� ������������ ��������� ����� ������
 * @package base
 */
class Validator {
    /**
     * ��������� �� ����. ����
     * @param email string
     * @access public
     * @return bool
     */
    static function checkEmail ( $email ) {
        if (!preg_match("/^(\".*\"|[A-Za-z0-9_\x2D]+(\.[A-Za-z0-9_\x2D]+)*)@(\[\d{1,3}(\.\d{1,3}){3}]|[A-Za-z0-9_\x2D]+(\.[A-Za-z0-9_\x2D]+)+)$/",
                $email, $matches)) {
            return false;
        }

        return true;
    }

    /**
     * ��������� �� ����. login
     *
     * ������ ����� - ������ �����,
     * ���������(�� ������ 2, �� ������ 15) - �����, �����, _ (����� ���.)
     * @param login string
     * @access public
     * @return bool
     */
    function isCorrectLogin ( $login ) {
        if(preg_match("~^([A-Za-z]{1})([A-Za-z0-9_]{2,15})$~D", $login, $matches))
            return true;
        else 
            return false;
    }

    /**
     * ��������� �� ����. password
     *
     * �� ������ 3, �� ����� 16 �������� �� ����.:
     * a-zA-Z0-9,_
     * @param password string
     * @access public
     * @return bool
     */
    function isCorrectPassword ( $password ) {
        if(preg_match("~^[A-Za-z0-9_,]{3,16}$~D", $password, $matches))
            return true;
        else 
            return false;
    }

    /**
     * ��������� �� ����. url
     * @param url string
     * @access public
     * @return void
     */
    function isCorrectURL ( $url ) {
        if(!preg_match( "~^http|https(:\/\/)([_0-9a-zA-Z_-]+\\.){1,}([a-z]{2,3})$~D", $url ))
            return false;
        else
            return true;
    }

    /**
     * ��������� �� ����. url
     * @param url string
     * @access public
     * @return void
     */
    function isCorrectDate ( $date ) {
        $ok = false;
        if ( preg_match ( "~([0-9]{4})-([0-9]{2})-([0-9]{2})~", $date, $regs) ) {
            if ( checkdate( $regs[2], $regs[3], $regs[1] ) ) {
                $ok = true;
            }
        }

        return  $ok;
    }

    static function validateShortname($shortName) {
    	if (mb_strlen($shortName) == 0)
    		return true;
        if(preg_match("~^([A-Za-z0-9_\\-]{1,200})$~D", $shortName, $matches))
            return true;
        else 
            return false;
    }

}

?>