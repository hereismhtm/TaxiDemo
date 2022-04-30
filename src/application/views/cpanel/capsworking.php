<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

tag::div('panel')->setClass('panel-success')->has([
    tag::div('panel-heading')->has([
        tag::p(tag::b(str::map_location_updated_vehs))->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        tag::p($last_caps_data[0] . ' ' . str::vehs_for_cabs_below . ':'),
        tag::div('row')->has([
            tag::div('col-sm-2'),
            tag::div('col-sm-8')->has(
                count($last_caps_data[1]) ?
                    tag::table(
                        $last_caps_data[1],
                        [str::numeric, str::situation, str::fullname, str::phone_number]
                    )->setClass('table-striped table-bordered')
                    :
                    tag::p('<i>' . str::no_cabs . '</i>')
            ),
            tag::div('col-sm-2')
        ])
    ])
]);

tag::h6($count_1day_drivers . ' ' . str::worked_cabs_in_24h)->sufln();
