<?php

define('CPANEL_VERSION_CODE', 'CPanel V3.6.30');

class CPanel extends Staticy_Unit
{
    public $_sfw_dont_log = ['login', 'logout', 'main'];

    function nfs()
    {
        load::lib('str', 'str/cpanel_' . data::ifcookie('lang', DEFAULT_UI_LANG));
    }

    function onStart()
    {
        if ((data::$action != 'login') && !auth::cpoint(4096, AUTH::OPOR)) {
            dirto(cd('cpanel/login'));
        }
    }

    function login()
    {
        load::view();
    }

    function logout()
    {
        auth::logout(cd('cpanel/login'));
    }

    function main($args)
    {
        data::$temp['subject'] = str::the_cpanel;
        load::view();
    }

    function search()
    {
        data::$temp['subject'] = str::search_results;
        load::view();
    }

    function settings()
    {
        db::get('system_vars', ['RADAR_SCOPE']);
        data::val('system_vars_data', db::res()[0]);

        data::$temp['subject'] = str::settings;
        load::view();
    }

    function overview()
    {
        $count_users = db::link()->count('users');
        $count_drivers = db::link()->count(
            'users',
            ['[>]vehicles' => ['User_ID' => 'Driver_ID']],
            [
                'users.User_ID'
            ],
            [
                'users.Form_SN[!]' => 0,
                'vehicles.isLinked' => 1
            ]
        );
        $count_5min_drivers = db::link()->count('vehicles', ['[>]users' => ['Driver_ID' => 'User_ID']], [
            'vehicles.Vehicle_ID'
        ], [
            'AND' => [
                'users.Type' => 2,
                'vehicles.Modification[>=]' => date('Y-m-d H:i:s', strtotime(MAP_UPDATE_TIMER)),
                'vehicles.isLinked' => 1
            ]
        ]);
        data::val('users_info', [$count_users, $count_drivers, $count_5min_drivers]);

        $count_vehicles = db::link()->count('vehicles', ['isLinked' => 1]);
        db::freeget("SELECT Class, COUNT(Class) AS Veh_Count, ROUND(COUNT(Class)/$count_vehicles*100, 2) AS Veh_Pers FROM vehicles WHERE isLinked = 1 GROUP BY Class ORDER BY Veh_Pers DESC");
        data::val('vehicles_info', [db::res(), $count_vehicles]);

        $requested_trips = db::link()->count('trips');
        $accepted_trips = db::link()->count('trips', ['Status[<>]' => [1, 5], 'Driver_ID[!]' => 0]);
        $accepted_pers = ($requested_trips != 0) ? round($accepted_trips / $requested_trips * 100, 2) : 0;
        $accepted_pers .= ' %';
        $active_trips = db::link()->count('trips', ['Status[<>]' => [1, 3]]);
        data::val('trips_info', [$requested_trips, $accepted_trips, $accepted_pers, $active_trips]);

        db::freeget("SELECT P_VC, ROUND(COUNT(P_VC)/$count_users*100, 2) AS Ver_Pers FROM users GROUP BY P_VC ORDER BY Ver_Pers DESC");
        $versions_data = array();
        foreach (db::res() as $key => $row) {
            if ($row['P_VC'] == '') {
                $versions_data[$key]['P_VC'] = 'UNDEFINED';
            } else {
                $versions_data[$key]['P_VC'] = $row['P_VC'];
            }
            $versions_data[$key]['Ver_Pers'] = $row['Ver_Pers'];
        }
        data::val('versions_data', $versions_data);

        data::$temp['subject'] = str::overview;
        load::view();
    }

    function driversforms()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(1, AUTH::OPOR, true);

        db::get('driversforms', [
            'Form_SN',
            'Fullname',
            'Phone',
            'IP'
        ], [
            'isAccepted' => 0,
            'ORDER' => ['Form_SN' => 'ASC']
        ]);

        $editor = data::editor(db::res());
        $editor->field('Fullname', tag::a(cd('cpanel/driverform/|Form_SN|'), '|Fullname|'));
        data::val('drivers_forms', $editor->getData());

