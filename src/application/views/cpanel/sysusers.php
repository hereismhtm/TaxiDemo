<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

$alert = load::$app->alert(str::alert_cpanel_sysusers_t, str::alert_cpanel_sysusers_f);

$f1 = tag::input_text()->setName('username')->flag('required');
$f2 = tag::input_password()->setName('password')->flag('required');
$f3 = tag::input_text()->setName('fullname')->flag('required');
$f4 = tag::input_checkbox(load::unit('models/assets', 'get_perms_array'), null, ['setName' => 'perms[]', 'sufln' => 1]);

HTML::with(
    [$f1, $f2, $f3, $f4],
    'setClass',
    'form-control'
);

$form = tag::form_tabular(
    'cpanel/register_sysuser',
    [str::username, str::password, str::fullname, str::granted_permissions],
    [$f1, $f2, $f3, $f4],
    str::register
);

$add_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::register_new_employee)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $form
    ])
]);

$sysusers_table = tag::table(
    data::val('sysusers_data'),
    [str::username, str::fullname]
)->setClass('table-striped table-bordered');

$sysusers_panel = tag::div('panel')->setClass('panel-primary')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::sys_employees_list)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $sysusers_table
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
        $sysusers_panel
    ),
    tag::div('col-sm-2')
]);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $add_panel
    ),
    tag::div('col-sm-2')
]);
