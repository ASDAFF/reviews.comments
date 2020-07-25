<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php' );


$enableAccess = false;

if ( $USER->IsAdmin() )
{
	$enableAccess = true;
}
else
{
	$arUserGroups = $USER->GetUserGroupArray();

	$moderationAccess = COption::GetOptionString( 'reviews.comments', 'MODERATION_ACCESS' );
	$arModerationGroups = explode( ',', $moderationAccess );


	$arIntersect = array_intersect( $arUserGroups, $arModerationGroups );
	if ( !empty( $arIntersect ) )
	{
		$enableAccess = true;
	}
}


if ( !$enableAccess )
{
	die();
}



CModule::IncludeModule( 'reviews.comments' );

IncludeTemplateLangFile( __FILE__ );
IncludeModuleLangFile( __FILE__ );



$APPLICATION->SetTitle( GetMessage( 'REVIEWS_COMMENTS_TITLE' ) );


if ( $_POST['ACTION'] == 'COMMENT_EDIT' && intval( $_POST['COMMENT_ID'] ) )
{
	if ( RVWcomments::editComment( intval( $_POST['COMMENT_ID'] ), $_POST['COMMENT_DATE'], $_POST['COMMENT_NAME'], intval( $_POST['COMMENT_RATING'] ), $_POST['COMMENT_TEXT'], $_POST['COMMENT_ANSWER'], $_POST['COMMENT_ADVANTAGE'], $_POST['COMMENT_DISADVANTAGE'] ) )
	{
		echo '<p class="reviews-comment-edit-success">' . GetMessage( 'COMMENT_EDIT_SUCCESS' ) . '</p>';
	}
}



CJSCore::Init( Array( 'jquery' ) );


$aTabs = Array(
	Array( 'DIV' => 'reviews-comments-options-moderation', 'TAB' => GetMessage( 'MODERATION_TITLE' ), 'TITLE' => GetMessage( 'MODERATION_TITLE' )),
	Array( 'DIV' => 'reviews-comments-options-list', 'TAB' => GetMessage( 'LIST_TITLE' ), 'TITLE' => GetMessage( 'LIST_TITLE' )),
);

if ( $USER->IsAdmin() )
{
	$aTabs[] = Array( 'DIV' => 'reviews-comments-options-settings', 'TAB' => GetMessage( 'SETTINGS_TITLE' ), 'TITLE' => GetMessage( 'SETTINGS_TITLE' ));
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);


$tabControl->Begin();
$tabControl->BeginNextTab();

	$arComments = Array();
	
	$dbComments = RVWcomments::getModerationComments();
	while ( $arComment = $dbComments->Fetch() )
	{
		$arImages = Array();
		if ( $arComment['IMAGES_IDS'] )
		{
			$arImagesIds = explode( ',', $arComment['IMAGES_IDS'] );
			
			if ( !empty( $arImagesIds ) )
			{
				foreach ( $arImagesIds as $imageId )
				{
					$image = CFile::GetPath( $imageId );
					$arImages[] = Array(
						'SRC' => $image,
						'ID' => $imageId
					);
				}
			}
		}
		
		$arComments[] = Array(
			'ID' => $arComment['ID'],
			'URL' => $arComment['URL'],
			'DATE' => ConvertTimeStamp( $arComment['DATE'], 'FULL' ),
			'NAME' => $arComment['NAME'],
			'RATING' => $arComment['RATING'],
			'COMMENT' => $arComment['COMMENT'],
			'IMAGES' => $arImages,
			'EMAIL' => $arComment['EMAIL'],
			'PHONE' => $arComment['PHONE'],
			'ADVANTAGE' => $arComment['ADVANTAGE'],
			'DISADVANTAGE' => $arComment['DISADVANTAGE'],
		);
	}
	
	
	if ( empty( $arComments ) )
	{
		echo GetMessage( 'MODERATION_IS_EMPTY' );
	}
	else
	{
		?>
			<div class="count"><?=GetMessage( 'COMMENTS_COUNT' )?>: <?=count( $arComments )?></div>
		
			<tr>
				<th>URL</th>
				<th class="w100"><?=GetMessage( 'DATE' )?></th>
				<th><?=GetMessage( 'PERSONAL_DATA' )?></th>
				<th class="w60"><?=GetMessage( 'RATING' )?></th>
				<th><?=GetMessage( 'COMMENT' )?></th>
				<th class="w100"><?=GetMessage( 'IMAGE' )?></th>
				<th class="w100"></th>
			</tr>
		
			<? foreach ( $arComments as $arComment ): ?>
			
				<tr>
					<td><a href="<?=$arComment['URL']?>" target="_blank" title="<?=GetMessage( 'OPEN_IN_NEW_WINDOW' )?>"><?=$arComment['URL']?></a></td>
					<td class="small center"><?=$arComment['DATE']?></td>
					<td>
						<?=GetMessage( 'NAME' )?>: <?=$arComment['NAME']?><br />
						
						<?
						if ( $arComment['EMAIL'] )
						{
							?>
								<?=GetMessage( 'ADDITIONAL_FIELDS_EMAIL' )?>: <?=$arComment['EMAIL']?><br />
							<?
						}
						
						if ( $arComment['PHONE'] )
						{
							?>
								<?=GetMessage( 'ADDITIONAL_FIELDS_PHONE' )?>: <?=$arComment['PHONE']?><br />
							<?
						}
						?>
					</td>
					<td class="center"><?=$arComment['RATING']?></td>
					<td>
						<?=$arComment['COMMENT']?><br /><br />
						
						<?
						if ( $arComment['ADVANTAGE'] )
						{
							?>
								<?=GetMessage( 'ADVANTAGE' )?>: <?=$arComment['ADVANTAGE']?><br />
							<?
						}
						
						if ( $arComment['DISADVANTAGE'] )
						{
							?>
								<?=GetMessage( 'DISADVANTAGE' )?>: <?=$arComment['DISADVANTAGE']?><br />
							<?
						}
						?>
						
					</td>
					<td class="image-container">
						<? if ( !empty( $arComment['IMAGES'] ) ): ?>
							<? foreach ( $arComment['IMAGES'] as $arImage ): ?>
								<img src="<?=$arImage['SRC']?>" data-id="<?=$arImage['ID']?>" />
							<? endforeach; ?>
						<? endif; ?>
					</td>
					<td class="center">
						<a href="javascript:;" class="activate" data-id="<?=$arComment['ID']?>"><?=GetMessage( 'ACTIVATE' )?></a>
						<a href="javascript:;" class="delete" data-id="<?=$arComment['ID']?>"><?=GetMessage( 'DELETE' )?></a>
					</td>
				</tr>
			
			<? endforeach; ?>

		<?
	}
	
