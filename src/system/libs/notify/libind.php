<?php

final class Notify
{
    /**
     * @deprecated
     */
    public static function android($to, $notification=null, $data=null, $settings=null)
    {
        return self::fcm($to, $notification, $data, $settings);
    }

    /**
     * Send notification to one or many devices.
     * Depends on Firebase service from Google.
     * @param string/array $to
     * @param array $notification
     * @param array $data
     * @param array $settings
     * @return array [http response code, response]
     */
    public static function fcm($to, $notification=null, $data=null, $settings=null)
    {
        $bodydata = array();

        if (is_numeric($to)) { // a user id
            $row = db::link()->select($GLOBALS['_notify_config']['fcm_devices_table'], [
                $GLOBALS['_notify_config']['fcm_user_token_field']
            ], [
                $GLOBALS['_notify_config']['fcm_user_id_field'] => $to
            ]);
            if (isset($row[0])) {
                $to = $row[0][$GLOBALS['_notify_config']['fcm_user_token_field']];
            } else {
                $to = 'N/A';
            }
        } else if (is_array($to)) {
            $bodydata['registration_ids'] = $to;
        } else {
            $bodydata['to'] = $to;
        }

        if (isset($notification)) $bodydata['notification'] = $notification;
        if (isset($data)) $bodydata['data'] = $data;
        if (isset($settings)) {
            foreach ($settings as $key => $value) {
                $bodydata[$key] = $value;
            }
        }

        $bodydata = json_encode($bodydata);

        $server_key = '';
        if (isset($GLOBALS['_notify_config']['fcm_server_key'])) {
            $server_key = $GLOBALS['_notify_config']['fcm_server_key'];
        } else {
            $server_key = $GLOBALS['_notify_config']['FCM_ACCESS_KEY'];
        }
        $headers = array(
            'Content-Type: application/json',
            'Authorization: key='. $server_key
        );

        return load::location('http://fcm.googleapis.com/fcm/send', $bodydata, $headers);
    }

    /**
     * Send SMS to a phone number.
     * Depends on BulkSMS EAPI.
     * @param string $to
     * @param array $notification
     * @return array [http response code, response]
     */
    public static function bulksms($phone, $text)
    {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );

        $username = $GLOBALS['_notify_config']['bulksms_username'];
        $password = $GLOBALS['_notify_config']['bulksms_password'];
        $data = '?';
        $data .= 'username='. $username. '&';
        $data .= 'password='. $password. '&';
        $data .= 'message='. $text. '&';
        $data .= 'msisdn='. $phone;

        return load::location('https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0'. $data, null, $headers);
    }
}
