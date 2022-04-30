<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

$alert = load::$app->alert(str::alert_cpanel_user_subscriptions_t, str::alert_cpanel_user_subscriptions_f);

$f1 = tag::input_number(null, 0, 10000, 0.01)->setName('payed')->flag('required');
$f2 = tag::input_number(null, 1, 360, 1)->setName('days')->flag('required');
tag::input_hidden(data::$args[0])->setName('user_id')->set('form', 'cpanel/add_subscription');

HTML::with(
    [$f1, $f2],
    'setClass',
    'form-control'
);

$form = tag::form_tabular(
    'cpanel/add_subscription',
    [str::payed_money, str::renew_days],
    [$f1, $f2],
    str::register
);

$add_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::register_subscription_renew)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $form
    ])
]);

$subscriptions_table = tag::table(
    data::val('subscriptions_data'),
    [str::period, str::payed_money, str::renew_date, str::expire_date]
)->setClass('table-bordered');

$subscriptions_panel = tag::div('panel')->setClass('panel-primary')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::user_subscriptions)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $subscriptions_table
    ])
]);

tag::div('row')->has([
    tag::div('col-sm-3'),
    tag::div('col-sm-6')->has(
        $alert
    ),
    tag::div('col-sm-3')
]);

tag::a(cd('cpanel/user/' . data::$args[0]), str::browse_file)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->setStyle('background-color: gray; color: white');

tag::p(str::payed_money_sum . ' ' . tag::code($payed_sum . ' ' . str::C));

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $subscriptions_panel
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
