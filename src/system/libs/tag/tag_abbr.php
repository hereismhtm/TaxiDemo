<?php

class Tag_abbr extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        $this->set['title'] = $args[0];
        $this->has = $args[1];
    }
}
