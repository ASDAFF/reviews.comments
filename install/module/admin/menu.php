<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$enableAccess = false;


$arUserGroups = $USER->GetUserGroupArray();

$moderationAccess = COption::GetOptionString( 'reviews.comments', 'MODERATION_ACCESS' );
$arModerationGroups = explode( ',', $moderationAccess );


$arIntersect = array_intersect( $arUserGroups, $arModerationGroups );
if ( !empty( $arIntersect ) )
{
	$enableAccess = true;
}


if ( $enableAccess )
{
	$aMenu = Array(
		'parent_menu' => 'global_menu_content',
		'sort' => 1,
		'url' => '/bitrix/admin/reviews_moderation.php',
		'text' => GetMessage( 'MENU_NAME' ),
		'icon' => 'forum_menu_icon'
	);

	return $aMenu;
}
?>