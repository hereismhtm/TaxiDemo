<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:" + $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
    exit(0);
}

class Mobile_1 extends Staticy_Unit
{
    public $_sfw_dont_log = [
        //'main',
        'connection',
        'vehicles_around',
        'vehicle_location',
        'set_availability',
        'read_availability',
        'read_money',
    ];

    function nfs()
    {
        load::lib('auth', 'mobile_auth');
    }

    function onStart()
    {
        if (!isset($_COOKIE['PHPSESSID']))
            die('PHPSESSID!');

        if ((data::$action != 'main' &&
            data::$action != 'connection' &&
            data::$action != 'registeration' &&
            data::$action != 'send_sms'
        ) && !auth::cpoint(2048, AUTH::OPOR)) {

            if (data::ispost('auth_access')) {

                $auth_access = explode('-', data::post('auth_access'));
                if (auth::login($auth_access[0], $auth_access[1], ['User_ID'])) {
                    data::session('user_id', auth::$fields['User_ID']);
                } else {
                    die('#x# login by POST[auth_access] failed');
                }
            } else {
                die('#x#');
            }
        }
    }

    function main()
    {
        //auth::logout();
        echo 'Taxi Mobile_1 Unit';
    }

    function connection()
    {
        $user_id = data::ifpost('user_id');
        $key = data::ifpost('key');
        $fcm = data::ifpost('fcm');
        if ($user_id == '' || $key == '' || $fcm == '') httpsta(400);

        $json = array();
        if (auth::login($user_id, $key, ['User_ID', 'Type', 'Frozen'])) {

            $user_id = auth::$fields['User_ID'];

            if (auth::$fields['Frozen'] == 1) {
                auth::logout();
                $json['is_login'] = true;
                $json['is_frozen'] = true;
            } else {

                $json['is_login'] = true;
                $json['user_type'] = auth::$fields['Type'];
                $json['trip_data'] = $this->_getLastTripData($user_id);
                $json['prices_data'] = $this->_getPricesData();

                data::session('user_id', $user_id);
                db::set('users', [
                    'KeyPass'         => auth::genSaltedHash($key),
                    'Pass'             => $key,
                    'Modification'     => date('Y-m-d H:i:s'),
                    (data::ifpost('app_mode') == 'C' ? 'CFCM' : 'PFCM') => $fcm,
                    (data::ifpost('app_mode') == 'C' ? 'C_VC' : 'P_VC') => data::ifpost('version_code', '')
                ], [
                    'User_ID' => $user_id
                ]);
            }
        } else {
            $json['is_login'] = false;
            $json['user_type'] = 0;
            $json['trip_data'] = $this->_getLastTripData(null);
        }

        $json['is_updated'] = (LAST_VERSION_CODE == data::ifpost('version_code', ''));
        echo json_encode($json);
    }

    function registeration()
    {
        $phone = data::ifpost('phone');
        $fcm = data::ifpost('fcm');
        if ($phone == '' || $fcm == '') httpsta(400);

        $phone = substr($phone, 4);
        if (ZERO_PREFIX_CUT && strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }
        $phone = '+' . CALLING_CODE . $phone;

        db::get('users', [
            'User_ID', 'Form_SN', 'Type',
            'KeyPass', 'Pass',
            'Fullname', 'Email', 'Logo'
        ], [
            'Phone' => $phone
        ]);

        $json = array();
        $json['prices_data'] = $this->_getPricesData();
        if (db::so()) {

            $user = db::res()[0];
            /*if ($user['Type'] == 3) {
                echo 'Registration denied, a trip in service right now.';
                return;
            }*/

            $json['user_type'] = $user['Type'];
            $json['trip_data'] = $this->_getLastTripData($user['User_ID']);

            $json['is_new'] = false;
            $json['user_id'] = $user['User_ID'];
            $json['key'] = ($user['Pass'] == '') ? hash('SHA1', random_int(100000, 999999)) : $user['Pass'];
            $json['is_cap'] = ($user['Form_SN'] != 0) ? true : false;
            if ($json['is_cap'] == true) {
                $vehicle_class = db::link()->select('vehicles', ['Class'], ['Driver_ID' => $user['User_ID'], 'isLinked' => 1]);
                $json['vehicle_class'] = !empty($vehicle_class) ? $vehicle_class[0]['Class'] : '';
            } else {
                $json['vehicle_class'] = '';
            }
            $json['gui'] = $json['is_cap'] ? 'C' : 'P';
            $json['fullname'] = $user['Fullname'];
            $json['email'] = $user['Email'];
            $json['logo'] = $user['Logo'];

            db::set('users', [
                'KeyPass'         => auth::genSaltedHash($json['key']),
                'Pass'             => $json['key'],
                'Modification'     => date('Y-m-d H:i:s'),
                (data::ifpost('app_mode') == 'C' ? 'CFCM' : 'PFCM') => $fcm,
                (data::ifpost('app_mode') == 'C' ? 'C_VC' : 'P_VC') => data::ifpost('version_code', '')
            ], [
                'User_ID' => $user['User_ID']
            ]);
        } else {

            $json['user_type'] = 0;
            $json['trip_data'] = $this->_getLastTripData(null);

            $json['is_new'] = true;
            $json['key'] = hash('SHA1', random_int(100000, 999999));
            /*!*/
            $json['is_cap'] = false;
            /*!*/
            $json['gui'] = 'P';

            db::put('users', [
                'Form_SN'         => 0,
                'Type'             => 0,
                'Phone'         => $phone,
                'KeyPass'         => auth::genSaltedHash($json['key']),
                'Pass'             => $json['key'],
                'Perm'             => 2048,
                'Fullname'         => '????',
                'Gender'         => 'X',
                'Email'         => '',
                'Logo'             => '',
                'Registration'     => date('Y-m-d H:i:s'),
                'Modification'     => date('Y-m-d H:i:s'),
                (data::ifpost('app_mode') == 'C' ? 'CFCM' : 'PFCM') => $fcm,
                (data::ifpost('app_mode') == 'C' ? 'C_VC' : 'P_VC') => data::ifpost('version_code', '')
            ]);
            $json['user_id'] = db::res();
        }

        auth::login($phone, $json['key'], ['User_ID']);
        data::session('user_id', auth::$fields['User_ID']);

        $json['is_updated'] = (LAST_VERSION_CODE == data::ifpost('version_code', ''));
        echo json_encode($json);
    }

