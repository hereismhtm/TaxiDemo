<?php

class Swap_Classes extends Staticy_Unit
{
    function getArray($args)
    {
        if (!isset($args[0])) return;
        $class = strtoupper($args[0]);

        switch ($class) {
            default:
                return [-1];
                break;
        }
    }
}
