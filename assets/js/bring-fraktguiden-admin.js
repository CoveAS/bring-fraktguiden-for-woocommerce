jQuery(
	function( $ ) {
		$( '.bring-notice.is-dismissible' ).each(
			function() {
				var notice_id = $( this ).data( 'notice_id' );
				$( this ).on(
					'click',
					'.notice-dismiss',
					function( e ) {
						e.preventDefault();
						$.post(
							bring_fraktguiden.ajaxurl,
							{
								action: 'bring_dismiss_notice',
								notice_id: notice_id
							}
						);
					}
				);
			}
		);
	}
);
