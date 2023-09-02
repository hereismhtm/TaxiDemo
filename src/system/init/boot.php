<?php

define('GENERATOR', 'Staticy Framework (Pre-alpha 0.5.36)');

require SYS_DIR. SEP. 'init'. SEP. 'gfunc.php';
define('ROUTE_VAR', 'cd');			// request identifier GET var such: yourdomain.com/?cd=UnitName
define('DEF_UNIT', 'index');		// default unit name
define('DEF_ACTION', 'main');		// default action name
require APP_DIR. SEP. 'config'. SEP. 'config.php';

if (DEV_ENV) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Default routing values

$_sfw_route = array('unit' => DEF_UNIT, 'action' => DEF_ACTION, 'args' => null);

// ----------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_Staticy_REQ_'])) {

    $request = ci($_POST['_Staticy_REQ_']);
    require SYS_DIR. SEP. 'init'. SEP. 'basic.php';
    require SYS_DIR. SEP. 'Staticy_Unit.php';
    require SYS_DIR. SEP. 'Staticy.php';
    require APP_DIR. SEP. 'config'. SEP. 'application.php';

    $_sfw_ufile = APP_DIR. SEP. 'requests'. SEP. strtolower($request). '.php';
    if (file_exists($_sfw_ufile)) {
        require $_sfw_ufile;
        $_sfw_route['unit'] = 'REQ_'. explode('.', basename($_sfw_ufile))[0];
    } else {
        $_sfw_ufile = APP_DIR. SEP. 'units'. SEP. DEF_UNIT. '.php';
        require $_sfw_ufile;
        $_sfw_route['unit'] 	= DEF_UNIT;
        $_sfw_route['action'] 	= 'REQ_'. $request;
    }

} else {

    // Set routing values

    $request = isset($_SERVER['PATH_INFO']) ? ci($_SERVER['PATH_INFO']) : null;
    if ($request == null) {
        $request = isset($_GET[ROUTE_VAR]) ? ci($_GET[ROUTE_VAR]) : null;
    }
    $request = trim($request, '/');

    if ($request != null) {

        $request = explode('/', $request);
        foreach ($request as $key => $value) {
            if ($key == 0) {
                $_sfw_route['unit'] = $value;
            } elseif ($key == 1) {
                $_sfw_route['action'] = $value;
            } else $_sfw_route['args'] .= $value. '/';
        }
        $_sfw_route['args'] = rtrim($_sfw_route['args'], '/');

        $_sfw_ufile = APP_DIR. SEP. 'units'. SEP. strtolower($_sfw_route['unit']). '.php';

        if (!file_exists($_sfw_ufile)) {

            if ($key == 0) {
                $_sfw_route['args'] = null;
            } else if ($_sfw_route['args'] == null) {
                $_sfw_route['args'] = $_sfw_route['action'];
            } else {
                $_sfw_route['args'] = $_sfw_route['action']. '/'. $_sfw_route['args'];
            }
            $_sfw_route['action']	= $_sfw_route['unit'];
            $_sfw_route['unit']		= DEF_UNIT;
        }

        unset($key);
        unset($value);
    }

    // Units and actions aliases

    require APP_DIR. SEP. 'config'. SEP. 'aliases.php';

    // Check for special units methods

    require SYS_DIR. SEP. 'init'. SEP. 'basic.php';
    if (!load::is_allowedAction( $_sfw_route['action'] ))
        httpsta(423);

    // Load the unit file

    $_sfw_ufile = APP_DIR. SEP. 'units'. SEP. strtolower($_sfw_route['unit']). '.php';

    if (file_exists($_sfw_ufile)) {
        require SYS_DIR. SEP. 'Staticy_Unit.php';
        require $_sfw_ufile;
    } else httpsta(404);

    if (!class_exists($_sfw_route['unit']))
        httpsta(501);

    if (!method_exists($_sfw_route['unit'], $_sfw_route['action']))
        httpsta(404);

    require SYS_DIR. SEP. 'Staticy.php';
    require APP_DIR. SEP. 'config'. SEP. 'application.php';
}

unset($request);

spl_autoload_register(function($class_name) {
    load::lib($class_name, null, true);
});

new Application;
