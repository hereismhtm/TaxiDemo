<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');

$alert = load::$app->alert(str::alert_cpanel_broadcast_t, str::alert_cpanel_broadcast_f);

$f1 = tag::input_text()->setName('msg_title');
$f2 = tag::textarea(null, 10, 50)->setName('msg_body')->flag('required');
$f3 = tag::input_radio([
    'P' => str::all_passenger,
    'C' => str::all_captains,
    'SMS' => str::phone_number_at_title_field_sms,
    'PSMS' => str::all_passenger_sms,
    'CSMS' => str::all_captains_sms
], null, ['sufln' => 1, 'set' => ['name' => 'send_to', 'form' => 'cpanel/send_broadcast'], 'flag' => 'required']);

HTML::with(
    [$f1, $f2, $f3],
    'setClass',
    'form-control'
);

$form = tag::form_tabular(
    'cpanel/send_broadcast',
    [str::title, str::message, str::to],
    [$f1, $f2, $f3],
    str::send_broadcast
)->sufln();

tag::div('row')->has([
    tag::div('col-sm-4'),
    tag::div('col-sm-4')->has([$alert, $form]),
    tag::div('col-sm-4')
]);
