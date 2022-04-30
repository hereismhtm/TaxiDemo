<?php

$current_lang = data::ifcookie('lang', DEFAULT_UI_LANG);
$switch_lang_object = $current_lang === 'en' ? ['ar', 'عربي'] : ['en', 'English'];
$lang_switcher = tag::a(
    cd(
        'index/lang/' . $switch_lang_object[0] .
            '/' .
            $GLOBALS['_sfw_route']['unit'] .
            '/' .
            $GLOBALS['_sfw_route']['action'] .
            '/' .
            $GLOBALS['_sfw_route']['args']
    ),
    $switch_lang_object[1]
);

data::$temp['lang_switcher'] = $lang_switcher;
