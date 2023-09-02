<?php

class Tag_nav extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (tag::$view->isCSS(HTML::BOOTSTRAP)) {

            $this->set['class'] = 'navbar navbar-default';

            $div = tag::div('container-fluid');

            if (isset($args[1])) {
                $div->has(tag::div('navbar-header')->has( tag::a($args[1], $args[2])->setClass('navbar-brand') ));
            }

            $div->has( $args[0]->setClass('nav navbar-nav') );	// TYPE: tag::ul

            $this->has($div);

        } else if (tag::$view->isCSS(HTML::MATERIALIZE)) {

            $div = tag::div('nav-wrapper');

            if (isset($args[1])) {
                $div->has( $args[1]->setClass('brand-logo') );
            }
            $div->has($args[0]);								// TYPE: tag::ul

            $this->has($div);

        }
    }
}
