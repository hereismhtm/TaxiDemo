<?php

class Trip_Tax extends Staticy_Unit
{
    function calc($args)
    {
        if (!isset($args[0])) return;
        $tax = $args[0];
        $driver_id = data::session('user_id');

        // cut tax from captain if there is no servered trips in current day
        $count = db::link()->count('trips', [
            'AND' => [
                'Status' => [4, 5],
                'Start[<>]' => [date('Y-m-d 00-00-00'), date('Y-m-d 23-59-59')],
                'Driver_ID' => $driver_id
            ]
        ]);

        if ($count > 0) {
            $tax = 0;
        }

        return $tax;
    }
}
