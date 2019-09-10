<?php

use think\facade\Env;

return [
    'default' => Env::get('filesystem.driver', 'local'),
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            'type'       => 'local',
            'root'       => app()->getRootPath() . 'public/storage',
            'url'        => '/storage',
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
        'publicAdmin' => [
            'type'       => 'local',
            'root'       => app()->getRootPath() . 'public/uploads/admin',
            'url'        => '/storage/admin',
            'visibility' => 'public',
        ],
        'publicIndex' => [
            'type'       => 'local',
            'root'       => app()->getRootPath() . 'public/uploads/index',
            'url'        => '/storage/index',
            'visibility' => 'public',
        ],
    ],
];
