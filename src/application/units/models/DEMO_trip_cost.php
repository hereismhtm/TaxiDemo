<?php

class Trip_Cost extends Staticy_Unit
{
    function calc($args)
    {
        if (!isset($args[0]) || !isset($args[1])) return;
        $class = strtoupper($args[0]);
        $distance = $args[1];
        $output = isset($args[2]) ? $args[2] : true;
        $passenger_id = data::session('user_id');

        $tzone = date('H') * 60;
        if ($tzone <= 360) { // 6AM
            $tzone = 'N';
        } else if ($tzone <= 840) { // 2PM
            $tzone = 'M';
        } else { // 10PM
            $tzone = 'A';
        }

        $trip_cost = false;
        $after_discount = 0;
        $balance = 0;
        $tax = 0;
        db::get('prices', ['Fixed', 'Kilo', 'Tax'], ['Class' => $class, 'Period' => $tzone]);
        if (db::so()) {
            $price = db::res()[0];
            if ($distance <= 5) {
                $trip_cost = $price['Fixed'];
            } else {
                $distance = $distance - 5;
                $trip_cost = $price['Fixed'] + $price['Kilo'] * $distance;
            }
            $after_discount = $trip_cost;
            $balance = db::get_r('users', ['Balance'], ['User_ID' => $passenger_id])[0]['Balance'];

            if ($balance > 0) {
                $after_discount = $trip_cost - $balance;
                if ($after_discount >= 0) {
                    $balance = 0;
                } else {
                    $balance = abs($after_discount);
                    $after_discount = 0;
                }
            }

            $trip_cost = $this->_num_format($trip_cost);
            $after_discount = $this->_num_format($after_discount);
            $balance = $this->_num_format($balance);
            $tax = $this->_num_format($price['Tax']);
        }

        if ($output) {
            echo json_encode([
                'trip_cost'         => $trip_cost,
                'after_discount'     => $after_discount
            ]);
        } else {
            return [$trip_cost, $after_discount, $balance, $tax];
        }
    }

    function _num_format($number)
    {
        return str_replace(',', null, number_format($number, 2));
    }
}
