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


RVWcomments::saveOptions( $showStats, $includeJquery, $sendMail, $sendMailAddress );
?>