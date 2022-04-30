<?php

tag::$view->of('~إنضم إلينا');
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->link('http://www.fontstatic.com/f=jazeera,assaf,stc,bahij', ['rel' => 'stylesheet', 'type' => 'text/css']);
tag::$view->html->set('dir', 'rtl');

tag::div('row')->has([
    tag::div('col-sm-1'),
    tag::div('col-sm-10')->has(
        tag::a(cd(), tag::img('~img/LOGO.jpg', 150, 150)->setStyle('float: left')->preln())
    ),
    tag::div('col-sm-1')
]);

tag::div('row')->has([
    tag::div('col-sm-1'),
    tag::div('col-sm-10')->has([
        tag::img('~img/im01.png')->setClass('img-responsive')->preln(),
        tag::h2('إنضم لبيئة عمل صممت خصيصا لك')->setStyle('font-family: jazeera')
    ]),
    tag::div('col-sm-1')
]);

tag::div('row')->has([
    tag::div('col-sm-6')->has(tag::img('~img/im02.png')->setClass('img-responsive')),
    tag::div('col-sm-6')->has([

        tag::h2('- شروط الإشتراك')->setStyle('font-family: assaf'),
        tag::ul([
            tag::p('مراعاة نظافة السيارة والتكييف'),
            tag::p('أن تكون السيارة غير مظللة'),
            tag::p('وجود علامة التاكسي بمكان بارز بالسيارة'),
            tag::p('أن لا يتجاوز عمر موديل السيارة 12 سنة'),
            tag::p('استخراج تصديق النقل الطارئ من الجهات المختصة'),
            tag::p('تعبئة الإستمارة ببيانات صحيحة وإرفاق المستندات المطلوبة')
        ])->setStyle('font-family: stc'),
    ])
])->sufln();

tag::div('row')->has([
    tag::div('col-sm-1'),
    tag::div('col-sm-10')->has(
        tag::img('~img/im03.png')->setClass('img-responsive')
    ),
    tag::div('col-sm-1'),
])->sufln();

tag::div('row')->has([
    tag::div('col-sm-6')->has(tag::img('~img/im04.png')->setClass('img-responsive')),
    tag::div('col-sm-6')->has([

        tag::h2('- المستندات المطلوبة')->setStyle('font-family: assaf'),
        tag::ul([
            tag::p('صورة شخصية رسمية واضحة'),
            tag::p('صورة الرقم الوطني أو البطاقة القومية'),
            tag::p('صورة من النقل الطارئ'),
            tag::p('صورة من رخصة القيادة'),
            tag::p('صورة من شهادة البحث'),
            tag::p('صور خارجية للسيارة من الجوانب الأربعة مع مراعاة وضوح لوحة الترخيص بصورة الجانب الأمامي'),
            tag::p('صورة واحدة تظهر حالة المساحة الداخلية للسيارة')
        ])->setStyle('font-family: stc'),
    ])
])->sufln();

tag::div('row')->has([
    tag::div('col-sm-1'),
    tag::div('col-sm-10')->has([
        tag::a(cd('driverform'), 'إنني موافق على شروط الإشتراك')->set(['class' => 'btn btn-xl btn-warning', 'role' => 'button'])
    ]),
    tag::div('col-sm-1')
])->sufln(2);

load::view('footer');