    function vehicle_location()
    {
        $lat = (float) data::ifpost('lat');
        $lng = (float) data::ifpost('lng');
        if ($lat == 0 || $lng == 0) httpsta(400);

        db::set('vehicles', [
            'LAT' => $lat,
            'LNG' => $lng,
            'Modification' => date('Y-m-d H:i:s')
        ], [
            'AND' => [
                'Driver_ID' => data::session('user_id'),
                'isLinked' => 1
            ]
        ]);

        echo db::so() ? '#s#' : '#f#';
    }

    function vehicles_around()
    {
        $class = data::ifpost('class');
        if ($class == '') httpsta(400);
        db::get('vehicles', ['[>]users' => ['Driver_ID' => 'User_ID']], [
            'vehicles.LAT',
            'vehicles.LNG'
        ], [
            'AND' => [
                'users.Frozen' => 0,
                'OR' => [
                    'users.Active_Until' => NULL,
                    'users.Active_Until[>=]' => date('Y-m-d H:i:s')
                ],
                'users.Type' => 2,
                'vehicles.Modification[>=]' => date('Y-m-d H:i:s', strtotime(MAP_UPDATE_TIMER)),
                'vehicles.isLinked' => 1,
                'vehicles.Class' => $class
            ]
        ]);

        echo json_encode(['vehicles' => db::res()]);
    }

    function trip_cost($args)
    {
        return load::unit('models/trip_cost', 'calc', $args);
    }

    function trip_data()
    {
        $class = strtoupper(data::ifpost('class'));
        $pick_up = data::ifpost('pick_up');
        $drop_off = data::ifpost('drop_off');
        if ($class == '' || $pick_up == '' || $drop_off == '') httpsta(400);
        $user_id = data::session('user_id');

        $json = array();
        $json['trip_sn'] = 0;
        $json['trip_cost'] = 0;

        db::get('users', ['Frozen', 'Active_Until'], ['User_ID' => $user_id]);
        $user = db::res()[0];

        if ($user['Frozen'] == 1) {
            $json['cause'] = 'user account frozen';
            echo json_encode($json);
            return;
        }

        if (
            date('Y-m-d', strtotime($user['Active_Until'])) != '1970-01-01' &&
            $user['Active_Until'] < date('Y-m-d H:i:s')
        ) {
            $json['cause'] = 'user account inactivated';
            echo json_encode($json);
            return;
        }

        // enable user to make a new trip even if there is one with state 0
        if (
            db::link()->has('trips', [
                'AND' => [
                    'Status[<>]' => [1, 3],
                    'Passenger_ID' => $user_id
                ]
            ])
        ) {
            $json['cause'] = 'user have active trip';
            echo json_encode($json);
            return;
        }

        $pick_up_coord = explode(',', $pick_up);
        $drop_off_coord = explode(',', $drop_off);
        if (count($pick_up_coord) != 2 || count($drop_off_coord) != 2) {
            $json['cause'] = 'bad coords format';
            echo json_encode($json);
            return;
        }
        if (
            !is_numeric($pick_up_coord[0]) ||
            !is_numeric($pick_up_coord[1]) ||
            !is_numeric($drop_off_coord[0]) ||
            !is_numeric($drop_off_coord[1])
        ) {
            $json['cause'] = 'not numeric coords';
            echo json_encode($json);
            return;
        }

        $coordinates1['lat'] = $pick_up_coord[0];
        $coordinates1['long'] = $pick_up_coord[1];
        $coordinates2['lat'] = $drop_off_coord[0];
        $coordinates2['long'] = $drop_off_coord[1];

        $gtd = $this->_getTripDetails(
            $coordinates1['lat'],
            $coordinates1['long'],
            $coordinates2['lat'],
            $coordinates2['long']
        );
        if (empty($gtd)) {
            $json['cause'] = 'get trip details failed';
            echo json_encode($json);
            return;
        }
        $origin = $gtd['origin'];
        $destination = $gtd['destination'];
        $distance = explode(' ', $gtd['distance'])[0];
        $duration = $gtd['duration'];
        $cost = $this->trip_cost([$class, $distance, false]);
        if ($cost[0] === false) {
            $json['cause'] = 'calculate trip cost failed';
            echo json_encode($json);
            return;
        }

        db::put('trips', [
            'Status'             => 0,
            'Class'             => $class,
            'Pick_Up'             => $pick_up,
            'Drop_Off'             => $drop_off,
            'Pick_Up_Address'     => $origin,
            'Drop_Off_Address'     => $destination,
            'Distance'             => $distance,
            'Duration'             => $duration,
            'Cost'                 => $cost[0],
            'After_Discount'     => $cost[1],
            'New_Balance'         => $cost[2],
            'Tax'                 => $cost[3],
            'Note'                 => data::ifpost('note', ''),
            'Start'             => date('Y-m-d H:i:s'),
            'End'                 => date('Y-m-d H:i:s'),
            'Passenger_ID'         => $user_id
        ]);

        if (db::so()) {
            $json['trip_sn'] = (int) db::res();
            $json['trip_cost'] = $cost[0];
            $json['after_discount'] = $cost[1];
            $this->trip_ping([db::res(), false]);
        }

        echo json_encode($json);
    }

