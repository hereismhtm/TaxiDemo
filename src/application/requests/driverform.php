<?php

class REQ_DriverForm extends Staticy_Unit
{
    function main()
    {
        load::lib('str', 'str/cpanel_' . data::ifcookie('lang', DEFAULT_UI_LANG));

        if (!data::issession('user_no')) {
            $success_count = db::link()->count('driversforms', [
                'IP' => $_SERVER['REMOTE_ADDR'],
                'PHPSESSID' => $_COOKIE['PHPSESSID'],
                'isAccepted' => 0
            ]);
            if ($success_count >= 4) {
                data::session('is', false);
                dirto(cd('driverform'));
            }
        }

        if (!data::issession('files_ready')) {
            data::session('files_ready', false);
        }

        $files_paths = data::ifsession('files_paths');
        if ($files_paths == null) $files_paths = array();

        if (data::session('files_ready') === false) {

            foreach (load::$app->files_keys as $fileKey) {
                if (isset($files_paths[$fileKey]))
                    continue;

                $status = fs::upfile_saveAs($fileKey, '~driversforms', $fileKey . '-' . time() . substr(rand(), 0, 6));
                if ($status[0])
                    $files_paths[$fileKey] = $status[1];
            }
            data::session('files_paths', $files_paths);

            if (
                data::post('is_submit_clicked') == 'yes' &&
                isset($files_paths['Logo_img']) &&
                isset($files_paths['Nat_img'])
            ) {
                data::session('files_ready', true);
            } else {
                data::session('post_data', $_POST);
                dirto(cd('driverform'));
            }
        }




        $fullname = data::post('Fullname');
        $gender = data::post('Gender');
        $phone = data::post('Phone');
        $email = strtolower(data::post('Email'));

        if (
            $fullname == '' ||
            !isset(load::unit('models/assets', 'get_users_genders')[$gender]) ||
            $phone == '' ||
            //strlen($phone) != 10 ||
            ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
        ) {
            data::session('is', false);
            data::session('post_data', $_POST);
            dirto(cd('driverform'));
        }

        db::put('driversforms', [
            'Timestamp'     => date('Y-m-d H:i:s'),
            'IP'             => $_SERVER['REMOTE_ADDR'],
            'PHPSESSID'     => $_COOKIE['PHPSESSID'],
            'Agent'         => $_SERVER['HTTP_USER_AGENT'],
            'isAccepted'     => data::issession('form_userid') ? 1 : 0,
            'Fullname'         => $fullname,
            'Gender'         => $gender,
            'Phone'         => $phone,
            'Email'         => $email,
            'Logo_img'         => $files_paths['Logo_img'],
            'Nat_img'         => $files_paths['Nat_img'],
            'CarFront_img'     => isset($files_paths['CarFront_img']) ? $files_paths['CarFront_img'] : '',
            'CarRight_img'    => isset($files_paths['CarRight_img']) ? $files_paths['CarRight_img'] : '',
            'CarBack_img'     => isset($files_paths['CarBack_img']) ? $files_paths['CarBack_img'] : '',
            'CarLeft_img'     => isset($files_paths['CarLeft_img']) ? $files_paths['CarLeft_img'] : '',
            'CarInside_img' => isset($files_paths['CarInside_img']) ? $files_paths['CarInside_img'] : '',
            'Emr_img'         => isset($files_paths['Emr_img']) ? $files_paths['Emr_img'] : '',
            'Lic_img'         => isset($files_paths['Lic_img']) ? $files_paths['Lic_img'] : '',
            'Cert_img'         => isset($files_paths['Cert_img']) ? $files_paths['Cert_img'] : ''
        ]);

        if (db::so()) {
            if (data::issession('form_userid')) {

                if (!auth::cpoint(8192, AUTH::OPAND)) {
                    auth::cpoint(4096, AUTH::OPOR, true);
                    auth::cpoint(2, AUTH::OPOR, true);
                }
                db::set('users', ['Form_SN' => db::res()], ['Form_SN' => -1, 'User_ID' => data::session('form_userid')]);
                if (!db::so()) {
                    db::del('driversforms', ['Form_SN' => db::res()]);
                }
            }

            unset($_SESSION['form_userid']);
            data::session('files_ready', false);
            data::session('files_paths', array());

            data::session('is', true);
            data::session('post_data', array());
            dirto(cd('driverform'));
        } else {

            data::session('is', false);
            data::session('post_data', $_POST);
            dirto(cd('driverform'));
        }
    }
}
