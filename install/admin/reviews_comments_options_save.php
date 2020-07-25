<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );

CModule::IncludeModule( 'reviews.comments' );

$showStats = htmlspecialchars( $_POST['SHOW_STATS'] );
$includeJquery = htmlspecialchars( $_POST['INCLUDE_JQUERY'] );
$sendMail = htmlspecialchars( $_POST['SEND_MAIL'] );
$sendMailAddress = htmlspecialchars( $_POST['SEND_MAIL_ADDRESS'] );
$enableModeration = htmlspecialchars( $_POST['ENABLE_MODERATION'] );
$enableImages = htmlspecialchars( $_POST['ENABLE_IMAGES'] );
$onlyAuthorized = htmlspecialchars( $_POST['ONLY_AUTHORIZED'] );
$showCommentsCount = intval( $_POST['SHOW_COMMENTS_COUNT'] );
$enableVoting = htmlspecialchars( $_POST['ENABLE_VOTING'] );
$moderationAccess = htmlspecialchars( $_POST['MODERATION_ACCESS'] );
$answerTitle = htmlspecialchars( $_POST['ANSWER_TITLE'] );
$additionalFieldsRequired = htmlspecialchars( $_POST['ADDITIONAL_FIELDS_REQUIRED'] );
$additionalFields = htmlspecialchars( $_POST['ADDITIONAL_FIELDS'] );
$enableUserpic = htmlspecialchars( $_POST['ENABLE_USERPIC'] );
$clearCacheComponents = htmlspecialchars( $_POST['CLEAR_CACHE_COMPONENTS'] );


RVWcomments::saveOptions( $showStats, $includeJquery, $sendMail, $sendMailAddress, $enableModeration, $enableImages, $onlyAuthorized, $showCommentsCount, $enableVoting, $moderationAccess, $answerTitle, $additionalFieldsRequired, $additionalFields, $enableUserpic, $clearCacheComponents );
?>