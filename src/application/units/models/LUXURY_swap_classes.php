<?php

class Swap_Classes extends Staticy_Unit
{
    function getArray($args)
    {
        if (!isset($args[0])) return;
        $class = strtoupper($args[0]);

        switch ($class) {
            case 'PRIME':
                return [1, 3, 5, 9];
                break;

            case 'LUXURY':
                return [2, 3];
                break;

            case 'VAN':
                return [4, 5];
                break;

            case 'LADY':
                return [8, 9];
                break;

            default:
                return [-1];
                break;
        }
    }
}
