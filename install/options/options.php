<?
/**
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require_once( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php' );

CModule::IncludeModule( 'reviews.comments' );

IncludeTemplateLangFile( __FILE__ );
IncludeModuleLangFile( __FILE__ );


CJSCore::Init( Array( 'jquery' ) );


$aTabs = Array(
	Array( 'DIV' => 'reviews-comments-options-moderation', 'TAB' => GetMessage( 'MODERATION_TITLE' ), 'TITLE' => GetMessage( 'MODERATION_TITLE' )),
	Array( 'DIV' => 'reviews-comments-options-list', 'TAB' => GetMessage( 'LIST_TITLE' ), 'TITLE' => GetMessage( 'LIST_TITLE' )),
	Array( 'DIV' => 'reviews-comments-options-settings', 'TAB' => GetMessage( 'SETTINGS_TITLE' ), 'TITLE' => GetMessage( 'SETTINGS_TITLE' )),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);


$tabControl->Begin();
$tabControl->BeginNextTab();

	$arComments = Array();
	
	$dbComments = RVWcomments::getModerationComments();
	while ( $arComment = $dbComments->Fetch() )
	{
		$arComments[] = Array(
			'ID' => $arComment['ID'],
			'URL' => $arComment['URL'],
			'DATE' => ConvertTimeStamp( $arComment['DATE'], 'FULL' ),
			'NAME' => $arComment['NAME'],
			'RATING' => $arComment['RATING'],
			'COMMENT' => $arComment['COMMENT']
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
				<th><?=GetMessage( 'NAME' )?></th>
				<th class="w60"><?=GetMessage( 'RATING' )?></th>
				<th><?=GetMessage( 'COMMENT' )?></th>
				<th class="w100"></th>
			</tr>
		
			<? foreach ( $arComments as $arComment ): ?>
			
				<tr>
					<td><a href="<?=$arComment['URL']?>" target="_blank" title="<?=GetMessage( 'OPEN_IN_NEW_WINDOW' )?>"><?=$arComment['URL']?></a></td>
					<td class="small center"><?=$arComment['DATE']?></td>
					<td><?=$arComment['NAME']?></td>
					<td class="center"><?=$arComment['RATING']?></td>
					<td><?=$arComment['COMMENT']?></td>
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
		$arComments[] = Array(
			'ID' => $arComment['ID'],
			'URL' => $arComment['URL'],
			'DATE' => ConvertTimeStamp( $arComment['DATE'], 'FULL' ),
			'NAME' => $arComment['NAME'],
			'RATING' => $arComment['RATING'],
			'COMMENT' => $arComment['COMMENT']
		);
	}
	
	
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
				<th><?=GetMessage( 'NAME' )?></th>
				<th class="w60"><?=GetMessage( 'RATING' )?></th>
				<th><?=GetMessage( 'COMMENT' )?></th>
				<th class="w100"></th>
			</tr>
		
			<? foreach ( $arComments as $key => $arComment ): ?>
			
				<tr class="comment filtered" data-url="<?=$arComment['URL']?>">
					<td><a href="<?=$arComment['URL']?>" target="_blank" title="<?=GetMessage( 'OPEN_IN_NEW_WINDOW' )?>"><?=$arComment['URL']?></a></td>
					<td class="small center"><?=$arComment['DATE']?></td>
					<td><?=$arComment['NAME']?></td>
					<td class="center"><?=$arComment['RATING']?></td>
					<td><?=$arComment['COMMENT']?></td>
					<td class="center">
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
	
$tabControl->BeginNextTab();

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
		<td colspan="2" class="center">
			<a href="javascript:;" id="reviews-comments-options-save">
				<?=GetMessage( 'SAVE' )?>
			</a>
		</td>
	</tr
	
	<?
	
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
				
				
				$.post(
					'/bitrix/admin/reviews_comments_options_save.php',
					{
						SHOW_STATS: showStats,
						INCLUDE_JQUERY: includeJquery,
						SEND_MAIL: sendMail,
						SEND_MAIL_ADDRESS: sendMailAddress,
						ENABLE_MODERATION: enableModeration
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