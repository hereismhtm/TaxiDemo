<?php

class REQ_Edit_UserLogo extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(131072, AUTH::OPOR, true);
        }

        $user_id = data::post('user_id');

        $logo = fs::upfile_saveAs('logo', '~userslogos', 'logo_u' . $user_id . '_' . rand() . '.jpg');
        if ($logo[0]) {
            db::set('users', [
                'Logo' => $logo[1]
            ], [
                'User_ID' => $user_id
            ]);
        }

        dirto(cd('cpanel/user/' . $user_id));
    }
}
