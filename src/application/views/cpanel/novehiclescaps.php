<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

tag::h6('5 novehiclescaps');

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        tag::table(
            $captains_data,
            [str::numeric, str::situation, str::fullname, str::phone_number]
        )->setClass('table-striped table-bordered')
    ),
    tag::div('col-sm-2')
]);
