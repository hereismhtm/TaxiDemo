<?php

class Tag_details extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($args[0])) $this->has( tag::summary($args[0]) );
        if (isset($args[1])) $this->has( $args[1] );
    }
}
