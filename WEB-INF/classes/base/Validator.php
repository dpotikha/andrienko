<?php

/**
 * Проверка правильности различных типов данных
 * @package base
 */
class Validator {
    /**
     * Проверить на корр. мыло
     * @param email string
     * @access public
     * @return bool
     */
    function checkEmail ( $email ) {
        if (!preg_match("/^(\".*\"|[A-Za-z0-9_\x2D]+(\.[A-Za-z0-9_\x2D]+)*)@(\[\d{1,3}(\.\d{1,3}){3}]|[A-Za-z0-9_\x2D]+(\.[A-Za-z0-9_\x2D]+)+)$/",
                $email, $matches)) {
            return false;
        }

        return true;
    }

    /**
     * Проверить на корр. login
     *
     * первая буква - только буква,
     * остальные(не меньше 2, не больше 15) - цифры, буквы, _ (буквы лат.)
     * @param login string
     * @access public
     * @return bool
     */
    function isCorrectLogin ( $login ) {
        if(ereg("^([A-Za-z]{1})([A-Za-z0-9_]{2,15})$", $login, $matches))
            return true;
        else 
            return false;
    }

    /**
     * Проверить на корр. password
     *
     * не меньше 3, не более 16 символов из след.:
     * a-zA-Z0-9,_
     * @param password string
     * @access public
     * @return bool
     */
    function isCorrectPassword ( $password ) {
        if(ereg("^[A-Za-z0-9_,]{3,16}$", $password, $matches))
            return true;
        else 
            return false;
    }

    /**
     * Проверить на корр. url
     * @param url string
     * @access public
     * @return void
     */
    function isCorrectURL ( $url ) {
        if(!ereg( "^http|https(:\/\/)([_0-9a-zA-Z_-]+\\.){1,}([a-z]{2,3})$", $url ))
            return false;
        else
            return true;
    }

    /**
     * Проверить на корр. url
     * @param url string
     * @access public
     * @return void
     */
    function isCorrectDate ( $date ) {
        $ok = false;
        if ( ereg ( "([0-9]{4})-([0-9]{2})-([0-9]{2})", $date, $regs) ) {
            if ( checkdate( $regs[2], $regs[3], $regs[1] ) ) {
                $ok = true;
            }
        }

        return  $ok;
    }

    function validateShortname($shortName) {
    	if (strlen($shortName) == 0)
    		return true;
        if(ereg("^([A-Za-z0-9_\\-]{1,30})$", $shortName, $matches))
            return true;
        else 
            return false;
    }

}

?>