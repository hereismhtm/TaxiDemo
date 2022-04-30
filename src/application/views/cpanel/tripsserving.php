<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

tag::h6($tripsserving_data[0] . ' ' . str::trip_in_serve_now);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        tag::table(
            $tripsserving_data[1],
            [str::the_trip_number, str::status, str::classification, str::order_time]
        )->setClass('table-striped table-bordered')
    ),
    tag::div('col-sm-2')
]);
