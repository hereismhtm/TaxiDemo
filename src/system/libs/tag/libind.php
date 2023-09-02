<?php

/*
 * Group of Classes for HTML elements.
 */

tag::initView();

final class Tag
{
    public static $view = null;
    public static $pagination = null;

    public static function initView()
    {
        self::$view = new HTML;
    }

    public static function initPagination($rows_count, $vals_array=[])
    {
        self::$pagination = new PAGINATION($rows_count, $vals_array);
    }

    /**
     * This method will generate a new tag object.
     */
    public static function __callStatic($tagname, $args=null)
    {
        $tagname 	= explode('_', strtolower($tagname));
        $tagmethod 	= isset($tagname[1]) ? '_'. $tagname[1] : null;
        $tagname 	= $tagname[0];
        $tag_file 	= load::homeOfLib('tag'). 'tag_'. $tagname. '.php';

        if (file_exists($tag_file)) {
            require_once $tag_file;
            $tagclass = 'Tag_'.$tagname;
            return new $tagclass($tagname, $tagmethod, $args);

        } else {
            if (isset(self::$tags_list[$tagname])) {
                return new TagsParent($tagname, $args);

            } else {
                if (DEV_ENV) load::error('Warning', '@Tag_lib >> Unknown tag name', $tagname);
            }
        }
    }

    public static $tags_list = array(
        'a' 			=> true,
        'abbr' 			=> true,
        'acronym' 		=> true,
        'address' 		=> true,
        'applet' 		=> true,
        'area' 			=> false,
        'article' 		=> true,
        'aside' 		=> true,
        'audio' 		=> true,
        'b' 			=> true,
        'base' 			=> false,
        'basefont' 		=> true,
        'bdi' 			=> true,
        'bdo' 			=> true,
        'big' 			=> true,
        'blockquote' 	=> true,
        'body' 			=> true,
        'br' 			=> false,
        'button' 		=> true,
        'canvas' 		=> true,
        'caption' 		=> true,
        'center' 		=> true,
        'cite' 			=> true,
        'code' 			=> true,
        'col' 			=> false,
        'colgroup' 		=> true,
        'datalist' 		=> true,
        'dd' 			=> true,
        'del' 			=> true,
        'details' 		=> true,
        'dfn' 			=> true,
        'dialog' 		=> true,
        'dir' 			=> true,
        'div' 			=> true,
        'dl' 			=> true,
        'dt' 			=> true,
        'em' 			=> true,
        'embed' 		=> false,
        'fieldset' 		=> true,
        'figcaption' 	=> true,
        'figure' 		=> true,
        'font' 			=> true,
        'footer' 		=> true,
        'form' 			=> true,
        'frame' 		=> true,
        'frameset' 		=> true,
        'head' 			=> true,
        'header' 		=> true,
        'h1' 			=> true,
        'h2' 			=> true,
        'h3' 			=> true,
        'h4' 			=> true,
        'h5' 			=> true,
        'h6' 			=> true,
        'hr' 			=> false,
        'html' 			=> true,
        'i' 			=> true,
        'iframe' 		=> true,
        'img' 			=> false,
        'input' 		=> false,
        'ins' 			=> true,
        'kbd' 			=> true,
        'keygen' 		=> false,
        'label' 		=> true,
        'legend' 		=> true,
        'li' 			=> true,
        'link' 			=> false,
        'main' 			=> true,
        'map' 			=> true,
        'mark' 			=> true,
        'menu' 			=> true,
        'menuitem' 		=> true,
        'meta' 			=> false,
        'meter' 		=> true,
        'nav' 			=> true,
        'noframes' 		=> true,
        'noscript' 		=> true,
        'object' 		=> true,
        'ol' 			=> true,
        'optgroup' 		=> true,
        'option' 		=> true,
        'output' 		=> true,
        'p' 			=> true,
        'param' 		=> false,
        'pre' 			=> true,
        'progress' 		=> true,
        'q' 			=> true,
        'rp' 			=> true,
        'rt' 			=> true,
        'ruby' 			=> true,
        's' 			=> true,
        'samp' 			=> true,
        'script' 		=> true,
        'section' 		=> true,
        'select' 		=> true,
        'small' 		=> true,
        'source' 		=> false,
        'span' 			=> true,
        'strike' 		=> true,
        'strong' 		=> true,
        'style' 		=> true,
        'sub' 			=> true,
        'summary' 		=> true,
        'sup' 			=> true,
        'table' 		=> true,
        'tbody' 		=> true,
        'td' 			=> true,
        'textarea' 		=> true,
        'tfoot' 		=> true,
        'th' 			=> true,
        'thead' 		=> true,
        'time' 			=> true,
        'title' 		=> true,
        'tr' 			=> true,
        'track' 		=> false,
        'tt' 			=> true,
        'u' 			=> true,
        'ul' 			=> true,
        'var' 			=> true,
        'video' 		=> true,
        'wbr' 			=> false,
    );
}

