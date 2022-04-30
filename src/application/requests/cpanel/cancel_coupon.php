<?php

class REQ_Cancel_Coupon extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(2097152, AUTH::OPOR, true);
        }

        $coupon_code = data::post('coupon_code');

        db::set('coupons', ['Deleted' => 1], ['Coupon_Code' => $coupon_code]);
        if (db::so()) {
            echo '#success#';
        } else {
            echo '#fail#';
        }
    }
}
