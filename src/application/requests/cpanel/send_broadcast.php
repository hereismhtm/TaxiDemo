<?php

class REQ_Send_Broadcast extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        if (!auth::cpoint(8192, AUTH::OPAND)) {
            auth::cpoint(4096, AUTH::OPOR, true);
            auth::cpoint(65536, AUTH::OPOR, true);
        }

        $msg_title = data::ifpost('msg_title', TAXI_NAME);
        $msg_body = data::post('msg_body');
        $send_to = data::post('send_to');

        $status = null;
        $is_sent = false;
        if ($send_to == 'SMS') {

            $status = $this->send_sms($msg_title, $msg_body);
            $is_sent = ($status[0] == 200 && strpos($status[1], 'OK') !== false);
        } else if ($send_to == 'PSMS') {

            $i = 0;
            while (true) {
                $res = db::link()->select('users', ['Phone'], ['Form_SN' => 0, 'LIMIT' => [$i, 100]]);
                if (!empty($res)) {
                    $status = $this->send_sms($res, $msg_body);
                    $is_sent = ($status[0] == 200 && strpos($status[1], 'OK') !== false);
                    if ($is_sent) {
                        $i += 100;
                    } else {
                        var_dump($status);
                        echo '</br></br>';
                        var_dump($res);
                        echo '</br></br>';
                        var_dump($msg_body);
                        echo '</br></br>';
                        echo 'send_to = ' . $send_to . ' , i = ' . $i;
                        die();
                        break;
                    }
                } else {
                    break;
                }
            }
        } else if ($send_to == 'CSMS') {

            $i = 0;
            while (true) {
                $res = db::link()->select('users', ['Phone'], ['Form_SN[!]' => 0, 'LIMIT' => [$i, 100]]);
                if (!empty($res)) {
                    $status = $this->send_sms($res, $msg_body);
                    $is_sent = ($status[0] == 200 && strpos($status[1], 'OK') !== false);
                    if ($is_sent) {
                        $i += 100;
                    } else {
                        var_dump($status);
                        echo '</br></br>';
                        var_dump($res);
                        echo '</br></br>';
                        var_dump($msg_body);
                        echo '</br></br>';
                        echo 'send_to = ' . $send_to . ' , i = ' . $i;
                        die();
                        break;
                    }
                } else {
                    break;
                }
            }
        } else {

            $fcm_data = [
                'fcm_data' => [
                    'code'         => 'BROADCAST',
                    'title'     => $msg_title,
                    'body'        => $msg_body
                ]
            ];

            if ($send_to == 'P') {
                load::lib('notify', 'mobile_passenger_notify');
                $status = notify::fcm('/topics/passengers', null, $fcm_data, ['content_available' => true]);
                $is_sent = ($status[0] == 200);
            } else { // == 'C'
                load::lib('notify', 'mobile_captain_notify');
                $status = notify::fcm('/topics/captains', null, $fcm_data, ['content_available' => true]);
                $is_sent = ($status[0] == 200);
            }
        }

        data::session('is', $is_sent);

        // echo result to get logged in database
        echo $status[0] . ' - ' . $status[1];

        dirto(cd('cpanel/broadcast'));
    }

    private function send_sms($phone, $text)
    {
        if ($phone == '' || $text == '') httpsta(400);

        if (is_array($phone)) {
            $phone = implode(';', array_map(function ($el) {
                return $el['Phone'];
            }, $phone));
            $phone = str_replace('+', '', $phone);
        } else {
            $phone = substr($phone, 4);
            if (ZERO_PREFIX_CUT && strpos($phone, '0') === 0) {
                $phone = substr($phone, 1);
            }
            $phone = CALLING_CODE . $phone;
        }

        $headers = array(
            'Content-Length: 0'
        );
        $data = '?';
        $data .= 'user=' . SMS_USERNAME . '&';
        $data .= 'pwd=' . SMS_PASSWORD . '&';
        $data .= 'sender=' . SMS_SENDER . '&';
        $data .= 'smstext=' . rawurlencode($text) . '&';
        $data .= 'nums=' . $phone;

        $status = load::location('http://196.202.134.90/dsms/webacc.aspx' . $data, null, $headers);
        return $status;
    }
}
