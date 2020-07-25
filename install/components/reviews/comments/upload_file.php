<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );


if ( !empty( $_FILES['image'] ) )
{
	$arFile = array_merge( $_FILES['image'] );
	$arFile['del'] = ${'image_del'};
    $arFile['MODULE_ID'] = 'comments';
	
	$fileId = CFile::SaveFile( $arFile, 'comments_images' );
	
	if ( $fileId )
	{
		$filePath = $_SERVER['DOCUMENT_ROOT'] . CFile::GetPath( $fileId );
		$tempPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $arFile['name'];
		
		$r = CFile::ResizeImageFile( $filePath, $tempPath, Array( 'width' => 1000, 'height' => 1000 ) );
		if ( $r )
		{
			unlink( $filePath );
            rename( $tempPath, $filePath );
		}
		
		$pic = CFile::ResizeImageGet( $fileId, Array( 'width' => 120, 'height' => 120 ) );
		?>
		<img id="reviews-comment-image" src="<?=$pic['src']?>" data-id="<?=$fileId?>" />
		<?
	}
}
?>