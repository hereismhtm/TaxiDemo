<?php

class Assets extends Staticy_Unit
{
    private $users_types = array(
        0 => ['string' => str::passenger, 'style' => 'label-default'],
        1 => ['string' => str::cab_mode_1, 'style' => 'label-primary'],
        2 => ['string' => str::cab_mode_2, 'style' => 'label-warning'],
        3 => ['string' => str::cab_mode_3, 'style' => 'label-success']
    );

    private $users_genders = array(
        'M'        => str::male,
        'F'        => str::female,
        'X'     => str::undefined
    );

    private $trips_status = array(
        '-2'    => str::trips_status_n2,
        '-1'    => str::trips_status_n1,
        '0'        => str::trips_status_0,
        '1'        => str::trips_status_1,
        '2'        => str::trips_status_2,
        '3'        => str::trips_status_3,
        '4'        => str::trips_status_4,
        '5'        => str::trips_status_5
    );

    // app 2048; emps 4096, admin 8192
    private $perms_array = array(
        '1'         => str::perm1,
        '2'         => str::perm2,
        '4'         => str::perm4,
        '8'         => str::perm8,
        '131072'     => str::perm131072,
        '16'         => str::perm16,
        '32'         => str::perm32,
        '262144'     => str::perm262144,
        '64'         => str::perm64,
        '128'         => str::perm128,
        '1048576'     => str::perm1048576,
        '256'         => str::perm256,
        '524288'     => str::perm524288,
        '512'         => str::perm512,
        '1024'         => str::perm1024,
        '2097152'     => str::perm2097152,
        '16384'     => str::perm16384,
        '32768'     => str::perm32768,
        '65536'     => str::perm65536,
    );

    function get_users_types()
    {
        return $this->users_types;
    }

    function get_users_genders()
    {
        return $this->users_genders;
    }

    function get_trips_status()
    {
        return $this->trips_status;
    }

    function get_perms_array()
    {
        return $this->perms_array;
    }
}
