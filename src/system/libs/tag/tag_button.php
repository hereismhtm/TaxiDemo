<?php

class Tag_button extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($tagmethod)) {
            $this->$tagmethod($args);
        } else {
            if (isset($args[0])) $this->has = $args[0];
        }
    }

    private function _submit($args)
    {
        $this->set['type'] 	= 'submit';
        if (isset($args[0])) $this->has = $args[0];
    }
}
