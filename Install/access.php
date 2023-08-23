<?php
declare(strict_types=1);
// 菜单层级最多三级
//[
//    [
//        'parent_access_id' => 0,
//        'access_name' => '示例',
//        'uri' => 'demo/demo/none',
//        'params' => '',
//        'sort' => 100,
//        'is_menu' => 1,
//        'menu_icon' => 'el-icon-data-analysis',
//        'children' => []
//    ]
//]
return [
    [
        'parent_access_id' => 0,
        'access_name' => '微信管理',
        'uri' => 'wechat/wechat/none',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'line-icon-weixin',
        'children' => [
            [
                'access_name' => '应用列表',
                'uri' => 'wechat/wechat/index',
                'sort' => 100,
                'is_menu' => 1
            ],
            [
                'access_name' => '微信支付',
                'uri' => 'wechat/wxpay/index',
                'sort' => 100,
                'is_menu' => 1
            ],
            [
                'access_name' => '支付服务商',
                'uri' => 'wechat/wxpay/partner/setting',
                'sort' => 100,
                'is_menu' => 1
            ],
            [
                'access_name' => '企业微信',
                'uri' => 'wechat/wechat/work',
                'sort' => 100,
                'is_menu' => 1
            ],
        ]
    ]
];