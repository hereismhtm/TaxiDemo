<?php

/**
 * --------------------------------
 * Staticy - PHP 7.0+ framework for LAN internal systems
 * --------------------------------
 *
 * This framework act similarly to the MVC concepts in two dimensions.
 * Backend: controller and model became one thing called "unit".
 * Frontend: same as MVC still called "view".
 * It was under private development stage before the publish.
 *
 * @author      Mustafa Alhadi [<mhtm@protonmail.com>, <gravatar.com/hereismhtm>]
 * @copyright   2020 Mustafa Alhadi
 * @license     MIT License
 */


/**
 * Main Staticy Framework class
 */
class Staticy
{
    /** @ignore */
    final function __construct()
    {
        //debug: echo '<br/>Staticy :: __construct()<br/>';

        load::$app = $this;

        load::$unit = new $GLOBALS['_sfw_route']['unit'];
        if (load::$unit->_sfw_force_ssl === true) {
            if (strpos(strtolower(APP_URL), 'https') === 0
                && isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http'
                ) {

                header('Location: '. rtrim(APP_URL, rtrim($_SERVER['REQUEST_URI'], '/')). $_SERVER['REQUEST_URI']);
                die();
            }
        }

        data::$unit 	= $GLOBALS['_sfw_route']['unit'];
        data::$action 	= $GLOBALS['_sfw_route']['action'];
        data::$args 	= explode('/', ci($GLOBALS['_sfw_route']['args']));
        if (data::$args[0] == '') {
            data::$args = array();
        }

        load::$app->onStart();
        load::$unit->onDo(data::$action, data::$args);
        load::$app->onStop();
    }

    /** @ignore */
    public function onStart()
    {
        //debug: echo 'onStart at Staticy<br/>';
    }

    /** @ignore */
    public function onStop()
    {
        //debug: echo 'onStop at Staticy<br/>';
    }

    /** @ignore */
    final function __destruct()
    {
        //debug: echo '<br/>Staticy :: __destruct()<br/>';
    }
}