    function trip_ping($args)
    {
        if (!isset($args[0])) httpsta(400);
        $trip_sn = $args[0];
        $output = isset($args[1]) ? $args[1] : true;
        $user_id = data::session('user_id');

        db::get('trips', [
            'Class',
            'Pick_Up',
            'Drop_Off',
            'Pick_Up_Address',
            'Drop_Off_Address',
            'Distance',
            'Cost',
            'After_Discount',
            'Tax',
            'Note'
        ], [
            'AND' => [
                'Trip_SN' => $trip_sn,
                'Status' => 0,
                'Passenger_ID' => $user_id
            ]
        ]);
        if (!db::so()) {
            if ($output) echo json_encode(['keep_searching' => false, 'vehicles_pings' => 0]);
            return;
        }
        $trip = db::res()[0];
        $lat = explode(',', $trip['Pick_Up'])[0];
        $lng = explode(',', $trip['Pick_Up'])[1];

        db::get('vehicles', ['[>]users' => ['Driver_ID' => 'User_ID']], [
            'vehicles.Vehicle_ID',
            'vehicles.Driver_ID',
            'vehicles.LAT',
            'vehicles.LNG'
        ], [
            'AND' => [
                'users.Frozen' => 0,
                'OR' => [
                    'users.Active_Until' => NULL,
                    'users.Active_Until[>=]' => date('Y-m-d H:i:s')
                ],
                'users.Type' => 2,
                'users.Credit[>=]' => $trip['Tax'],
                'vehicles.Modification[>=]' => date('Y-m-d H:i:s', strtotime(MAP_UPDATE_TIMER)),
                'vehicles.isLinked' => 1,
                'OR' => [
                    'vehicles.Class' => $trip['Class'],
                    'vehicles.Swap_Classes' => load::unit(
                        'models/swap_classes',
                        'getArray',
                        [$trip['Class']]
                    )
                ]
            ]
        ]);

        $list = array();
        foreach (db::res() as $key => $vehicle) {
            $list[$key]['Vehicle_ID'] = $vehicle['Vehicle_ID'];
            $list[$key]['Driver_ID'] = $vehicle['Driver_ID'];
            $list[$key]['VD'] = sqrt(pow($lat - $vehicle['LAT'], 2) + pow($lng - $vehicle['LNG'], 2));
        }
        usort($list, function ($a, $b) {
            return $a['VD'] <=> $b['VD'];
        });

        $VEHICLE_DISTANC_MAXLIMIT = 0.01786973636319; // 2.0 Km
        /*$RADAR_SCOPE = 0;
        if (!data::issession('RADAR_SCOPE')) {
            $RADAR_SCOPE = data::session(
                'RADAR_SCOPE',
                db::get_r('system_vars', ['RADAR_SCOPE'])[0]['RADAR_SCOPE']
            );
        }
        switch ($RADAR_SCOPE) {
            case 1:
                $VEHICLE_DISTANC_MAXLIMIT = 0.0085562796366187;
                break;

            case 2:
                $VEHICLE_DISTANC_MAXLIMIT = 0.017549913234259;
                break;

            case 3:
                $VEHICLE_DISTANC_MAXLIMIT = 0.026552444166405;
                break;

            case 4:
                $VEHICLE_DISTANC_MAXLIMIT = 0.035586987460306;
                break;

            case 5:
                $VEHICLE_DISTANC_MAXLIMIT = 0.044600796069353;
                break;
        }*/

        $fcm_tokens_array = array();
        foreach ($list as $vehicle) {

            // stop selecting if vehicle outside radar range
            if ($vehicle['VD'] > $VEHICLE_DISTANC_MAXLIMIT) {
                break;
            }

            $fcm_tokens_array[] = db::link()->select('users', ['CFCM'], ['User_ID' => $vehicle['Driver_ID']])[0]['CFCM'];
        }
        if (!empty($fcm_tokens_array)) {
            db::put('pings', [
                'Vehicles_Count' => count($fcm_tokens_array),
                'Trip_SN' => $trip_sn,
                'Passenger_ID' => $user_id,
                'Sent' => date('Y-m-d H:i:s'),
                'isAccepted' => 0
            ]);

            if (db::so()) {
                load::lib('notify', 'mobile_captain_notify');
                notify::fcm(
                    $fcm_tokens_array,
                    null,
                    [
                        'fcm_data' => [
                            'code'                 => 'TRIP_PING',
                            'for_who'             => 'DRIVER',
                            'psn'                 => db::res(),
                            'trip_sn'             => $trip_sn,
                            'pick_up'             => $trip['Pick_Up'],
                            'drop_off'             => $trip['Drop_Off'],
                            'pick_up_address'     => $trip['Pick_Up_Address'],
                            'drop_off_address'     => $trip['Drop_Off_Address'],
                            'distance'             => $trip['Distance'],
                            'cost'                 => $trip['Cost'],
                            'after_discount'     => $trip['After_Discount'],
                            'note'                 => $trip['Note']
                        ]
                    ],
                    ['priority' => 'high', 'content_available' => true]
                );
            }

            if ($output)
                echo json_encode(['keep_searching' => true, 'vehicles_pings' => count($fcm_tokens_array)]);
        } else {

            if ($output)
                echo json_encode(['keep_searching' => true, 'vehicles_pings' => 0]);
        }
    }

