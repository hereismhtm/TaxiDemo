<?php
// No need to set protected access for members here
// only public or private access.

class Application extends Staticy
{
    function onStart()
    {
        date_default_timezone_set('Africa/Khartoum');
    }

    public $vehicles_classes = array(
        'PRIME'        => 'برايم',
        'LUXURY'     => 'لوكشري',
        'VAN'        => 'حافلة',
        'LADY'         => 'سيدتي',
    );

    public $files_keys = array(
        'Logo_img',
        'Nat_img',
        'CarFront_img', 'CarRight_img', 'CarBack_img', 'CarLeft_img', 'CarInside_img',
        'Emr_img', 'Lic_img', 'Cert_img'
    );

    function alert($s_Msg, $f_Msg)
    {
        $msg_box = null;
        if (data::issession('is')) {

            $is = data::session('is') ? 'success' : 'fail';
            unset($_SESSION['is']);

            switch ($is) {
                case 'success':
                    $msg_box = tag::div('alert alert-success alert-dismissible')->set('role', 'alert')->has([
                        tag::button(tag::span('&times;'))
                            ->set(['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert']),
                        $s_Msg
                    ]);
                    break;

                case 'fail':
                    $msg_box = tag::div('alert alert-danger alert-dismissible')->set('role', 'alert')->has([
                        tag::button(tag::span('&times;'))
                            ->set(['type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert']),
                        $f_Msg
                    ]);
                    break;
            }
        }
        return $msg_box;
    }

    //function onStop() {}
}
