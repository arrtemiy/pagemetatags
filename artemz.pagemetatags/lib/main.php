<?php
/**
 * Created by PhpStorm
 * User: Artem Zinatullin
 * Date: 26.06.2022 18:47
 */

namespace PageMetatags;

use CIBlockElement;
use CModule;

class Main
{

    public static function setMetaTags()
    {

        global $APPLICATION;

        CModule::IncludeModule('iblock');

        $protocol = ($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url = $protocol.$_SERVER['SERVER_NAME'].explode('?', $_SERVER['REQUEST_URI'], 2)[0];

        $arReturn = [];
        $arFilter = ['=PROPERTY_URL' => $url, 'ACTIVE' => 'Y'];

        $rs = CIBlockElement::GetList([], $arFilter, false, false, []);

        if ($ob = $rs->GetNextElement()) {
            $arReturn = $ob->GetFields();
            $arReturn['PROPERTIES'] = $ob->GetProperties();
            if ($arReturn['PROPERTIES']['TITLE']['VALUE']) $APPLICATION->SetPageProperty('title', $arReturn['PROPERTIES']['TITLE']['VALUE']);
            if ($arReturn['PROPERTIES']['DESCRIPTION']['VALUE']) $APPLICATION->SetPageProperty('description', $arReturn['PROPERTIES']['DESCRIPTION']['VALUE']);
        }

        return $arReturn['PROPERTIES']['H1']['VALUE'];

    }

//    public static function content(&$content)
//    {
//        $h1 = self::setMetaTags();
//        if ($h1)
//            $content = preg_replace('/<h1(.*?)>(.*?)<\/h1/', '<h1$1>' . $h1 . '</h1', $content);
//    }
}
