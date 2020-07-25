<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!class_exists('RVWcomments')) {
    class RVWcomments
    {
        static function needConvert()
        {
            $arSiteFilter = Array(
                'ACTIVE' => 'Y',
                'ID' => SITE_ID
            );
            $dbSite = CSite::GetList($by = 'ID', $order = 'ASC', $arSiteFilter);
            if ($arSite = $dbSite->Fetch()) {
                if (strstr(strtolower($arSite['CHARSET']), 'windows') || strstr($arSite['CHARSET'], '1251')) {
                    return 'Y';
                }
            }

            return 'N';
        }

        function getComments($url)
        {
            global $DB;

            return $DB->Query('SELECT DATE,NAME,COMMENT,RATING,IMAGE_ID FROM reviews_comments WHERE URL="' . $url . '" AND ACTIVE="Y" ORDER BY DATE DESC');
        }


        function addComment($name, $rating, $text, $url, $imageId)
        {

            if (RVWcomments::needConvert() == 'Y') {
                $name = iconv('UTF-8', 'Windows-1251', $name);
                $text = iconv('UTF-8', 'Windows-1251', $text);
            }


            global $DB;

            $active = 'Y';
            if (COption::GetOptionString('reviews.comments', 'ENABLE_MODERATION') == 'Y') {
                $active = 'N';
            }

            return $DB->Query('INSERT INTO 
								reviews_comments 
								(ACTIVE,DATE,NAME,RATING,COMMENT,URL,IMAGE_ID) 
								VALUES 
								("' . $active . '",' . time() . ',"' . $name . '",' . $rating . ',"' . $text . '","' . $url . '",' . $imageId . ')');
        }


        function sendMail($url, $name, $rating, $text, $arSites)
        {
            if (RVWcomments::needConvert() == 'Y') {
                $name = iconv('UTF-8', 'Windows-1251', $name);
                $text = iconv('UTF-8', 'Windows-1251', $text);
            }


            $emailTo = COption::GetOptionString('reviews.comments', 'SEND_MAIL_ADDRESS');

            $arFields = Array(
                'URL' => 'http://' . $_SERVER['HTTP_HOST'] . $url,
                'NAME' => $name,
                'RATING' => $rating,
                'TEXT' => $text,
                'EMAIL_TO' => $emailTo,
                'MODULE_PAGE' => 'http://' . $_SERVER['HTTP_HOST'] . '/bitrix/admin/settings.php?lang=ru&mid=reviews.comments'
            );
            CEvent::Send('REVIEWS_COMMENT', $arSites, $arFields);
        }


        function getModerationComments()
        {
            global $DB;

            return $DB->Query('SELECT ID,URL,DATE,NAME,COMMENT,RATING,IMAGE_ID FROM reviews_comments WHERE ACTIVE="N" ORDER BY DATE DESC');
        }


        function getListComments()
        {
            global $DB;

            return $DB->Query('SELECT ID,URL,DATE,NAME,COMMENT,RATING,IMAGE_ID FROM reviews_comments WHERE ACTIVE="Y" ORDER BY DATE DESC');
        }


        function activateComment($commentId)
        {
            global $DB;

            $DB->Query('UPDATE reviews_comments SET ACTIVE="Y" WHERE ID=' . $commentId);
        }


        function deleteComment($commentId)
        {
            global $DB;

            $DB->Query('DELETE FROM reviews_comments WHERE ID=' . $commentId);
        }


        function saveOptions($showStats, $includeJquery, $sendMail, $sendMailAddress, $enableModeration)
        {
            if ($showStats) {
                COption::SetOptionString('reviews.comments', 'SHOW_STATS', $showStats);
            }

            if ($includeJquery) {
                COption::SetOptionString('reviews.comments', 'INCLUDE_JQUERY', $includeJquery);
            }

            if ($sendMail) {
                COption::SetOptionString('reviews.comments', 'SEND_MAIL', $sendMail);
            }

            COption::SetOptionString('reviews.comments', 'SEND_MAIL_ADDRESS', $sendMailAddress);

            if ($enableModeration) {
                COption::SetOptionString('reviews.comments', 'ENABLE_MODERATION', $enableModeration);
            }
        }
    }
}
?>