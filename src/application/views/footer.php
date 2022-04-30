<?php

tag::div('row')->has([

    tag::div('col-sm-4')->has([
        tag::h1(TAXI_NAME)->preln(),
        tag::h4('ضع الشعار هنا')->sufln()
    ]),

    tag::div('col-sm-4')->has(
        tag::div()->has([
            tag::h4('اتصل بنا'),
            tag::p('هاتف: 0901111111'),
            tag::p('هاتف: 0902222222'),
            tag::p('إيميل: ' . CONTACT_MAIL)
        ])->preln()->sufln()
    ),

    tag::div('col-sm-4')->has(
        tag::div()->has([
            tag::h4('المكتب'),
            tag::p('شارع كذا، حي كذا'),
            tag::p('عمارة كذا'),
            tag::p('طابق كذا شقة رقم كذا')
        ])->preln()->sufln()
    )

])->setStyle('color: white; background-color: #2e7d32; font-family: bahij;');
