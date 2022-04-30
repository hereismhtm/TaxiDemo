<?php

class Trip_Tax extends Staticy_Unit
{
    function calc($args)
    {
        if (!isset($args[0])) return;
        $tax = $args[0];

        return $tax;
    }
}
