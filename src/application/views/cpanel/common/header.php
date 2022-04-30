<?php

//tag::$view->mainDiv->setStyle('background-color: #EFEBE9;');
//tag::$view->html->setStyle('background-color: #EFEBE9;');

load::view('cpanel/common/lang_switcher');

$div_header1 = tag::div('col-sm-2')->has([
    tag::p(data::session('username') == 'admin' ? '<b>' . str::administrator . '</b>' : data::session('fullname')),
    data::$temp['lang_switcher']->sufln(),
    tag::a(cd('cpanel'), str::the_cpanel)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->sufsp(),
    tag::a(cd('cpanel/logout'), str::logout)->set(['class' => 'btn btn-xs btn-danger', 'role' => 'button', 'style' => 'background-color: brown'])
]);

$div_header2 = tag::div('col-sm-10')->has(tag::h1(data::$temp['subject']));

tag::div('row')->has([
    (str::DIR == 'rtl' ? $div_header1 : $div_header2),
    (str::DIR == 'rtl' ? $div_header2 : $div_header1)
]);

tag::div('row')->has([
    tag::div('col-sm-3'),
    tag::div('col-sm-6')->has(
        tag::form('cpanel/search')->has(
            tag::input_search(data::ifsession('search_keyword'))->set('placeholder', str::search_box_hint)->setName('keyword')->setClass('form-control')->flag('required')
        )->sufln()
    ),
    tag::div('col-sm-3')
]);
