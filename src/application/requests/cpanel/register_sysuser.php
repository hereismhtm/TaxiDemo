<?php

class REQ_Register_SysUser extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        auth::cpoint(8192, AUTH::OPAND, true);

        $username = data::post('username');
        $password = data::post('password');
        $fullname = data::post('fullname');
        $perms = data::post('perms');

        $permissions = 4096;
        foreach ($perms as $value) {
            $permissions += $value;
        }

        db::put('system_users', [
            'Username'         => $username,
            'Password'         => auth::genSaltedHash($password),
            'Permissions'     => $permissions,
            'Fullname'         => $fullname,
            'Join_Date'     => date('Y-m-d H:i:s')
        ]);

        data::session('is', db::so());
        dirto(cd('cpanel/sysusers'));
    }
}
