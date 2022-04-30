<?php

class REQ_Login extends Staticy_Unit
{
    function main()
    {
        if (auth::login(data::post('Username'), data::post('Password'), ['User_No', 'Username', 'Fullname'])) {
            data::session('user_no', auth::$fields['User_No']);
            data::session('username', auth::$fields['Username']);
            data::session('fullname', auth::$fields['Fullname']);
            dirto(cd('cpanel'));
        } else {
            data::session('is', false);
            data::val('username', data::post('Username'));
            dirto(cd('cpanel/login'));
        }
    }
}
