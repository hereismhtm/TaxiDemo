<?php

class REQ_Edit_sysUser extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        auth::cpoint(8192, AUTH::OPAND, true);

        $user_no = data::post('user_no');
        $fullname = data::post('fullname');
        $perms = data::post('perms');

        $permissions = 4096;
        foreach ($perms as $value) {
            $permissions += $value;
        }

        db::set('system_users', [
            'Fullname' => $fullname,
            'Permissions' => $permissions
        ], [
            'User_No' => $user_no
        ]);

        data::session('is', db::so());
        dirto(cd('cpanel/sysuser/' . $user_no));
    }
}