    function ping_answer()
    {
        $psn = data::ifpost('psn');
        if ($psn == '') httpsta(400);
        $driver_id = data::session('user_id');

        db::get('pings', [
            'Trip_SN',
            'Passenger_ID'
        ], [
            'AND' => [
                'PSN' => $psn,
                'isAccepted' => 0
            ]
        ]);

        if (db::so()) {
            $ping = db::res()[0];

            if (data::ifpost('answer') == 'OK') {
                db::set('pings', [
                    'isAccepted' => 1
                ], [
                    'PSN' => $psn,
                    'Passenger_ID[!]' => $driver_id,
                    'isAccepted' => 0
                ]);

                if (db::so()) {

                    db::get('vehicles', ['Vehicle_ID'], ['Driver_ID' => $driver_id, 'isLinked' => 1]);
                    if (!db::so()) {
                        echo '#f#';
                        return;
                    }
                    $vehicle_id = db::res()[0]['Vehicle_ID'];

                    db::get('trips', ['Tax'], ['Trip_SN' => $ping['Trip_SN']]);
                    $tax = db::res()[0]['Tax'];

                    db::set('trips', [
                        'Status' => 1,
                        'Vehicle_ID' => $vehicle_id,
                        'Driver_ID' => $driver_id,
                        'Tax' => load::unit('models/trip_tax', 'calc', [$tax]),
                        'End' => date('Y-m-d H:i:s')
                    ], [
                        'AND' => [
                            'Trip_SN' => $ping['Trip_SN'],
                            'Status' => 0,
                            'Driver_ID' => 0
                        ]
                    ]);

                    if (db::so()) {
                        db::set('users', ['Type' => 3], ['User_ID' => $driver_id]);
                        $fcm_token = db::link()->select('users', ['PFCM'], ['User_ID' => $ping['Passenger_ID']])[0]['PFCM'];
                        load::lib('notify', 'mobile_passenger_notify');
                        notify::fcm(
                            $fcm_token,
                            null,
                            [
                                'fcm_data' => [
                                    'code'                 => 'PING_ANSWER',
                                    'for_who'             => 'PASSENGER',
                                    'accepted_trip_sn'     => $ping['Trip_SN']
                                ]
                            ],
                            ['priority' => 'high', 'content_available' => true]
                        );
                    }
                }
            }
        }

        echo db::so() ? '#s#' : '#f#';
    }

