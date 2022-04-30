<?php

tag::$view->of('~' . str::login);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

$logo = tag::a(
    cd(),
    tag::img('~img/LOGO.jpg')->setClass('img-responsive')->setClass('center-block')
)->preln();
$logo_title = tag::h3(tag::b(str::cpanel . ' ' . TAXI_NAME))->setClass('text-center');

$alert = load::$app->alert(null, str::alert_cpanel_login_f);

$f1 = tag::input_text(data::ifval('username'))->setName('Username')->flag('required')->set(['class' => 'form-control', 'form' => 'cpanel/login']);
$f2 = tag::input_password()->setName('Password')->flag('required')->set(['class' => 'form-control', 'form' => 'cpanel/login']);

$login_header1 = tag::div('col-sm-1')->has(tag::a('http://www.example.com', tag::img('~img/example.jpg', 64, 64))->set('target', 'blank'));

$login_header2 = tag::div('col-sm-11')->has(tag::h3(str::login_header)->setStyle('color: white;'));

// ---- PAGE GRID ----

tag::div('row')->has([
    (str::DIR == 'rtl' ? $login_header1 : $login_header2),
    (str::DIR == 'rtl' ? $login_header2 : $login_header1)
])->setStyle('background-color: black;');

tag::div('row')->has([
    tag::div('col-sm-4'),
    tag::div('col-sm-4')->has([$logo, $logo_title]),
    tag::div('col-sm-4')
]);

load::view('cpanel/common/lang_switcher');

tag::div('row')->has([
    tag::div('col-sm-4'),

    tag::div('col-sm-4')->has([
        $alert,
        tag::div('form-group')->has([tag::label(str::username, $f1), $f1]),
        tag::div('form-group')->has([tag::label(str::password, $f2), $f2]),
        data::$temp['lang_switcher']->sufln(2),
        tag::form('cpanel/login')->has(tag::input_submit(str::enter)->setClass(['glyphicon glyphicon-circle-arrow-left', 'btn btn-primary']))->sufln()
    ]),

    tag::div('col-sm-4')
]);
