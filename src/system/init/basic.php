<?php

data::initVals();

final class Load
{
    private static $memory = array();

    public static $app = null;
    public static $unit = null;

    /**
     * Specify required library.
     * Search the library file in application othewise in system.
     * @param string $name
     * @param string $configName
     * @param boolean $spl_autoload_register
     * @return void
     */
    public static function lib($name, $configName=null, $spl_autoload_register=false)
    {
        $describe = 'lib:'. strtolower($name);
        if (!isset(self::$memory[$describe])) {

            $lib_path = ROOT. APP_DIR. SEP. 'libs'. SEP. strtolower($name). SEP;
            $lib_file = $lib_path. 'libind.php';
            if (file_exists($lib_file)) {
                self::$memory[$describe] = $lib_path;

                if (isset($configName)) {
                    require APP_DIR. SEP. 'config'. SEP. 'config_'. $configName. '.php';
                }
                require $lib_file;
                return;
            }

            $lib_path = ROOT. SYS_DIR. SEP. 'libs'. SEP. strtolower($name). SEP;
            $lib_file = $lib_path. 'libind.php';
            if (file_exists($lib_file)) {
                self::$memory[$describe] = $lib_path;

                if (isset($configName)) {
                    require APP_DIR. SEP. 'config'. SEP. 'config_'. $configName. '.php';
                }
                require $lib_file;
                return;
            }

            if (!$spl_autoload_register) {
                load::error('Error', 'No library called', $name);
            }

        } else {

            if (isset($configName)) {
                require APP_DIR. SEP. 'config'. SEP. 'config_'. $configName. '.php';
            }
        }
    }

    /**
     * Specify required library.
     * Search the library file in system only.
     * @param string $name
     * @param string $configName
     * @return void
     */
    public static function slib($name, $configName=null)
    {
        $describe = 'lib:'. strtolower($name);
        if (!isset(self::$memory[$describe])) {

            $lib_path = ROOT. SYS_DIR. SEP. 'libs'. SEP. strtolower($name). SEP;
            $lib_file = $lib_path. 'libind.php';
            if (file_exists($lib_file)) {
                self::$memory[$describe] = $lib_path;

                if (isset($configName)) {
                    require APP_DIR. SEP. 'config'. SEP. 'config_'. $configName. '.php';
                }
                require $lib_file;
            } else
                load::error('Error', 'No system library called', $name);

        } else {

            if (isset($configName)) {
                require APP_DIR. SEP. 'config'. SEP. 'config_'. $configName. '.php';
            }
        }
    }

    /**
     * Call method of unit.
     * @param string $name
     * @param string $action
     * @param *any $args
     * @return *any
     */
    public static function unit($name, $action=DEF_ACTION, $args=null)
    {
        if (!self::is_allowedAction( $action )) httpsta(423);

        $describe = 'unit:'. strtolower($name);
        if (!isset(self::$memory[$describe])) {

            $unit_file = APP_DIR. SEP. 'units'. SEP. strtolower($name). '.php';
            if (file_exists($unit_file)) {
                require $unit_file;
                $name = explode('/', $name);
                $name = end($name);
                if (!class_exists($name))
                    load::error('Error', 'No unit class called', $name);
                self::$memory[$describe] = new $name;
            } else
                load::error('Error', 'No unit called', $name);
        }

        return self::$memory[$describe]->$action($args);
    }

    /**
     * Call method of unit across the onDo() method.
     * @param string $name
     * @param string $action
     * @param *any $args
     * @return *any
     */
    public static function xunit($name, $action=DEF_ACTION, $args=null)
    {
        if (!self::is_allowedAction( $action )) httpsta(423);

        $describe = 'unit:'. strtolower($name);
        if (!isset(self::$memory[$describe])) {

            $unit_file = APP_DIR. SEP. 'units'. SEP. strtolower($name). '.php';
            if (file_exists($unit_file)) {
                require $unit_file;
                $name = explode('/', $name);
                $name = end($name);
                if (!class_exists($name))
                    load::error('Error', 'No unit class called', $name);
                self::$memory[$describe] = new $name;
            } else
                load::error('Error', 'No unit called', $name);
        }

        return self::$memory[$describe]->onDo($action, $args);
    }

