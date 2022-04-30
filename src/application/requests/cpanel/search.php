<?php

class REQ_Search extends Staticy_Unit
{
    public $_sfw_dont_log = false;

    function main()
    {
        auth::cpoint(4096, AUTH::OPOR, true);
        load::lib('str', 'str/cpanel_' . data::ifcookie('lang', DEFAULT_UI_LANG));

        $keyword = data::post('keyword');
        if ($keyword == '') {
            data::session('search_keyword', '');
            dirto(cd('cpanel/search'));
        }

        /*
         * driversforms table
         */
        db::get('driversforms', [
            'Form_SN',
            'Fullname',
            'Phone'
        ], [
            'OR' => [
                'Form_SN'         => $keyword,
                'Fullname[~]'     => $keyword,
                'Phone[~]'         => $keyword,
                'Email[~]'         => $keyword
            ],
            'ORDER' => ['Fullname', 'Form_SN' => 'DESC'],
            'LIMIT' => 30
        ]);
        data::session('driversforms_search', db::res());
        if (db::so()) {
            $editor = data::editor(db::res());
            $editor->field('Fullname', tag::a(cd('cpanel/driverform/|Form_SN|'), '|Fullname|'));
            data::session('driversforms_search', $editor->getdata());
        }


        /*
         * users table
         */
        db::get('users', [
            'User_ID',
            'Type',
            'Fullname',
            'Phone'
        ], [
            'OR' => [
                'User_ID'         => $keyword,
                'Fullname[~]'     => $keyword,
                'Phone[~]'         => $keyword,
                'Email[~]'         => $keyword
            ],
            'ORDER' => ['Fullname', 'Type' => 'DESC'],
            'LIMIT' => 30
        ]);
        data::session('users_search', db::res());
        if (db::so()) {
            $editor = data::editor(db::res());
            $editor->field('Fullname', tag::a(cd('cpanel/user/|User_ID|'), '|Fullname|'));
            $editor->remove('User_ID');
            $users_search = $editor->getdata();

            foreach ($users_search as $key => $user) {
                $users_search[$key]['Type'] = load::unit('models/assets', 'get_users_types')[$user['Type']]['string'];
            }
            data::session('users_search', $users_search);
        }


        data::session('search_keyword', $keyword);
        dirto(cd('cpanel/search'));
    }
}
