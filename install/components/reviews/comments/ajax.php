<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$url = htmlspecialchars( $_POST['URL'] );
$name = htmlspecialchars( $_POST['NAME'] );
$rating = intval( $_POST['RATING'] );
$text = htmlspecialchars( $_POST['TEXT'] );
$imagesIds = htmlspecialchars( $_POST['IMAGES_IDS'] );
$email = htmlspecialchars( $_POST['EMAIL'] );
$phone = htmlspecialchars( $_POST['PHONE'] );
$advantage = htmlspecialchars( $_POST['ADVANTAGE'] );
$disadvantage = htmlspecialchars( $_POST['DISADVANTAGE'] );


if ( $url && $name && $rating && $text )
{
	require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );
	
	IncludeTemplateLangFile(__FILE__);
	IncludeModuleLangFile(__FILE__);
	
	CModule::IncludeModule( 'reviews.comments' );
	
	
	global $USER;
	if ( !is_object( $USER ) )
	{
		$USER = new CUser;
	}
	
	
	$userId = 0;
	
	if ( $USER->IsAuthorized() )
	{
		$userId = $USER->GetID();
	}
	
	
	$dbResult = RVWcomments::addComment( $name, $rating, $text, $url, $imagesIds, $userId, $email, $phone, $advantage, $disadvantage );
								
	
	if ( $dbResult->result && COption::GetOptionString( 'reviews.comments', 'SEND_MAIL' ) == 'Y' )
	{
		$arFilter = Array(
			'TYPE_ID' => 'REVIEWS_COMMENT'
		);
		$dbEventType = CEventType::GetList( $arFilter, Array() );
		if ( $arEventType = $dbEventType->Fetch() )
		{
			$eventTypeId = $arEventType['ID'];
		}
		else
		{
			$et = new CEventType;		
				$arFields = Array(
					'LID' => 'ru',
					'EVENT_NAME' => 'REVIEWS_COMMENT',
					'NAME' => GetMessage( 'EVENT_TYPE_NAME' ),
					'DESCRIPTION' => '#URL# - ' . GetMessage( 'EVENT_TYPE_DESCRIPTION_URL' ) . '
#NAME# - ' . GetMessage( 'EVENT_TYPE_DESCRIPTION_NAME' ) . '
#RATING# - ' . GetMessage( 'EVENT_TYPE_DESCRIPTION_RATING' ) . '
#TEXT# - ' . GetMessage( 'EVENT_TYPE_DESCRIPTION_TEXT' )
				);		
				$eventTypeId = $et->Add( $arFields );
			}
			
			
			
			if ( $eventTypeId )
			{
				$arSites = Array();
					
				$dbSites = CSite::GetList( $by = '', $order = '', Array() );
				while ( $arSite = $dbSites->Fetch() )
				{
					$arSites[] = $arSite['LID'];
				}
					
					
					
				$arFilter = Array(
					'TYPE_ID' => 'REVIEWS_COMMENT'
				);
				$dbEventMessage = CEventMessage::GetList( $by = '', $order = '', $arFilter );
				if ( $arEventMessage = $dbEventMessage->Fetch() )
				{
					$eventMessageId = $arEventMessage['ID'];
				}
				else
				{
					$em = new CEventMessage;
					$messageHtml = GetMessage( 'EVENT_MESSAGE_TITLE' ) . ': <br /><br />
' . GetMessage( 'EVENT_MESSAGE_URL' ) . ': <a href="#URL#" target="_blank" title="' . GetMessage( 'OPEN_IN_NEW_WINDOW' ) . '">#URL#</a><br />
' . GetMessage( 'EVENT_MESSAGE_NAME' ) . ': #NAME#<br />
' . GetMessage( 'EVENT_MESSAGE_RATING' ) . ': #RATING#<br />
' . GetMessage( 'EVENT_MESSAGE_TEXT' ) . ': #TEXT#<br /><br />
' . GetMessage( 'EVENT_MESSAGE_EMAIL' ) . ': #EMAIL#<br /><br />
' . GetMessage( 'EVENT_MESSAGE_PHONE' ) . ': #PHONE#<br /><br />
' . GetMessage( 'EVENT_MESSAGE_ADVANTAGE' ) . ': #ADVANTAGE#<br /><br />
' . GetMessage( 'EVENT_MESSAGE_DISADVANTAGE' ) . ': #DISADVANTAGE#<br /><br />
' . GetMessage( 'EVENT_MESSAGE_MODERATION' ) . ' - <a href="#MODULE_PAGE#" target="_blank" title="' . GetMessage( 'OPEN_IN_NEW_WINDOW' ) . '">#MODULE_PAGE#</a>';
					$arFields = Array(
						'ACTIVE' => 'Y',
						'EVENT_NAME' => 'REVIEWS_COMMENT',
						'LID' => $arSites,
						'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
						'EMAIL_TO' => '#EMAIL_TO#',
						'SUBJECT' => '#SITE_NAME# | ' . GetMessage( 'EVENT_MESSAGE_SUBJECT' ),
						'BODY_TYPE' => 'html',
						'MESSAGE' => $messageHtml
					);
					$eventMessageId = $em->Add( $arFields );
				}
			}
			
			
			
			if ( $eventMessageId )
			{
				RVWcomments::sendMail( $url, $name, $rating, $text, $arSites );
			}
	}
}
?>