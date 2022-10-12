<?php

$_appcfg = include_once EASYSWOOLE_ROOT . '/App/Common/Config/index.php';

return array_merge_multi($_appcfg, [
    'LOG' => [
        'logConsole' => true,
        'dir' => EASYSWOOLE_ROOT . '/Log/',
    ],

    'UPLOAD' => [
        'dir' => EASYSWOOLE_ROOT . '/Public/',
        'domain' => 'http://image-admin-easyswoole.develop',
    ],
    'export_dir' => EASYSWOOLE_ROOT . '/Public/excel/',
]);
