<div id="reviews-comments">
	<div class="reviews-comments-title">
		<?=GetMessage( 'TITLE' )?>
	</div>
	<div class="reviews-comments-rating">
		<div class="reviews-comments-rating-background">
			<div class="reviews-comments-rating-foreground" style="width: <?=intval( $arResult['STARS_WIDTH'] )?>%"></div>
		</div>
	</div>
	<div class="reviews-comments-count">
		(<?=$arResult['COMMENTS_COUNT']?>)
	</div>
	
	
	<? if ( $arParams['OPTIONS']['SHOW_STATS'] == 'Y' ): ?>
	<div>
		<div class="reviews-comments-stats">
		
			<? for ( $i = 5; $i >= 1; $i-- ): ?>
			
				<div class="reviews-comments-stat" data-rating="<?=$i?>">
					<div class="reviews-comments-stat-title">
						<?=$arResult['STATS'][$i]['TITLE']?>
					</div>
					<div class="reviews-comments-stat-line">
						<div class="reviews-comments-stat-line-fill" style="width: <?=intval( $arResult['STATS'][$i]['WIDTH'] )?>%"></div>
					</div>
					<div class="reviews-comments-stat-count">
						(<?=intval( $arResult['STATS'][$i]['COUNT'] )?>)
					</div>
					<span class="reviews-comments-stat-only-rating"><?=GetMessage( 'SHOW_ONLY_WITH_RATING' )?> <?=$i?></span>
				</div>
			
			<? endfor; ?>
			
		</div>
		<div class="reviews-comments-button">
			<a href="javascript:;" id="reviews-scroll-to-add-button"><?=GetMessage( 'WRITE_THE_REVIEW' )?></a>
		</div>
	</div>
	<? endif; ?>
	
	
	<div class="reviews-comments-block">
		<? if ( empty( $arResult['COMMENTS'] ) ): ?>
			<?=GetMessage( 'NO_COMMENTS' )?>
		<? else: ?>
		
			<? foreach ( $arResult['COMMENTS'] as $k => $arComment ): ?>
			
				<?
				$page = 1;
				if ( $arParams['OPTIONS']['SHOW_COMMENTS_COUNT'] )
				{
					$page = ceil( ( $k + 1 ) / $arParams['OPTIONS']['SHOW_COMMENTS_COUNT'] );
				}
				?>
			
				<div class="reviews-comment <? if ( $page > 1 ): ?>hidden<? endif; ?> <?=$unactiveClass?>" data-rating="<?=$arComment['RATING']?>" data-page="<?=$page?>">
					
					<?
					if ( $arParams['OPTIONS']['ENABLE_USERPIC'] == 'Y' )
					{
					?>
						<div class="reviews-comment-left">
						
							<? if ( $arComment['USERPIC'] ): ?>
								
								<div class="reviews-comment-userpic">
									<img src="<?=$arComment['USERPIC']?>" />
								</div>
								
							<? else: ?>
							
								<div class="reviews-comment-userletter">
									<span><?=substr( $arComment['NAME'], 0, 1 )?></span>
								</div>
							
							<? endif; ?>
							
							
						</div>
					<?
					}
					?>
					
					<div class="reviews-comment-right">
						<div class="reviews-comment-header">
							<div class="reviews-comment-name">
								<?=$arComment['NAME']?>
							</div>
							<div class="reviews-comment-date">
								<?=$arComment['DATE']?>
							</div>
						</div>
						<div class="reviews-comment-rating">
							<div class="reviews-comment-rating-background">
								<div class="reviews-comment-rating-foreground" style="width: <?=$arComment['RATING_WIDTH']?>%"></div>
							</div>
						</div>
						<div class="reviews-comment-text">
							<?=$arComment['COMMENT']?>
							
							
							<?
							if ( $arComment['ADVANTAGE'] )
							{
								?>
									<p><b><?=GetMessage( 'ADDITIONAL_FIELD_ADVANTAGE' )?>:</b> <?=$arComment['ADVANTAGE']?></p>
								<?
							}
							
							if ( $arComment['DISADVANTAGE'] )
							{
								?>
									<p><b><?=GetMessage( 'ADDITIONAL_FIELD_DISADVANTAGE' )?>:</b> <?=$arComment['DISADVANTAGE']?></p>
								<?
							}
							?>
							
						</div>
						
						<? if ( !empty( $arComment['IMAGES'] ) ): ?>
						
							<div class="reviews-comment-image">
							
								<? foreach ( $arComment['IMAGES'] as $image ): ?>
							
									<img src="<?=$image?>" />
								
								<? endforeach; ?>
								
							</div>
							
						<? endif; ?>
						
						
						
						<?
						if ( $arComment['ACTIVE'] == 'Y' )
						{
							?>
								<? if ( $arParams['OPTIONS']['ENABLE_VOTING'] == 'Y' ): ?>
									<div class="reviews-comment-voting">
										<a href="javascript:;" class="reviews-comment-vote-up <? if ( $arComment['VOTED'] ): ?>disabled<? endif; ?>" data-id="<?=$arComment['ID']?>" data-vote="UP">
											<span><?=intval( $arComment['VOTES']['UP'] )?></span><img src="/bitrix/components/reviews/comments/templates/.default/img/vote_up.svg" />
										</a>
										<a href="javascript:;" class="reviews-comment-vote-down <? if ( $arComment['VOTED'] ): ?>disabled<? endif; ?>" data-id="<?=$arComment['ID']?>" data-vote="DOWN">
											<span><?=intval( $arComment['VOTES']['DOWN'] )?></span><img src="/bitrix/components/reviews/comments/templates/.default/img/vote_down.svg" />
										</a>
									</div>
								<? endif; ?>
								
								
								<? 
								if ( $arResult['ANSWERS'][$arComment['ID']] )
								{
									?>
										<div class="reviews-comment-answer">
											<b><?=$arParams['OPTIONS']['ANSWER_TITLE']?>:</b>
											<p><?=$arResult['ANSWERS'][$arComment['ID']]?></p>
										</div>
									<? 
								}
								elseif ( $arParams['CAN_MODERATE'] )
								{
									?>
										<div class="reviews-comment-answer-add">
											<textarea></textarea>
											<a href="javascript:;" data-id="<?=$arComment['ID']?>"><?=GetMessage( 'ANSWER_ADD' )?></a>
										</div>
									<?
								}
								?>
							<?
						}
						else
						{
							?>
								<div class="reviews-comment-moderation">
									<?=GetMessage( 'MODERATION_INFO' )?>
									<a href="javascript:;" data-id="<?=$arComment['ID']?>" data-action="ACTIVATE"><?=GetMessage( 'MODERATION_BUTTON' )?></a>
									<a href="javascript:;" data-id="<?=$arComment['ID']?>" data-action="DELETE" data-confirm-message="<?=GetMessage( 'MODERATION_CONFIRM_MESSAGE' )?>"><?=GetMessage( 'MODERATION_BUTTON_DELETE' )?></a>
								</div>
							<?
						}
						?>
						
						
					</div>
				</div>
			
			<? endforeach; ?>
		
		<? endif; ?>
		
		<a href="javascript:;" id="reviews-comments-show-more-comments" <? if ( $arParams['OPTIONS']['SHOW_COMMENTS_COUNT'] >= count( $arResult['COMMENTS'] ) ): ?>style="display: none"<? endif; ?>><?=GetMessage( 'SHOW_MORE_COMMENTS' )?></a>
		
	</div>
	<div class="reviews-comments-form">
	

		<? if ( $arResult['SHOW_FORM'] == 'Y' ): ?>
		
			<div class="reviews-comments-form-title">
				<?=GetMessage( 'WRITE_THE_REVIEW' )?>
			</div>
			<div class="reviews-comments-form-item">
				<label><?=GetMessage( 'NAME' )?></label>
				<input type="text" id="reviews-comment-name" value="<?=$USER->GetFirstName()?>" />
			</div>
			<div class="reviews-comments-form-item">
				<label><?=GetMessage( 'RATING' )?></label>
				<div class="reviews-comments-form-rating">
					
					<? for ( $i = 1; $i <= 5; $i++ ): ?>
						<div class="reviews-comments-form-rating-star" data-rating="<?=$i?>"></div>
					<? endfor; ?>
					
				</div>
			</div>
			
			
			<?
			$arAdditionalFieldsValues = Array(
				'email',
				'phone',
				'advantage'
			);
			
			foreach ( $arAdditionalFieldsValues as $code )
			{
				if ( !in_array( $code, $arParams['OPTIONS']['ADDITIONAL_FIELDS_REQUIRED'] ) && !in_array( $code, $arParams['OPTIONS']['ADDITIONAL_FIELDS'] ) )
				{
					continue;
				}
				
				$upperCode = strtoupper( $code );
				
				$required = false;
				if ( in_array( $code, $arParams['OPTIONS']['ADDITIONAL_FIELDS_REQUIRED'] ) )
				{
					$required = true;
				}
				
				if ( $code == 'advantage' )
				{
					?>
						<div class="reviews-comments-form-item">
							<label><?=GetMessage( 'ADDITIONAL_FIELD_ADVANTAGE' )?><?=( $required ? '*' : '' )?></label>
							<textarea id="reviews-comment-advantage" <?=( $required ? 'class="reviews-required"' : '' )?>></textarea>
						</div>
						<div class="reviews-comments-form-item">
							<label><?=GetMessage( 'ADDITIONAL_FIELD_DISADVANTAGE' )?><?=( $required ? '*' : '' )?></label>
							<textarea id="reviews-comment-disadvantage" <?=( $required ? 'class="reviews-required"' : '' )?>></textarea>
						</div>
					<?
				}
				else
				{
					?>
						<div class="reviews-comments-form-item">
							<label><?=GetMessage( 'ADDITIONAL_FIELD_' . $upperCode )?><?=( $required ? '*' : '' )?></label>
							<input type="text" id="reviews-comment-<?=$code?>" <?=( $required ? 'class="reviews-required"' : '' )?> />
						</div>
					<?
				}
			}
			?>
			
			
			<div class="reviews-comments-form-item">
				<label><?=GetMessage( 'COMMENT' )?></label>
				<textarea id="reviews-comment-text"></textarea>
			</div>
			

			<? if ( COption::GetOptionString( 'reviews.comments', 'ENABLE_IMAGES' ) == 'Y' ): ?>
			
				<div id="reviews-comment-images">
				
					<form id="reviews-comment-image-form-1" method="post" enctype="multipart/form-data" action="/bitrix/components/reviews/comments/upload_file.php" target="hidden-frame" data-loading-text="<?=GetMessage( 'IMAGE_LOADING_TEXT' )?>">
						<div class="reviews-comments-form-item">
							<label><?=GetMessage( 'IMAGE' )?></label>
							<input type="file" id="reviews-comment-image-1" class="reviews-comment-image" name="image" />
						</div>
					</form>
				
				</div>
				
				<iframe id="hidden-frame" name="hidden-frame" src="/bitrix/components/reviews/comments/upload_file.php"></iframe>
			
			<? endif; ?>

			
			<div class="reviews-comments-form-item">
				<div><?=GetMessage( 'REQUIRE_STAR' )?></div>
				<div class="reviews-comments-button">
				
					<?
					$successText = GetMessage( 'SUCCESS_TEXT' );
					$class = '';
					if ( COption::GetOptionString( 'reviews.comments', 'ENABLE_MODERATION' ) == 'Y' )
					{
						$successText = GetMessage( 'SUCCESS_MODERATION_TEXT' );
						$class = 'with-moderation';
					}
					?>
				
					<a href="javascript:;" id="reviews-add-button" data-loading-text="<?=GetMessage( 'LOADING_TEXT' )?>" data-success-text="<?=$successText?>" class="<?=$class?>"><?=GetMessage( 'SEND_THE_REVIEW' )?></a>
				</div>
			</div>
			<input type="hidden" id="reviews-comment-url" value="<?=$arResult['URL']?>" />
		
		<? else: ?>
		
			<?=GetMessage( 'ONLY_AUTHORIZED' )?>
		
		<? endif; ?>

		
		
	</div>
</div>