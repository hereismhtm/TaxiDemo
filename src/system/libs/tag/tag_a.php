<?php

class Tag_a extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        $this->set['href'] = $args[0];

        if (isset($args[1])) {
            $this->has = $args[1];
        } else {
            $this->has = $args[0];
        }
    }
}
