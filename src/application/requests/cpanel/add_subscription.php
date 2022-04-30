<?php

class REQ_Add_Subscription extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(1048576, AUTH::OPOR, true);
        }

        $user_id = data::post('user_id');
        $days = data::post('days');
        $payed = data::post('payed');

        db::get('users', ['Active_Until'], ['User_ID' => $user_id]);

        if (!db::so() || $days == 0) {
            data::session('is', false);
            dirto(cd('cpanel/user/' . $user_id . '/subscriptions'));
        }

        $expire_date = date('Y-m-d', strtotime(db::res()[0]['Active_Until']));
        if ($expire_date == '1970-01-01') {
            $expire_date = date('Y-m-d H:i:s', strtotime('+ ' . $days . ' day'));
        } else {
            if (db::res()[0]['Active_Until'] >= date('Y-m-d H:i:s')) {
                $expire_date = date('Y-m-d H:i:s', strtotime(db::res()[0]['Active_Until'] . ' + ' . $days . ' day'));
            } else {
                $expire_date = date('Y-m-d H:i:s', strtotime('+ ' . $days . ' day'));
            }
        }

        db::put('subscriptions', [
            'User_ID' => $user_id,
            'Days' => $days,
            'Payed' => $payed,
            'Payment_Date' => date('Y-m-d H:i:s'),
            'Expire_Date' => $expire_date
        ]);
        db::set('users', ['Active_Until' => $expire_date], ['User_ID' => $user_id]);

        data::session('is', db::so());
        dirto(cd('cpanel/user/' . $user_id . '/subscriptions'));
    }
}
