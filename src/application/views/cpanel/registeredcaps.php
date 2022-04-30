<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->html->set('dir', str::DIR);

tag::$view->script("
    function filter_by_vclass(vclass) {
        var link = '" . cd('cpanel/registeredcaps') . "';
        link = link + '&vals=vclass/' + vclass;
        window.location.href = link;
    }
");

load::view('cpanel/common/header');

$select_vclass_div = tag::div('col-sm-3')->has(
    tag::select(load::$app->vehicles_classes, $vclass)->prehas(tag::option('--- ' . str::vehs_classification . ' ---', 'ALL'))->set('onChange', 'filter_by_vclass(this.value);')
);

$pagination_div = tag::div('col-sm-9')->has(
    tag::$pagination->echo()
);

tag::div('row')->has([
    (str::DIR == 'rtl' ? $select_vclass_div : $pagination_div),
    (str::DIR == 'rtl' ? $pagination_div : $select_vclass_div)
])->sufln();

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        tag::table(
            $captains_data,
            [str::numeric, str::situation, str::fullname, str::phone_number, str::model, str::plate]
        )->setClass('table-striped table-bordered')
    ),
    tag::div('col-sm-2')
]);
