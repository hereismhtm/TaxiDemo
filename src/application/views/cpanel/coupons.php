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
        $('button.cancel_coupon-btn').click(function() {
            
            var coupon_id = this.id;

            vex.dialog.confirm({
                message: '" . str::do_del_coupon . "',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::yup . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                ],
                callback: function (value) {
                    if (value) {
                        $.post('" . APP_URL . SEP . "',
                        {
                            _Staticy_REQ_ : 'cpanel/cancel_coupon',
                            coupon_code : coupon_id
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

$alert = load::$app->alert(str::alert_cpanel_coupons_t, str::alert_cpanel_coupons_f);

$f1 = tag::input_text()->setName('coupon_code')->flag('required');
$f2 = tag::input_number(null, 0.01, 1000, 0.01)->setName('coupon_value')->flag('required');
$f3 = tag::input_number(null, 1, 10000, 1)->setName('coupon_amount')->flag('required');
$f4 = tag::input_date()->setName('expire_date')->flag('required');

HTML::with(
    [$f1, $f2, $f3, $f4],
    'setClass',
    'form-control'
);

$form = tag::form_tabular(
    'cpanel/add_coupon',
    [str::coupon_code, str::coupon_value, str::coupon_amount, str::valid_until],
    [$f1, $f2, $f3, $f4],
    str::register
);

$add_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::register_coupon)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $form
    ])
]);

$coupons_table = tag::table(
    data::val('coupons_data'),
    [str::code, str::value, str::amount, str::used, str::valid_until],
    ['|~|' => '_Deleted', '1' => ['class' => 'danger']]
)->setClass('table-bordered');

$coupons_panel = tag::div('panel')->setClass('panel-primary')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::registered_coupons)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        $coupons_table
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
        $coupons_panel
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
