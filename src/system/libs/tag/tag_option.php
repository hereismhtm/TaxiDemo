<?php

class Tag_option extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        $this->has($args[0]);
        if (isset($args[1])) {
            $this->set['value'] = $args[1];
        } else {
            $this->set['value'] = '';
        }
    }
}