    function trip_event()
    {
        $trip_sn = data::ifpost('trip_sn');
        $event = data::ifpost('event');
        if ($trip_sn == '' || $event == '') httpsta(400);
        $user_id = data::session('user_id');

        db::get('trips', [
            'Status',
            'Driver_ID',
            'Passenger_ID',
            'Cost',
            'After_Discount',
            'New_Balance',
            'Tax'
        ], [
            'Trip_SN' => $trip_sn
        ]);
        if (!db::so()) {
            echo '#f#';
            return;
        }
        $trip = db::res()[0];

        switch ($event) {

            case 'cancel':
                if ($trip['Status'] == -1) {
                    echo '#s#';
                } else {
                    if ($user_id == $trip['Driver_ID']) {
                        db::set('trips', [
                            'Status' => -1,
                            'End' => date('Y-m-d H:i:s'),
                            'Driver_Note' => data::ifpost('cause', '')
                        ], [
                            'AND' => [
                                'Trip_SN' => $trip_sn,
                                'Status[<>]' => [0, 3],
                                'Driver_ID' => $user_id,
                                'End[<=]' => date('Y-m-d H:i:s', strtotime(TRIP_CANCEL_TIMER))
                            ]
                        ]);
                        echo db::so() ? '#s#' : '#f#';
                    } else if ($user_id == $trip['Passenger_ID']) {
                        db::set('trips', [
                            'Status' => -1,
                            'End' => date('Y-m-d H:i:s')
                        ], [
                            'AND' => [
                                'Trip_SN' => $trip_sn,
                                'Status[<>]' => [0, 3],
                                'Passenger_ID' => $user_id
                            ]
                        ]);
                        echo db::so() ? '#s#' : '#f#';
                    } else {
                        echo '#f#';
                        return;
                    }

                    if (db::so() && $trip['Status'] != 0) {

                        $passenger_fcm_token = db::link()->select('users', ['PFCM'], ['User_ID' => $trip['Passenger_ID']])[0]['PFCM'];
                        load::lib('notify', 'mobile_passenger_notify');
                        notify::fcm(
                            $passenger_fcm_token,
                            null,
                            [
                                'fcm_data' => [
                                    'code'             => 'CANCEL_TRIP_EVENT',
                                    'for_who'         => 'PASSENGER',
                                    'trip_sn'         => $trip_sn
                                ]
                            ],
                            ['priority' => 'high', 'content_available' => true]
                        );

                        db::set('users', ['Type' => 2], ['User_ID' => $trip['Driver_ID'], 'Type' => 3]);
                        if (data::ifpost('do_punishment') == 1) {
                            db::set('users', ['Credit[-]' => CANCELLATION_PUNISHMENT], ['User_ID' => $trip['Driver_ID']]);
                        }
                        $driver_fcm_token = db::link()->select('users', ['CFCM'], ['User_ID' => $trip['Driver_ID']])[0]['CFCM'];
                        load::lib('notify', 'mobile_captain_notify');
                        notify::fcm(
                            $driver_fcm_token,
                            null,
                            [
                                'fcm_data' => [
                                    'code'             => 'CANCEL_TRIP_EVENT',
                                    'for_who'         => 'DRIVER',
                                    'trip_sn'         => $trip_sn
                                ]
                            ],
                            ['priority' => 'high', 'content_available' => true]
                        );
                    }
                }
                break;

            case 'waiting':
                if ($trip['Status'] == 2) {
                    echo '#s#';
                } else {
                    db::set('trips', [
                        'Status' => 2,
                        'End' => date('Y-m-d H:i:s')
                    ], [
                        'AND' => [
                            'Trip_SN' => $trip_sn,
                            'Status' => 1,
                            'Driver_ID' => $user_id
                        ]
                    ]);
                    echo db::so() ? '#s#' : '#f#';

                    if (db::so()) {

                        $fcm_token = db::link()->select('users', ['PFCM'], ['User_ID' => $trip['Passenger_ID']])[0]['PFCM'];
                        load::lib('notify', 'mobile_passenger_notify');
                        notify::fcm(
                            $fcm_token,
                            null,
                            [
                                'fcm_data' => [
                                    'code'                 => 'WAITING_TRIP_EVENT',
                                    'for_who'             => 'PASSENGER',
                                    'trip_sn'             => $trip_sn
                                ]
                            ],
                            ['priority' => 'high', 'content_available' => true]
                        );
                    }
                }
                break;

            case 'boarding':
                if ($trip['Status'] == 3) {
                    echo '#s#';
                } else {
                    db::set('trips', [
                        'Status' => 3,
                        'End' => date('Y-m-d H:i:s')
                    ], [
                        'AND' => [
                            'Trip_SN' => $trip_sn,
                            'Status' => 2,
                            'Driver_ID' => $user_id
                        ]
                    ]);
                    echo db::so() ? '#s#' : '#f#';

                    if (db::so()) {

                        $fcm_token = db::link()->select('users', ['PFCM'], ['User_ID' => $trip['Passenger_ID']])[0]['PFCM'];
                        load::lib('notify', 'mobile_passenger_notify');
                        notify::fcm(
                            $fcm_token,
                            null,
                            [
                                'fcm_data' => [
                                    'code'                 => 'BOARDING_TRIP_EVENT',
                                    'for_who'             => 'PASSENGER',
                                    'trip_sn'             => $trip_sn
                                ]
                            ],
                            ['priority' => 'high', 'content_available' => true]
                        );
                    }
                }
                break;

            case 'arriving':
                if ($trip['Status'] == 4) {
                    echo '#s#';
                } else {
                    db::set('trips', [
                        'Status' => 4,
                        'End' => date('Y-m-d H:i:s')
                    ], [
                        'AND' => [
                            'Trip_SN' => $trip_sn,
                            'Status' => 3,
                            'Driver_ID' => $user_id
                        ]
                    ]);
                    echo db::so() ? '#s#' : '#f#';

                    if (db::so()) {

                        db::set('users', [
                            'Type' => 2,
                            'Credit[-]' => $trip['Tax']
                        ], [
                            'User_ID' => $trip['Driver_ID']
                        ]);
                        $bonus = $trip['Cost'] - $trip['After_Discount'];
                        if ($bonus > 0) {
                            db::set('users', [
                                'Credit[+]' => $bonus
                            ], [
                                'User_ID' => $trip['Driver_ID']
                            ]);
                        }

                        db::set('users', [
                            'Balance' => $trip['New_Balance']
                        ], [
                            'User_ID' => $trip['Passenger_ID']
                        ]);
                        $fcm_token = db::link()->select('users', ['PFCM'], ['User_ID' => $trip['Passenger_ID']])[0]['PFCM'];
                        load::lib('notify', 'mobile_passenger_notify');
                        notify::fcm(
                            $fcm_token,
                            null,
                            [
                                'fcm_data' => [
                                    'code'                 => 'ARRIVING_TRIP_EVENT',
                                    'for_who'             => 'PASSENGER',
                                    'trip_sn'             => $trip_sn
                                ]
                            ],
                            ['priority' => 'high', 'content_available' => true]
                        );
                    }
                }
                break;

            case 'finish':
                if ($trip['Status'] == 5) {
                    echo '#s#';
                } else {
                    db::set('trips', [
                        'Status' => 5,
                        'End' => date('Y-m-d H:i:s'),
                        'Driver_Note' => data::ifpost('driver_note', '')
                    ], [
                        'AND' => [
                            'Trip_SN' => $trip_sn,
                            'Status' => 4,
                            'Driver_ID' => $user_id
                        ]
                    ]);
                    echo db::so() ? '#s#' : '#f#';
                }
                break;

            default:
                echo '#f#';
                break;
        }
    }

