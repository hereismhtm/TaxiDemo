<?php
session_start();

define('RES_DIR', 'res');                    // resources directory from public root
define('APP_DIR', 'src/application');        // application (project) directory from public root
define('SYS_DIR', 'src/system');            // framework system directory from public root

define('SEP', '/');
define('ROOT', realpath(null) . SEP);
require SYS_DIR . SEP . 'init' . SEP . 'boot.php';
