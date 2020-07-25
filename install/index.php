<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);

Class reviews_comments extends CModule
{
    const MODULE_ID = 'reviews.comments';
    var $MODULE_ID = 'reviews.comments';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $strError = '';

    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'ASDAFF';
        $this->PARTNER_URI = 'https://asdaff.github.io/';
    }

    function InstallDB($arParams = array())
    {
        global $DB;
        if (!is_object($DB)) {
            $DB = new CDatabase;
        }
        $DB->Query("CREATE TABLE IF NOT EXISTS `reviews_comments` (
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `DATE` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `URL` varchar(255) CHARACTER SET utf8 NOT NULL,
  `NAME` varchar(255) CHARACTER SET utf8 NOT NULL,
  `COMMENT` varchar(4096) CHARACTER SET utf8 NOT NULL,
  `RATING` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL,
  `ACTIVE` enum('N','Y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `USER_ID` mediumint(8) DEFAULT NULL,
  `IMAGES_IDS` varchar(255) CHARACTER SET utf8 NOT NULL,
  `EMAIL` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `PHONE` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ADVANTAGE` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `DISADVANTAGE` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `URL` (`URL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $DB->Query("CREATE TABLE IF NOT EXISTS `reviews_comments_answers` (
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `COMMENT_ID` mediumint(8) unsigned NOT NULL,
  `TEXT` varchar(4096) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `COMMENT_ID` (`COMMENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

        $DB->Query("CREATE TABLE IF NOT EXISTS `reviews_comments_voting` (
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `COMMENT_ID` mediumint(8) unsigned NOT NULL,
  `VOTE` enum('UP','DOWN') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `COMMENT_ID` (`COMMENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");


        return true;
    }

    function UnInstallDB($arParams = array())
    {
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles($arParams = array())
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php') continue;
                    file_put_contents($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/' . self::MODULE_ID . '/admin/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true, true);

        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.') continue;
                    CopyDirFiles($p . '/' . $item, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $item, true, true);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function UnInstallFiles()
    {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.') continue;
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }

        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');

        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || !is_dir($path = $p . '/' . $item)) continue;
                    $folder = opendir($path);
                    while (false !== $elem = readdir($folder)) {
                        if ($elem == '..' || $elem == '.') continue;
                        DeleteDirFilesEx('/bitrix/components/' . $item . '/' . $elem);
                    }
                    closedir($folder);
                }
                closedir($dir);
            }
        }
        return true;
    }

    function DoInstall()
    {
        global $APPLICATION;
        $this->InstallFiles();
        $this->InstallDB();
        RegisterModule(self::MODULE_ID);

        COption::SetOptionString(self::MODULE_ID, 'SHOW_STATS', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'INCLUDE_JQUERY', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'SEND_MAIL', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'SEND_MAIL_ADDRESS', COption::GetOptionString('main', 'email_from'));

        COption::SetOptionString(self::MODULE_ID, 'ENABLE_IMAGES', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'ENABLE_MODERATION', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'ENABLE_USERPIC', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'ENABLE_VOTING', 'Y');
        COption::SetOptionString(self::MODULE_ID, 'ONLY_AUTHORIZED', 'N');
        COption::SetOptionString(self::MODULE_ID, 'SHOW_COMMENTS_COUNT	', '5');
    }

    function DoUninstall()
    {
        global $APPLICATION;
        UnRegisterModule(self::MODULE_ID);
        $this->UnInstallDB();
        $this->UnInstallFiles();
    }
} ?>