<?php

class Tag_select extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($args[0])) {
            $args[0] = data::tabulation($args[0]);

            foreach ($args[0] as $something => $called) {
                $option = tag::option($called, $something);

                if (isset($args[1]) && $something == $args[1]) {
                    $option->flag('selected');
                }
                if (isset($args[2])) {
                    foreach ($args[2] as $method => $param) {
                        $option->$method($param);
                    }
                }
                $this->has( $option );
            }

        } else {
            if (isset($args[1])) {
                $this->has(tag::option($args[1], ''));
            }
        }
    }
}