<?php

class REQ_Cancel_Trip extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(524288, AUTH::OPOR, true);
        }

        $trip_sn = data::post('trip_sn');

        db::get('trips', ['Status', 'Driver_ID', 'Passenger_ID'], ['Trip_SN' => $trip_sn]);
        if (db::so()) {
            $trip = db::res()[0];

            db::set('trips', [
                'Status' => -2,
                'End' => date('Y-m-d H:i:s')
            ], [
                'Trip_SN' => $trip_sn,
                'Status[<>]' => [0, 3]
            ]);

            if (db::so()) {
                if ($trip['Status'] != 0) {

                    db::set('users', [
                        'Type' => 2
                    ], [
                        'User_ID' => $trip['Driver_ID'],
                        'Type' => 3
                    ]);
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

                    $passenger_fcm_token = db::link()->select('users', ['PFCM'], ['User_ID' => $trip['Passenger_ID']])[0]['PFCM'];
                    load::lib('notify', 'mobile_captain_notify');
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
                }

                echo '#success#';
            } else {
                echo '#fail#';
            }
        } else {
            echo '#fail#';
        }
    }
}