        data::$temp['subject'] = str::registration_form;
        load::view();
    }

    function registeredcaps()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(8, AUTH::OPOR, true);

        $vclass = data::val('vclass', data::ifval('vclass', 'ALL'));
        tag::initPagination(
            db::link()->count(
                'users',
                ['[>]vehicles' => ['User_ID' => 'Driver_ID']],
                [
                    'users.User_ID'
                ],
                [
                    'users.Form_SN[!]' => 0,
                    'vehicles.Class' . ($vclass == 'ALL' ? '[!~]' : null) => $vclass,
                    'vehicles.isLinked' => 1
                ]
            ),
            ['rows', 'page', 'vclass' => $vclass]
        );
        db::get('users', ['[>]vehicles' => ['User_ID' => 'Driver_ID']], [
            'users.User_ID',
            'users.Form_SN(_Form_SN)',
            'users.Type',
            'users.Fullname',
            'users.Phone',
            'vehicles.Model',
            'vehicles.Plate'
        ], [
            'users.Form_SN[!]' => 0,
            'vehicles.Class' . ($vclass == 'ALL' ? '[!~]' : null) => $vclass,
            'vehicles.isLinked' => 1,
            'ORDER' => ['users.User_ID' => 'ASC'],
            'LIMIT' => tag::$pagination->limit()
        ]);

        $editor = data::editor(db::res());
        $editor->field('Fullname', tag::a(cd('cpanel/user/|User_ID|'), '|Fullname|'));
        $captains_data = $editor->getData();

        foreach ($captains_data as $key => $user) {
            if ($user['_Form_SN'] != 0 && $user['Type'] == 0) {
                $captains_data[$key]['Type'] = str::cab_mode_0;
            } else {
                $captains_data[$key]['Type'] = load::unit('models/assets', 'get_users_types')[$user['Type']]['string'];
            }
        }

        data::val('captains_data', $captains_data);

        data::$temp['subject'] = str::registered_cabs;
        load::view();
    }

    /**
     * @todo
     */
    function novehiclescaps()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(8, AUTH::OPOR, true);

        db::get('vehicles', ['[>]users' => ['User_ID' => 'Driver_ID']], [
            'users.User_ID',
            'users.Form_SN(_Form_SN)',
            'users.Type',
            'users.Fullname',
            'users.Phone'
        ], [
            'users.Form_SN[!]' => 0,
            'vehicles.isLinked' => 1,
            'ORDER' => ['users.User_ID' => 'ASC']
        ]);

        $editor = data::editor(db::res());
        $editor->field('Fullname', tag::a(cd('cpanel/user/|User_ID|'), '|Fullname|'));
        $captains_data = $editor->getData();

        foreach ($captains_data as $key => $user) {
            if ($user['_Form_SN'] != 0 && $user['Type'] == 0) {
                $captains_data[$key]['Type'] = str::cab_mode_0;
            } else {
                $captains_data[$key]['Type'] = load::unit('models/assets', 'get_users_types')[$user['Type']]['string'];
            }
        }

        data::val('captains_data', $captains_data);

        data::$temp['subject'] = 'novehiclescaps';
        load::view();
    }

    function capsworking()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(8, AUTH::OPOR, true);

        db::get('vehicles', ['[>]users' => ['Driver_ID' => 'User_ID']], [
            'users.User_ID',
            'users.Form_SN(_Form_SN)',
            'users.Type',
            'users.Fullname',
            'users.Phone'
        ], [
            'AND' => [
                'users.Type' => 2,
                'vehicles.Modification[>=]' => date('Y-m-d H:i:s', strtotime(MAP_UPDATE_TIMER)),
                'vehicles.isLinked' => 1
            ],
            'ORDER' => ['users.User_ID' => 'DESC']
        ]);
        $count_5min_drivers = count(db::res());

        $editor = data::editor(db::res());
        $editor->field('Fullname', tag::a(cd('cpanel/user/|User_ID|'), '|Fullname|'));
        $caps_data = $editor->getData();

        foreach ($caps_data as $key => $user) {
            if ($user['_Form_SN'] != 0 && $user['Type'] == 0) {
                $caps_data[$key]['Type'] = str::cab_mode_0;
            } else {
                $caps_data[$key]['Type'] = load::unit('models/assets', 'get_users_types')[$user['Type']]['string'];
            }
        }
        data::val('last_caps_data', [$count_5min_drivers, $caps_data]);

        $count_1day_drivers = db::link()->count('users', [
            'Form_SN[!]' => 0,
            'Modification[>=]' => date('Y-m-d H:i:s', strtotime('-1 Day')),
            'ORDER' => ['User_ID' => 'DESC']
        ]);
        data::val('count_1day_drivers', $count_1day_drivers);

        data::$temp['subject'] = str::cabs_working_now;
        load::view();
    }

    function tripsserving()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(256, AUTH::OPOR, true);

        db::get('trips', [
            'Trip_SN',
            'Status',
            'Class',
            'Start'
        ], [
            'Status[<>]' => [1, 3],
            'ORDER' => ['Trip_SN' => 'DESC']
        ]);
        $count_active = count(db::res());

        $trips_data = array();
        foreach (db::res() as $key => $trip) {
            $trips_data[$key]['Trip_SN'] = tag::a(cd('cpanel/trip/' . $trip['Trip_SN']), $trip['Trip_SN']);
            $trips_data[$key]['Status'] = load::unit('models/assets', 'get_trips_status')[$trip['Status']];
            $trips_data[$key]['Class'] = $trip['Class'];
            $trips_data[$key]['Start'] = $trip['Start'];
        }
        data::val('tripsserving_data', [$count_active, $trips_data]);

        data::$temp['subject'] = str::trips_serving_now;
        load::view();
    }

    function driverform($args)
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(1, AUTH::OPOR, true);

        db::get('driversforms', '*', ['Form_SN' => $args[0]]);

        data::val('isAccepted', db::res()[0]['isAccepted']);

        $editor = data::editor(db::res());
        $editor->remove('Form_SN');
        $editor->remove('IP');
        $editor->remove('PHPSESSID');
        $editor->remove('Agent');
        $editor->remove('isAccepted');
        data::val('driver_form', $editor->getData()[0]);

        db::get('users', ['User_ID'], ['Form_SN' => $args[0]]);
        data::val('user_id', db::so() ? db::res()[0]['User_ID'] : 0);

        data::$temp['subject'] = str::form_number . ' ' . $args[0];
        load::view();
    }

    function users()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(8, AUTH::OPOR, true);

        tag::initPagination(
            db::link()->count('users'),
            ['rows', 'page']
        );
        db::get('users', [
            'User_ID',
            'Form_SN(_Form_SN)',
            'Type',
            'Fullname',
            'Phone'
        ], [
            'ORDER' => ['User_ID' => 'ASC'],
            'LIMIT' => tag::$pagination->limit()
        ]);

        $editor = data::editor(db::res());
        $editor->field('Fullname', tag::a(cd('cpanel/user/|User_ID|'), '|Fullname|'));
        $users_data = $editor->getData();

        foreach ($users_data as $key => $user) {
            if ($user['_Form_SN'] != 0 && $user['Type'] == 0) {
                $users_data[$key]['Type'] = str::cab_mode_0;
            } else {
                $users_data[$key]['Type'] = load::unit('models/assets', 'get_users_types')[$user['Type']]['string'];
            }
        }

        $count_drivers = db::link()->count(
            'users',
            ['[>]vehicles' => ['User_ID' => 'Driver_ID']],
            [
                'users.User_ID'
            ],
            [
                'users.Form_SN[!]' => 0,
                'vehicles.isLinked' => 1
            ]
        );
        $count_5min_drivers = db::link()->count('vehicles', ['[>]users' => ['Driver_ID' => 'User_ID']], [
            'vehicles.Vehicle_ID'
        ], [
            'AND' => [
                'users.Type' => 2,
                'vehicles.Modification[>=]' => date('Y-m-d H:i:s', strtotime(MAP_UPDATE_TIMER)),
                'vehicles.isLinked' => 1
            ]
        ]);

        data::val('count_drivers', $count_drivers);
        data::val('count_5min_drivers', $count_5min_drivers);
        data::val('users_data', $users_data);

        data::$temp['subject'] = str::app_users;
        load::view();
    }

    function user($args)
    {
        if (isset($args[1]) && $args[1] == 'subscriptions') {
            if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(1048576, AUTH::OPOR, true);

            db::get('subscriptions', [
                'Days',
                'Payed',
                'Payment_Date',
                'Expire_Date'
            ], [
                'User_ID' => $args[0],
                'ORDER' => ['Sub_SN' => 'DESC']
            ]);

            $editor = data::editor(db::res());
            $editor->field('Days', '|Days| ' . str::day);
            $editor->field('Payed', '|Payed| ' . str::C);
            data::val('subscriptions_data', $editor->getData());

            $payed_sum = db::link()->sum('subscriptions', ['Payed'], ['User_ID' => $args[0]]);
            if ($payed_sum == null) $payed_sum = 0;
            data::val('payed_sum', $payed_sum);

            data::$temp['subject'] = str::user_subscription_record . ' ' . $args[0];
            load::view('cpanel/user_subscriptions');
            return;
        }

        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(8, AUTH::OPOR, true);

        db::get('users', [
            'Form_SN',
            'Active_Until',
            'Type',
            'Phone',
            'Balance',
            'Credit',
            'Fullname',
            'Gender',
            'Email',
            'Logo',
            'Registration',
            'Modification',
            'P_VC'
        ], [
            'User_ID' => $args[0]
        ]);
        data::val('user_data', db::res()[0]);

        data::val('vehicle_data', [
            'Vehicle_ID' => 0,
            'Class' => null,
            'Model' => null,
            'Color' => null,
            'Plate' => null,
            'LAT'     => 0,
            'LNG'     => 0
        ]);
        if (db::res()[0]['Form_SN'] != 0) {
            db::get('vehicles', [
                'Vehicle_ID',
                'Class',
                'Model',
                'Color',
                'Plate',
                'LAT',
                'LNG'
            ], [
                'AND' => [
                    'Driver_ID' => $args[0],
                    'IsLinked' => 1
                ],
                'LIMIT' => 1
            ]);
            if (db::so()) {
                data::val('vehicle_data', db::res()[0]);
            }
        }

        db::get('trips', [
            'Trip_SN',
            'Status',
            'Class',
            'Start',
            'Driver_ID'
        ], [
            'OR' => [
                'Driver_ID' => $args[0],
                'Passenger_ID' => $args[0]
            ],
            'ORDER' => ['Trip_SN' => 'DESC'],
            'LIMIT' => 30
        ]);
        $trips_data = array();
        foreach (db::res() as $key => $trip) {
            $trips_data[$key]['Trip_SN'] = tag::a(cd('cpanel/trip/' . $trip['Trip_SN']), $trip['Trip_SN']);
            $trips_data[$key]['Status'] = load::unit('models/assets', 'get_trips_status')[$trip['Status']];
            $trips_data[$key]['Class'] = $trip['Class'];
            $trips_data[$key]['Start'] = $trip['Start'];
            $trips_data[$key]['user_description'] = ($trip['Driver_ID'] == $args[0]) ? str::captain : str::passenger;
        }
        data::val('trips_data', $trips_data);

        data::$temp['subject'] = str::user_number . ' ' . $args[0];
        load::view();
    }

    function trips()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(256, AUTH::OPOR, true);

        tag::initPagination(
            db::link()->count('trips', ['Status[<>]' => [1, 5]]),
            ['rows', 'page']
        );
        db::get('trips', [
            'Trip_SN',
            'Status',
            'Class',
            'Start'
        ], [
            'Status[<>]' => [1, 5],
            'ORDER' => ['Trip_SN' => 'DESC'],
            'LIMIT' => tag::$pagination->limit()
        ]);

        $trips_data = array();
        foreach (db::res() as $key => $trip) {
            $trips_data[$key]['Trip_SN'] = tag::a(cd('cpanel/trip/' . $trip['Trip_SN']), $trip['Trip_SN']);
            $trips_data[$key]['Status'] = load::unit('models/assets', 'get_trips_status')[$trip['Status']];
            $trips_data[$key]['Class'] = $trip['Class'];
            $trips_data[$key]['Start'] = $trip['Start'];
        }

        data::val('count_active_trips', db::link()->count('trips', ['Status[<>]' => [1, 3]]));

        data::val('trips_data', $trips_data);

        data::$temp['subject'] = str::passengers_trips;
        load::view();
    }

    function xtrips()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(256, AUTH::OPOR, true);

        tag::initPagination(
            db::link()->count('trips', ['Status[]' => [-1, -2], 'Driver_ID[!]' => 0]),
            ['rows', 'page']
        );
        db::get('trips', [
            'Trip_SN',
            'Status',
            'Class',
            'Start'
        ], [
            'Status[]' => [-1, -2],
            'Driver_ID[!]' => 0,
            'ORDER' => ['Trip_SN' => 'DESC'],
            'LIMIT' => tag::$pagination->limit()
        ]);

        $trips_data = array();
        foreach (db::res() as $key => $trip) {
            $trips_data[$key]['Trip_SN'] = tag::a(cd('cpanel/trip/' . $trip['Trip_SN']), $trip['Trip_SN']);
            $trips_data[$key]['Status'] = load::unit('models/assets', 'get_trips_status')[$trip['Status']];
            $trips_data[$key]['Class'] = $trip['Class'];
            $trips_data[$key]['Start'] = $trip['Start'];
        }

        data::val('trips_data', $trips_data);

        data::$temp['subject'] = str::canceled_trips;
        load::view();
    }

    function trip($args)
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(256, AUTH::OPOR, true);

        db::get('trips', [
            '[>]users(c_user)' => ['Driver_ID' => 'User_ID'],
            '[>]users(p_user)' => ['Passenger_ID' => 'User_ID'],
            '[>]vehicles' => 'Vehicle_ID'
        ], [
            'trips.Status',
            'trips.Class',
            'trips.Pick_Up_Address',
            'trips.Drop_Off_Address',
            'trips.Distance',
            'trips.Duration',
            'trips.Cost',
            'trips.After_Discount',
            'trips.Tax',
            'trips.Note',
            'trips.Start',
            'trips.End',
            'trips.Evaluation',
            'trips.Passenger_Note',
            'trips.Driver_Note',

            'trips.Vehicle_ID',
            'vehicles.Model',
            'vehicles.Plate',
            'vehicles.Class(V_Class)',
            'vehicles.Color',

            'trips.Driver_ID',
            'c_user.Fullname(Driver_Name)',

            'trips.Passenger_ID',
            'p_user.Fullname(Passenger_Name)',

        ], [
            'trips.Trip_SN' => $args[0]
        ]);
        $trip_data = array();
        foreach (db::res()[0] as $key => $value) {
            if ($key == 'Status') {
                $trip_data['Status'] = load::unit('models/assets', 'get_trips_status')[$value];
                continue;
            }

            if ($key == 'Evaluation' && db::res()[0]['Driver_ID'] == 0) continue;
            if ($key == 'Passenger_Note' && db::res()[0]['Driver_ID'] == 0) continue;

            if (
                $key == 'V_Class' ||
                $key == 'Model' ||
                $key == 'Plate' ||
                $key == 'Color'
            ) {
                if (db::res()[0]['Driver_ID'] == 0) continue;

                if (!isset($trip_data['Vehicle_Details'])) $trip_data['Vehicle_Details'] = '';

                if ($key == 'V_Class') {
                    $trip_data['Vehicle_Details'] .= load::$app->vehicles_classes[strtoupper($value)] . ' ';
                } else if ($key == 'Color') {
                    $trip_data['Vehicle_Details'] .= tag::span('&nbsp;&nbsp;&nbsp;&nbsp;')->setStyle('background-color: ' . $value . '; border: 1px solid black;');
                } else {
                    $trip_data['Vehicle_Details'] .= $value . ' ';
                }

                continue;
            }

            if ($key == 'Driver_Note' && db::res()[0]['Driver_ID'] == 0) continue;

            if (
                $key == 'Driver_Name' ||
                $key == 'Passenger_Name'
            ) {
                if ($key == 'Driver_Name' && db::res()[0]['Driver_ID'] == 0) continue;

                $id = ($key == 'Driver_Name') ? db::res()[0]['Driver_ID'] : db::res()[0]['Passenger_ID'];
                $trip_data[$key] = tag::a(cd('cpanel/user/' . $id), $value);
                continue;
            }

            if (
                $key == 'Vehicle_ID' ||
                $key == 'Driver_ID' ||
                $key == 'Passenger_ID'
            ) {
                continue;
            }

            $trip_data[$key] = $value;
        }
        data::val('trip_data', $trip_data);

        data::$temp['subject'] = str::trip_number . ' ' . $args[0];
        load::view();
    }

    function coupons()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(512, AUTH::OPOR, true);

        db::get('coupons', [
            'Coupon_Code',
            'Coupon_Value',
            'Coupon_Amount',
            'Used_Amount',
            'Expire_Date',
            'Deleted(_Deleted)'
        ], [
            'ORDER' => ['Coupon_SN' => 'DESC']
        ]);

        $editor = data::editor(db::res());
        $editor->field('Coupon_Value', '|Coupon_Value| ' . str::C);
        $editor->field('Coupon_Amount', '|Coupon_Amount| ' . str::coupon);
        $coupons_data = $editor->getData();

        foreach ($coupons_data as $key => $value) {
            if ($value['_Deleted'] == 0) {
                $coupons_data[$key]['Coupon_Code'] .= tag::button(str::delete)->setID($value['Coupon_Code'])->set(['type' => 'button', 'class' => 'btn btn-xs cancel_coupon-btn'])->presp();
            } else {
                $coupons_data[$key]['Coupon_Code'] .= tag::code(str::deleted)->presp();
            }
        }
        data::val('coupons_data', $coupons_data);

        data::$temp['subject'] = str::discount_coupons;
        load::view();
    }

    function prices()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(16384, AUTH::OPOR, true);

        if (db::link()->count('prices') == 0) {
            foreach (load::$app->vehicles_classes as $key => $title) {
                db::put('prices', ['Class' => $key, 'Period' => 'M']);
                db::put('prices', ['Class' => $key, 'Period' => 'A']);
                db::put('prices', ['Class' => $key, 'Period' => 'N']);
            }
            db::set('prices', ['Fixed' => 30, 'Kilo' => 5.5, 'Tax' => 0]);
        }

        db::get('prices', ['Class', 'Period', 'Fixed', 'Kilo', 'Tax']);
        data::val('prices_data', db::res());

        data::$temp['subject'] = str::pricing;
        load::view();
    }

    function broadcast()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) auth::cpoint(65536, AUTH::OPOR, true);

        data::$temp['subject'] = str::broadcast;
        load::view();
    }

    function sysusers()
    {
        auth::cpoint(8192, AUTH::OPAND, true);

        db::get('system_users', [
            'User_No',
            'Username',
            'Fullname'
        ], [
            'User_No[!]' => 1,
            'ORDER' => ['User_No' => 'DESC']
        ]);

        $editor = data::editor(db::res());
        $editor->field('Fullname', tag::a(cd('cpanel/sysuser/|User_No|'), '|Fullname|'));
        $editor->remove('User_No');
        data::val('sysusers_data', $editor->getData());

        data::$temp['subject'] = str::sys_employees;
        load::view();
    }

    function sysuser($args)
    {
        auth::cpoint(8192, AUTH::OPAND, true);
        if ($args[0] == 1) dirto(cd('cpanel/sysusers'));

        db::get('system_users', [
            'Username',
            'Permissions',
            'Fullname',
            'Join_Date'
        ], [
            'User_No' => $args[0]
        ]);
        data::val('sysuser_data', db::res()[0]);

        $perm_array = array();
        $permissions = array();
        $n = db::res()[0]['Permissions'];
        while ($n != 0) {
            $perm_array[] = ($n % 2 == 0) ? false : true;
            $n >>= 1;
        }
        foreach ($perm_array as $key => $value) {
            if ($value == true) {
                $permissions[] = pow(2, $key);
            }
        }
        data::val('permissions', $permissions);

        data::$temp['subject'] = str::employee_number . ' ' . $args[0];
        load::view();
    }
}
