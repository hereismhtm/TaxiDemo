<?php

tag::$view->of('~' . str::cab_form);
tag::$view->addCSS(HTML::BOOTSTRAP);
tag::$view->link('http://www.fontstatic.com/f=jazeera,dubai,bahij', ['rel' => 'stylesheet', 'type' => 'text/css']);
tag::$view->html->set('dir', str::DIR);

tag::$view->script("
    $( document ).ready(function() {

        $(':submit').on('click', function() {
            if (
                $('input[name=Logo_img]').prop('disabled') == true &&
                $('input[name=Nat_img]').prop('disabled') == true &&
                $('input[name=Fullname]').val() != '' &&
                $('input[name=Gender]').val() != '' &&
                $('input[name=Phone]').val() != ''
            ) {
                $('#is_submit_clicked').val('yes');
            } else {
                $('#is_submit_clicked').val('no');
            }
        });

        $('.file-sent').attr({
            disabled: 'disabled'
        });
        $('.show-progress').attr({
            hidden: null,
            value: 1,
            max: 1
        });
    });

    $(':file').on('change', function() {

        var file = this.files[0];
        if (file.size > 10485760) {
            alert('" . str::maximum_pic_size_10mb . "');
            return;
        }
        if (file.type != 'image/jpeg' && file.type != 'image/png') {
            alert('" . str::upload_image_file_only . "');
            return;
        }

        var file_id = this.id;
        var dform = document.forms.namedItem('driverform');

        $.ajax({
            type: 'POST',
            data: new FormData(dform),
            cache: false,
            contentType: false,
            processData: false,

            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            $('#Progress_'+file_id).attr({
                                hidden: null,
                                value: e.loaded,
                                max: e.total,
                            });

                            if (e.loaded == e.total) {
                                $('#'+file_id).attr({
                                    disabled: 'disabled'
                                });
                            }
                        }
                    } , false);
                }
                return myXhr;
            },
        });
    });
");

if (data::issession('user_no')) {
    data::$temp['subject'] = null;
    load::view('cpanel/common/header');
} else {
    load::view('cpanel/common/lang_switcher');
}

$alert = load::$app->alert(
    str::alert_index_driverform_t,
    str::alert_index_driverform_f
);

if (!data::issession('user_no')) {
    tag::div('row')->has([
        tag::div('col-sm-1')->has(
            tag::a(cd(), tag::img('~img/LOGO.jpg', 80, 80)->preln())
        ),
        tag::div('col-sm-1')->has(
            data::$temp['lang_switcher']
        ),
        tag::div('col-sm-10')
    ]);
}

$f1 = tag::input_text(data::ifpost('Fullname'))->setName('Fullname')->flag('required');
$f2 = tag::input_radio(['M' => str::male, 'F' => str::female], data::ifpost('Gender'), ['sufsp' => 2, 'set' => ['name' => 'Gender', 'form' => 'driverform'], 'flag' => 'required']);
$f3 = tag::input_tel(data::ifpost('Phone'))->setName('Phone')->set(['maxlength' => 20, 'placeholder' => str::in_international_format_start_by_plus . CALLING_CODE])->flag('required');
$f4 = tag::input_email(data::ifpost('Email'))->setName('Email')->set('placeholder', str::optional);

$fi1 = tag::input_file()->setName('Logo_img')->flag('required');
$fi2 = tag::input_file()->setName('Nat_img')->flag('required');
$fi3 = tag::input_file()->setName('CarFront_img');
$fi4 = tag::input_file()->setName('CarRight_img');
$fi5 = tag::input_file()->setName('CarBack_img');
$fi6 = tag::input_file()->setName('CarLeft_img');
$fi7 = tag::input_file()->setName('CarInside_img');
$fi8 = tag::input_file()->setName('Emr_img');
$fi9 = tag::input_file()->setName('Lic_img');
$fi10 = tag::input_file()->setName('Cert_img');

$pfi1 = tag::progress()->setID('Progress_Logo_img')->flag('hidden');
$pfi2 = tag::progress()->setID('Progress_Nat_img')->flag('hidden');
$pfi3 = tag::progress()->setID('Progress_CarFront_img')->flag('hidden');
$pfi4 = tag::progress()->setID('Progress_CarRight_img')->flag('hidden');
$pfi5 = tag::progress()->setID('Progress_CarBack_img')->flag('hidden');
$pfi6 = tag::progress()->setID('Progress_CarLeft_img')->flag('hidden');
$pfi7 = tag::progress()->setID('Progress_CarInside_img')->flag('hidden');
$pfi8 = tag::progress()->setID('Progress_Emr_img')->flag('hidden');
$pfi9 = tag::progress()->setID('Progress_Lic_img')->flag('hidden');
$pfi10 = tag::progress()->setID('Progress_Cert_img')->flag('hidden');

$files_paths = data::ifsession('files_paths');
if ($files_paths == null) $files_paths = array();

foreach (load::$app->files_keys as $loc => $fileKey) {
    if (isset($files_paths[$fileKey])) {
        $f = 'fi' . ++$loc;
        $$f->setClass('file-sent');
        $f = 'p' . $f;
        $$f->setClass('show-progress');
    }
}

HTML::with(
    [$f1, $f2, $f3, $f4, $fi1, $fi2, $fi3, $fi4, $fi5, $fi6, $fi7, $fi8, $fi9, $fi10],
    'setClass',
    'form-control'
);

$form = tag::form_tabular('driverform', [
    '[*] ' . str::personal_pic . $pfi1,
    '[*] ' . str::national_number . $pfi2,
    str::car_front_side . $pfi3,
    str::car_right_side . $pfi4,
    str::car_back_side . $pfi5,
    str::car_left_side . $pfi6,
    str::car_inner_space . $pfi7,
    str::document_A . $pfi8,
    str::document_B . $pfi9,
    str::document_C . $pfi10,
    '[*] ' . str::fullname4th,
    '[*] ' . str::gender,
    '[*] ' . str::phone_number,
    str::email
], [$fi1, $fi2, $fi3, $fi4, $fi5, $fi6, $fi7, $fi8, $fi9, $fi10, $f1, $f2, $f3, $f4], str::send_form)
    ->set('enctype', 'multipart/form-data');

tag::input_hidden('no')->setName('is_submit_clicked')->setID('is_submit_clicked')->set('form', 'driverform');

$form_userid = data::issession('form_userid') ? ' >> ' . tag::span(str::user . ' ' . data::session('form_userid'))->setStyle('color: blue') : null;
tag::div('row')->has([
    tag::div('col-sm-3'),
    tag::div('col-sm-6')->has([
        $alert,
        tag::h3(str::captain_registration_form . $form_userid)->setStyle('font-family: jazeera'),
        tag::p(str::this_star_refer_to_required_fields),
        $form->setStyle('font-family: dubai'),
        tag::a(cd('new_driverform'), str::clear_form_inputs)->preln()
    ]),
    tag::div('col-sm-3')
])->sufln(2);

if (!data::issession('user_no')) {
    load::view('footer');
}
