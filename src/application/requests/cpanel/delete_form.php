<?php

class REQ_Delete_Form extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(4, AUTH::OPOR, true);
        }

        $form_sn = data::post('form_sn');

        db::del('driversforms', [
            'Form_SN' => $form_sn
        ]);

        echo db::so() ? '#success#' : '#fail#';

        db::set('users', [
            'Form_SN' => -1
        ], [
            'Form_SN' => $form_sn
        ]);
    }
}