final class HTML
{
    const BOOTSTRAP 		= 'Bootstrap';
    const MATERIALIZE 		= 'Materialize';

    public $html;
    public $mainDiv;

    private $is_bootstrap 	= false;
    private $is_materialize = false;
    private $charset;
    private $title;
    private $icon;
    private $head;
    private $body;
    private $stream;

    function __construct()
    {
        $this->charset 	= tag::meta()->set('charset', 'UTF-8');
        $this->title 	= tag::title()->has($GLOBALS['_tag_config']['app_name']);
        $this->icon 	= tag::link()->set(array(
            'rel'	=> 'icon',
            'type'	=> 'image/x-icon',
            'href'	=> APP_URL. SEP. RES_DIR. SEP. 'img'. SEP. 'favicon.ico'
        ));

        $this->html = tag::html();

        $this->head = tag::head();
        $this->head->has(array(
            tag::meta()->set(array('name'=>'application-name', 'content'=>$GLOBALS['_tag_config']['app_name'])),
            tag::meta()->set(array('name'=>'author', 'content'=>$GLOBALS['_tag_config']['author'])),
            tag::meta()->set(array('name'=>'generator', 'content'=>GENERATOR)),
            tag::meta()->set(array('name'=>'description', 'content'=>$GLOBALS['_tag_config']['description'])),
            tag::meta()->set(array('name'=>'keywords', 'content'=>$GLOBALS['_tag_config']['keywords']))
        ));

        $this->body 	= tag::body();
        $this->mainDiv	= tag::div();
    }

    function __destruct()
    {
        echo $this;
    }

    function __tostring()
    {
        return '<!DOCTYPE html>'. $this->html->has(array(
            $this->head->prehas(array(
                $this->charset,
                $this->title,
                $this->icon
            )),
            $this->body->prehas($this->mainDiv->has($this->stream))
        ));
    }

    public function of($title, $icon='favicon.ico', $charset='UTF-8')
    {
        $this->title->clean();
        if (strpos($title, '~') === 0) {
            $this->title->has($GLOBALS['_tag_config']['app_name']. ' | '. substr($title, 1));
        } else {
            $this->title->has($title);
        }
        $this->icon->set('href', APP_URL. SEP. RES_DIR. SEP. 'img'. SEP. $icon);
        $this->charset->set('charset', $charset);
    }

