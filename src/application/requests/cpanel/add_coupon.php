<?php

class REQ_Add_Coupon extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(1024, AUTH::OPOR, true);
        }

        $coupon_code = strtoupper(data::post('coupon_code'));
        $coupon_value = data::post('coupon_value');
        $coupon_amount = data::post('coupon_amount');
        $expire_date = data::post('expire_date');

        if ($coupon_value == 0 || $coupon_amount == 0 || $expire_date < date('Y-m-d')) {
            data::session('is', false);
            dirto(cd('cpanel/coupons'));
        }

        db::put('coupons', [
            'Coupon_Code' => $coupon_code,
            'Coupon_Value' => $coupon_value,
            'Coupon_Amount' => $coupon_amount,
            'Used_Amount' => 0,
            'Expire_Date' => $expire_date,
            'Deleted' => 0
        ]);

        data::session('is', db::so());
        dirto(cd('cpanel/coupons'));
    }
}
