<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

tag::div('row')->has([
    tag::div('col-sm-4'),
    tag::div('col-sm-4')->has([

        tag::div('row')->has(tag::div('col-sm-12')->has([
            tag::a(cd('cpanel/driversforms'), str::registration_form)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
            /*tag::a(cd('cpanel/acceptedforms'), str::accepted_forms)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),*/
            tag::a(cd('cpanel/registeredcaps'), str::registered_cabs)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
        ]))->sufln(),


        tag::div('row')->has(tag::div('col-sm-12')->has([
            tag::a(cd('cpanel/trips'), str::passengers_trips)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
            tag::a(cd('cpanel/xtrips'), str::canceled_trips)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
        ]))->sufln(),

        tag::div('row')->has(tag::div('col-sm-12')->has([
            tag::a(cd('cpanel/users'), str::app_users)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
            tag::a(cd('cpanel/sysusers'), str::sys_employees)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
        ]))->sufln(),

        tag::div('row')->has(tag::div('col-sm-12')->has([
            tag::a(cd('cpanel/coupons'), str::discount_coupons)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
            tag::a(cd('cpanel/prices'), str::pricing)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
            tag::a(cd('cpanel/broadcast'), str::broadcast)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
        ]))->sufln(),

        tag::div('row')->has(tag::div('col-sm-12')->has([
            tag::a(cd('cpanel/settings'), str::settings)->set(['class' => 'btn btn-primary', 'role' => 'button'])->sufsp(),
            tag::a(cd('cpanel/overview'), str::overview)->set(['class' => 'btn btn-warning', 'role' => 'button'])->sufsp(),
        ]))->sufln(),

    ]),
    tag::div('col-sm-4')
]);
