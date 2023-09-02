<?php

class Staticy_Unit
{
    public $_sfw_force_ssl = true;
    public $_sfw_dont_log = true;

    final function __construct()
    {
        //debug: echo '<br/>Staticy_Unit :: __construct()<br/>';
        $this->nfs();
    }

    /** @ignore */
    public final function onDo($action, $args)
    {
        //debug: echo 'onDo at Staticy_Unit<br/>';

        $this->onStart();
        $response = $this->$action($args);
        $this->onStop();
        $this->eventlog();
        return $response;
    }

    /** @ignore */
    public final function eventlog()
    {
        if ($this->_sfw_dont_log === false || is_array($this->_sfw_dont_log)) {

            $this->_sfw_dont_log = ($this->_sfw_dont_log === false)? array() : array_flip($this->_sfw_dont_log);
            if (!isset($this->_sfw_dont_log[data::$action])) {
                db::log();
            }
        }
    }

    /**
     * --{SHOULD USE FOR LOADING LIBRARIES ONLY}--
     * Overrideable to select required files for unit
     * ex. write this line inside to load "tag" library...
     * load::lib('tag');
     * @return void
     */
    public function nfs()
    {
        //debug: echo 'nfs at Staticy_Unit<br/>';
    }

    /** @ignore */
    public function onStart()
    {
        //debug: echo 'onStart at Staticy_Unit<br/>';
    }

    /** @ignore */
    public function onStop()
    {
        //debug: echo 'onStop at Staticy_Unit<br/>';
    }

    final function __destruct()
    {
        //debug: echo '<br/>Staticy_Unit :: __destruct()<br/>';
    }
}
