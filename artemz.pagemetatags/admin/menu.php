<?php
/**
 * Created by PhpStorm
 * User: Artem Zinatullin
 * Date: 25.06.2022 10:20
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$menu = [
//    [
//        'parent_menu' => 'global_menu_content',
//        'sort' => 2000,
//        'text' => Loc::getMessage('PAGEMETATAGS_MENU_TITLE'),
//        'title' => Loc::getMessage('PAGEMETATAGS_MENU_TITLE'),
//        'url' => 'artemz-pagemetatags.php',
//        'items_id' => 'menu-references',
//        'items' => [
//            [
//                'text' => Loc::getMessage('PAGEMETATAGS_SUBMENU_TITLE'),
//                'url' => 'artemz-pagemetatags.php?page=\'settings\'',
//                'title' => Loc::getMessage('PAGEMETATAGS_SUBMENU_TITLE'),
//            ],
//        ],
//    ],
];

return $menu;