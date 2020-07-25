<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );


$action = htmlspecialchars( $_POST['ACTION'] );
$commentId = htmlspecialchars( $_POST['ID'] );
$text = htmlspecialchars( $_POST['TEXT'] );


if ( $action && $commentId && CModule::IncludeModule( 'reviews.comments' ) )
{
	switch ( $action )
	{
		case 'ACTIVATE':
			RVWcomments::activateComment( $commentId );
			break;
			
		case 'DELETE':
			RVWcomments::deleteComment( $commentId );
			break;
			
		case 'ANSWER':
			RVWcomments::addAnswer( $commentId, $text );
			break;
	}
}
?>