<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

$alert = '';
if (data::issession('is')) {
    if (data::session('settings_request') == 'set_password') {
        $alert = load::$app->alert(str::alert_cpanel_settings_t_set_password, str::alert_cpanel_settings_f_set_password);
    } else if (data::session('settings_request') == 'set_radar') {
        $alert = load::$app->alert(str::alert_cpanel_settings_t_set_radar, str::alert_cpanel_settings_f_set_radar);
    }
    unset($_SESSION['settings_request']);
}

$f1 = tag::input_password()->setName('old_password')->flag('required');
$f2 = tag::input_password()->setName('new_password')->flag('required');
$f3 = tag::input_password()->setName('repeat_password')->flag('required');

HTML::with(
    [$f1, $f2, $f3],
    'setClass',
    'form-control'
);

$password_form = tag::form_tabular(
    'cpanel/set_password',
    [str::old_password, str::new_password, str::repeat_password],
    [$f1, $f2, $f3],
    str::edit
);

$password_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::edit_password_of . ' ' . tag::kbd(data::session('username')))->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $password_form
    ])
]);

$f1 = tag::select(
    [
        1 => str::one_kilo,
        2 => str::two_kilo,
        3 => str::three_kilo,
        4 => str::four_kilo,
        5 => str::five_kilo,
    ],
    $system_vars_data['RADAR_SCOPE']
)->setName('radar_scope')->setClass('form-control');

$radar_form = tag::form_tabular(
    'cpanel/set_radar',
    [str::send_orders_radar_range],
    [$f1],
    str::set
);

$radar_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::set_service_radar)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $radar_form
    ])
]);

tag::div('row')->has([
    tag::div('col-sm-3'),
    tag::div('col-sm-6')->has(
        $alert
    ),
    tag::div('col-sm-3')
]);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $password_panel
    ),
    tag::div('col-sm-2')
]);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $radar_panel
    ),
    tag::div('col-sm-2')
]);
