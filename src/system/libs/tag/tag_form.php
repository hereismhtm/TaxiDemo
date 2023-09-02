<?php

class Tag_form extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($args[0])) {

            $this->set['name']		=	$args[0];	// important for AJAX usage
            $this->set['id']		=	$args[0];	// important to link an input tag with it's form
            $this->set['action']	=	APP_URL. SEP;
            $this->set['method']	=	'post';

            $this->has .= tag::input_hidden($args[0])->setName('_Staticy_REQ_');
        }

        if (isset($tagmethod)) {
            $this->$tagmethod($args);
        }
    }

    private function _tabular($args)
    {
        HTML::with( $args[2], 'set', ['form' => $args[0]] );

        $table = tag::table();
        $count = count($args[1]);
        $i = 0;
        do {
            $table->body([ tag::label($args[1][$i], $args[2][$i]), $args[2][$i] ]);
            $i++;
        } while ( $i < $count );

        $this->has .= $table. tag::input_submit($args[3]);
    }
}
