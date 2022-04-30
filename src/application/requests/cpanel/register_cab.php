<?php

class REQ_Register_Cab extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(16, AUTH::OPOR, true);
        }

        $user_id = data::post('user_id');
        $fullname = data::post('fullname');
        $email = data::ifpost('email');
        $gender = data::post('gender');

        db::set('users', [
            'Form_SN'         => -1,
            'Type'             => 1,
            'Fullname'         => $fullname,
            'Gender'         => $gender,
            'Email'         => $email,
            'Modification'     => date('Y-m-d H:i:s')
        ], [
            'User_ID' => $user_id
        ]);

        echo db::so() ? '#success#' : '#fail#';
    }
}
