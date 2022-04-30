<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

if (!data::ifval('drivers_forms')) {
    tag::h6(str::no_forms_in_sys . '.');
}

tag::a(cd('new_driverform'), str::open_new_registration_form);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        tag::table(
            data::val('drivers_forms'),
            [str::numeric, str::fullname, str::phone_number, str::ip_address]
        )->setClass('table-striped table-bordered')
    ),
    tag::div('col-sm-2')
]);
