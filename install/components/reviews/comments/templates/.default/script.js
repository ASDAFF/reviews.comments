/*
 * Copyright (c) 28/6/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$( document ).ready(
	function ()
	{
		$( '#reviews-scroll-to-add-button' ).click(
			function ()
			{
				var formOffsetTop = $( '#reviews-comments .reviews-comments-form' ).offset().top - 110; 
				
				$( 'html, body' ).animate(
					{
						scrollTop: formOffsetTop
					},
					500
				);
			}
		);
		
		
		
		$( '#reviews-comments .reviews-comments-form-rating-star' ).mouseover(
			function ()
			{
				var overedRating = $( this ).data( 'rating' );
				
				$( '#reviews-comments .reviews-comments-form-rating-star' ).css( 'background-position', '0 0' );
				
				for ( var i = 1; i <= overedRating; i++ )
				{
					$( '#reviews-comments .reviews-comments-form-rating-star[data-rating="' + i + '"]' ).css( 'background-position', '0 -22px' );
				}
			}
		);
		
		
		
		$( '#reviews-comments .reviews-comments-form-rating-star' ).click(
			function ()
			{
				$( '#reviews-comments .reviews-comments-form-rating' ).css( 'border-color', '#fff' );
				
				$( '#reviews-comments .reviews-comments-form-rating-star' ).removeClass( 'active' );
				$( '#reviews-comments .reviews-comments-form-rating-star' ).removeClass( 'selected' );
				$( this ).addClass( 'selected' );
				
				var clickedRating = $( this ).data( 'rating' );
				
				for ( var i = 1; i <= clickedRating; i++ )
				{
					$( '#reviews-comments .reviews-comments-form-rating-star[data-rating="' + i + '"]' ).addClass( 'active' );
				}
			}
		);
		
		
		
		$( '#reviews-comments .reviews-comments-form-rating' ).mouseleave(
			function ()
			{
				$( '#reviews-comments .reviews-comments-form-rating-star' ).each(
					function ( index, element )
					{
						if ( !$( element ).hasClass( 'active' ) )
						{
							$( element ).css( 'background-position', '0 0' );
						}
					}
				);
				
			}
		);
		
		
		$( '#reviews-add-button' ).click(
			function ()
			{
				var name = $( '#reviews-comment-name' ).val();
				var rating = $( '#reviews-comments .reviews-comments-form-rating-star.selected' ).data( 'rating' );
				var text = $( '#reviews-comment-text' ).val();
				
				var email = '';
				var phone = '';
				var advantage = '';
				var disadvantage = '';
				
				var imagesIds = '';
				if ( $( '#reviews-comment-images img' ).length > 0 )
				{
					$( '#reviews-comment-images img' ).each(
						function ( index, element )
						{
							var imageId = $( element ).data( 'id' );
							
							if ( imagesIds )
							{
								imagesIds += ',';
							}
							
							imagesIds += imageId;
						}
					);
				}
				
				
				var needScroll = !$( this ).hasClass( 'with-moderation' );
				
				var error = false;
				
				if ( !name )
				{
					$( '#reviews-comment-name' ).css( 'border-color', 'red' );
					error = true;
				}
				
				if ( !rating )
				{
					$( '#reviews-comments .reviews-comments-form-rating' ).css( 'border-color', 'red' );
					error = true;
				}
				
				if ( !text )
				{
					$( '#reviews-comment-text' ).css( 'border-color', 'red' );
					error = true;
				}
				
				
				
				if ( $( '#reviews-comment-email' ).length )
				{
					email = $( '#reviews-comment-email' ).val();
					
					if ( $( '#reviews-comment-email' ).hasClass( 'reviews-required' ) && !email )
					{
						$( '#reviews-comment-email' ).css( 'border-color', 'red' );
						error = true;
					}
				}
				
				if ( $( '#reviews-comment-phone' ).length )
				{
					phone = $( '#reviews-comment-phone' ).val();
					
					if ( $( '#reviews-comment-phone' ).hasClass( 'reviews-required' ) && !phone )
					{
						$( '#reviews-comment-phone' ).css( 'border-color', 'red' );
						error = true;
					}
				}
				
				if ( $( '#reviews-comment-advantage' ).length )
				{
					advantage = $( '#reviews-comment-advantage' ).val();
					
					if ( $( '#reviews-comment-advantage' ).hasClass( 'reviews-required' ) && !advantage )
					{
						$( '#reviews-comment-advantage' ).css( 'border-color', 'red' );
						error = true;
					}
				}
				
				if ( $( '#reviews-comment-disadvantage' ).length )
				{
					disadvantage = $( '#reviews-comment-disadvantage' ).val();
					
					if ( $( '#reviews-comment-disadvantage' ).hasClass( 'reviews-required' ) && !disadvantage )
					{
						$( '#reviews-comment-disadvantage' ).css( 'border-color', 'red' );
						error = true;
					}
				}
				
				
				
				
				if ( !error )
				{
					var info = $( this ).parent();
					var loadingText = $( this ).data( 'loading-text' );
					var successText = $( this ).data( 'success-text' );
					
					info.html( loadingText );
					
					var url = $( '#reviews-comment-url' ).val();
					
					$.post(
						'/bitrix/components/reviews/comments/ajax.php',
						{
							NAME: name,
							RATING: rating,
							TEXT: text,
							URL: url,
							IMAGES_IDS: imagesIds,
							EMAIL: email,
							PHONE: phone,
							ADVANTAGE: advantage,
							DISADVANTAGE: disadvantage,
						},
						function ( data )
						{
							info.html( successText );
							info.css( 'color', 'green' );
							info.css( 'font-weight', 'bold' );
							
							
							var commentsBlockOffset = $( '#reviews-comments .reviews-comments-block' ).offset().top - 110; 
				
							if ( needScroll )
							{
								$( 'html, body' ).animate(
									{
										scrollTop: commentsBlockOffset
									},
									1000
								);
								
								$.post(
									'',
									function ( d )
									{
										$( '.reviews-comments-block' ).html( $( d ).find( '.reviews-comments-block' ).html() );
									}
								);
							}
						}
					);
				}
			}
		);
		
		
		
		$( '#reviews-comment-name, #reviews-comment-text, .reviews-required' ).keyup(
			function ()
			{
				$( this ).css( 'border-color', '#5599cc' );
			}
		);
		
		
		
		$( 'body' ).on(
			'change',
			'.reviews-comment-image',
			function ()
			{
				var counter = $( '#reviews-comment-images form' ).length;
				
				$( '#reviews-comment-image-form-' + counter ).submit();
				$( this ).remove();
				$( '#reviews-comment-image-form-' + counter + ' .reviews-comments-form-item' ).append( '<span>' + $( '#reviews-comment-image-form-' + counter ).data( 'loading-text' ) + '</span>' );
			}
		);
		
		
		
		$( '#hidden-frame' ).on(
			'load',
			function ()
			{
				
				if ( $( '#reviews-comment-images input[type="file"]' ).length == 0 )
				{
					var pic = $( '#hidden-frame' ).contents().find( 'body' ).html();
					var length = $( '#reviews-comment-images form' ).length;
					$( '#reviews-comment-image-form-' + length + ' .reviews-comments-form-item span' ).remove();
					$( '#reviews-comment-image-form-' + length + ' .reviews-comments-form-item' ).append( pic );
					

					var nextCounter = length + 1;
					var loadingText = $( '#reviews-comment-image-form-' + length ).data( 'loading-text' );
					
					var additionalImageForm = '';
					additionalImageForm += '<form id="reviews-comment-image-form-' + nextCounter + '" method="post" enctype="multipart/form-data" action="/bitrix/components/reviews/comments/upload_file.php" target="hidden-frame" data-loading-text="' + loadingText + '">';
					additionalImageForm += '<div class="reviews-comments-form-item">';
					additionalImageForm += '<label></label>';
					additionalImageForm += '<input type="file" id="reviews-comment-image-' + nextCounter + '" class="reviews-comment-image" name="image" />';
					additionalImageForm += '</div></form>';

					$( '#reviews-comment-images' ).append( additionalImageForm );
				}
			}
		);
		
		
		$( '#reviews-comments' ).on(
			'click',
			' .reviews-comment-image img',
			function ()
			{
				$( this ).toggleClass( 'active' );
			}
		);
		
		
		$( '.reviews-comments-stat' ).click(
			function ()
			{
				if ( !$( this ).hasClass( 'active' ) )
				{
					var rating = $( this ).data( 'rating' );
					
					$( '.reviews-comments-stat' ).removeClass( 'active' );
					$( this ).addClass( 'active' );
					
					$( '.reviews-comment' ).addClass( 'hidden' );
					$( '.reviews-comment[data-rating="' + rating + '"]' ).removeClass( 'hidden' );
					
					$( '#reviews-comments-show-more-comments' ).hide();
				}
				else
				{
					$( '.reviews-comments-stat' ).removeClass( 'active' );
					$( '.reviews-comment' ).removeClass( 'hidden' );
					
					if ( $( '.reviews-comment[data-page="2"]' ).length > 0 )
					{
						$( '#reviews-comments-show-more-comments' ).show();
						
						$( '.reviews-comment' ).addClass( 'hidden' );
						$( '.reviews-comment[data-page="1"]' ).removeClass( 'hidden' );
					}
				}
			}
		);
		
		
		$( '#reviews-comments-show-more-comments' ).click(
			function ()
			{
				var visibleCount = $( '.reviews-comment:visible' ).length;
				var currentPage = $( '.reviews-comment:visible' ).eq( visibleCount - 1 ).data( 'page' );
				var nextPage = currentPage + 1;
				
				$( '.reviews-comment[data-page="' + nextPage + '"]' ).removeClass( 'hidden' );
				
				if ( $( '.reviews-comment[data-page="' + ( nextPage + 1 ) + '"]' ).length == 0 )
				{
					$( this ).hide();
				}
			}
		);
		
		
		$( 'body' ).on(
			'click',
			'.reviews-comment-vote-up, .reviews-comment-vote-down',
			function ()
			{
				if ( !$( this ).hasClass( 'disabled' ) )
				{
					var commentId = $( this ).data( 'id' );
					var vote = $( this ).data( 'vote' );
					
					if ( commentId && vote )
					{
						$.post(
							'/bitrix/components/reviews/comments/ajax_vote.php',
							{
								COMMENT_ID: commentId,
								VOTE: vote
							}
						);
						
						var val = parseInt( $( this ).find( 'span' ).text() );
						val++;
						$( this ).find( 'span' ).text( val );
						
						$( this ).parent().find( 'a' ).addClass( 'disabled' );
					}
				}
			}
		);
		
		
		$( 'body' ).on(
			'click',
			'.reviews-comment-moderation a',
			function ()
			{
				var id = $( this ).data( 'id' );
				var action = $( this ).data( 'action' );
				var confirmMessage = $( this ).data( 'confirm-message' );
				
				var canDo = true;
				if ( action == 'DELETE' )
				{
					canDo = confirm( confirmMessage );
				}
				
				
				if ( canDo )
				{
					$.post(
						'/includes/reviews_ajax.php',
						{
							ID: id,
							ACTION: action
						},
						function ()
						{
							$.post(
								'',
								function ( d )
								{
									$( '.reviews-comments-block' ).html( $( d ).find( '.reviews-comments-block' ).html() );
								}
							);
						}
					);
				}
			}
		);
		
		
		$( 'body' ).on(
			'click',
			'#reviews-comments .reviews-comment-answer-add a',
			function ()
			{
				var id = $( this ).data( 'id' );
				var text = $( this ).parent().find( 'textarea' ).val();
				
				$.post(
					'/includes/reviews_ajax.php',
					{
						ID: id,
						ACTION: 'ANSWER',
						TEXT: text
					},
					function ()
					{
						$.post(
							'',
							function ( d )
							{
								$( '.reviews-comments-block' ).html( $( d ).find( '.reviews-comments-block' ).html() );
							}
						);
					}
				);
			}
		);
	}
);