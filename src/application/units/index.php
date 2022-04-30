<?php

class Index extends Staticy_Unit
{
    function main()
    {
        tag::p('Welcome at TaxiDemo project.');
        tag::a(cd('cpanel'))->sufln();
        tag::a(cd('join'))->sufln();
        tag::a(cd('share'))->sufln();
    }

    function lang($args)
    {
        if (isset($args[0])) {
            data::cookie('lang', $args[0]);
            dirto(cd($args[1] . '/' . $args[2] . '/' . $args[3]));
        }
    }

    function join()
    {
        load::view();
    }

    function share()
    {
        load::view();
    }

    function driverform()
    {
        load::lib('str', 'str/index_' . data::ifcookie('lang', DEFAULT_UI_LANG));

        if (data::isval('userid')) {
            data::session('form_userid', data::val('userid'));
        }
        $_POST = data::issession('post_data') ? data::session('post_data') : array();
        load::view();
    }

    function new_driverform()
    {
        unset($_SESSION['form_userid']);
        data::session('files_ready', false);
        data::session('files_paths', array());
        data::session('post_data', array());
        dirto(cd('driverform'));
    }
}
