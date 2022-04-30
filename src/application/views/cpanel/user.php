<?php

function printActivationStatus($active_until)
{
    $expire_date = date('d-m-Y', strtotime($active_until));
    if ($expire_date == '01-01-1970') {
        return str::permanently_active;
    } else {
        $active_until_date = date('d-m-Y', strtotime($active_until));
        if ($active_until >= date('Y-m-d H:i:s')) {
            return str::active_until_date . ' ' . $active_until_date;
        } else {
            return str::freezed_until_date . ' ' . $active_until_date;
        }
    }
}

function _num_format($number)
{
    return str_replace(',', null, number_format($number, 2));
}

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->addCSS('vex/vex');
tag::$view->addCSS('vex/vex-theme-os');
tag::$view->addJS('vex/vex.combined.min');
tag::$view->script("vex.defaultOptions.className = 'vex-theme-os'");
tag::$view->link('https://fonts.googleapis.com/css?family=Overpass+Mono', ['rel' => 'stylesheet']);
tag::$view->link('http://www.fontstatic.com/f=jazeera', ['rel' => 'stylesheet', 'type' => 'text/css']);
tag::$view->html->set('dir', str::DIR);

tag::$view->script("
    $(document).ready(function() {
        $('button.register_cab-btn').click(function() {
            vex.dialog.open({
                message: '" . str::validate_this_date . ":',
                input: [
                    '<input name=\'fullname\' type=\'text\' value=\'" . $user_data['Fullname'] . "\' placeholder=\' " . str::fullname4th . "\' required />',
                    '<input name=\'email\' type=\'email\' value=\'" . $user_data['Email'] . "\' maxlength=100 placeholder=\' " . str::email_optinal . "\' />',
                    '<input name=\'gender\' type=\'radio\' value=\'M\' required /> " . str::male . " ',
                    '<input name=\'gender\' type=\'radio\' value=\'F\' required /> " . str::female . " '
                ].join(''),
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::next . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::back . "' })
                ],
                callback: function (data) {
                    if (data) {
                        vex.dialog.confirm({
                            message: '" . str::you_about_upgrade_user_as_cab . str::comma . " " . str::do_process . "',
                            buttons: [
                                $.extend({}, vex.dialog.buttons.YES, { text: '" . str::proceed . "' }),
                                $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                            ],
                            callback: function (value) {
                                if (value) {
                                    $.post('" . APP_URL . SEP . "',
                                    {
                                        _Staticy_REQ_ : 'cpanel/register_cab',
                                        user_id : " . data::$args[0] . ",
                                        fullname : data.fullname,
                                        email : data.email,
                                        gender : data.gender
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

        $('button.downgrade_cab-btn').click(function() {
            vex.dialog.confirm({
                message: '" . str::do_downgrade_cab_to_passenger . "',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::yup . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                ],
                callback: function (value) {
                    if (value) {
                        $.post('" . APP_URL . SEP . "',
                        {
                            _Staticy_REQ_ : 'cpanel/downgrade_cab',
                            user_id : " . data::$args[0] . "
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

        $('button.fill_credit-btn').click(function() {
            vex.dialog.open({
                message: '" . str::input_amount_to_fill_in_ . str::CD . ":',
                input: [
                    '<input name=\'credit\' type=\'number\' step=\'0.01\' placeholder=\' 0.00\' required />',
                    '<input name=\'fill_type\' type=\'radio\' value=\'balance\' required /> " . str::discount_credit . " ',
                    '<input name=\'fill_type\' type=\'radio\' value=\'credit\' required /> " . str::operate_credit . " '
                ].join(''),
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::next . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::back . "' })
                ],
                callback: function (data) {
                    if (data) {
                        var fill_type_stirng = (data.fill_type=='balance')? '" . str::discount_credit . "' : '" . str::operate_credit . "';
                        vex.dialog.confirm({
                            message: '" . str::you_about_filling_account_by_ . "'+fill_type_stirng+' " . str::equal . " '+data.credit+' " . str::CD . str::comma . "'+' " . str::do_process . "',
                            buttons: [
                                $.extend({}, vex.dialog.buttons.YES, { text: '" . str::proceed . "' }),
                                $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                            ],
                            callback: function (value) {
                                if (value) {
                                    $.post('" . APP_URL . SEP . "',
                                    {
                                        _Staticy_REQ_ : 'cpanel/fill_credit',
                                        user_id : " . data::$args[0] . ",
                                        credit : data.credit,
                                        fill_type : data.fill_type
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

        $('button.edit_user-btn').click(function() {
            vex.dialog.open({
                message: '" . str::edit_user_data . ":',
                input: [
                    '<input name=\'phone\' type=\'text\' dir=\'ltr\' value=\'" . $user_data['Phone'] . "\' maxlength=20 placeholder=\' " . str::phone_number . "\' required />',
                    '<input name=\'fullname\' type=\'text\' value=\'" . $user_data['Fullname'] . "\' placeholder=\' " . str::fullname4th . "\' required />',
                    '<input name=\'email\' type=\'email\' value=\'" . $user_data['Email'] . "\' maxlength=100 placeholder=\' " . str::email_optinal . "\' />',
                    '<input name=\'gender\' type=\'radio\' value=\'M\' required /> " . str::male . " ',
                    '<input name=\'gender\' type=\'radio\' value=\'F\' required /> " . str::female . " ',
                    '<input name=\'gender\' type=\'radio\' value=\'X\' required /> " . str::undefined . " '
                ].join(''),
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::next . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::back . "' })
                ],
                callback: function (data) {
                    if (data) {
                        vex.dialog.confirm({
                            message: '" . str::you_about_edit_user_data . str::comma . " " . str::r_u_sure . "',
                            buttons: [
                                $.extend({}, vex.dialog.buttons.YES, { text: '" . str::yup_comma_do_edit_data . "' }),
                                $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                            ],
                            callback: function (value) {
                                if (value) {
                                    $.post('" . APP_URL . SEP . "',
                                    {
                                        _Staticy_REQ_ : 'cpanel/edit_user',
                                        user_id : " . data::$args[0] . ",
                                        phone: data.phone,
                                        fullname : data.fullname,
                                        email : data.email,
                                        gender : data.gender
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

        $('button.show_update_avatar_panel').click(function() {
            $('#update_avatar_panel').show()
        });

        $('button.hide_update_avatar_panel-btn').click(function() {
            $('#update_avatar_panel').hide()
        });
    });
");

load::view('cpanel/common/header');
tag::a(cd('cpanel/users'), str::app_users)->sufln(2);

$alert = load::$app->alert(data::ifsession('msg'), str::alert_cpanel_user_f);

tag::div('row')->has([
    tag::div('col-sm-3'),
    tag::div('col-sm-6')->has($alert),
    tag::div('col-sm-3')
]);

$vehicle_panel = null;
$vehicle_location_map = null;

if ($user_data['Form_SN'] != 0) {
    tag::button(str::downgrade_user_to_passenger)->set(['type' => 'button', 'class' => 'btn btn-xs downgrade_cab-btn'])->sufln(2);

    $vehicle_panel = tag::div('panel')->setClass('panel-default')->has([
        tag::div('panel-heading')->has(
            tag::p(str::vehicle_data)->setClass('panel-title')
        ),
        tag::div('panel-body')->has([
            tag::form('cpanel/link_vehicle')->has([
                tag::label_followedBy(str::classification, tag::select(load::$app->vehicles_classes, $vehicle_data['Class'])->prehas(tag::option('----'))->setName('Class')->setClass('form-control')->flag('required')),
                tag::label_followedBy(str::brand_dash_model, tag::input_text($vehicle_data['Model'])->setName('Model')->set('placeholder', str::ex_toyota_corolla)->setClass('form-control')->flag('required')),
                tag::label_followedBy(str::external_color, tag::input_color($vehicle_data['Color'])->setName('Color')->setClass('form-control')->flag('required')),
                tag::label_followedBy(str::licence_plate_number, tag::input_number($vehicle_data['Plate'])->setName('Plate')->setClass('form-control')->flag('required')),
                tag::input_hidden($vehicle_data['Vehicle_ID'])->setName('Vehicle_ID'),
                tag::input_hidden(data::$args[0])->setName('Driver_ID'),
                tag::input_submit(str::register_veh_data)->preln()->setStyle('background-color: gray')
            ])
        ])
    ]);

    if ($vehicle_data['Vehicle_ID'] != 0) {
        $vehicle_location_map = tag::div()
            ->setID('map')
            ->setStyle('height: 400px; width: 100%;')
            ->sufln();

        tag::$view->script("
            function initMap() {
                var uluru = {lat: " . $vehicle_data['LAT'] . ", lng: " . $vehicle_data['LNG'] . "};
                var map = new google.maps.Map(document.getElementById('map'), {
                  zoom: 15,
                  center: uluru
                });
                var marker = new google.maps.Marker({
                  position: uluru,
                  map: map
                });
            }
        ");
        tag::$view->script(null, [
            'src' => 'https://maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAPS_KEY . '&callback=initMap'
        ]);
    }
} else {
    tag::button(str::upgrade_this_user_to_cab)->set(['type' => 'button', 'class' => 'btn btn-success btn-md register_cab-btn'])->sufln(2);
}

tag::span(str::user_account . ' ' . printActivationStatus($user_data['Active_Until']));
tag::a(cd('cpanel/user/' . data::$args[0] . '/subscriptions'), str::subscription_record)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button'])->presp()->sufln(1);

if ($user_data['Form_SN'] != 0 && $user_data['Type'] == 0) {
    $type_string = str::cab_mode_0;
} else {
    $type_string = load::unit('models/assets', 'get_users_types')[$user_data['Type']]['string'];
}
$form_desc_line = null;
if ($user_data['Form_SN'] > 0) {
    $form_desc_line = tag::p(str::registration_form_number . ': ' . tag::a(cd('cpanel/driverform/' . $user_data['Form_SN']), $user_data['Form_SN']));
} else if ($user_data['Form_SN'] < 0) {
    $form_desc_line = tag::p(str::no_registration_form . ' ' . tag::a(cd('driverform') . '&vals=userid/' . data::$args[0], str::add_form)->set(['class' => 'btn btn-xs btn-default', 'role' => 'button']));
} else {
    $form_desc_line = null;
}

$div_user_body1 = tag::div('col-sm-4')->setStyle('float: right')->has(
    tag::a($user_data['Logo'], tag::img($user_data['Logo'] == '' ? '~img/avatar.jpg' : $user_data['Logo'], 200, 200))->sufln(2)
);

$div_user_body2 = tag::div('col-sm-4')->setStyle('float: right')->has([

    tag::p(tag::b($user_data['Phone']))->setStyle("font-family: 'Overpass Mono', monospace;"),
    tag::p($user_data['Email']),
    tag::p(str::gender . ': ' . load::unit('models/assets', 'get_users_genders')[$user_data['Gender']]),
    tag::p(str::registered_since . ' ' . date('d-m-Y', strtotime($user_data['Registration']))),
    tag::p(str::app_version . ' ' . ($user_data['P_VC'] != '' ? $user_data['P_VC'] : 'N/A')),
    tag::p(str::last_conn . ' ' . date('d-m-Y', strtotime($user_data['Modification'])) . ' ' . str::at . ' ' . date('H:i', strtotime($user_data['Modification']))),
    $form_desc_line,
    tag::button(str::edit_user_data)->set(['type' => 'button', 'class' => 'btn btn-primary btn-xs edit_user-btn'])->setStyle('background-color: gray; color: white')->sufsp(),
    tag::button(str::update_personal_pic)->set(['type' => 'button', 'class' => 'btn btn-primary btn-xs show_update_avatar_panel'])->setStyle('background-color: gray; color: white')->sufln(2),

    tag::div('panel')->setClass('panel-default')->setID('update_avatar_panel')->setStyle('display: none')->has([
        tag::div('panel-heading')->has([
            tag::h6(str::pick_new_personal_pic)->setClass('panel-title')
        ]),
        tag::div('panel-body')->has([
            tag::form('cpanel/edit_userlogo')->set('enctype', 'multipart/form-data')->has([
                tag::input_hidden(data::$args[0])->setName('user_id'),
                tag::input_file()->setName('logo')->flag('required')->sufln(),
                tag::input_submit(str::upload_pic)->sufsp(),
                tag::button(str::cancel)->set(['type' => 'button', 'class' => 'btn btn-default hide_update_avatar_panel-btn'])
            ])
        ])
    ]),

    $vehicle_location_map

]);

$div_user_body3 = tag::div('col-sm-4')->setStyle('float: right')->has(
    $vehicle_panel
);

tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3([
            tag::b($user_data['Fullname'])->setStyle("font-family: 'jazeera';")->sufsp(),
            tag::span('# ' . $type_string)
                ->setClass('label')
                ->setClass(load::unit('models/assets', 'get_users_types')[$user_data['Type']]['style'])
        ])->setClass('panel-title'),
        tag::span(str::the_discount_credit . ' ' . tag::code(_num_format($user_data['Balance']) . ' ' . str::C))->sufsp(),
        tag::span(str::the_operate_credit . ' ' . tag::code(_num_format($user_data['Credit']) . ' ' . str::C))->sufsp(),
        tag::button(str::fill_account_credit)->set(['type' => 'button', 'class' => 'btn btn-default btn-xs fill_credit-btn'])->setStyle('background-color: pink; color: black')
    ]),
    tag::div('panel-body')->has([
        tag::div('row')->has([
            (str::DIR == 'rtl' ? $div_user_body1 : $div_user_body3),
            (str::DIR == 'rtl' ? $div_user_body2 : $div_user_body2),
            (str::DIR == 'rtl' ? $div_user_body3 : $div_user_body1)
        ]),
    ])
]);

if (count($trips_data)) {
    $trips_snap = tag::table(data::val('trips_data'), [str::numeric, str::status, str::_class, str::order_time, str::user_desc]);
} else {
    $trips_snap = tag::p(str::no_trips);
}
tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h1(tag::b(str::last_related_trips))->setClass('panel-title')
    ]),
    tag::div('panel-body')->has(
        $trips_snap
    )
]);
