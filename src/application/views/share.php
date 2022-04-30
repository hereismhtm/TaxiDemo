<?php

tag::$view->of('~إنضم إلينا');
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->mainDiv->setStyle("
    background: url('" . cd('~img/LOGO.jpg') . "');
    background-color: #006838;
    background-repeat: no-repeat;
    padding: 30px;
    background-origin: content-box;
    background-position: center;
");


tag::div('row')->has([
    tag::div('col-sm-1'),
    tag::div('col-sm-10')->has([
        tag::h1(TAXI_NAME)->setStyle('color: #ffffff;'),
        tag::a(
            'https://play.google.com/store/apps/details?id=com.example.taxidemo',
            tag::img('~img/playstore.png', 300, 100)->setClass('center-block')
        )->preln(16),
        tag::a(
            '#',
            tag::img('~img/appstore.png', 300, 100)->setClass('center-block')
        )
    ]),
    tag::div('col-sm-1')
]);
