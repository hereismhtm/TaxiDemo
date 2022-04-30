<?php

class REQ_Fill_Credit extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        $fill_type = data::post('fill_type');
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            if ($fill_type == 'balance') {
                auth::cpoint(64, AUTH::OPOR, true);
            } else {
                auth::cpoint(128, AUTH::OPOR, true);
            }
        }
        load::lib('str', 'str/cpanel_' . data::ifcookie('lang', DEFAULT_UI_LANG));

        $user_id = data::post('user_id');
        $credit = data::post('credit');


        if (!is_numeric($credit) || $credit == 0) {
            echo str::credit_fill_failed_bcz_bad_amount;
            return;
        }

        if (
            db::link()->has('trips', [
                'AND' => [
                    'Status[<>]' => [1, 3],
                    'Passenger_ID' => $user_id
                ]
            ])
        ) {
            echo str::credit_fill_failed_bcz_active_trip;
            return;
        }

        $field = ($fill_type == 'balance') ? 'Balance' : 'Credit';

        db::set('users', [
            $field . '[+]'         => $credit,
            'Modification'         => date('Y-m-d H:i:s')
        ], [
            'User_ID' => $user_id
        ]);

        echo db::so() ? '#success#' : '#fail#';
    }
}
