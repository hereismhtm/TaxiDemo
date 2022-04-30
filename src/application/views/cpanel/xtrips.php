<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

tag::h6(str::trips_accepted_then_canceled);
tag::$pagination->echo();

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        tag::table(
            $trips_data,
            [str::the_trip_number, str::status, str::_class, str::order_time]
        )->setClass('table-striped table-bordered')
    ),
    tag::div('col-sm-2')
]);
