<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

load::view('cpanel/common/header');
tag::p(CPANEL_VERSION_CODE);

$users_table = tag::table()->set('border', 1);
$users_table->body([str::users_sum, $users_info[0]]);
$users_table->body([
    str::registered_cabs,
    $users_info[1] . tag::a(cd('cpanel/registeredcaps'), str::show)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->presp()
]);
$users_table->body([
    str::cabs_working_now,
    $users_info[2] . tag::a(cd('cpanel/capsworking'), str::show)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->presp()
]);

$vehicles_table = tag::table($vehicles_info[0], [str::vehs_classification, str::number, str::percentage])->set('border', 1);
$vehicles_table->foot([str::registred_vehs_number, $vehicles_info[1] . ' ' . str::vehicle, '-']);

$trips_table = tag::table()->set('border', 1);
$trips_table->body([str::ordered_trips, $trips_info[0]]);
$trips_table->body([str::executed_number, $trips_info[1]]);
$trips_table->body([str::execute_percentage, $trips_info[2]]);
$trips_table->body([
    str::trips_serving_now,
    $trips_info[3] . tag::a(cd('cpanel/tripsserving'), str::show)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->presp()
]);

$versions_table = tag::table($versions_data, [str::app_version, str::app_version_percentage])->set('border', 1);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has([
        $users_table,
        $vehicles_table,
        $trips_table,
        $versions_table
    ]),
    tag::div('col-sm-2')
]);
