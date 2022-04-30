<?php

class REQ_Delete_sysUser extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        auth::cpoint(8192, AUTH::OPAND, true);

        $user_no = data::post('user_no');

        db::del('system_users', [
            'User_No' => $user_no
        ]);

        echo db::so() ? '#success#' : '#fail#';
    }
}