$tabControl->BeginNextTab();

	$arComments = Array();
	
	$dbComments = RVWcomments::getListComments();
	while ( $arComment = $dbComments->Fetch() )
	{
		$arImages = Array();
		if ( $arComment['IMAGES_IDS'] )
		{
			$arImagesIds = explode( ',', $arComment['IMAGES_IDS'] );
			
			if ( !empty( $arImagesIds ) )
			{
				foreach ( $arImagesIds as $imageId )
				{
					$image = CFile::GetPath( $imageId );
					$arImages[] = Array(
						'SRC' => $image,
						'ID' => $imageId
					);
				}
			}
		}
		
		$arComments[] = Array(
			'ID' => $arComment['ID'],
			'URL' => $arComment['URL'],
			'DATE' => ConvertTimeStamp( $arComment['DATE'], 'FULL' ),
			'NAME' => $arComment['NAME'],
			'RATING' => $arComment['RATING'],
			'COMMENT' => $arComment['COMMENT'],
			'IMAGES' => $arImages,
			'EMAIL' => $arComment['EMAIL'],
			'PHONE' => $arComment['PHONE'],
			'ADVANTAGE' => $arComment['ADVANTAGE'],
			'DISADVANTAGE' => $arComment['DISADVANTAGE'],
		);
		
	}
	
	
	$arAnswers = RVWcomments::getAnswers();
	
	
	if ( empty( $arComments ) )
	{
		echo GetMessage( 'LIST_IS_EMPTY' );
	}
	else
	{
		?>
			<div class="filter">
				<?=GetMessage( 'FILTER' )?>: <input type="text" id="reviews-comments-options-list-filter"/>
			</div>
			
			<div class="count">
				<?=GetMessage( 'COMMENTS_COUNT' )?>: <span><?=count( $arComments )?></span>
			</div>

			<tr>
				<th>URL</th>
				<th class="w100"><?=GetMessage( 'DATE' )?></th>
				<th><?=GetMessage( 'PERSONAL_DATA' )?></th>
				<th class="w60"><?=GetMessage( 'RATING' )?></th>
				<th><?=GetMessage( 'COMMENT' )?></th>
				<th class="w100"><?=GetMessage( 'IMAGE' )?></th>
				<th class="w100"></th>
			</tr>
		
			<? foreach ( $arComments as $key => $arComment ): ?>
			
				<tr class="comment filtered" data-url="<?=$arComment['URL']?>">
					<td><a href="<?=$arComment['URL']?>" target="_blank" title="<?=GetMessage( 'OPEN_IN_NEW_WINDOW' )?>"><?=$arComment['URL']?></a></td>
					<td class="small center reviews-comment-date"><?=$arComment['DATE']?></td>
					<td class="reviews-comment-name">
						<?=GetMessage( 'NAME' )?>: <span data-field="NAME"><?=$arComment['NAME']?></span><br />
						
						<?
						if ( $arComment['EMAIL'] )
						{
							?>
								<?=GetMessage( 'ADDITIONAL_FIELDS_EMAIL' )?>: <?=$arComment['EMAIL']?><br />
							<?
						}
						
						if ( $arComment['PHONE'] )
						{
							?>
								<?=GetMessage( 'ADDITIONAL_FIELDS_PHONE' )?>: <?=$arComment['PHONE']?><br />
							<?
						}
						?>
					</td>
					<td class="center reviews-comment-rating"><?=$arComment['RATING']?></td>
					<td>
						<div class="reviews-comment-text"><?=$arComment['COMMENT']?></div>
						<br />
						
						<?
						if ( $arComment['ADVANTAGE'] )
						{
							?>
								<div>
									<?=GetMessage( 'ADVANTAGE' )?>: <span class="reviews-comment-advantage"><?=$arComment['ADVANTAGE']?></span>
								</div>
							<?
						}
						
						if ( $arComment['DISADVANTAGE'] )
						{
							?>
								<div>
									<?=GetMessage( 'DISADVANTAGE' )?>: <span class="reviews-comment-disadvantage"><?=$arComment['DISADVANTAGE']?></span>
								</div>
							<?
						}
						?>
						
						
						<? if ( $arAnswers[$arComment['ID']] ): ?>
							<div class="reviews-comment-answer">
								<b><?=GetMessage( 'YOUR_ANSWER' )?></b>: <p><?=$arAnswers[$arComment['ID']]?></p>
							</div>
						<? endif; ?>
					</td>
					
					<td class="image-container reviews-comment-picture">
						<? if ( !empty( $arComment['IMAGES'] ) ): ?>
							<? foreach ( $arComment['IMAGES'] as $arImage ): ?>
								<img src="<?=$arImage['SRC']?>" data-id="<?=$arImage['ID']?>" />
							<? endforeach; ?>
						<? endif; ?>
					</td>
					
					<td class="center">
						<a href="javascript:;" class="edit" data-id="<?=$arComment['ID']?>"><?=GetMessage( 'EDIT' )?></a>
						<a href="javascript:;" class="delete" data-id="<?=$arComment['ID']?>"><?=GetMessage( 'DELETE' )?></a>
					</td>
				</tr>

			
			<? endforeach; ?>
			
			<tr class="more">
				<td colspan="6">
					<a href="javascript:;" id="reviews-comments-options-list-more">
						<?=GetMessage( 'SHOW_MORE' )?>
					</a>
				</td>
			</tr>

		<?
	}
	
	

