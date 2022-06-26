<?php
/**
 * Created by PhpStorm
 * User: Artem Zinatullin
 * Date: 25.06.2022 14:59
 */

Bitrix\Main\Loader::registerAutoloadClasses(
    'artemz.pagemetatags',
    [
        'PageMetatags\\Main' => 'lib/main.php',
    ]
);