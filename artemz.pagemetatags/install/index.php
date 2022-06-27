<?php
/**
 * Created by PhpStorm
 * User: Artem Zinatullin
 * Date: 25.06.2022 10:31
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class artemz_pagemetatags extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'artemz.pagemetatags';
        $this->MODULE_NAME = Loc::getMessage('PAGEMETATAGS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('PAGEMETATAGS_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('PAGEMETATAGS_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'mail@artem3.ru';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        $arFieldsIBT = [
            'ID' => str_replace('.', '_', $this->MODULE_ID),
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 500,
            'LANG' => [
                'en' => [
                    'NAME' => Loc::getMessage('PAGEMETATAGS_MODULE_NAME'),
                ],
                'ru' => [
                    'NAME' => Loc::getMessage('PAGEMETATAGS_MODULE_NAME'),
                ]
            ]
        ];

        if ($this->addIblockType($arFieldsIBT)) {
            $arFieldsForIblock = [
                "ACTIVE" => "Y",
                "NAME" => Loc::getMessage('PAGEMETATAGS_MODULE_NAME'),
                "CODE" => $arFieldsIBT["ID"],
                "IBLOCK_TYPE_ID" => $arFieldsIBT["ID"],
                "SITE_ID" => "s1",
                "GROUP_ID" => ["2" => "R"],
                "FIELDS" => [
                    "CODE" => [
                        "IS_REQUIRED" => "Y",
                        "DEFAULT_VALUE" => [
                            "TRANS_CASE" => "L",
                            "UNIQUE" => "Y",
                            "TRANSLITERATION" => "Y",
                            "TRANS_SPACE" => "-",
                            "TRANS_OTHER" => "-"
                        ]
                    ]
                ]
            ];

            if ($iBlockId = $this->addIblock($arFieldsForIblock)) {
                $arFieldsUrl = [
                    'NAME' => 'URL',
                    'ACTIVE' => 'Y',
                    'SORT' => '10',
                    'CODE' => 'URL',
                    'PROPERTY_TYPE' => 'S',
                    'COL_COUNT' => '60',
                    'IBLOCK_ID' => $iBlockId,
                    'WITH_DESCRIPTION' => 'N'
                ];
                $arFieldsTitle = [
                    "NAME" => "TITLE",
                    'ACTIVE' => 'Y',
                    'SORT' => '20',
                    'CODE' => 'TITLE',
                    'PROPERTY_TYPE' => 'S',
                    'COL_COUNT' => '60',
                    'IBLOCK_ID' => $iBlockId,
                    'WITH_DESCRIPTION' => 'N'
                ];
                $arFieldsDesc = [
                    'NAME' => 'DESCRIPTION',
                    'ACTIVE' => 'Y',
                    'SORT' => '30',
                    'CODE' => 'DESCRIPTION',
                    'PROPERTY_TYPE' => 'S',
                    'COL_COUNT' => '60',
                    'IBLOCK_ID' => $iBlockId,
                    'WITH_DESCRIPTION' => 'N'
                ];
                $arFieldsH1 = [
                    'NAME' => 'H1',
                    'ACTIVE' => 'Y',
                    'SORT' => '30',
                    'CODE' => 'H1',
                    'PROPERTY_TYPE' => 'S',
                    'COL_COUNT' => '60',
                    'IBLOCK_ID' => $iBlockId,
                    'WITH_DESCRIPTION' => 'N'
                ];
                $this->addProp($arFieldsUrl);
                $this->addProp($arFieldsTitle);
                $this->addProp($arFieldsDesc);
            //    $this->addProp($arFieldsH1);

                RegisterModuleDependences('main','OnBeforeEndBufferContent', $this->MODULE_ID, 'PageMetatags\\Main','setMetaTags');
                RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'PageMetatags\\Main', 'content');
            }

        }
    }

    public function doUninstall()
    {
        UnRegisterModuleDependences('main','OnBeforeEndBufferContent', $this->MODULE_ID, 'PageMetatags\\Main','setMetaTags');
        UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, 'PageMetatags\\Main', 'content');
    //    $this->dellIblockType();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    private function addIblockType($arFieldsIBT)
    {
        global $DB;
        CModule::IncludeModule('iblock');

        $iblockType = $arFieldsIBT["ID"];

        // Работа с типом инфоблока
        // проверяем наличие нужного типа инфоблока
        $dbIblockType = CIBlockType::GetList(['SORT' => 'ASC'], ['ID' => $iblockType]);
        // если его нет - создаём
        if (!$arIblockType = $dbIblockType->Fetch()) {
            $obBlocktype = new CIBlockType;
            $DB->StartTransaction();
            $resIBT = $obBlocktype->Add($arFieldsIBT);
            if (!$resIBT) {
                $DB->Rollback();
                echo 'Error: ' . $obBlocktype->LAST_ERROR;
                die();
            } else {
                $DB->Commit();
            }
        } else {
            return false;
        }

        return $iblockType;
    }

    private function addIblock($arFieldsIB)
    {
        CModule::IncludeModule('iblock');

        $iblockCode = $arFieldsIB['CODE'];
        $iblockType = $arFieldsIB['TYPE'];

        $ib = new CIBlock;

        // проверка на наличие создание/обновление
        $resIBE = CIBlock::GetList([], ['TYPE' => $iblockType, 'CODE' => $iblockCode]);
        if ($arResIBE = $resIBE->Fetch()) {
            return false; // желаемый код занят
        } else {
            $ID = $ib->Add($arFieldsIB);
            $iblockID = $ID;
        }

        return $iblockID;
    }

    private function addProp($arFieldsProp)
    {
        CModule::IncludeModule('iblock');

        $ibp = new CIBlockProperty;
        $propID = $ibp->Add($arFieldsProp);

        return $propID;
    }

    private function dellIblocks()
    {
        global $DB;
        CModule::IncludeModule('iblock');

        $DB->StartTransaction();
        if (!CIBlockType::Delete($this->IBLOCK_TYPE)) {
            $DB->Rollback();
            CAdminMessage::ShowMessage([
                "TYPE" => 'ERROR',
                "MESSAGE" => GetMessage('VTEST_IBLOCK_TYPE_DELETE_ERROR'),
                "DETAILS" => '',
                "HTML" => true
            ]);
        }
        $DB->Commit();
    }

    private function dellIblockType()
    {
        CModule::IncludeModule('iblock');
        CIBlockType::Delete(str_replace('.', '_', $this->MODULE_ID));
    }

}