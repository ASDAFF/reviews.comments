<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$commentId = intval( $_POST['COMMENT_ID'] );
$vote = htmlspecialchars( $_POST['VOTE'] );


if ( $commentId && $vote )
{
	require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );


	CModule::IncludeModule( 'reviews.comments' );
	
	
	RVWcomments::addVote( $commentId, $vote );
	
	
	require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php' );
}
?>