if ( $USER->IsAdmin() )
{	

$tabControl->BeginNextTab();


	global $DB;
	
	$arUsersGroups = Array();
	
	$query = 'SELECT ID,NAME FROM b_group WHERE ID NOT IN (1,2)';
	
	$dbUsersGroups = $DB->Query( $query );
	while ( $arUsersGroup = $dbUsersGroups->Fetch() )
	{
		$arUsersGroups[] = $arUsersGroup;
	}

	?>
	
	<tr>
		<td colspan="2" class="center"><b><?=GetMessage( 'SETTINGS_PUBLIC' )?></b></td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'SHOW_STATS' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-show-stats" <?=( COption::GetOptionString( 'reviews.comments', 'SHOW_STATS' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	<tr>
		<td class="right"><?=GetMessage( 'INCLUDE_JQUERY' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-include-jquery" <?=( COption::GetOptionString( 'reviews.comments', 'INCLUDE_JQUERY' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ENABLE_IMAGES' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-enable-images" <?=( COption::GetOptionString( 'reviews.comments', 'ENABLE_IMAGES' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ONLY_AUTHORIZED' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-only-authorized" <?=( COption::GetOptionString( 'reviews.comments', 'ONLY_AUTHORIZED' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'SHOW_COMMENTS_COUNT' )?>:</td>
		<td class="center">
			<input type="text" id="reviews-comments-option-show-comments-count" value="<?=COption::GetOptionString( 'reviews.comments', 'SHOW_COMMENTS_COUNT' )?>" />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ENABLE_VOTING' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-enable-voting" <?=( COption::GetOptionString( 'reviews.comments', 'ENABLE_VOTING' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ANSWER_TITLE' )?>:</td>
		<td class="center">
			<input type="text" id="reviews-comments-option-answer-title" value="<?=COption::GetOptionString( 'reviews.comments', 'ANSWER_TITLE' )?>" />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ENABLE_USERPIC' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-enable-userpic" <?=( COption::GetOptionString( 'reviews.comments', 'ENABLE_USERPIC' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	
	
	<?
	$additionalFieldsRequired = COption::GetOptionString( 'reviews.comments', 'ADDITIONAL_FIELDS_REQUIRED' );
	$arAdditionalFieldsRequired = explode( ',', $additionalFieldsRequired );
	
	$additionalFields = COption::GetOptionString( 'reviews.comments', 'ADDITIONAL_FIELDS' );
	$arAdditionalFields = explode( ',', $additionalFields );
	
	$arAdditionalFieldsValues = Array(
		'email',
		'phone',
		'advantage'
	);
	?>
	
	<tr>
		<td class="right"><?=GetMessage( 'ADDITIONAL_FIELDS_REQUIRED' )?>:</td>
		<td class="center">
			<select id="reviews-comments-option-additional-fields-required" multiple size="3">
			
				<?
				foreach ( $arAdditionalFieldsValues as $code )
				{
					$upperCode = strtoupper( $code );
					?>
						<option value="<?=$code?>" <?=( in_array( $code, $arAdditionalFieldsRequired ) ? 'selected' : '' )?>><?=GetMessage( 'ADDITIONAL_FIELDS_' . $upperCode )?></option>
					<?
				}
				?>

			</select>
			<p><?=GetMessage( 'MODERATION_ACCESS_HELP' )?></p>
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ADDITIONAL_FIELDS' )?>:</td>
		<td class="center">
			<select id="reviews-comments-option-additional-fields" multiple size="3">
				
				<?
				foreach ( $arAdditionalFieldsValues as $code )
				{
					$upperCode = strtoupper( $code );
					?>
						<option value="<?=$code?>" <?=( in_array( $code, $arAdditionalFields ) ? 'selected' : '' )?>><?=GetMessage( 'ADDITIONAL_FIELDS_' . $upperCode )?></option>
					<?
				}
				?>
				
			</select>
			<p><?=GetMessage( 'MODERATION_ACCESS_HELP' )?></p>
		</td>
	</tr>
	
	
	
	<tr>
		<td colspan="2" class="center"><b><?=GetMessage( 'SETTINGS_ADMIN' )?></b></td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'SEND_MAIL' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-send-mail" <?=( COption::GetOptionString( 'reviews.comments', 'SEND_MAIL' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	<tr>
		<td class="right"><?=GetMessage( 'SEND_MAIL_ADDRESS' )?>:</td>
		<td class="center">
			<input type="text" id="reviews-comments-option-send-mail-address" value="<?=COption::GetOptionString( 'reviews.comments', 'SEND_MAIL_ADDRESS' )?>" />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'ENABLE_MODERATION' )?>:</td>
		<td class="center">
			<input type="checkbox" id="reviews-comments-option-enable-moderation" <?=( COption::GetOptionString( 'reviews.comments', 'ENABLE_MODERATION' ) == 'Y' ? 'checked' : '' )?> />
		</td>
	</tr>
	
	<tr>
		<td class="right"><?=GetMessage( 'CLEAR_CACHE_COMPONENTS' )?>:</td>
		<td class="center">
			<input type="text" id="reviews-comments-option-clear-cache-components" value="<?=COption::GetOptionString( 'reviews.comments', 'CLEAR_CACHE_COMPONENTS' )?>" />
		</td>
	</tr>
	
	
	<? if ( !empty( $arUsersGroups ) ): ?>
	
		<?
		$moderationAccess = COption::GetOptionString( 'reviews.comments', 'MODERATION_ACCESS' );
		$arModerationGroups = explode( ',', $moderationAccess );
		?>
		
		<tr>
			<td class="right"><?=GetMessage( 'MODERATION_ACCESS' )?>:</td>
			<td class="center">
				<select id="reviews-comments-option-moderation-access" multiple>
					
					<? foreach ( $arUsersGroups as $arUsersGroup ): ?>
					
						<option value="<?=$arUsersGroup['ID']?>" <? if ( in_array( $arUsersGroup['ID'], $arModerationGroups ) ): ?>selected<? endif; ?>><?=$arUsersGroup['NAME']?></option>
					
					<? endforeach; ?>
					
				</select>
				<p><?=GetMessage( 'MODERATION_ACCESS_HELP' )?></p>
			</td>
		</tr>
		
	<? endif; ?>
	
	<tr>
		<td colspan="2" class="center">
			<a href="javascript:;" id="reviews-comments-options-save">
				<?=GetMessage( 'SAVE' )?>
			</a>
		</td>
	</tr
	
	<?

}
	
	
$tabControl->End();
?>


<style type="text/css">
#reviews-comments-options-list .filter
{
	margin-bottom: 50px;
}

#reviews-comments-options-moderation .count,
#reviews-comments-options-list .count
{
	margin-bottom: 20px;
}

#reviews-comments-options-moderation_edit_table,
#reviews-comments-options-list_edit_table,
#reviews-comments-options-settings_edit_table
{
	width: 100%;
	border-collapse: collapse;
}

#reviews-comments-options-list_edit_table tr.comment,
#reviews-comments-options-list_edit_table tr.more
{
	display: none;
}

#reviews-comments-options-list_edit_table tr.comment.filtered.page
{
	display: table-row;
}

#reviews-comments-options-moderation_edit_table th,
#reviews-comments-options-moderation_edit_table td,
#reviews-comments-options-list_edit_table th,
#reviews-comments-options-list_edit_table td,
#reviews-comments-options-settings_edit_table td
{
	text-align: left;
	font-family: Verdana;
	font-size: 12px;
}

#reviews-comments-options-settings_edit_table td
{
	width: 50%;
}

#reviews-comments-options-moderation_edit_table th,
#reviews-comments-options-moderation_edit_table td.center,
#reviews-comments-options-list_edit_table th,
#reviews-comments-options-list_edit_table td.center,
#reviews-comments-options-settings_edit_table td.center
{
	text-align: center;
}

#reviews-comments-options-moderation_edit_table th.w100,
#reviews-comments-options-list_edit_table th.w100
{
	width: 100px;
}

#reviews-comments-options-moderation_edit_table th.w60,
#reviews-comments-options-list_edit_table th.w60
{
	width: 60px;
}

#reviews-comments-options-moderation_edit_table td,
#reviews-comments-options-list_edit_table td,
#reviews-comments-options-settings_edit_table td
{
	border: 1px solid #ddd;
	padding: 4px;
	position: relative;
}

#reviews-comments-options-settings_edit_table td.right
{
	text-align: right;
}

#reviews-comments-options-moderation_edit_table td.small,
#reviews-comments-options-list_edit_table td.small
{
	font-size: 11px;
}

#reviews-comments-options-moderation_edit_table a,
#reviews-comments-options-list_edit_table a,
#reviews-comments-options-settings_edit_table a
{
	display: block;
	margin-top: 5px;
	margin-bottom: 5px;
}

#reviews-comments-options-moderation_edit_table a.activate,
#reviews-comments-options-moderation_edit_table a.delete,
#reviews-comments-options-list_edit_table tr.more a,
#reviews-comments-options-settings_edit_table a
{
	text-decoration: none;
	color: white;
	padding: 5px 15px;
}

#reviews-comments-options-moderation_edit_table a.activate,
#reviews-comments-options-settings_edit_table a
{
	background-color: #18c118;
}

#reviews-comments-options-moderation_edit_table a.delete
{
	background-color: #e83e3e;
}

#reviews-comments-options-list_edit_table a.edit
{
	background-color: #b8cb3e;
	text-decoration: none;
	color: white;
	padding: 5px 15px;
}

#reviews-comments-options-list_edit_table a.delete
{
	background-color: #e83e3e;
	text-decoration: none;
	color: white;
	padding: 5px 15px;
}

#reviews-comments-options-list_edit_table tr.more a
{
	background-color: #6c84dc;
	width: 100px;
	margin: auto;
	text-align: center;
}

#reviews-comments-options-settings_edit_table a
{
	width: 100px;
	margin: auto;
	text-align: center;
}

#reviews-comments-options-moderation_edit_table img,
#reviews-comments-options-list_edit_table img
{
	max-width: 100px;
	max-height: 100px;
	cursor: pointer;
}

#reviews-comments-options-moderation_edit_table img.active,
#reviews-comments-options-list_edit_table img.active
{
	position: absolute;
	top: 0;
	right: 0;
	max-width: 500px;
	max-height: 500px;
	z-index: 1;
}

.image-container
{
	position: relative;
}

#reviews-comment-edit-popup
{
	position: absolute;
    right: 0;
    top: -10px;
    background-color: #fff;
    color: #000;
    width: 500px;
    z-index: 1;
    border-radius: 8px;
    padding: 10px 20px;
	box-shadow: 0 0 20px #999;
	cursor: default;
}

#reviews-comment-edit-popup table
{
	width: 100%;
	border-collapse: collapse;
}

#reviews-comment-edit-popup i
{
	font-style: normal;
	color: #777;
}

#reviews-comment-edit-popup textarea
{
	resize: none;
	width: 300px;
	height: 80px;
}

#reviews-comment-edit-save,
#reviews-comment-edit-cancel
{
	display: inline-block !important;
	text-decoration: none;
	color: #fff;
	padding: 10px 20px;
	margin: 0 20px 0;
	width: 100px;
	box-sizing: border-box;
}

#reviews-comment-edit-save
{
	background-color: #b8cb3e;	
}

#reviews-comment-edit-cancel
{
	background-color: #e83e3e;
}

.reviews-comment-edit-success
{
	color: #4eb324;
    font-weight: bold;
}

.reviews-comment-edit-picture
{
	display: inline-block;
	margin: 2px;
}

#reviews-comments-option-show-comments-count
{
	width: 40px;
	text-align: right;
}

.reviews-comment-answer
{
	margin-top: 20px;
	border-top: 1px solid #ccc;
	padding-top: 10px;
}

.reviews-comment-answer p
{
	margin: 2px 0;
}
</style>


<script type="text/javascript">
$( document ).ready(
	function ()
	{
		$( '#reviews-comments-options-moderation_edit_table a.activate' ).click(
			function ()
			{
				var commentId = $( this ).data( 'id' );
				var parentBlock = $( this ).parent();
				
				$.post(
					'/bitrix/admin/reviews_comments_options_ajax.php',
					{
						ACTION: 'ACTIVATE',
						ID: commentId
					},
					function ( data )
					{
						parentBlock.html( '<p style="color: green"><?=GetMessage( 'PUBLISHED' )?></p>' );
					}
				);
			}
		);
		
		
		$( '#reviews-comments-options-moderation_edit_table a.delete, #reviews-comments-options-list_edit_table a.delete' ).click(
			function ()
			{
				var commentId = $( this ).data( 'id' );
				var parentBlock = $( this ).parent();
				
				$.post(
					'/bitrix/admin/reviews_comments_options_ajax.php',
					{
						ACTION: 'DELETE',
						ID: commentId
					},
					function ( data )
					{
						parentBlock.html( '<p style="color: red"><?=GetMessage( 'DELETED' )?></p>' );
					}
				);
			}
		);
		
		
		$( '#reviews-comments-options-list_edit_table a.edit' ).click(
			function ()
			{
				$( '#reviews-comment-edit-popup' ).remove();
				
				var commentId = $( this ).data( 'id' );
				
				var commentTr = $( this ).parent().parent();
				
				var commentDate = commentTr.find( 'td.reviews-comment-date' ).text();
				var commentName = commentTr.find( 'td.reviews-comment-name span[data-field="NAME"]' ).text();
				var commentRating = commentTr.find( 'td.reviews-comment-rating' ).text();
				var commentText = commentTr.find( 'td div.reviews-comment-text' ).text();
				var commentAdvantage = commentTr.find( 'td span.reviews-comment-advantage' ).text();
				var commentDisadvantage = commentTr.find( 'td span.reviews-comment-disadvantage' ).text();
				var commentAnswer = commentTr.find( 'td div.reviews-comment-answer p' ).text();
				
				var commentPictures = '';
				
				if ( commentTr.find( 'td.reviews-comment-picture img' ).length > 0 )
				{
					commentTr.find( 'td.reviews-comment-picture img' ).each(
						function ( index, element )
						{
							var src = $( element ).attr( 'src' );
							var id = $( element ).data( 'id' );
							
							commentPictures += '<div class="reviews-comment-edit-picture"><img src="' + src + '" data-id="' + id + '" /><br /><input type="checkbox" name="COMMENT_PICTURE_DELETE_' + id + '" value="Y" /><?=GetMessage( 'DELETE' )?></div>';
						}
					);
				}
				
				
				var popup = '';
				
				popup += '<div id="reviews-comment-edit-popup">';
				
				popup += '<form action="" method="post" enctype="multipart/form-data"><table>';
				
				popup += '<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />';
				popup += '<input type="hidden" name="ACTION" value="COMMENT_EDIT" />';
				popup += '<input type="hidden" name="COMMENT_ID" value="' + commentId + '" />';
				
				popup += '<tr><td><?=GetMessage( 'DATE' )?></td><td><input name="COMMENT_DATE" type="text" value="' + commentDate + '" /><br /><i><?=GetMessage( 'DATE_FORMAT' )?></i></td></tr>';
				popup += '<tr><td><?=GetMessage( 'NAME' )?></td><td><input name="COMMENT_NAME" type="text" value="' + commentName + '" /></td></tr>';
				
				
				var ratingSelect = '<select name="COMMENT_RATING">';
				
				for ( var i = 1; i <= 5; i++ )
				{
					var selected = '';
					
					if ( i == commentRating )
					{
						selected = 'selected';
					}
					
					ratingSelect += '<option value="' + i + '" ' + selected + '>' + i + '</option>';
				}
				
				ratingSelect += '</select>';
				
				
				popup += '<tr><td><?=GetMessage( 'RATING' )?></td><td>' + ratingSelect + '</td></tr>';
				popup += '<tr><td><?=GetMessage( 'COMMENT' )?></td><td><textarea name="COMMENT_TEXT">' + commentText + '</textarea></td></tr>';
				
				popup += '<tr><td><?=GetMessage( 'ADVANTAGE' )?></td><td><textarea name="COMMENT_ADVANTAGE">' + commentAdvantage + '</textarea></td></tr>';
				popup += '<tr><td><?=GetMessage( 'DISADVANTAGE' )?></td><td><textarea name="COMMENT_DISADVANTAGE">' + commentDisadvantage + '</textarea></td></tr>';
				
				popup += '<tr><td><?=GetMessage( 'IMAGE' )?></td><td>' + commentPictures + '<br /><input type="file" name="COMMENT_PICTURE_1" class="reviews-comment-edit-image-add" data-counter="1" /></td></tr>';
				
				popup += '<tr><td><?=GetMessage( 'YOUR_ANSWER' )?></td><td><textarea name="COMMENT_ANSWER">' + commentAnswer + '</textarea></td></tr>';
				
				popup += '</table></form>';
				
				popup += '<div class="reviews-comment-edit-buttons">';
				
				popup += '<a href="javascript:;" id="reviews-comment-edit-save"><?=GetMessage( 'SAVE' )?></a>';
				popup += '<a href="javascript:;" id="reviews-comment-edit-cancel"><?=GetMessage( 'CANCEL' )?></a>';
				
				popup += '</div>';
				
				popup += '</div>';
				
				$( this ).parent().append( popup );
			}
		);
		
		
		$( 'body' ).on(
			'click',
			'#reviews-comment-edit-cancel',
			function ()
			{
				$( '#reviews-comment-edit-popup' ).remove();
			}
		);
		
		$( 'body' ).on(
			'click',
			'#reviews-comment-edit-save',
			function ()
			{
				$( '#reviews-comment-edit-popup form' ).submit();
			}
		);
		
		
		$( '#reviews-comments-options-list-filter' ).keyup(
			function ()
			{
				var filter = $( this ).val();
				
				if ( !filter )
				{
					$( '#reviews-comments-options-list_edit_table tr.comment' ).addClass( 'filtered' );
					
					setCommentsCount();
					setPages();
				}
				else
				{
					$( '#reviews-comments-options-list_edit_table tr.comment' ).removeClass( 'filtered' );
					
					$( '#reviews-comments-options-list_edit_table tr.comment' ).each(
						function ( index, element )
						{
							var url = $( element ).data( 'url' );
							
							if ( strstr( url, filter ) )
							{
								$( element ).addClass( 'filtered' );
							}
						}
					);
					
					setCommentsCount();
					setPages();
				}
			}
		);
		
		
		
		$( '#reviews-comments-options-list-more' ).click(
			function ()
			{
				var countOnPage = 50;
				var currentCountOnPage = $( '#reviews-comments-options-list_edit_table tr.comment.filtered.page' ).length;
				var newCountOnPage = countOnPage + currentCountOnPage;
				
				$( '#reviews-comments-options-list_edit_table tr.comment.filtered' ).addClass( 'page' );
	
				$( '#reviews-comments-options-list_edit_table tr.comment.filtered' ).each(
					function ( index, element )
					{
						var key = index + 1;
						
						if ( key > newCountOnPage )
						{
							$( element ).removeClass( 'page' );
						}
					}
				);
				
				
				$( '#reviews-comments-options-list_edit_table tr.more' ).hide();
				if ( $( '#reviews-comments-options-list_edit_table tr.comment.filtered:hidden' ).length > 0 )
				{
					$( '#reviews-comments-options-list_edit_table tr.more' ).show();
				}
			}
		);
		
		
		setPages();
		
		
		$( '#reviews-comments-options-save' ).click(
			function ()
			{
				var parentBlock = $( this ).parent();
				
				var showStats = $( '#reviews-comments-option-show-stats' ).prop( 'checked' );
				if ( showStats == 'true' || showStats == true )
				{
					showStats = 'Y';
				}
				else
				{
					showStats = 'N';
				}
				
				var includeJquery = $( '#reviews-comments-option-include-jquery' ).prop( 'checked' );
				if ( includeJquery == 'true' || includeJquery == true )
				{
					includeJquery = 'Y';
				}
				else
				{
					includeJquery = 'N';
				}
				
				var sendMail = $( '#reviews-comments-option-send-mail' ).prop( 'checked' );
				if ( sendMail == 'true' || sendMail == true )
				{
					sendMail = 'Y';
				}
				else
				{
					sendMail = 'N';
				}
				
				var sendMailAddress = $( '#reviews-comments-option-send-mail-address' ).val();
				
				var enableModeration = $( '#reviews-comments-option-enable-moderation' ).prop( 'checked' );
				if ( enableModeration == 'true' || enableModeration == true )
				{
					enableModeration = 'Y';
				}
				else
				{
					enableModeration = 'N';
				}
				
				var enableImages = $( '#reviews-comments-option-enable-images' ).prop( 'checked' );
				if ( enableImages == 'true' || enableImages == true )
				{
					enableImages = 'Y';
				}
				else
				{
					enableImages = 'N';
				}
				
				var onlyAuthorized = $( '#reviews-comments-option-only-authorized' ).prop( 'checked' );
				if ( onlyAuthorized == 'true' || onlyAuthorized == true )
				{
					onlyAuthorized = 'Y';
				}
				else
				{
					onlyAuthorized = 'N';
				}
				
				var showCommentsCount = $( '#reviews-comments-option-show-comments-count' ).val();
				
				
				var enableVoting = $( '#reviews-comments-option-enable-voting' ).prop( 'checked' );
				if ( enableVoting == 'true' || enableVoting == true )
				{
					enableVoting = 'Y';
				}
				else
				{
					enableVoting = 'N';
				}
				
				var enableUserpic = $( '#reviews-comments-option-enable-userpic' ).prop( 'checked' );
				if ( enableUserpic == 'true' || enableUserpic == true )
				{
					enableUserpic = 'Y';
				}
				else
				{
					enableUserpic = 'N';
				}
				
				
				var moderationAccess = '';
				if ( $( '#reviews-comments-option-moderation-access' ).length > 0 )
				{
					if ( $( '#reviews-comments-option-moderation-access option:selected' ).length > 0 )
					{
						$( '#reviews-comments-option-moderation-access option:selected' ).each(
							function ( index, element )
							{
								if ( moderationAccess )
								{
									moderationAccess += ',';
								}
								
								moderationAccess += $( element ).val();
							}
						);
					}
				}
				
				
				var answerTitle = $( '#reviews-comments-option-answer-title' ).val();
				
				var clearCacheComponents = $( '#reviews-comments-option-clear-cache-components' ).val();
				
				
				var additionalFieldsRequired = '';
				if ( $( '#reviews-comments-option-additional-fields-required option:selected' ).length > 0 )
				{
					$( '#reviews-comments-option-additional-fields-required option:selected' ).each(
						function ( index, element )
						{
							if ( additionalFieldsRequired )
							{
								additionalFieldsRequired += ',';
							}
							
							additionalFieldsRequired += $( element ).val();
						}
					);
				}
				
				var additionalFields = '';
				if ( $( '#reviews-comments-option-additional-fields option:selected' ).length > 0 )
				{
					$( '#reviews-comments-option-additional-fields option:selected' ).each(
						function ( index, element )
						{
							if ( additionalFields )
							{
								additionalFields += ',';
							}
							
							additionalFields += $( element ).val();
						}
					);
				}
				

				
				$.post(
					'/bitrix/admin/reviews_comments_options_save.php',
					{
						SHOW_STATS: showStats,
						INCLUDE_JQUERY: includeJquery,
						SEND_MAIL: sendMail,
						SEND_MAIL_ADDRESS: sendMailAddress,
						ENABLE_MODERATION: enableModeration,
						ENABLE_IMAGES: enableImages,
						ONLY_AUTHORIZED: onlyAuthorized,
						SHOW_COMMENTS_COUNT: showCommentsCount,
						ENABLE_VOTING: enableVoting,
						MODERATION_ACCESS: moderationAccess,
						ANSWER_TITLE: answerTitle,
						ADDITIONAL_FIELDS_REQUIRED: additionalFieldsRequired,
						ADDITIONAL_FIELDS: additionalFields,
						ENABLE_USERPIC: enableUserpic,
						CLEAR_CACHE_COMPONENTS: clearCacheComponents,
					},
					function ( data )
					{
						parentBlock.append( '<div style="color: green;" id="reviews-comments-tmp-notice"><?=GetMessage( 'CHANGES_SAVED' )?></div>' );
						setTimeout(
							function ()
							{
								$( '#reviews-comments-tmp-notice' ).remove();
							},
							3000
						);
					}
				);
			}
		);
		
		
		$( '#reviews-comments-options-moderation_edit_table img, #reviews-comments-options-list_edit_table td.reviews-comment-picture img' ).click(
			function ()
			{
				$( this ).toggleClass( 'active' );
			}
		);
		
		
		$( 'body' ).on(
			'change',
			'.reviews-comment-edit-image-add',
			function ()
			{
				var counter = $( this ).data( 'counter' );
				var nextCounter = counter + 1;
				
				if ( $( '.reviews-comment-edit-image-add[data-counter="' + nextCounter + '"]' ).length == 0 )
				{
					$( this ).parent().append( '<br /><input type="file" name="COMMENT_PICTURE_' + nextCounter + '" class="reviews-comment-edit-image-add" data-counter="' + nextCounter + '" />' );
				}
			}
		);
		
		
		$( '#reviews-comments-option-show-comments-count' ).change(
			function ()
			{
				var val = $( this ).val();
				
				if ( val <= 1 )
				{
					val = 1;
					$( this ).val( val );
				}
			}
		);
	}
);



function strstr( haystack, needle, bool ) 
{
	var pos = 0;
	pos = haystack.indexOf( needle );
	if ( pos == -1 )
	{
		return false;
	} 
	else
	{
		return haystack.slice( pos );
	}
}



function setCommentsCount()
{
	var count = $( '#reviews-comments-options-list_edit_table tr.comment.filtered' ).length;
	$( '#reviews-comments-options-list .count span' ).text( count );
}


function setPages()
{
	var countOnPage = 50;
	
	$( '#reviews-comments-options-list_edit_table tr.comment' ).addClass( 'page' );
	$( '#reviews-comments-options-list_edit_table tr.more' ).hide();
	
	$( '#reviews-comments-options-list_edit_table tr.comment.filtered' ).each(
		function ( index, element )
		{
			var key = index + 1;
			
			if ( key > countOnPage )
			{
				$( element ).removeClass( 'page' );
				$( '#reviews-comments-options-list_edit_table tr.more' ).show();
			}
		}
	);
}
</script>


<?
require_once( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php' );
?>