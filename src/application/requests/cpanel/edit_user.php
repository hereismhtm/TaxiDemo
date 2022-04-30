<?php

class REQ_Edit_User extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(131072, AUTH::OPOR, true);
        }

        $user_id = data::post('user_id');
        $phone = data::post('phone');
        $fullname = data::post('fullname');
        $email = data::ifpost('email');
        $gender = data::post('gender');

        db::set('users', [
            'Phone' => $phone,
            'Fullname' => $fullname,
            'Email' => $email,
            'Gender' => $gender
        ], [
            'User_ID' => $user_id
        ]);

        if (!db::so()) data::session('is', false);
        echo db::so() ? '#success#' : '#fail#';
    }
}
