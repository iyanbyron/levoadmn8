<?php
return [
    'operation_log' => [
        'enable' => true,
        'except' => [
            'admin/operation*',
        ],
    ],
    //获取服务器状态
    'PUSH_MESSAGE_STATUS' => false,
    'PUSH_MESSAGE_INFO' => 'ws://127.0.0.1:3737',

];
