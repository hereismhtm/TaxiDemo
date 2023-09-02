<?php

final class Auth
{
    const OPOR = 0;
    const OPAND = 1;
    const OPSAME = 2;

    public static $fields = array();

    /**
     * Login process.
     * @param string $logname
     * @param string $logpass
     * @param array $fields
     * @param string,number $permission
     * @return boolean
     */
    public static function login($logname, $logpass, $fields=null, $permission=null)
    {
        if (!isset($_SESSION['_auth_failed'])) $_SESSION['_auth_failed'] = 0;
        if ($_SESSION['_auth_failed'] >= 10) httpsta(429);

        if (!isset($permission)) $permission = $GLOBALS['_auth_config']['perm_field'];

        // if TRUE, it is a field name from: $_auth_config['login_table'] need
        // to be fetched as user permission.
        if (is_string($permission)) {
            $fields[] = $permission;
        }

        $where = array();
        if (is_array($GLOBALS['_auth_config']['id_field'])) { // such ['username', 'email']

            $OR = array();
            foreach ($GLOBALS['_auth_config']['id_field'] as $value) {
                $OR[$value] = $logname;
            }
            $where['OR'] = $OR;
        } else {

            $where[$GLOBALS['_auth_config']['id_field']] = $logname;
        }

        $fields[] = $GLOBALS['_auth_config']['pass_field'];
        $record = db::link()->select($GLOBALS['_auth_config']['login_table'], $fields, $where);
        if (!empty($record)) {
            if (count($record) != 1) {
                httpsta(409);
            }

            if ( self::chkSaltedHash($record[0][$GLOBALS['_auth_config']['pass_field']], $logpass) ) {

                $token = hash('SHA256',
                    $GLOBALS['_auth_config']['network_token'].
                    $_SERVER['HTTP_USER_AGENT'].
                    $_COOKIE['PHPSESSID']
                );
                $_SESSION['_auth_token'] = $token;
                $_SESSION['_auth_perm']  = is_string($permission)? $record[0][$permission] : $permission;
                $_SESSION['_auth_idname'] = $logname;

                foreach ($record[0] as $key => $value) {
                    if (
                        $key == $GLOBALS['_auth_config']['pass_field']
                        ||
                        $key == $permission
                    ) { continue; }

                    self::$fields[$key] = $value;
                }

                unset($_SESSION['_auth_failed']);
                return true;
            }
        }

        $_SESSION['_auth_failed'] += 1;
        return false;
    }

    /**
     * Authentication Check Point.
     * Checking if a user is already logged in and have the right
     * permission for the page is trying to access.
     * If $auto parameter set to [true], this will redirect
     * the request to login page to handel it if the failure
     * at token check stage.
     * Error 401 occur if the failure at page permission check stage.
     * @param number $page_permission
     * @param number $check_operator
     * @param boolean $auto
     * @return boolean
     */
    public static function cpoint($page_permission, $check_operator=self::OPOR, $auto=false)
    {
        $token = hash('SHA256',
            $GLOBALS['_auth_config']['network_token'].
            $_SERVER['HTTP_USER_AGENT'].
            (isset($_COOKIE['PHPSESSID'])? $_COOKIE['PHPSESSID'] : null)
        );
        if (!isset($_SESSION['_auth_token']) || $token != $_SESSION['_auth_token']) {
            if ($auto === true) {
                $login_page = isset($GLOBALS['_auth_config']['login_page'])? $GLOBALS['_auth_config']['login_page'] : DEF_UNIT;
                header('Location:'. cd($login_page));
                die();
            } else {
                return false;
            }
        }

        $perm_array = array();
        $n = $page_permission;
        while ($n != 0) {
            $perm_array[] = ($n%2 == 0)? false : true;
            $n>>=1;
        }

        $n = $_SESSION['_auth_perm'];
        $flag = false;
        foreach ($perm_array as $p) {

            $v = ($n == 0)? false : ( ($n%2 == 0)? false : true );

            switch ($check_operator) {

                case self::OPOR:
                if ($p==true && $v==true) $flag = true;
                break;

                case self::OPAND:
                if ($p==true && $v==false) $flag = true;
                break;

                case self::OPSAME:
                if ($p!=$v) $flag = true;
                break;
            }

            if ($flag) break;

            if ($n != 0) $n>>=1;
        }

        switch ($check_operator) {

            case self::OPOR:
            $ispass = $flag;
            break;

            case self::OPAND:
            case self::OPSAME:
            $ispass = !$flag;
            break;
        }

        if (!$ispass && $auto) {
            httpsta(401);
        } else {
            return $ispass;
        }
    }

    public static function logout($auto=false)
    {
        session_destroy();
        if ($auto === true) 		dirto();
        elseif ($auto !== false) 	dirto($auto);
    }

    public static function genSaltedHash($text)
    {
        $salt = hash('MD5', $text.random_int(1000, 1000000));
        return '#'. $salt .'-'. hash('SHA256', '#'.$salt.$text).'#';
    }

    public static function chkSaltedHash($sh, $text)
    {
        if ($sh == '#') return false;		// '#' = the default database value for pass_field
        $sh = explode('-', $sh);
        return hash('SHA256', $sh[0].$text).'#' == $sh[1];
    }
}