    /**
     * Include a view file.
     * @param string $name
     * @param string $type
     * @return void
     */
    public static function view($name=null, $type='php')
    {
        if (!isset($name)) {
            $name = $GLOBALS['_sfw_route']['unit']. SEP. $GLOBALS['_sfw_route']['action'];
            $name = ltrim($name, DEF_UNIT);
        }

        $_sfw_vfile = APP_DIR. SEP. 'views'. SEP. strtolower($name). '.'. $type;
        if (file_exists($_sfw_vfile)) {
            unset($name);
            unset($type);
            foreach (data::getVals() as $key => $value) {
                $$key = $value;
            }
            include $_sfw_vfile;
        } else
            load::error('Warning', 'No view of type ('. $type. ') called', $name);
    }

    /**
     * Fitch an url location.
     * @param string $url
     * @param string $bodyData
     * @param array $headers
     * @return array [HTTP status code, response]
     */
    public static function location($url, $bodyData=null, $headers=null)
    {
        if (!isset($headers)) {
            $headers = array('Content-Type: text/html');
        }

        // Open connection
        $curl = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            if (isset($bodyData)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $bodyData);
            }

            // Execute post
            $result = curl_exec($curl);
            if ($result === false) {
                load::error('Error', 'Loading location failed, '. curl_error($curl));
            }
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Close connection
            curl_close($curl);

            return array($httpCode, $result);
        } else {
            load::error('Error', 'Loading location failed because no URL passed');
        }
    }

    /**
     * Check if requested action is allowed to excecute.
     * @param string $action
     * @return boolean
     */
    public static function is_allowedAction($action)
    {
        $action = strtolower($action);

        if (strpos($action, '_') 		=== 0
            || strpos($action, 'req_') 	=== 0
            || $action 	== 'ondo'
            || $action 	== 'nfs'
            || $action 	== 'onstart'
            || $action 	== 'onstop')

            return false;
        else
            return true;
    }

    /**
     * Get the loaded library path.
     * @param string $name
     * @return string
     */
    public static function homeOfLib($name)
    {
        if (isset(self::$memory['lib:'. strtolower($name)])) {
            return self::$memory['lib:'. strtolower($name)];
        } else
            load::error('Error', 'This library not loaded yet', $name);
    }

    /**
     * Report something went wrong.
     * @param string $status
     * @param string $message
     * @param string $something
     * @return void/echo
     */
    public static function error($status, $message, $something=null)
    {
        if (ob_get_length()) ob_clean();
        $report = '<h2>'. $status. '</h2>';
        $report .= '<p>'. $message;
        if (isset($something)) {
            $report .= ': '. $something;
        }
        $report .= '</p>';
        $report .= '<hr/><p><small>'. GENERATOR. '</small></p>';
        if (strtolower($status) == 'error') {
            die($report);
        } else {
            echo $report;
        }
    }
}

// ----------------------------------------------------------------

final class Data
{
    const RESULT_EDITOR = 0;

    private static $vals 	= array();

    public static $unit 	= null;
    public static $action 	= null;
    public static $args		= array();
    public static $temp		= array();

    public static function getVals()
    {
        return self::$vals;
    }

    public static function initVals()
    {
        if ( isset($_GET['vals']) ) {
            $get = ci($_GET['vals']);
            $get = rtrim($get, SEP);
            $get = explode(SEP, $get);
            $i = 0;
            while ( isset($get[$i]) && isset($get[$i+1]) ) {
                self::$vals[ $get[$i] ] = $get[$i+1];
                $i += 2;
            }
        }
    }

    public static function gluedUrl($page, $passed_vals=null)
    {
        $str = '';
        foreach ((isset($passed_vals)? $passed_vals : self::$vals) as $key => $value) {
            if (!is_array($value)) {
                $str .= $key. SEP. $value. SEP;
            }
        }
        $str = rtrim($str, SEP);
        if ($str != '') {
            $page .= (strpos($page, '?') !== false) ? '&vals=' : '?vals=';
            $page .= $str;
        }
        return $page;
    }