    function rate_trip()
    {
        $trip_sn = data::ifpost('trip_sn');
        $rating = data::ifpost('rating');
        if ($trip_sn == '' || $rating == '') httpsta(400);

        db::set('trips', [
            'Evaluation'     => $rating
        ], [
            'Trip_SN' => $trip_sn,
            'Passenger_ID' => data::session('user_id')
        ]);

        echo db::so() ? '#s#' : '#f#';
    }

    function send_driver_note()
    {
        $trip_sn = data::ifpost('trip_sn');
        $driver_note = data::ifpost('driver_note');
        if ($trip_sn == '' || $driver_note == '') httpsta(400);
        $driver_id = data::session('user_id');

        db::set('trips', [
            'Driver_Note' => $driver_note
        ], [
            'Trip_SN' => $trip_sn,
            'Driver_ID' => $driver_id
        ]);

        echo db::so() ? '#s#' : '#f#';
    }

    function trip_passenger_data()
    {
        $trip_sn = data::ifpost('trip_sn');
        if ($trip_sn == '') httpsta(400);

        db::get('users', [
            '[>]trips' => ['User_ID' => 'Passenger_ID']
        ], [
            'users.Fullname',
            'users.Phone',
            'users.Logo',
            'trips.Pick_Up',
            'trips.Drop_Off'
        ], [
            'AND' => [
                'trips.Trip_SN' => $trip_sn,
                'trips.Status[<>]' => [1, 4],
                'trips.Driver_ID' => data::session('user_id')
            ]
        ]);

        $json = array();
        if (db::so()) {
            $result = db::res()[0];
            $json['fullname'] = $result['Fullname'];
            $json['phone'] = $result['Phone'];
            $json['logo'] = $result['Logo'];
            $json['pick_up'] = $result['Pick_Up'];
            $json['drop_off'] = $result['Drop_Off'];
        }

        echo json_encode($json);
    }

    function trip_driver_data()
    {
        $trip_sn = data::ifpost('trip_sn');
        if ($trip_sn == '') httpsta(400);

        db::get('users', [
            '[>]trips' => ['User_ID' => 'Driver_ID'],
            '[<]vehicles' => ['Vehicle_ID', 'Driver_ID']
        ], [
            'users.Fullname',
            'users.Phone',
            'users.Logo',
            'vehicles.Model',
            'vehicles.Color',
            'vehicles.Plate',
            'vehicles.LAT',
            'vehicles.LNG'
        ], [
            'AND' => [
                'trips.Trip_SN' => $trip_sn,
                'trips.Status[<>]' => [1, 4],
                'trips.Passenger_ID' => data::session('user_id')
            ]
        ]);

        $json = array();
        if (db::so()) {
            $result = db::res()[0];
            $json['fullname'] = $result['Fullname'];
            $json['phone'] = $result['Phone'];
            $json['logo'] = $result['Logo'];
            $json['model'] = $result['Model'];
            $json['color'] = $result['Color'];
            $json['plate'] = $result['Plate'];
            $json['lat'] = $result['LAT'];
            $json['lng'] = $result['LNG'];
        }

        echo json_encode($json);
    }

    function set_passenger_data()
    {
        $fullname = data::ifpost('fullname');
        if ($fullname == '') httpsta(400);
        $email = data::ifpost('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = '';
        }
        $user_id = data::session('user_id');

        if (db::link()->has('users', ['AND' => ['User_ID' => $user_id, 'Form_SN[!]' => 0]])) {
            echo '#no#';
            return;
        }

        db::set('users', [
            'Fullname' => $fullname,
            'Email' => $email
        ], [
            'User_ID' => $user_id
        ]);

        echo db::so() ? '#s#' : '#f#';
    }

    function set_passenger_logo()
    {
        $logo64 = data::ifpost('logo64');
        if ($logo64 == '') httpsta(400);
        $user_id = data::session('user_id');

        if (db::link()->has('users', ['AND' => ['User_ID' => $user_id, 'Form_SN[!]' => 0]])) {
            echo '#no#';
            return;
        }

        $logo = fs::base64_saveAs($logo64, '~userslogos', 'logo_u' . $user_id . '_' . rand() . '.jpg');
        if (!$logo[0]) {
            echo '#f#';
            return;
        }

        db::set('users', [
            'Logo' => $logo[1]
        ], [
            'User_ID' => $user_id
        ]);

        echo $logo[1];
    }

