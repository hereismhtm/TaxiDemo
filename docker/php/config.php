<?php

/**
 * Configuration for: Error reporting, set to [true] for
 * development error reporting.
 * Useful to show every little problem during development,
 * but only show hard errors in production.
 */
define('DEV_ENV', false);
define('DEFAULT_UI_LANG', 'ar');

/**
 * Server name (domain) or IP address.
 * Beginning by: [http://] or [https://].
 */
define('APP_URL', 'http://taxidemo.localhost');
define('TAXI_NAME', 'TaxiDemo');
define('LAST_VERSION_CODE', 'Android1.2');

$_tag_config['app_name']         = TAXI_NAME;
$_tag_config['author']             = 'Example Ltd.';
$_tag_config['description']     = 'Taxi mobile app';
$_tag_config['keywords']         = 'taxi';

$_db_config['database_name']     = 'taxidemo';
$_db_config['database_type']     = 'mysql';
$_db_config['server']             = 'db';
$_db_config['username']         = 'myuser';
$_db_config['password']         = 'mypassword';
$_db_config['charset']             = 'utf8';

$_auth_config['login_page']         =    'cpanel/login';
$_auth_config['login_table']         =    'system_users';
$_auth_config['id_field']             =    'Username';
$_auth_config['pass_field']         =    'Password';
$_auth_config['perm_field']         =    'Permissions';
$_auth_config['network_token']        =    $_SERVER['REMOTE_ADDR'] . '{RandomSecretValue}';

define('NOREPLY_MAIL', 'noreply@taxidemo.pro');
define('CONTACT_MAIL', 'contact@taxidemo.pro');
// DO NOT FORGET to edit 'views' folder root files.

define('GOOGLE_MAPS_KEY', 'XXXXXXXXXX');
define('MAP_UPDATE_TIMER', '-5 Min');
define('TRIP_CANCEL_TIMER', '0 Min');
define('CANCELLATION_PUNISHMENT', 0);

define('CALLING_CODE', 249);
define('ZERO_PREFIX_CUT', true);
define('SMS_SENDER', 'Taxi%20Demo');
define('SMS_USERNAME', 'taxidemo');
define('SMS_PASSWORD', 'XXXXXXXXXX');
