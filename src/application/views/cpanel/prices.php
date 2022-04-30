<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

$alert = load::$app->alert(str::alert_cpanel_prices_t, str::alert_cpanel_prices_f);

$f1 = tag::select(load::$app->vehicles_classes)->prehas(tag::option('----'))->setName('class')->flag('required');
$f2 = tag::select(['M' => str::period_price_m, 'A' => str::period_price_a, 'N' => str::period_price_n, 'G' => str::period_price_g])->prehas(tag::option('----'))->setName('period')->flag('required');
$f3 = tag::input_number(null, 0, 100000, 0.10)->setName('fixed')->flag('required');
$f4 = tag::input_number(null, 0, 100000, 0.10)->setName('kilo')->flag('required');
$f5 = tag::input_number(null, 0, 100000, 0.10)->setName('tax')->flag('required');

HTML::with(
    [$f1, $f2, $f3, $f4, $f5],
    'setClass',
    'form-control'
);

$form = tag::form_tabular(
    'cpanel/set_prices',
    [str::classification, str::period, str::fixed_price, str::kilo_price, str::trip_tax],
    [$f1, $f2, $f3, $f4, $f5],
    str::edit
);

$set_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::edit_trip_price)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $form
    ])
]);

$prices_table = tag::table(
    data::val('prices_data'),
    [str::_class, str::_period, str::_fixed, str::_kilo, str::_tax]
)->setClass('table-bordered');

$prices_panel = tag::div('panel')->setClass('panel-primary')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::trips_price)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $prices_table
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
        $prices_panel
    ),
    tag::div('col-sm-2')
]);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $set_panel
    ),
    tag::div('col-sm-2')
]);