    public static function body($json_decode=false)
    {
        if ($json_decode) {
            return json_decode(file_get_contents('php://input'));
        } else {
            return file_get_contents('php://input');
        }
    }

    public static function val($name, $value=null)
    {
        if (isset($value)) {
            self::$vals[$name] = $value;
            return $value;
        } elseif (isset(self::$vals[$name])) {
            return self::$vals[$name];
        } else
            load::error('Error', 'No value called', $name);
    }

    public static function isval($name)
    {
        return (isset( self::$vals[$name] )) ? true : false;
    }

    public static function ifval($name, $otherwise=null)
    {
        return (isset( self::$vals[$name] )) ? self::$vals[$name] : $otherwise;
    }

    public static function session($name, $value=null)
    {
        if (isset($value)) {
            $_SESSION[$name] = $value;
            return $value;
        } elseif (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        } else
            load::error('Error', 'No session called', $name);
    }

    public static function issession($name)
    {
        return (isset( $_SESSION[$name] )) ? true : false;
    }

    public static function ifsession($name, $otherwise=null)
    {
        return (isset( $_SESSION[$name] )) ? $_SESSION[$name] : $otherwise;
    }

    public static function cookie($name, $value=null, $seconds=2592000)
    {
        if (isset($value)) {
            setcookie($name, $value, time() + $seconds, '/');
            return $value;
        } elseif (isset($_COOKIE[$name])) {
            return ci($_COOKIE[$name]);
        } else
            load::error('Error', 'No cookie called', $name);
    }

    public static function iscookie($name)
    {
        return (isset( $_COOKIE[$name] )) ? true : false;
    }

    public static function ifcookie($name, $otherwise=null)
    {
        return (isset( $_COOKIE[$name] )) ? ci($_COOKIE[$name]) : $otherwise;
    }

    public static function post($name)
    {
        return ci($_POST[$name]);
    }

    public static function ispost($name)
    {
        return (isset( $_POST[$name] )) ? true : false;
    }

    public static function ifpost($name, $otherwise=null)
    {
        return (isset( $_POST[$name] )) ? ci($_POST[$name]) : $otherwise;
    }

    public static function get($name)
    {
        return ci($_GET[$name]);
    }

    public static function isget($name)
    {
        return (isset( $_GET[$name] )) ? true : false;
    }

    public static function ifget($name, $otherwise=null)
    {
        return (isset( $_GET[$name] )) ? ci($_GET[$name]) : $otherwise;
    }

    public static function editor($data, $editor_type=self::RESULT_EDITOR)
    {
        switch ($editor_type) {
            case self::RESULT_EDITOR:
                return new _SFW_DATA_ResultEditor($data);
                break;
        }
    }

    public static function tabulation($result)
    {
        // if TRUE so it is a result array need initialization
        if ( isset($result[0]) && is_array($result[0]) && count($result[0])>=2 ) {
            $res = array();
            foreach ($result as $row) {
                $temp = array();
                foreach ($row as $val) {
                    $temp[] = $val;
                }
                $res[ $temp[0] ] = $temp[1];
            }
            return $res;
        } else {
            return $result;
        }
    }
}

// ----------------------------------------------------------------

final class _SFW_DATA_ResultEditor
{
    private $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    private function parse($exp, $i)
    {
        $index = 1;
        while ( isset($exp[$index]) ) {
            $exp[$index] = $this->data[$i][ $exp[$index] ];
            $index += 2;
        }
        return $exp;
    }

    public function field($key, $value)
    {
        $exp = explode('|', $value);
        foreach ($this->data as $i => $row) {
            $this->data[$i][$key] = implode('', $this->parse($exp, $i));
        }
    }

    public function filter($key, $value)
    {
        foreach ($this->data as $i => $row) {
            if ($row[$key] != $value) {
                unset( $this->data[$i] );
            }
        }
    }

    public function remove($key)
    {
        foreach ($this->data as $i => $row) {
            unset( $this->data[$i][$key] );
        }
    }

    public function append($key)
    {
        foreach ($this->data as $i => $row) {
            $this->data[$i][$key] = null;
        }
    }

    public function getData()
    {
        return $this->data;
    }
}
