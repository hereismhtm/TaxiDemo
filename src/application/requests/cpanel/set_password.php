<?php

class REQ_Set_Password extends Staticy_Unit
{
    function main()
    {
        auth::cpoint(4096, AUTH::OPOR, true);

        $user_no = data::session('user_no');

        $old_password = data::post('old_password');
        $new_password = data::post('new_password');
        $repeat_password = data::post('repeat_password');

        $slated_hash = db::get_r('system_users', ['Password'], ['User_No' => $user_no])[0]['Password'];

        if (
            (hash('MD5', $new_password) != hash('MD5', $repeat_password)) ||
            !auth::chkSaltedHash($slated_hash, $old_password) ||
            auth::chkSaltedHash($slated_hash, $new_password)
        ) {
            data::session('is', false);
            data::session('settings_request', 'set_password');
            dirto(cd('cpanel/settings'));
        }

        db::set('system_users', [
            'Password' => auth::genSaltedHash($new_password)
        ], [
            'User_No' => $user_no
        ]);

        data::session('is', db::so());
        data::session('settings_request', 'set_password');
        dirto(cd('cpanel/settings'));
    }
}