    function send_complain()
    {
        $name = data::ifpost('name');
        $email = data::ifpost('email');
        $complain = data::ifpost('complain');
        if ($name == '' || $email == '' || $complain == '') httpsta(400);

        $headers = "From: " . TAXI_NAME . " Mobile App <" . NOREPLY_MAIL . ">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";

        mail(
            CONTACT_MAIL,
            TAXI_NAME . ' contact us form',

            "TIME : " . date('Y-m-d H:i:s') .
                "<br/><br/>name : " . $name .
                "<br/>email : " . $email .
                "<br/>user_id : " . data::session('user_id') .
                "<br/><br/>message :<br/>" . $complain,

            $headers
        );

        echo '#s#';
    }

    function use_coupon()
    {
        $code = strtoupper(data::ifpost('code'));
        if ($code == '') httpsta(400);
        $user_id = data::session('user_id');

        if (
            db::link()->has('trips', [
                'AND' => [
                    'Status[<>]' => [1, 3],
                    'Passenger_ID' => $user_id
                ]
            ])
        ) {
            echo json_encode(['success' => false, 'reason' => 'active_trip']);
            return;
        }

        db::get('coupons', [
            'Coupon_SN',
            'Coupon_Value',
            'Coupon_Amount'
        ], [
            'Coupon_Code' => $code,
            'Expire_Date[>=]' => date('Y-m-d')
        ]);
        if (db::so()) {
            $coupon = db::res()[0];

            if (db::link()->has('used_coupons', ['Coupon_SN' => $coupon['Coupon_SN'], 'User_ID' => $user_id])) {
                echo json_encode(['success' => false, 'reason' => 'used_coupon']);
                return;
            }

            if (db::link()->count('used_coupons', ['Coupon_SN' => $coupon['Coupon_SN']]) >= $coupon['Coupon_Amount']) {
                echo json_encode(['success' => false, 'reason' => 'nomore_coupon']);
                return;
            }

            db::put('used_coupons', ['Coupon_SN' => $coupon['Coupon_SN'], 'User_ID' => $user_id, 'Used_Time' => date('Y-m-d H:i:s')]);
            if (db::so()) {
                db::set('coupons', ['Used_Amount[+]' => 1], ['Coupon_SN' => $coupon['Coupon_SN']]);
            }
            db::set('users', ['Balance[+]' => $coupon['Coupon_Value']], ['User_ID' => $user_id]);
            $balance = db::get_r('users', ['Balance'], ['User_ID' => $user_id])[0]['Balance'];

            echo json_encode(['success' => true, 'balance' => $this->_num_format($balance)]);
        } else {
            echo json_encode(['success' => false, 'reason' => 'rejected_coupon']);
        }
    }

    function set_availability()
    {
        $value = data::ifpost('value');
        if ($value == '') httpsta(400);
        $driver_id = data::session('user_id');

        if (is_numeric($value)) {
            $value = abs($value);
        } else {
            httpsta(415);
        }

        if ($value != 1 && $value != 2) httpsta(422);

        db::set('users', [
            'Type' => $value
        ], [
            'User_ID' => $driver_id,
            'Form_SN[!]' => 0,
            'Type[!]' => 3
        ]);

        if (db::get_r('users', ['Type'], ['User_ID' => $driver_id])[0]['Type'] == $value) {
            echo '#s#';
        } else {
            echo '#f#';
        }
    }

    function read_availability()
    {
        $driver_id = data::session('user_id');

        db::get('users', [
            'Type'
        ], [
            'User_ID' => $driver_id,
            'Form_SN[!]' => 0,
        ]);

        echo db::so() ? db::res()[0]['Type'] : '#f#';
    }

    function swap_code()
    {
        $code = data::ifpost('code');
        if ($code == '') httpsta(400);
        $driver_id = data::session('user_id');

        if ($code == 0) { // read vehicle class
            db::get('vehicles', [
                'Class'
            ], [
                'AND' => [
                    'Driver_ID' => $driver_id,
                    'isLinked' => 1
                ]
            ]);

            echo db::so() ? db::res()[0]['Class'] : 'N/A';
            return;
        }

        db::set('vehicles', [
            'Swap_Classes' => $code
        ], [
            'AND' => [
                'Driver_ID' => $driver_id,
                'isLinked' => 1
            ]
        ]);

        echo db::so() ? '#s#' : '#f#';
    }

    function trips_history()
    {
        load::lib('str', 'str/cpanel_' . DEFAULT_UI_LANG);

        $user_id = data::session('user_id');

        db::get('trips', [
            '[<]users(d)' => ['Driver_ID' => 'User_ID'],
            '[<]users(p)' => ['Passenger_ID' => 'User_ID']
        ], [
            'trips.Trip_SN',
            'trips.Status',
            'trips.Pick_Up',
            'trips.Start',
            'trips.Drop_Off',
            'trips.End',
            'trips.Class',
            'trips.After_Discount',
            'd.Fullname(Captain_Fullname)',
            'p.Fullname(Passenger_Fullname)'
        ], [
            'AND' => [
                (data::ifpost('app_mode') == 'C' ? 'trips.Driver_ID' : 'trips.Passenger_ID') => $user_id,
                'trips.Status[!]' => 0
            ],
            'ORDER' => ['Trip_SN' => 'DESC'],
            'LIMIT' => 10
        ]);

        $history_array = array();
        foreach (db::res() as $trip) {
            $trip['Status'] = load::unit('models/assets', 'get_trips_status')[$trip['Status']];
            $history_array[] = $trip;
        }

        echo json_encode(['history' => $history_array]);
    }

