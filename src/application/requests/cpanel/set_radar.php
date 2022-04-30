<?php

class REQ_Set_Radar extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        auth::cpoint(8192, AUTH::OPAND, true);

        $radar_scope = data::post('radar_scope');

        db::set('system_vars', [
            'RADAR_SCOPE' => $radar_scope
        ]);

        data::session('is', db::so());
        data::session('settings_request', 'set_radar');
        dirto(cd('cpanel/settings'));
    }
}
