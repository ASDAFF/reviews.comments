<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if ( !class_exists( 'RVWcomments' ) )
{
	class RVWcomments
	{
		static function needConvert()
		{
			$arSiteFilter = Array(
				'ACTIVE' => 'Y',
				'ID' => SITE_ID
			);
			$dbSite = CSite::GetList( $by = 'ID', $order = 'ASC', $arSiteFilter );
			if ( $arSite = $dbSite->Fetch() )
			{
				if ( strstr( strtolower( $arSite['CHARSET'] ), 'windows' ) || strstr( $arSite['CHARSET'], '1251' ) )
				{
					return 'Y';
				}
			}
			
			return 'N';
		}
		
		function getComments( $url )
		{
			global $DB;
			
			return $DB->Query( 'SELECT DATE,NAME,COMMENT,RATING,IMAGE_ID,USER_ID FROM reviews_comments WHERE URL="' . $url . '" AND ACTIVE="Y" ORDER BY DATE DESC' );
		}

		
		function addComment( $name, $rating, $text, $url, $imageId, $userId = 0 )
		{
			
			if ( RVWcomments::needConvert() == 'Y' )
			{
				$name = iconv( 'UTF-8', 'Windows-1251', $name );
				$text = iconv( 'UTF-8', 'Windows-1251', $text );
			}
			
			
			global $DB;
			
			$active = 'Y';
			if ( COption::GetOptionString( 'reviews.comments', 'ENABLE_MODERATION' ) == 'Y' )
			{
				$active = 'N';
			}
			
			return $DB->Query( 'INSERT INTO 
								reviews_comments 
								(ACTIVE,DATE,NAME,RATING,COMMENT,URL,IMAGE_ID,USER_ID) 
								VALUES 
								("' . $active . '",' . time() . ',"' . $name . '",' . $rating . ',"' . $text . '","' . $url . '",' . $imageId . ',' . $userId . ')' );
		}
		
		
		function editComment( $commentId, $date, $name, $rating, $text )
		{
			
			if ( RVWcomments::needConvert() == 'Y' )
			{
				$name = iconv( 'UTF-8', 'Windows-1251', $name );
				$text = iconv( 'UTF-8', 'Windows-1251', $text );
			}
			
			$arDateTime = explode( ' ', $date );
			$arDate = explode( '.', $arDateTime[0] );
			
			$timeTmp = $arDate[2] . '-' . $arDate[1] . '-' . $arDate[0] . ' ' . $arDateTime[1];
			
			$time = strtotime( $timeTmp );
			
			
			if ( isset( $_FILES['COMMENT_PICTURE'] ) )
			{
				$arFile = array_merge( $_FILES['COMMENT_PICTURE'] );
				$arFile['del'] = ${'image_del'};
				$arFile['MODULE_ID'] = 'comments';
				
				$fileId = CFile::SaveFile( $arFile, 'comments_images' );
			}
			
			
			global $DB;
			
			$query = 'UPDATE reviews_comments SET DATE=' . $time . ',NAME="' . $name . '",RATING=' . $rating . ',COMMENT="' . $text . '"';
			
			
			if ( $_POST['COMMENT_PICTURE_DELETE'] == 'Y' )
			{
				$query .= ',IMAGE_ID=NULL';
			}
			elseif ( $fileId )
			{
				$query .= ',IMAGE_ID=' . $fileId;
			}
			
			
			
			$query .= ' WHERE ID=' . $commentId;
			
			return $DB->Query( $query );
		}
		
		
		function sendMail( $url, $name, $rating, $text, $arSites )
		{
			if ( RVWcomments::needConvert() == 'Y' )
			{
				$name = iconv( 'UTF-8', 'Windows-1251', $name );
				$text = iconv( 'UTF-8', 'Windows-1251', $text );
			}
			
			
			$emailTo = COption::GetOptionString( 'reviews.comments', 'SEND_MAIL_ADDRESS' );
			
			$arFields = Array(
				'URL' => 'http://' . $_SERVER['HTTP_HOST'] . $url,
				'NAME' => $name,
				'RATING' => $rating,
				'TEXT' => $text,
				'EMAIL_TO' => $emailTo,
				'MODULE_PAGE' => 'http://' . $_SERVER['HTTP_HOST'] . '/bitrix/admin/settings.php?lang=ru&mid=reviews.comments'
			);
			CEvent::Send( 'REVIEWS_COMMENT', $arSites, $arFields );
		}
		
		
		function getModerationComments()
		{
			global $DB;
			
			return $DB->Query( 'SELECT ID,URL,DATE,NAME,COMMENT,RATING,IMAGE_ID FROM reviews_comments WHERE ACTIVE="N" ORDER BY DATE DESC' );
		}
		
		
		function getListComments()
		{
			global $DB;
			
			return $DB->Query( 'SELECT ID,URL,DATE,NAME,COMMENT,RATING,IMAGE_ID FROM reviews_comments WHERE ACTIVE="Y" ORDER BY DATE DESC' );
		}
		
		
		function activateComment( $commentId )
		{
			global $DB;
			
			$DB->Query( 'UPDATE reviews_comments SET ACTIVE="Y" WHERE ID=' . $commentId );
		}
		
		
		function deleteComment( $commentId )
		{
			global $DB;
			
			$DB->Query( 'DELETE FROM reviews_comments WHERE ID=' . $commentId );
		}
		
		
		function saveOptions( $showStats, $includeJquery, $sendMail, $sendMailAddress, $enableModeration, $enableImages, $onlyAuthorized )
		{
			if ( $showStats )
			{
				COption::SetOptionString( 'reviews.comments', 'SHOW_STATS', $showStats );
			}

			if ( $includeJquery )
			{
				COption::SetOptionString( 'reviews.comments', 'INCLUDE_JQUERY', $includeJquery );
			}

			if ( $sendMail )
			{
				COption::SetOptionString( 'reviews.comments', 'SEND_MAIL', $sendMail );
			}

			COption::SetOptionString( 'reviews.comments', 'SEND_MAIL_ADDRESS', $sendMailAddress );
			
			if ( $enableModeration )
			{
				COption::SetOptionString( 'reviews.comments', 'ENABLE_MODERATION', $enableModeration );
			}
			
			if ( $enableImages )
			{
				COption::SetOptionString( 'reviews.comments', 'ENABLE_IMAGES', $enableImages );
			}
			
			if ( $onlyAuthorized )
			{
				COption::SetOptionString( 'reviews.comments', 'ONLY_AUTHORIZED', $onlyAuthorized );
			}
		}
	}
}
?>