<?php

class Tag_label extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($tagmethod)) {
            $this->$tagmethod($args);
        } else {
            $this->basic($args);
        }
    }

    private function basic($args)
    {
        $this->has = $args[0];

        if (isset($args[1])) {

            $id 	= $args[1]->get('id');
            $name 	= $args[1]->get('name');

            if ( $name == null) {
                $name = $args[1]->set['name'] = trim( strtolower($args[0]) );
            }

            if ($id == null) {
                $this->set['for'] = $args[1]->set['id'] = $name;
            } else {
                $this->set['for'] = $id;
            }
        }
    }

    private function _followedBy($args)
    {
        $this->basic($args);
        $this->suf($args[1]);
    }
}
