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
        'menu_icon' => 'el-icon-chat-dot-round',
        'children' => [
            [
                'access_name' => '应用列表',
                'uri' => 'wechat/wechat/index',
                'sort' => 100,
                'is_menu' => 1
            ]
        ]
    ]
];