    function read_money()
    {
        db::get('users', [
            'Active_Until',
            'Balance',
            'Credit'
        ], [
            'User_ID' => data::session('user_id')
        ]);

        $json = array();
        if (db::so()) {
            $money = db::res()[0];
            $json['subscription'] = $this->_getRemainedSubscriptionMoney($money['Active_Until']);
            $json['balance'] = $this->_num_format($money['Balance']);
            $json['credit'] = $this->_num_format($money['Credit']);
        }

        echo json_encode($json);
    }

    function send_sms()
    {
        load::lib('str', 'str/mobile_' . DEFAULT_UI_LANG);

        $phone = data::ifpost('phone');
        $text = data::ifpost('text');
        if ($phone == '' || $text == '') httpsta(400);

        $phone = substr($phone, 4);
        if (ZERO_PREFIX_CUT && strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }
        $phone = CALLING_CODE . $phone;


        if (data::ifpost('app_mode') == 'C') {
            $ok = true;
            if (db::link()->has('users', ['Phone' => '+' . $phone])) {
                if (db::link()->has('users', ['Phone' => '+' . $phone, 'Form_SN' => 0]))
                    $ok = false;
            } else {
                $ok = false;
            }
            if (!$ok) {
                echo '#z#';
                return;
            }
        }

        $headers = array(
            'Content-Length: 0'
        );
        //$text = random_int(100000, 999999);
        $data = '?';
        $data .= 'user=' . SMS_USERNAME . '&';
        $data .= 'pwd=' . SMS_PASSWORD . '&';
        $data .= 'sender=' . SMS_SENDER . '&';
        $data .= 'smstext=' . rawurlencode(str::your_activation_code_is_ . ' ' . $text) . '&';
        $data .= 'nums=' . $phone;

        $status = load::location('http://196.202.134.90/dsms/webacc.aspx' . $data, null, $headers);

        if ($status[0] == 200 && strpos($status[1], 'OK') !== false) {
            //db::put('sms', ['phone' => $phone, 'text' => $text]);
            echo '#s#';
        } else {
            echo '#f#';
        }
    }




    //----------------

    function _num_format($number)
    {
        return str_replace(',', null, number_format($number, 2));
    }

    function _getRemainedSubscriptionMoney($active_until)
    {
        $expire_date = date('Y-m-d', strtotime($active_until));
        if ($expire_date == '1970-01-01') {
            return "-1";
        }

        db::get('subscriptions', [
            'Days',
            'Payed',
            'Expire_Date'
        ], [
            'User_ID' => data::session('user_id'),
            'Expire_Date[>=]' => date('Y-m-d H:i:s'),
            'ORDER' => ['Sub_SN' => 'ASC']
        ]);

        $remained = (float) 0;
        $first = true;
        foreach (db::res() as $subscription) {

            if ($first) {
                $sec = strtotime($subscription['Expire_Date']) - strtotime(date('Y-m-d H:i:s'));
                $days = (float) ($sec / 86400);
                $dayPrice = (float) ($subscription['Payed'] / $subscription['Days']);
                $remained += (float) ($days * $dayPrice);
            } else {
                $remained += doubleval($subscription['Payed']);
            }

            $first = false;
        }

        return $this->_num_format($remained);
    }

    function _getLastTripData($user_id)
    {
        $last_trip = array();
        if ($user_id != null) {
            $last_trip = db::link()->select(
                'trips',
                [
                    'Trip_SN',
                    'Status',
                    'Pick_Up',
                    'Drop_Off',
                    'After_Discount'
                ],
                [
                    'OR' => [
                        'Driver_ID' => $user_id,
                        'Passenger_ID' => $user_id
                    ],
                    'ORDER' => ['Trip_SN' => 'DESC'],
                    'LIMIT' => 1
                ]
            );
        }

        $j = array();
        $j['trip_sn'] = count($last_trip) ? $last_trip[0]['Trip_SN'] : 0;
        $j['trip_status'] = count($last_trip) ? $last_trip[0]['Status'] : 0;
        $j['pick_up'] = count($last_trip) ? $last_trip[0]['Pick_Up'] : 0;
        $j['drop_off'] = count($last_trip) ? $last_trip[0]['Drop_Off'] : 0;
        $j['after_discount'] = count($last_trip) ? $last_trip[0]['After_Discount'] : 0;

        return $j;
    }

    function _getPricesData()
    {
        return db::link()->select('prices', ['Class', 'Fixed', 'Kilo'], ['Period' => 'M']);
    }

    function _getTripDetails($lat1, $long1, $lat2, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&key=" . GOOGLE_MAPS_KEY . "&mode=driving";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        //echo $response;
        $response_a = json_decode($response, true);
        if ($response_a['status'] != 'OK' || $response_a['rows'][0]['elements'][0]['status'] != 'OK') {
            return array();
        }

        return array(
            'origin' => $response_a['origin_addresses'][0],
            'destination' => $response_a['destination_addresses'][0],
            'distance' => $response_a['rows'][0]['elements'][0]['distance']['text'],
            'duration' => $response_a['rows'][0]['elements'][0]['duration']['text']
        );
    }
}
