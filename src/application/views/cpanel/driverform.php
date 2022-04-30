<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->addCSS('vex/vex');
tag::$view->addCSS('vex/vex-theme-flat-attack');
tag::$view->addJS('vex/vex.combined.min');
tag::$view->script("vex.defaultOptions.className = 'vex-theme-flat-attack'");
tag::$view->html->set('dir', str::DIR);

tag::$view->script("
    $(document).ready(function() {
        $('button.accept_cab-btn').click(function() {
            vex.dialog.open({
                message: '" . str::check_name_and_phone_number . ":',
                input: [
                    '<input name=\'fullname\' type=\'text\' value=\'" . $driver_form['Fullname'] . "\' placeholder=\' " . str::fullname4th . "\' required />',
                    '<input name=\'phone\' type=\'tel\' value=\'" . $driver_form['Phone'] . "\' maxlength=20 placeholder=\' " . str::phone_number_start_by_plus . "\' required />'
                ].join(''),
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::next . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::back . "' })
                ],
                callback: function (data) {
                    if (data) {
                        vex.dialog.confirm({
                            message: '" . str::you_about_accept_form_as_cab . str::comma . " " . str::do_process . "',
                            buttons: [
                                $.extend({}, vex.dialog.buttons.YES, { text: '" . str::proceed . "' }),
                                $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                            ],
                            callback: function (value) {
                                if (value) {
                                    $.post('" . APP_URL . SEP . "',
                                    {
                                        _Staticy_REQ_ : 'cpanel/accept_cab',
                                        form_sn : " . data::$args[0] . ",
                                        fullname : data.fullname,
                                        phone : data.phone
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
                    }
                }
            })
        });

        $('button.delete_form-btn').click(function() {
            vex.dialog.confirm({
                message: '" . str::do_del_form . "',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::yup . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                ],
                callback: function (value) {
                    if (value) {
                        $.post('" . APP_URL . SEP . "',
                        {
                            _Staticy_REQ_ : 'cpanel/delete_form',
                            form_sn : " . data::$args[0] . "
                        },
                        function(result, status) {
                            if (result != '#success#' && result != '#fail#') {
                                alert(result)
                            } else {
                                location.replace('" . cd('cpanel/driversforms') . "')
                            }
                        });
                    }
                }
            })
        });
    });
");

load::view('cpanel/common/header');
tag::a(cd('cpanel/driversforms'), str::registration_form)->sufln(2);

if ($isAccepted == 0) {
    tag::button(str::accept_form_as_new_cab)->set(['type' => 'button', 'class' => 'btn btn-success btn-md accept_cab-btn'])->sufln(2);
} else {
    tag::span(str::form_data_accepted)->sufsp();
    tag::a(cd('cpanel/user/' . $user_id), str::browse_file)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->setStyle('background-color: gray; color: white')->sufln(2);
}
tag::button(str::del_form)->set(['type' => 'button', 'class' => 'btn btn-danger btn-md delete_form-btn'])->sufln(2);

$table = tag::table();
$elements = [
    'Timestamp' => str::form_registration_timestamp,
    'Fullname' => str::fullname4th,
    'Gender' => str::gender,
    'Phone' => str::phone_number,
    'Email' => str::email,
    'Logo_img' => str::personal_pic,
    'Nat_img' => str::national_number,
    'CarFront_img' => str::car_front_side,
    'CarRight_img' => str::car_right_side,
    'CarBack_img' => str::car_back_side,
    'CarLeft_img' => str::car_left_side,
    'CarInside_img' => str::car_inner_space,
    'Emr_img' => str::document_A,
    'Lic_img' => str::document_B,
    'Cert_img' => str::document_C
];

foreach ($driver_form as $key => $value) {
    if ($key == 'Gender') {
        $table->body([$elements[$key], load::unit('models/assets', 'get_users_genders')[$value]]);
    } else {
        if ($value == '') $value = str::undefined;
        if (strpos($value, 'http') !== false) {
            $value = tag::a($value, tag::img($value, 250, 250));
        }
        $table->body([$elements[$key], $value]);
    }
}

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has($table),
    tag::div('col-sm-2')
]);
