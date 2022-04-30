<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->addCSS('vex/vex');
tag::$view->addCSS('vex/vex-theme-os');
tag::$view->addJS('vex/vex.combined.min');
tag::$view->script("vex.defaultOptions.className = 'vex-theme-os'");
tag::$view->html->set('dir', str::DIR);

tag::$view->script("
    $(document).ready(function() {
        $('button.cancel_trip-btn').click(function() {
            vex.dialog.confirm({
                message: '" . str::do_cancel_trip . "',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::yup . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                ],
                callback: function (value) {
                    if (value) {
                        $.post('" . APP_URL . SEP . "',
                        {
                            _Staticy_REQ_ : 'cpanel/cancel_trip',
                            trip_sn : " . data::$args[0] . "
                        },
                        function(result, status) {
                            if (result != '#success#' && result != '#fail#') {
                                alert(result)
                            } else {
                                location.reload(true)
                            }
                        });
                    }
                }
            })
        });
    });
");

load::view('cpanel/common/header');
tag::a(cd('cpanel/trips'), str::passengers_trips)->sufln(2);

$table = tag::table()->setClass('table-striped table-bordered');
foreach ($trip_data as $key => $value) {
    $table->body([str_replace('_', ' ', $key), $value]);
}

if (
    $trip_data['Status'] == load::unit('models/assets', 'get_trips_status')[0] ||
    $trip_data['Status'] == load::unit('models/assets', 'get_trips_status')[1] ||
    $trip_data['Status'] == load::unit('models/assets', 'get_trips_status')[2] ||
    $trip_data['Status'] == load::unit('models/assets', 'get_trips_status')[3]
) {
    tag::button(str::cancel_trip)->set(['type' => 'button', 'class' => 'btn btn-xs cancel_trip-btn'])->sufln(2);
}

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $table
    ),
    tag::div('col-sm-2')
]);
