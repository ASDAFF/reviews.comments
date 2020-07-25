<? if (!class_exists('RVWcomments')) {
    class RVWcomments
    {
        static function needConvert()
        {
            $arSiteFilter = Array('ACTIVE' => 'Y', 'ID' => SITE_ID);
            $dbSite = CSite::GetList($by = 'ID', $order = 'ASC', $arSiteFilter);
            if ($arSite = $dbSite->Fetch()) {
                if (strstr(strtolower($arSite['CHARSET']), 'windows') || strstr($arSite['CHARSET'], '1251')) {
                    return 'Y';
                }
            }
            return 'N';
        }

        static function clearCacheComponents()
        {
            $clearCacheComponents = COption::GetOptionString('reviews.comments', 'CLEAR_CACHE_COMPONENTS');
            if (!$clearCacheComponents) {
                return;
            }
            $_440941831 = explode(',', $clearCacheComponents);
            if (empty($_440941831)) {
                return;
            }
            foreach ($_440941831 as $_204554210) {
                $_1114790465 = str_replace(':', '/', $_204554210);
                BXClearCache(true, '/' . SITE_ID . '/' . $_1114790465 . '/');
            }
        }

        function getComments($url, $_252425372 = true)
        {
            global $DB;
            $active = '';
            if ($_252425372) {
                $active = 'AND ACTIVE="Y"';
            }
            return $DB->Query('SELECT ID,DATE,NAME,COMMENT,RATING,IMAGES_IDS,USER_ID,ACTIVE,ADVANTAGE,DISADVANTAGE FROM reviews_comments WHERE URL="' . $url . '" ' . $active . 'ORDER BY DATE DESC');
        }

        function addComment($name, $rating, $text, $url, $imageId, $userId = 0, $email = '', $phone = '', $advantage = '', $disadvantage = '')
        {
            if (RVWcomments::needConvert() == 'Y') {
                $name = iconv('UTF-8', 'Windows-1251', $name);
                $text = iconv('UTF-8', 'Windows-1251', $text);
                $advantage = iconv('UTF-8', 'Windows-1251', $advantage);
                $disadvantage = iconv('UTF-8', 'Windows-1251', $disadvantage);
            }
            $text = str_replace('\\', '/', $text);
            global $DB;
            $active = 'Y';
            if (COption::GetOptionString('reviews.comments', 'ENABLE_MODERATION') == 'Y') {
                $active = 'N';
            }
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
            return $DB->Query('INSERT INTO 
								reviews_comments 
								(ACTIVE,DATE,NAME,RATING,COMMENT,URL,IMAGES_IDS,USER_ID,EMAIL,PHONE,ADVANTAGE,DISADVANTAGE) 
								VALUES 
								("' . $active . '",' . time() . ',"' . $name . '",' . $rating . ',"' . $text . '","' . $url . '","' . $imageId . '",' . $userId . ',"' . $email . '","' . $phone . '","' . $advantage . '","' . $disadvantage . '")');
        }

        function editComment($commentId, $_1067754885, $name, $rating, $text, $_2127864769 = '', $advantage = '', $disadvantage = '')
        {
            if (RVWcomments::needConvert() == 'Y') {
                $name = iconv('UTF-8', 'Windows-1251', $name);
                $text = iconv('UTF-8', 'Windows-1251', $text);
                $_2127864769 = iconv('UTF-8', 'Windows-1251', $_2127864769);
                $advantage = iconv('UTF-8', 'Windows-1251', $advantage);
                $disadvantage = iconv('UTF-8', 'Windows-1251', $disadvantage);
            }
            $text = str_replace('\\', '/', $text);
            $_1899366073 = explode('', $_1067754885);
            $_602473921 = explode('.', $_1899366073[0]);
            $_309858299 = $_602473921[2] . '-' . $_602473921[1] . '-' . $_602473921[0] . '' . $_1899366073[1];
            $_1482062631 = strtotime($_309858299);
            global $DB;
            $imageId = '';
            $_1256108957 = Array();
            $_2031691902 = $DB->Query('SELECT IMAGES_IDS FROM reviews_comments WHERE ID=' . $commentId);
            if ($_193508113 = $_2031691902->Fetch()) {
                if ($_193508113['IMAGES_IDS']) {
                    $_1256108957 = explode(',', $_193508113['IMAGES_IDS']);
                    $_1587769490 = Array();
                    foreach ($_POST as $_1862631971 => $_1478981721) {
                        if (strstr($_1862631971, 'COMMENT_PICTURE_DELETE')) {
                            $_2088979180 = substr($_1862631971, strlen('COMMENT_PICTURE_DELETE_'));
                            if ($_1478981721 == 'Y') {
                                $_1587769490[] = $_2088979180;
                            }
                        }
                    }
                    if (!empty($_1587769490)) {
                        foreach ($_1256108957 as $_41787181 => $_872148740) {
                            if (in_array($_872148740, $_1587769490)) {
                                unset($_1256108957[$_41787181]);
                            }
                        }
                    }
                }
            }
            for ($_484185890 = 1; $_484185890 <= 20; $_484185890++) {
                if (isset($_FILES['COMMENT_PICTURE_' . $_484185890]) && !empty($_FILES['COMMENT_PICTURE_' . $_484185890])) {
                    $_699138148 = array_merge($_FILES['COMMENT_PICTURE_' . $_484185890]);
                    $_699138148['del'] = ${'image_del'};
                    $_699138148['MODULE_ID'] = 'comments';
                    $_1866739666 = CFile::SaveFile($_699138148, 'comments_images');
                    $_1256108957[] = $_1866739666;
                }
            }
            if (!empty($_1256108957)) {
                foreach ($_1256108957 as $_41787181 => $_813875360) {
                    if (!$_813875360) {
                        unset($_1256108957[$_41787181]);
                    }
                }
                $imageId = implode(',', $_1256108957);
            }
            $name = str_replace('"', '\"', $name);
            $text = str_replace('"', '\"', $text);
            $_465813820 = 'UPDATE reviews_comments SET DATE=' . $_1482062631 . ',NAME="' . $name . '",RATING=' . $rating . ',COMMENT="' . $text . '",IMAGES_IDS="' . $imageId . '",ADVANTAGE="' . $advantage . '",DISADVANTAGE="' . $disadvantage . '"';
            $_465813820 .= ' WHERE ID=' . $commentId;
            $_82889122 = $DB->Query($_465813820);
            $_465813820 = 'SELECT ID FROM reviews_comments_answers WHERE COMMENT_ID=' . $commentId;
            if ($_1383944156 = $DB->Query($_465813820)) {
                if ($_121273323 = $_1383944156->Fetch()) {
                    $_465813820 = 'UPDATE reviews_comments_answers SET TEXT="' . $_2127864769 . '" WHERE COMMENT_ID=' . $commentId;
                    $DB->Query($_465813820);
                } else {
                    $_465813820 = 'INSERT reviews_comments_answers (COMMENT_ID,TEXT) VALUES (' . $commentId . ',"' . $_2127864769 . '")';
                    $DB->Query($_465813820);
                }
            }
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
            return $_82889122;
        }

        function sendMail($url, $name, $rating, $text, $arSites)
        {
            if (RVWcomments::needConvert() == 'Y') {
                $name = iconv('UTF-8', 'Windows-1251', $name);
                $text = iconv('UTF-8', 'Windows-1251', $text);
            }
            $emailTo = COption::GetOptionString('reviews.comments', 'SEND_MAIL_ADDRESS');
            $arFields = Array('URL' => 'http://' . $_SERVER['HTTP_HOST'] . $url, 'NAME' => $name, 'RATING' => $rating, 'TEXT' => $text, 'EMAIL_TO' => $emailTo, 'MODULE_PAGE' => 'http://' . $_SERVER['HTTP_HOST'] . '/bitrix/admin/settings.php?lang=ru&mid=reviews.comments');
            CEvent::Send('REVIEWS_COMMENT', $arSites, $arFields);
        }

        function getModerationComments()
        {
            global $DB;
            return $DB->Query('SELECT ID,URL,DATE,NAME,COMMENT,RATING,IMAGES_IDS,EMAIL,PHONE,ADVANTAGE,DISADVANTAGE FROM reviews_comments WHERE ACTIVE="N" ORDER BY DATE DESC');
        }

        function getListComments()
        {
            global $DB;
            return $DB->Query('SELECT ID,URL,DATE,NAME,COMMENT,RATING,IMAGES_IDS,EMAIL,PHONE,ADVANTAGE,DISADVANTAGE FROM reviews_comments WHERE ACTIVE="Y" ORDER BY DATE DESC');
        }

        function activateComment($commentId)
        {
            global $DB;
            $DB->Query('UPDATE reviews_comments SET ACTIVE="Y" WHERE ID=' . $commentId);
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
        }

        function deleteComment($commentId)
        {
            global $DB;
            $_1195508854 = '';
            $_1055471779 = $DB->Query('SELECT IMAGES_IDS FROM reviews_comments WHERE ID=' . $commentId);
            if ($_699138148 = $_1055471779->Fetch()) {
                $_1195508854 = $_699138148['IMAGES_IDS'];
            }
            if ($_1195508854) {
                $_1420088065 = explode(',', $_1195508854);
                foreach ($_1420088065 as $_1866739666) {
                    CFile::Delete($_1866739666);
                }
            }
            $DB->Query('DELETE FROM reviews_comments WHERE ID=' . $commentId);
            $DB->Query('DELETE FROM reviews_comments_voting WHERE COMMENT_ID=' . $commentId);
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
        }

        function saveOptions($showStats, $includeJquery, $sendMail, $sendMailAddress, $enableModeration, $enableImages, $onlyAuthorized, $showCommentsCount, $enableVoting, $moderationAccess, $answerTitle, $additionalFieldsRequired, $additionalFields, $enableUserPic, $clearCacheComponents)
        {
            if (strpos(SITE_CHARSET, 'windows') !== false || strpos(SITE_CHARSET, '1251') !== false) {
                $answerTitle = iconv('UTF-8', 'Windows-1251', $answerTitle);
            }
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
            if ($enableImages) {
                COption::SetOptionString('reviews.comments', 'ENABLE_IMAGES', $enableImages);
            }
            if ($onlyAuthorized) {
                COption::SetOptionString('reviews.comments', 'ONLY_AUTHORIZED', $onlyAuthorized);
            }
            if ($showCommentsCount) {
                COption::SetOptionString('reviews.comments', 'SHOW_COMMENTS_COUNT', $showCommentsCount);
            }
            if ($enableVoting) {
                COption::SetOptionString('reviews.comments', 'ENABLE_VOTING', $enableVoting);
            }
            COption::SetOptionString('reviews.comments', 'MODERATION_ACCESS', $moderationAccess);
            COption::SetOptionString('reviews.comments', 'ANSWER_TITLE', $answerTitle);
            COption::SetOptionString('reviews.comments', 'ADDITIONAL_FIELDS_REQUIRED', $additionalFieldsRequired);
            COption::SetOptionString('reviews.comments', 'ADDITIONAL_FIELDS', $additionalFields);
            if ($enableUserPic) {
                COption::SetOptionString('reviews.comments', 'ENABLE_USERPIC', $enableUserPic);
            }
            COption::SetOptionString('reviews.comments', 'CLEAR_CACHE_COMPONENTS', $clearCacheComponents);
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
        }

        function getVotes()
        {
            $_2051273253 = Array();
            global $DB;
            $_465813820 = 'SELECT COMMENT_ID,VOTE FROM reviews_comments_voting';
            $_1168426370 = $DB->Query($_465813820);
            while ($_504490177 = $_1168426370->Fetch()) {
                switch ($_504490177['VOTE']) {
                    case 'UP':
                        $_2051273253[$_504490177['COMMENT_ID']]['UP']++;
                        break;
                    case 'DOWN':
                        $_2051273253[$_504490177['COMMENT_ID']]['DOWN']++;
                        break;
                }
            }
            return $_2051273253;
        }

        function addVote($commentId, $_1184850657)
        {
            global $APPLICATION;
            $APPLICATION->set_cookie('REVIEWS_COMMENTS_VOTED_FOR_' . $commentId, $_1184850657);
            global $DB;
            $_465813820 = 'INSERT INTO reviews_comments_voting (COMMENT_ID,VOTE) VALUES (' . $commentId . ',"' . $_1184850657 . '")';
            $DB->Query($_465813820);
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
        }

        function getAnswers()
        {
            $_380178908 = Array();
            global $DB;
            $_465813820 = 'SELECT COMMENT_ID,TEXT FROM reviews_comments_answers';
            if ($_490078313 = $DB->Query($_465813820)) {
                while ($_1673365986 = $_490078313->Fetch()) {
                    $_380178908[$_1673365986['COMMENT_ID']] = $_1673365986['TEXT'];
                }
            }
            return $_380178908;
        }

        function addAnswer($commentId, $text)
        {
            global $DB;
            $text = str_replace('\\', '/', $text);
            $DB->Query('INSERT reviews_comments_answers (COMMENT_ID,TEXT) VALUES (' . $commentId . ',"' . $text . '")');
            BXClearCache(true, '/' . SITE_ID . '/reviews/comments/');
            RVWcomments::clearCacheComponents();
        }
    }
}; ?>