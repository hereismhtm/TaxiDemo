<?php

class Tag_img extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (strpos($args[0], '~') === 0) {
            $this->set['src'] = APP_URL. SEP. RES_DIR. SEP. substr($args[0], 1);
        } else {
            $this->set['src'] = $args[0];
        }

        if (isset($args[1])) $this->set['width'] = $args[1];
        if (isset($args[2])) $this->set['height'] = $args[2];
    }
}
