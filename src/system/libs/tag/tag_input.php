<?php

class Tag_input extends TagsParent
{
    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (isset($tagmethod)) {
            $this->$tagmethod($args);
        } else {
            $this->set['type'] = $args[0];
            if (isset($args[1])) $this->set['name'] 	= $args[1];
            if (isset($args[2])) $this->set['value'] 	= $args[2];
        }
    }

    private function _button($args)
    {
        $this->set['type'] 	= 'button';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _checkbox($args)
    {
        if (is_array($args[0])) {
            $group = '';
            $args[0] = data::tabulation($args[0]);

            foreach ($args[0] as $something => $called) {
                $checkbox = tag::input('checkbox', null, $something)->suf(' '. $called);

                if (isset($args[1])) {
                    if (
                            (is_array($args[1]) && in_array($something, $args[1]))
                        ||
                            ($something == $args[1])
                        ) {
                        $checkbox->flag('checked');
                    }
                }

                if (isset($args[2])) {
                    foreach ($args[2] as $method => $param) {
                        $checkbox->$method($param);
                    }
                }

                $group.= $checkbox;
            }

            $this->renderStream = $group;

        } else {
            $this->set['type'] = 'checkbox';
            $this->suffix = ' '. $args[0];
        }
    }

    private function _color($args)
    {
        $this->set['type'] = 'color';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _date($args)
    {
        $this->set['type'] = 'date';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _datetime($args)
    {
        $this->set['type'] = 'datetime';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _datetimeLocal($args)
    {
        $this->set['type'] = 'datetime-local';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _email($args)
    {
        $this->set['type'] = 'email';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _file($args)
    {
        $this->set['type'] = 'file';
    }

    private function _hidden($args)
    {
        $this->set['type'] = 'hidden';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _image($args)
    {
        $this->set['type'] = 'image';
        if (strpos($args[0], '~') === 0) {
            $this->set['src'] = APP_URL. SEP. RES_DIR. SEP. substr($args[0], 1);
        } else {
            $this->set['src'] = $args[0];
        }
        if (isset($args[1])) $this->set['width'] = $args[1];
        if (isset($args[2])) $this->set['height'] = $args[2];
    }

    private function _month($args)
    {
        $this->set['type'] = 'month';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _number($args)
    {
        $this->set['type'] = 'number';
        if (isset($args[0])) $this->set['value']	= $args[0];
        if (isset($args[1])) $this->set['min']		= $args[1];
        if (isset($args[2])) $this->set['max']		= $args[2];
        if (isset($args[3])) $this->set['step']		= $args[3];
    }

    private function _password($args)
    {
        $this->set['type'] = 'password';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _radio($args)
    {
        if (is_array($args[0])) {
            $group = '';
            $args[0] = data::tabulation($args[0]);

            foreach ($args[0] as $something => $called) {
                $radio = tag::input('radio', null, $something)->suf(' '. $called);

                if (isset($args[1]) && $something == $args[1]) {
                    $radio->flag('checked');
                }

                if (isset($args[2])) {
                    foreach ($args[2] as $method => $param) {
                        $radio->$method($param);
                    }
                }

                $group.= $radio;
            }

            $this->renderStream = $group;

        } else {
            $this->set['type'] = 'radio';
            $this->suffix = ' '. $args[0];
        }
    }

    private function _range($args)
    {
        $this->set['type'] = 'range';
        if (isset($args[0])) $this->set['value']	= $args[0];
        if (isset($args[1])) $this->set['step']		= $args[1];
        if (isset($args[2])) $this->set['min']		= $args[2];
        if (isset($args[3])) $this->set['max']		= $args[3];
    }

    private function _reset($args)
    {
        $this->set['type'] = 'reset';
        if (isset($args[0])) $this->set['value'] = $args[0];
        if (tag::$view->isCSS(HTML::BOOTSTRAP)) $this->set['class'] = 'btn btn-default';
    }

    private function _search($args)
    {
        $this->set['type'] = 'search';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _submit($args)
    {
        $this->set['type'] = 'submit';
        if (isset($args[0])) $this->set['value'] = $args[0];
        if (tag::$view->isCSS(HTML::BOOTSTRAP)) $this->set['class'] = 'btn btn-primary';
    }

    private function _tel($args)
    {
        $this->set['type'] = 'tel';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _text($args)
    {
        $this->set['type'] = 'text';
        if (isset($args[0])) $this->set['value'] = $args[0];
        //if (isset($args[1])) $this->set['placeholder'] = $args[1];
    }

    private function _time($args)
    {
        $this->set['type'] = 'time';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _url($args)
    {
        $this->set['type'] = 'url';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }

    private function _week($args)
    {
        $this->set['type'] = 'week';
        if (isset($args[0])) $this->set['value'] = $args[0];
    }
}
