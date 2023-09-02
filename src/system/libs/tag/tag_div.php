<?php

class Tag_div extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($tagmethod)) {
            $this->$tagmethod($args);
        } else {
            if (isset($args[0])) $this->set['class'] = $args[0];
            if (isset($args[1])) $this->set['id'] = $args[1];
        }
    }
}
