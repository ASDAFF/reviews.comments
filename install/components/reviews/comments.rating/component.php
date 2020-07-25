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


if ( !$arParams['URL'] )
{
	$arParams['URL'] = $APPLICATION->GetCurPage();
}




if ( $this->StartResultCache( $arParams['CACHE_TIME'] ) )
{

	$arResult['RATING_SUM'] = 0;
	$arResult['COMMENTS_COUNT'] = 0;
	$arResult['AVERAGE_RATING'] = 0;
	
	
	
	$dbComments = RVWcomments::getComments( $arParams['URL'] );
	while ( $arComment = $dbComments->Fetch() )
	{
		$arResult['COMMENTS_COUNT']++;
		$arResult['RATING_SUM'] += $arComment['RATING'];
		$arResult['STATS'][$arComment['RATING']]['COUNT']++;
	}

	
	if ( $arResult['COMMENTS_COUNT'] > 0 )
	{
		$arResult['AVERAGE_RATING'] = $arResult['RATING_SUM'] / $arResult['COMMENTS_COUNT'];
		$arResult['STARS_WIDTH'] = $arResult['AVERAGE_RATING'] / 5 * 100;
	}

	
	
	$this->EndResultCache();
}



$this->IncludeComponentTemplate();