jQuery(function ($) {

	const unblock_options = () => {
		$ ( '.bring-fraktguiden-date-options' ).unblock();
	};
	const block_options = () => {
		$ ( '.bring-fraktguiden-date-options' ).block(
			{
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			}
		);
	};
	$(document).on( 'update_checkout', block_options );

	let busy = false;
	const select_time_slot = function () {
		if (busy) {
			return;
		}
		busy = true;
		const elem = $( this );
		elem.addClass( 'alternative-date-item--chosen' )
			.siblings()
			.removeClass( 'alternative-date-item--chosen' );
		block_options();
		$.post(
			_fraktguiden_checkout.ajaxurl,
			{
				action: 'bring_select_time_slot',
				time_slot: elem.data( 'time_slot' ),
			},
			function () {
				busy = false;
				unblock_options();
			}
		);
	};

	const bind_buttons = () => {
		$( '.bring-fraktguiden-date-options .alternative-date-item--choice' ).on(
			'click',
			select_time_slot
		);
	};

	$( document ).on( 'updated_checkout', bind_buttons );
	bind_buttons();
});
