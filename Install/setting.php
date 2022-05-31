<?php
declare(strict_types=1);
//[
//    'demo' => [
//        [
//            'setting_key' => 'demo_string',
//            'setting_description' => '字符串类型配置示例',
//            'setting_value' => '示例字符串',
//            'type' => 'string',
//        ]
//    ]
//];
return [
    'wechat_work' => [
        [
            'setting_key' => 'wechat_work_aes_key',
            'setting_description' => '应用aes_key',
            'setting_value' => '',
            'type' => 'string',
        ],
        [
            'setting_key' => 'wechat_work_token',
            'setting_description' => '应用token',
            'setting_value' => '',
            'type' => 'string',
        ],
        [
            'setting_key' => 'wechat_work_secret',
            'setting_description' => '应用secret',
            'setting_value' => '',
            'type' => 'string',
        ],
        [
            'setting_key' => 'wechat_work_suite_id',
            'setting_description' => '应用suite_id',
            'setting_value' => '',
            'type' => 'string',
        ],
        [
            'setting_key' => 'wechat_openwork_secret',
            'setting_description' => '第三方应用secret',
            'setting_value' => '',
            'type' => 'string',
        ],
        [
            'setting_key' => 'wechat_openwork_corpid',
            'setting_description' => '第三方应用corpid',
            'setting_value' => '',
            'type' => 'string',
        ],
    ],
];