    public function addCSS($cssName, $setArray=null)
    {
        if ($cssName == self::BOOTSTRAP) {
            $this->head->has(array(
                tag::meta()->set(array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1')),
                (DEV_ENV) ?
                    tag::link()->set(array('rel'=>'stylesheet', 'href'=>APP_URL. SEP. RES_DIR. SEP. 'css'. SEP. 'bootstrap.min.css'))
                    :
                    tag::link()->set(array('rel'=>'stylesheet', 'href'=>'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'))
            ));

            $this->mainDiv->setClass('container-fluid');

            $this->body->has(array(
                (DEV_ENV) ?
                    tag::script()->set('src', APP_URL. SEP. RES_DIR. SEP. 'js'. SEP. 'jquery.min.js')
                    :
                    tag::script()->set('src', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'),
                (DEV_ENV) ?
                    tag::script()->set('src', APP_URL. SEP. RES_DIR. SEP. 'js'. SEP. 'bootstrap.min.js')
                    :
                    tag::script()->set('src', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js')
            ));

            $this->is_bootstrap = true;
            return;
        }

        if ($cssName == self::MATERIALIZE) {
            $this->head->has(array(
                tag::meta()->set(array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1.0')),
                tag::link()->set(array('rel'=>'stylesheet', 'href'=>'https://fonts.googleapis.com/icon?family=Material+Icons')),
                tag::link()->set(array('rel'=>'stylesheet', 'href'=>APP_URL. SEP. RES_DIR. SEP. 'css'. SEP. 'materialize.min.css', 'media'=>'screen,projection'))
            ));

            $this->mainDiv->setClass('container');

            $this->body->has(array(
                tag::script()->set('src', 'https://code.jquery.com/jquery-2.1.1.min.js'),
                tag::script()->set('src', APP_URL. SEP. RES_DIR. SEP. 'js'. SEP. 'materialize.min.js')
            ));

            $this->is_materialize = true;
            return;
        }

        $file = RES_DIR. SEP. 'css'. SEP. $cssName. '.css';
        if (file_exists($file)) {
            $css = tag::link()->set(array(
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => APP_URL. SEP. $file
            ));
            if (isset($setArray)) {
                $css->set($setArray);
            }
            $this->head->has($css);
        } else {
            load::error('Warning', '@Tag_lib >> No style file called', $cssName);
        }
    }

    public function isCSS($cssName)
    {
        if ($cssName == self::BOOTSTRAP)		return $this->is_bootstrap;
        if ($cssName == self::MATERIALIZE)		return $this->is_materialize;
        return false;
    }

    public function addJS($jsName, $setArray=null)
    {
        $file = RES_DIR. SEP. 'js'. SEP. $jsName. '.js';
        if (file_exists($file)) {
            $js = tag::script()->set(array(
                'type' => 'text/javascript',
                'src' => APP_URL. SEP. $file
            ));
            if (isset($setArray)) {
                $js->set($setArray);
            }
            $this->body->has($js);
        } else {
            load::error('Warning', '@Tag_lib >> No script file called', $jsName);
        }
    }

    public function link($href, $setArray=null)
    {
        $link = tag::link()->set('href', $href);
        if (isset($setArray)) {
            $link->set($setArray);
        }
        $this->head->has($link);
    }

    public function script($code, $setArray=null)
    {
        $script = tag::script($code);
        if (isset($setArray)) {
            $script->set($setArray);
        }
        $this->body->has($script);
    }

    public function stream($str)
    {
        $this->stream .= $str;
    }

    public static function with($objects, $method, $param)
    {
        foreach ($objects as $object) {
            $object->$method($param);
        }
        return $objects;
    }
}

final class PAGINATION
{
    public $rows_count;

    public $rows;
    public $begin;
    public $previous;
    public $current;
    public $next;
    public $end;

    private $rows_key;
    private $page_key;
    private $rows_key_num = 25;
    private $page_key_num = 1;
    private $vals_array = array();

    function __construct($rows_count, $vals_array)
    {
        $this->rows_count = $rows_count;
        foreach ($vals_array as $key => $value) {
            if (empty($this->rows_key)) {
                if ($key == 0) {
                    $this->rows_key = $value;
                } else {
                    $this->rows_key = $key;
                    $this->rows_key_num = $value;
                }
            } else if (empty($this->page_key)) {
                if ($key == 1) {
                    $this->page_key = $value;
                } else {
                    $this->page_key = $key;
                    $this->page_key_num = $value;
                }
            } else {
                $this->vals_array[$key] = $value;
            }
        }

        $this->rows = data::ifval($this->rows_key, $this->rows_key_num);
        if ($this->rows > 100) $this->rows = 100;
        $this->current = data::ifval($this->page_key, $this->page_key_num);

        $this->begin = ($rows_count > 0)? 1 : 0;
        $this->end = (int)($rows_count / $this->rows);
        if ($rows_count % $this->rows != 0) $this->end++;

        $this->previous = ($this->current > 1)? ($this->current - 1) : 0;
        $this->next = ($this->current < $this->end)? ($this->current + 1) : 0;
    }

    public function limit()
    {
        return [ ($this->current - 1) * $this->rows, $this->rows ];
    }

    public function echo()
    {
        // echo Bootstrap row div mode

        $link_style = 'color: white; padding: 1px 4px; text-align: center; text-decoration: none; display: inline-block;';

        $div_pagination_1 = tag::div('col-sm-4')->has([

            ($this->begin)?
                tag::a(cd(data::$unit. SEP. data::$action, $this->getValsArray($this->begin)),
                    '<<'
                )->setStyle('background-color: gray; '.$link_style)->sufsp(4)
                :
                null,

            ($this->previous)?
                tag::a(cd(data::$unit. SEP. data::$action, $this->getValsArray($this->previous)),
                    '<-'
                )->setStyle('background-color: gray; '.$link_style)->sufsp(2)
                :
                tag::strong('<-')->setStyle('background-color: black; '.$link_style)->sufsp(2),

            ($this->current)?
                tag::strong('[ '.$this->current.' ]')->sufsp(2)
                :
                null,

            ($this->next)?
                tag::a(cd(data::$unit. SEP. data::$action, $this->getValsArray($this->next)),
                    '->'
                )->setStyle('background-color: gray; '.$link_style)->sufsp(4)
                :
                tag::strong('->')->setStyle('background-color: black; '.$link_style)->sufsp(4),

            ($this->end)?
                tag::a(cd(data::$unit. SEP. data::$action, $this->getValsArray($this->end)),
                    '>>'
                )->setStyle('background-color: gray; '.$link_style)->sufsp()
                :
                null,

        ]);

        $div_pagination_2 = tag::div('col-sm-8')->has(
            tag::p('# '.$this->rows_count)
        );

        $direction = tag::$view->html->get('dir');
        return tag::div('row')->has([
            ((empty($direction) || $direction == 'ltr')? $div_pagination_1 : $div_pagination_2)
            ,
            ((empty($direction) || $direction == 'ltr')? $div_pagination_2 : $div_pagination_1)
        ]);
    }

    private function getValsArray($p)
    {
        $custom_vals_array = $this->vals_array;
        $custom_vals_array[$this->page_key] = $p;
        $custom_vals_array[$this->rows_key] = $this->rows;
        return $custom_vals_array;
    }
}

class TagsParent
{
    protected $tagname;
    protected $normal_element;
    protected $prefix 	= null;
    protected $set 		= array();
    protected $prehas 	= null;
    protected $has 		= null;
    protected $suffix 	= null;
    protected $rendered = false;
    protected $renderStream = null;

    protected final function construct($tagname)
    {
        $this->tagname = $tagname;
        $this->normal_element = Tag::$tags_list[$tagname];
    }

    /** @ignore */
    function __construct($tagname, $args)
    {
        $this->construct($tagname);
        if ($this->normal_element && isset($args[0])) $this->has($args[0]);
    }

    /** @ignore */
    function __destruct()
    {
        if (!$this->rendered) tag::$view->stream($this->render());
    }

    /** @ignore */
    function __tostring()
    {
        return $this->render();
    }

    /** @ignore */
    final public function out()
    {
        $this->__destruct();
    }

    /** @ignore */
    final public function clean()
    {
        $this->prehas 	= null;
        $this->has 		= null;
        //return $this;
    }

    /** @ignore */
    final public function clear()
    {
        $this->prefix 	= null;
        $this->set 		= array();
        $this->suffix 	= null;
        $this->clean();
    }

    /** @ignore */
    final public function get($attribute)
    {
        return isset($this->set[$attribute]) ? $this->set[$attribute] : null;
    }

    /** @ignore */
    final public function pre($str)
    {
        $this->prefix .= $str;
        return $this;
    }

    /** @ignore */
    final public function presp($number=1)
    {
        $str = '&nbsp;';
        if ($number > 1) {
            for ($i=1; $i<$number; $i++) $str .= '&nbsp;';
        }
        $this->prefix .= $str;
        return $this;
    }

    /** @ignore */
    final public function preln($number=1)
    {
        $str = '<br/>';
        if ($number > 1) {
            for ($i=1; $i<$number; $i++) $str .= '<br/>';
        }
        $this->prefix .= $str;
        return $this;
    }

    /** @ignore */
    final public function set($attribute, $value=null)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $key => $val) {
                $this->set[$key] = $val;
            }
        } else $this->set[$attribute] = $value;
        return $this;
    }

    /** @ignore */
    final public function setID($id)
    {
        $this->set['id'] = $id;
        return $this;
    }

    /** @ignore */
    final public function setName($name)
    {
        $this->set['name'] = $name;
        return $this;
    }

    /** @ignore */
    final public function setNV($name, $value)
    {
        $this->set['name'] = $name;
        $this->set['value'] = $value;
        return $this;
    }

    /** @ignore */
    final public function setStyle($style)
    {
        $this->set['style'] = $style;
        return $this;
    }

    /** @ignore */
    final public function setClass($class)
    {
        if (is_array($class)) {
            $this->set['class'] = '';
            foreach ($class as $value) {
                $this->set['class'] = $this->set['class']. ' '. $value;
            }
        } else if (isset($this->set['class'])) {
            $this->set['class'] = $this->set['class']. ' '. $class;
        } else {
            $this->set['class'] = $class;
        }
        $this->set['class'] = trim($this->set['class']);
        return $this;
    }

    /** @ignore */
    final public function noClass()
    {
        unset($this->set['class']);
        return $this;
    }

    /** @ignore */
    final public function flag($attribute)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $value) {
                $this->set[$value] = $value;
            }
        } else $this->set[$attribute] = $attribute;
        return $this;
    }

    /** @ignore */
    final public function prehas($content)
    {
        if (is_array($content)) {
            foreach ($content as $something) {
                $this->prehas .= $something;
            }
        } else $this->prehas .= $content;
        return $this;
    }

    /** @ignore */
    final public function has($content)
    {
        if (is_array($content)) {
            foreach ($content as $something) {
                $this->has .= $something;
            }
        } else $this->has .= $content;
        return $this;
    }

    /** @ignore */
    final public function suf($str)
    {
        $this->suffix .= $str;
        return $this;
    }

    /** @ignore */
    final public function sufsp($number=1)
    {
        $str = '&nbsp;';
        if ($number > 1) {
            for ($i=1; $i<$number; $i++) $str .= '&nbsp;';
        }
        $this->suffix .= $str;
        return $this;
    }

    /** @ignore */
    final public function sufln($number=1)
    {
        $str = '<br/>';
        if ($number > 1) {
            for ($i=1; $i<$number; $i++) $str .= '<br/>';
        }
        $this->suffix .= $str;
        return $this;
    }

    /**
     * AJAX = Asynchronous JavaScript and XML.
     */
    final public function ajax($event, $args=null, $element_id=null)
    {
        if (!isset($element_id)) $element_id = $this->set['id'];
        $js_function = 'ajax_'. $event. '_for_'. $element_id.'_R'.rand();
        $this->set[$event] = $js_function. '()';
        $address = cd('AJAX_'. $event. '_for_'. $element_id. SEP);

        $script = "
            function ".$js_function."() {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        document.getElementById('".$element_id."').innerHTML = xmlhttp.responseText;
                    }
                };
        ";

        if (isset($args)) {
            foreach ($args as $key => $arg) {
                if (strpos($arg, '.') !== false) {
                    $form_var = explode('.', $arg);
                    $script .= 'var v'.$key.' = document.'.$form_var[0].'.'.$form_var[1].'.value;';
                }
            }
        }

        if (isset($args)) {
            foreach ($args as $key => $arg) {
                if (strpos($arg, '.') !== false) {
                    $address .= "'+v". $key. "+'". SEP;
                } else {
                    $address .= $arg. SEP;
                }
            }
        }

        tag::$view->script($script. "
                xmlhttp.open('GET', '".$address."', true);
                xmlhttp.send();
            }
        ");
        return $this;
    }

    /**
     * This function print XHTML scripts on the page.
     */
    final protected function render()
    {
        $this->rendered = true;

        if ($this->renderStream != null) {
            return strval($this->renderStream);
        }

        $stream = $this->prefix;
        $stream .= '<'. $this->tagname;

        foreach($this->set as $attribute => $value) {
            $stream .= ' '. $attribute. '="'. $value. '"';
        }

        if(!$this->normal_element) {
            $stream .= '/>';
        }
        else $stream .= '>'. $this->prehas. $this->has. '</'. $this->tagname. '>';

        $stream .= $this->suffix;

        return $stream;
    }
}
