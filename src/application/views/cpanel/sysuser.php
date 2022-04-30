<?php

tag::$view->of('~' . data::$temp['subject']);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->addCSS('vex/vex');
tag::$view->addCSS('vex/vex-theme-flat-attack');
tag::$view->addJS('vex/vex.combined.min');
tag::$view->script("vex.defaultOptions.className = 'vex-theme-flat-attack'");
tag::$view->link('http://www.fontstatic.com/f=jazeera', ['rel' => 'stylesheet', 'type' => 'text/css']);
tag::$view->html->set('dir', str::DIR);

tag::$view->script("
    $(document).ready(function() {
        $('button.delete_form-btn').click(function() {
            vex.dialog.confirm({
                message: '" . str::do_del_employee . "',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, { text: '" . str::yup . "' }),
                    $.extend({}, vex.dialog.buttons.NO, { text: '" . str::cancel . "' })
                ],
                callback: function (value) {
                    if (value) {
                        $.post('" . APP_URL . SEP . "',
                        {
                            _Staticy_REQ_ : 'cpanel/delete_sysuser',
                            user_no : " . data::$args[0] . "
                        },
                        function(result, status) {
                            if (result != '#success#' && result != '#fail#') {
                                alert(result)
                            } else {
                                location.replace('" . cd('cpanel/sysusers') . "')
                            }
                        });
                    }
                }
            })
        });
    });
");

load::view('cpanel/common/header');
tag::a(cd('cpanel/sysusers'), str::sys_employees)->sufln(2);

tag::button(str::del_the_employee)->set(['type' => 'button', 'class' => 'btn btn-danger btn-sm delete_form-btn'])->sufln(2);

$alert = load::$app->alert(str::alert_cpanel_sysuser_t, str::alert_cpanel_sysuser_f);

$f1 = tag::input_text($sysuser_data['Fullname'])->setName('fullname')->flag('required');
$f2 = tag::input_checkbox(load::unit('models/assets', 'get_perms_array'), $permissions, ['setName' => 'perms[]', 'sufln' => 1]);
tag::input_hidden(data::$args[0])->setName('user_no')->set('form', 'cpanel/edit_sysuser');

HTML::with(
    [$f1, $f2],
    'setClass',
    'form-control'
);

$form = tag::form_tabular(
    'cpanel/edit_sysuser',
    [str::fullname, str::granted_permissions],
    [$f1, $f2],
    str::edit
);

$edit_panel = tag::div('panel')->setClass('panel-default')->has([
    tag::div('panel-heading')->has([
        tag::h3(str::edit_employee_account)->setClass('panel-title')
    ]),
    tag::div('panel-body')->has(
        $form
    )
]);

$data_panel = tag::div('panel')->setClass('panel-primary')->has([
    tag::div('panel-heading')->has([
        tag::h3(
            tag::b($sysuser_data['Fullname'])->setStyle("font-family: 'jazeera';")
        )->setClass('panel-title')
    ]),
    tag::div('panel-body')->has([
        tag::p(str::username . ' ' . tag::kbd($sysuser_data['Username'])),
        tag::p(str::join_date . ': ' . date('d-m-Y H:i:s', strtotime($sysuser_data['Join_Date'])))
    ])
]);

tag::div('row')->has([
    tag::div('col-sm-3'),
    tag::div('col-sm-6')->has(
        $alert
    ),
    tag::div('col-sm-3')
]);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $data_panel
    ),
    tag::div('col-sm-2')
]);

tag::div('row')->has([
    tag::div('col-sm-2'),
    tag::div('col-sm-8')->has(
        $edit_panel
    ),
    tag::div('col-sm-2')
]);
