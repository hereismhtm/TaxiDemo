<?php

class Tag_table extends TagsParent
{
    private $thead;
    private $tbody;
    private $tfoot;

    function __construct($tagname, $tagmethod, $args)
    {
        $this->construct($tagname);

        if (tag::$view->isCSS(HTML::BOOTSTRAP)) $this->set['class'] = 'table';
        if (isset($tagmethod)) {
            $this->$tagmethod($args);
        } else {

            if (isset($args[0])) {
                $first_row = true;
                foreach ($args[0] as $row) {
                    if ($first_row) {
                        if (isset($args[1])) {
                            $this->head($args[1]);
                        } else {
                            $this->head($row, true);
                        }
                        $first_row = false;
                    }
                    $this->body($row, isset($args[2]) ? $args[2] : null);
                }
            }
        }
    }

    function __destruct()
    {
        $this->has( array($this->thead, $this->tbody, $this->tfoot) );
        if (!$this->rendered) tag::$view->stream($this->render());
    }

    function __tostring()
    {
        $this->has( array($this->thead, $this->tbody, $this->tfoot) );
        return $this->render();
    }

    public function thead()
    {
        if (!isset($this->thead)) $this->thead = tag::thead();
        return $this->thead;
    }

    public function tbody()
    {
        if (!isset($this->tbody)) $this->tbody = tag::tbody();
        return $this->tbody;
    }

    public function tfoot()
    {
        if (!isset($this->tfoot)) $this->tfoot = tag::tfoot();
        return $this->tfoot;
    }

    public function head($row, $by_key=false)
    {
        if (!isset($this->thead)) $this->thead = tag::thead();

        $tr = tag::tr();
        foreach ($row as $key => $value) {
            if (strpos($key, '_') === 0) {
                continue;
            }
            $th = tag::th( ($by_key)? $key : $value );
            if ((tag::$view->html->get('dir')) == 'rtl') {
                $th->setStyle('text-align: right;');
            }
            $tr->has($th);
        }

        $this->thead->has($tr);
        return $this;
    }

    public function body($row, $trSetArray=null)
    {
        if (!isset($this->tbody)) $this->tbody = tag::tbody();

        $tr = tag::tr();
        foreach ($row as $key => $value) {
            if (strpos($key, '_') === 0) {
                continue;
            }
            $tr->has(tag::td( $value ));
        }

        if ( isset($trSetArray) ) {

            if (isset($trSetArray['|~|'])) {
                $condition = $trSetArray['|~|'];

                foreach ($trSetArray as $k => $v) {
                    if ($row[$condition] == $k) {
                        $tr->set($v);
                    }
                }
            } else {
                $tr->set($trSetArray);
            }
        }

        $this->tbody->has($tr);
        return $this;
    }

    public function foot($row)
    {
        if (!isset($this->tfoot)) $this->tfoot = tag::tfoot();

        $tr = tag::tr();
        foreach ($row as $value) {
            $tr->has(tag::td( $value ));
        }

        $this->tfoot->has($tr);
        return $this;
    }

    private function _editable($args)
    {
        if (!isset($this->tbody)) $this->tbody = tag::tbody();

        $first_row = true;
        $formNumber = 0;
        foreach ($args[0] as $row) {

            if ($first_row) {
                $this->head($row, true);
                $first_row = false;
            }

            $tr = tag::tr();
            foreach ($row as $key => $value) {
                $text_box = tag::input_text($value)->set(array('name' => $key, 'form' => $args[1].'_'.$formNumber));
                $tr->has( tag::td( $text_box ) );
            }

            $tr->has(tag::td( tag::form($args[1])->has(tag::input_submit($args[2]))->setID($args[1].'_'.$formNumber) ));
            $this->tbody->has($tr);
            $formNumber++;
        }

        return $this;
    }
}
