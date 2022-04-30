<?php

class REQ_Set_Prices extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(32768, AUTH::OPOR, true);
        }

        $class = data::post('class');
        $period = data::post('period');
        $fixed = data::post('fixed');
        $kilo = data::post('kilo');
        $tax = data::post('tax');

        db::set('prices', [
            'Fixed' => $fixed,
            'Kilo' => $kilo,
            'Tax' => $tax
        ], [
            'Class' => $class,
            'Period' => ($period == 'G') ? ['M', 'A', 'N'] : $period
        ]);

        data::session('is', db::so());
        dirto(cd('cpanel/prices'));
    }
}
