<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

$is_results = false;


$count = count(data::ifsession('driversforms_search'));
if ($count) {
    $is_results = true;

    tag::div('panel')->has([
        tag::div('panel-heading')->setStyle('background-color: gray; color: white')->has(
            tag::h3(tag::b($count) . ' ' . str::form)->setClass('panel-title')
        ),
        tag::div('panel-body')->has([
            tag::div('row')->has([
                tag::div('col-sm-2'),
                tag::div('col-sm-8')->has(
                    tag::table(data::session('driversforms_search'), [str::numeric, str::fullname, str::phone_number])
                ),
                tag::div('col-sm-2')
            ])
        ])
    ])->setClass('panel-default');
}


$count = count(data::ifsession('users_search'));
if ($count) {
    $is_results = true;

    tag::div('panel')->has([
        tag::div('panel-heading')->setStyle('background-color: gray; color: white')->has(
            tag::h3(tag::b($count) . ' ' . str::user)->setClass('panel-title')
        ),
        tag::div('panel-body')->has([
            tag::div('row')->has([
                tag::div('col-sm-2'),
                tag::div('col-sm-8')->has(
                    tag::table(data::session('users_search'), [str::situation, str::fullname, str::phone_number])
                ),
                tag::div('col-sm-2')
            ])
        ])
    ])->setClass('panel-default');
}


if ($is_results == false) {
    tag::p(str::no_matched_search_results);
}
