<?php

class Tag_blockquote extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        $this->has($args[0]);
        $this->has(tag::footer($args[1]));
    }
}
