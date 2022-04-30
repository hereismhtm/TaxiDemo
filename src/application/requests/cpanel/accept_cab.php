<?php

class REQ_Accept_Cab extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(2, AUTH::OPOR, true);
        }

        $form_sn = data::post('form_sn');
        $fullname = data::post('fullname');
        $phone = data::post('phone');

        db::get('driversforms', [
            'Gender',
            'Email',
            'Logo_img'
        ], [
            'Form_SN' => $form_sn
        ]);

        if (db::so()) {
            $form = db::res()[0];

            db::get('users', ['User_ID'], ['AND' => ['Phone' => $phone, 'Form_SN' => 0]]);
            if (db::so()) {
                db::set('users', [
                    'Form_SN'         => $form_sn,
                    'Type'             => 1,
                    'Fullname'         => $fullname,
                    'Gender'         => $form['Gender'],
                    'Email'         => $form['Email'],
                    'Logo'             => $form['Logo_img'],
                    'Modification'     => date('Y-m-d H:i:s')
                ], [
                    'User_ID' => db::res()[0]['User_ID']
                ]);
            } else {
                $key = hash('SHA1', rand() . rand() . rand() . rand());
                db::put('users', [
                    'Form_SN'         => $form_sn,
                    'Type'             => 1,
                    'Phone'         => $phone,
                    'KeyPass'         => auth::genSaltedHash($key),
                    'Pass'             => $key,
                    'Perm'             => 2048,
                    'Fullname'         => $fullname,
                    'Gender'         => $form['Gender'],
                    'Email'         => $form['Email'],
                    'Logo'             => $form['Logo_img'],
                    'Registration' => date('Y-m-d H:i:s')
                ]);
            }

            if (db::so()) {
                db::set('driversforms', ['isAccepted' => 1], ['Form_SN' => $form_sn]);
            }

            echo db::so() ? '#success#' : '#fail#';
        } else {
            echo '#fail#';
        }
    }
}
