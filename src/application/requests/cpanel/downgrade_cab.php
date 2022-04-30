<?php

class REQ_Downgrade_Cab extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(262144, AUTH::OPOR, true);
        }

        $user_id = data::post('user_id');

        db::get('users', ['Form_SN'], ['User_ID' => $user_id]);
        if (db::so()) {
            $form_sn = db::res()[0]['Form_SN'];

            db::set('users', [
                'Form_SN' => 0,
                'Type' => 0
            ], [
                'User_ID' => $user_id
            ]);

            if (db::so()) {
                db::del('driversforms', ['Form_SN' => $form_sn]);
                db::set('vehicles', ['isLinked' => 0], ['Driver_ID' => $user_id]);
                echo '#success#';
            } else {
                echo '#fail#';
            }
        } else {
            echo '#fail#';
        }
    }
}
