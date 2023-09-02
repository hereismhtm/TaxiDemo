<?php

class Tag_ul extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($args[0]) && is_array($args[0])) {

            if (tag::$view->isCSS(HTML::MATERIALIZE)) {
                $this->setID('nav-mobile');
                $this->setClass('hide-on-med-and-down');
            }

            foreach ($args[0] as $value) {
                $this->has(tag::li($value));
            }
        }
    }
}
