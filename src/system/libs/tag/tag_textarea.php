<?php

class Tag_textarea extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($args[0])) {
            $this->has = $args[0];
        }

        if (isset($args[1])) {
            $this->set['rows'] = $args[1];
        }

        if (isset($args[2])) {
            $this->set['cols'] = $args[2];
        }
    }
}
