<?php

$_appcfg = include_once EASYSWOOLE_ROOT . '/App/Common/Config/index.php';

return [
        "LOG" => [
                'dir' => EASYSWOOLE_ROOT . '/Log/',
            ] + $_appcfg['LOG'],

        'UPLOAD' => [
            'dir' => EASYSWOOLE_ROOT . '/Public/',
            'domain' => 'http://image-admin-easyswoole.develop',
        ],
        'export_dir' => EASYSWOOLE_ROOT . '/Public/excel/',
    ] + $_appcfg;
