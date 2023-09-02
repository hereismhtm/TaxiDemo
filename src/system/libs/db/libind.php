<?php

require load::homeOfLib('db'). 'Medoo.php';
use Medoo\Medoo;

final class DB
{
    private static $link 	= null;
    private static $links 	= array();
    private static $results = array();
    private static $status 	= array();

    public static function name($db_name, $details=null)
    {
        if (isset(self::$links[$db_name])) {
            self::$link = self::$links[$db_name];
            return;
        }

        if (!isset($details)) {
            $details = $GLOBALS['_db_config'];
        } else {
            $details['database_type'] 	= $details[0];
            $details['server'] 			= $details[1];
            $details['username'] 		= $details[2];
            $details['password'] 		= $details[3];
            $details['charset'] 		= $details[4];
        }
        $details['database_name'] 		= $db_name;
        self::$links[$db_name] = new Medoo($details);
        self::$link = self::$links[$db_name];
    }

    public static function log($via=null)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        self::$link->insert('__logs', [
            'via' => isset($via) ? $via : data::ifsession('_auth_idname', 'N/A'),
            'unit' 			=> data::$unit,
            'action' 		=> data::$action,
            'args' 			=> json_encode(data::$args),
            'post' 			=> json_encode($_POST),
            'echo' 			=> ob_get_contents(),
            'stamp' 		=> date('Y-m-d H:i:s'),
            'cookie' 		=> $_COOKIE['PHPSESSID'],
            'agent' 		=> $_SERVER['HTTP_USER_AGENT'],
            'ip' 			=> $_SERVER['REMOTE_ADDR']
        ]);
    }

    public static function link()
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        return self::$link;
    }

    public static function quote($data)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        return self::$link->quote($data);
    }

    public static function res($index=null)
    {
        return isset($index) ? self::$results[$index] : end(self::$results);
    }

    public static function so($index=null)
    {
        return isset($index) ? self::$status[$index] : end(self::$status);
    }

    public static function extract($sql)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        $pdo_obj = self::$link->query($sql);
        if (!is_object($pdo_obj)) {
            load::error('Warning', '@DB_lib >> Check db::extract() SELECT query syntax', $sql);
        } else {
            return $pdo_obj->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public static function freeget($sql)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        $pdo_obj = self::$link->query($sql);
        if (!is_object($pdo_obj)) {
            load::error('Warning', '@DB_lib >> Check db::freeget() SELECT query syntax', $sql);
        } else {
            $pdo_array = $pdo_obj->fetchAll(PDO::FETCH_ASSOC);

            self::$results[] = $pdo_array;
            $s = !empty($pdo_array);
            self::$status[] = $s;
            return $s;
        }
    }

    public static function get($arg1, $arg2, $arg3=null, $arg4=null)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        self::$results[] = self::$link->select($arg1, $arg2, $arg3, $arg4);
        $s = !empty(end(self::$results));
        self::$status[] = $s;
        return $s;
    }

    public static function get_r($arg1, $arg2, $arg3=null, $arg4=null)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        $result = self::$link->select($arg1, $arg2, $arg3, $arg4);
        self::$results[] = $result;
        self::$status[] = !empty($result);
        return $result;
    }

    public static function put($arg1, $arg2)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        self::$link->insert($arg1, $arg2);
        self::$results[] = self::$link->id();
        $s = end(self::$results) ? true : false;
        self::$status[] = $s;
        return $s;
    }

    public static function put_r($arg1, $arg2)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        self::$link->insert($arg1, $arg2);
        $result = self::$link->id();
        self::$results[] = $result;
        self::$status[] = $result ? true : false;
        return $result;
    }

    public static function set($arg1, $arg2, $arg3=null)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        self::$results[] = self::$link->update($arg1, $arg2, $arg3)->rowCount();
        $s = end(self::$results) ? true : false;
        self::$status[] = $s;
        return $s;
    }

    public static function set_r($arg1, $arg2, $arg3=null)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        $result = self::$link->update($arg1, $arg2, $arg3)->rowCount();
        self::$results[] = $result;
        self::$status[] = $result ? true : false;
        return $result;
    }

    public static function del($arg1, $arg2)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        self::$results[] = self::$link->delete($arg1, $arg2)->rowCount();
        $s = end(self::$results) ? true : false;
        self::$status[] = $s;
        return $s;
    }

    public static function del_r($arg1, $arg2)
    {
        if (self::$link == null) self::name($GLOBALS['_db_config']['database_name']);

        $result = self::$link->delete($arg1, $arg2)->rowCount();
        self::$results[] = $result;
        self::$status[] = $result ? true : false;
        return $result;
    }
}
