<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

switch ( CModule::IncludeModuleEx( 'reviews.comments' ) )
{
	case 0:
		echo GetMessage( 'MODULE_NOT_INSTALLED' ); 
		break;
	
	case 2:
		echo GetMessage( 'MODULE_DEMO' ); 
		break;
		
	case 3:
		echo GetMessage( 'MODULE_DEMO_EXPIRED' ); 
		break;
}




$this->setFrameMode( true );


$additionalFieldsRequred = COption::GetOptionString( 'reviews.comments', 'ADDITIONAL_FIELDS_REQUIRED' );
$additionalFields = COption::GetOptionString( 'reviews.comments', 'ADDITIONAL_FIELDS' );

$arParams['OPTIONS'] = Array(
	'SHOW_STATS' => COption::GetOptionString( 'reviews.comments', 'SHOW_STATS' ),
	'INCLUDE_JQUERY' => COption::GetOptionString( 'reviews.comments', 'INCLUDE_JQUERY' ),
	'ENABLE_MODERATION' => COption::GetOptionString( 'reviews.comments', 'ENABLE_MODERATION' ),
	'SHOW_COMMENTS_COUNT' => COption::GetOptionString( 'reviews.comments', 'SHOW_COMMENTS_COUNT' ),
	'ENABLE_VOTING' => COption::GetOptionString( 'reviews.comments', 'ENABLE_VOTING' ),
	'ANSWER_TITLE' => COption::GetOptionString( 'reviews.comments', 'ANSWER_TITLE' ),
	'ADDITIONAL_FIELDS_REQUIRED' => explode( ',', $additionalFieldsRequred ),
	'ADDITIONAL_FIELDS' => explode( ',', $additionalFields ),
	'ENABLE_USERPIC' => COption::GetOptionString( 'reviews.comments', 'ENABLE_USERPIC' ),
);





$arParams['CAN_MODERATE'] = false;

if ( $USER->IsAdmin() )
{
	$arParams['CAN_MODERATE'] = true;
}
else
{
	$arUserGroups = $USER->GetUserGroupArray();

	$moderationAccess = COption::GetOptionString( 'reviews.comments', 'MODERATION_ACCESS' );
	$arModerationGroups = explode( ',', $moderationAccess );


	$arIntersect = array_intersect( $arUserGroups, $arModerationGroups );
	if ( !empty( $arIntersect ) )
	{
		$arParams['CAN_MODERATE'] = true;
	}
}





if ( $arParams['OPTIONS']['INCLUDE_JQUERY'] == 'Y' )
{
	CJSCore::Init( Array( 'jquery' ) );
}


global $APPLICATION;
if ( !is_object( $APPLICATION ) )
	$APPLICATION = new CMain;

$url = $APPLICATION->GetCurPage();
$arParams['URL'] = $url;



