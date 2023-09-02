<?php

final class Reply
{
    public static function json($flag, $header, $schema)
    {
        $json['reply']['flag'] = $flag;
        foreach ($header as $key => $value) {
            $json['reply'][$key] = $value;
        }
        $json['schema'] = $schema;
        echo json_encode($json);
    }

    public static function json_b($flag)
    {
        $json['reply']['flag'] = $flag;
        echo json_encode($json);
    }

    public static function json_h($flag, $header)
    {
        $json['reply']['flag'] = $flag;
        foreach ($header as $key => $value) {
            $json['reply'][$key] = $value;
        }
        echo json_encode($json);
    }

    public static function json_s($flag, $schema)
    {
        $json['reply']['flag'] = $flag;
        $json['schema'] = $schema;
        echo json_encode($json);
    }
}