if ( $this->StartResultCache( $arParams['CACHE_TIME'] ) )
{
	
	if ( $arParams['OPTIONS']['ENABLE_VOTING'] == 'Y' )
	{
		$arVotes = RVWcomments::getVotes();
	}
	
	
	
	$arResult['RATING_SUM'] = 0;
	$arResult['COMMENTS_COUNT'] = 0;
	$arResult['AVERAGE_RATING'] = 0;
	$arResult['COMMENTS'] = Array();
	$arResult['STATS'] = Array(
		'5' => Array(
			'TITLE' => GetMessage( 'STATS_STARS_5' )
		),
		'4' => Array(
			'TITLE' => GetMessage( 'STATS_STARS_4' )
		),
		'3' => Array(
			'TITLE' => GetMessage( 'STATS_STARS_3' )
		),
		'2' => Array(
			'TITLE' => GetMessage( 'STATS_STARS_2' )
		),
		'1' => Array(
			'TITLE' => GetMessage( 'STATS_STARS_1' )
		)
	);
	
	
	
	
	$onlyActive = true;
	if ( $arParams['CAN_MODERATE'] )
	{
		$onlyActive = false;
	}
	
	
	
	$dbComments = RVWcomments::getComments( $url, $onlyActive );
	while ( $arComment = $dbComments->Fetch() )
	{
		$arResult['COMMENTS_COUNT']++;
		$arResult['RATING_SUM'] += $arComment['RATING'];
		$arResult['STATS'][$arComment['RATING']]['COUNT']++;
		
		$arImages = Array();
		if ( $arComment['IMAGES_IDS'] )
		{
			$arImagesIds = explode( ',', $arComment['IMAGES_IDS'] );
			
			if ( !empty( $arImagesIds ) )
			{
				foreach ( $arImagesIds as $imageId )
				{
					$image = CFile::GetPath( $imageId );
					$arImages[] = $image;
				}
			}
		}
		
		
		$userpic = false;
		if ( $arParams['ENABLE_USERPIC'] == 'Y' )
		{
			if ( $arComment['USER_ID'] )
			{
				global $DB;
				$dbUser = $DB->Query( 'SELECT PERSONAL_PHOTO FROM b_user WHERE ID=' . $arComment['USER_ID'] );
				if ( $arUser = $dbUser->Fetch() )
				{
					if ( $arUser['PERSONAL_PHOTO'] )
					{
						$pic = CFile::ResizeImageGet( $arUser['PERSONAL_PHOTO'], Array( 'width' => 54, 'height' => 54 ), BX_RESIZE_IMAGE_EXACT );
						$userpic = $pic['src'];
					}
				}
			}
		}
		
		
		$arCommentVotes = Array();
		if ( $arParams['OPTIONS']['ENABLE_VOTING'] == 'Y' && !empty( $arVotes[$arComment['ID']] ) )
		{
			$arCommentVotes = $arVotes[$arComment['ID']];
		}
		
		
		$voted = false;
		if ( $APPLICATION->get_cookie( 'REVIEWS_COMMENTS_VOTED_FOR_' . $arComment['ID'] ) )
		{
			$voted = true;
		}
		
		
		$arResult['COMMENTS'][] = Array(
			'ID' => $arComment['ID'],
			'NAME' => $arComment['NAME'],
			'COMMENT' => $arComment['COMMENT'],
			'RATING_WIDTH' => $arComment['RATING'] / 5 * 100,
			'DATE' => ConvertTimeStamp( $arComment['DATE'], 'SHORT' ),
			'IMAGES' => $arImages,
			'USERPIC' => $userpic,
			'RATING' => $arComment['RATING'],
			'VOTES' => $arCommentVotes,
			'VOTED' => $voted,
			'ACTIVE' => $arComment['ACTIVE'],
			'ADVANTAGE' => $arComment['ADVANTAGE'],
			'DISADVANTAGE' => $arComment['DISADVANTAGE'],
		);
	}
	
	$arResult['ANSWERS'] = RVWcomments::getAnswers();
	
	
	if ( $arResult['COMMENTS_COUNT'] > 0 )
	{
		$arResult['AVERAGE_RATING'] = $arResult['RATING_SUM'] / $arResult['COMMENTS_COUNT'];
		$arResult['STARS_WIDTH'] = $arResult['AVERAGE_RATING'] / 5 * 100;
		
		
		foreach ( $arResult['STATS'] as $k => $arStat )
		{
			$arResult['STATS'][$k]['WIDTH'] = $arStat['COUNT'] / $arResult['COMMENTS_COUNT'] * 100;
		}
	}
	
	$arResult['URL'] = $url;
	
	
	global $USER;
	if ( !is_object( $USER ) )
	{
		$USER = new CUser;
	}
	
	$arResult['SHOW_FORM'] = 'N';
	if ( COption::GetOptionString( 'reviews.comments', 'ONLY_AUTHORIZED' ) != 'Y' || $USER->IsAuthorized() )
	{
		$arResult['SHOW_FORM'] = 'Y';
	}
	
	
	
	$this->EndResultCache();
}



$this->IncludeComponentTemplate();
?>