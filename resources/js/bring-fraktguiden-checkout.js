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

	function oneColumnShipping() {
		const tr = $('.woocommerce-shipping-totals');
		if (tr.children.length !== 2) {
			return;
		}
		const header = tr.find('th');
		const cell = tr.find('td');
		if (! header.length || ! cell.length) {
			return;
		}
		// Move the header and give it colspan 2
		header.attr('colspan', 2)
		tr.before(
			$('<tr>').append(header)
		);
		// Give cell colspan 2
		cell.attr('colspan', 2)
	}
	if (_fraktguiden_checkout.one_column_shipping) {
		$(document).on( 'updated_checkout', oneColumnShipping );
		oneColumnShipping();
	}

	let busy = false;
	const select_time_slot = function () {
		if (busy) {
			return;
		}
		busy = true;
		const elem = $( this );
		$('.alternative-date-item--chosen')
			.removeClass( 'alternative-date-item--chosen alternative-date-item--selected' );
		elem.addClass( 'alternative-date-item--chosen alternative-date-item--selected' );
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

		$('.bring-fraktguiden-logo, .bring-fraktguiden-description, .bring-fraktguiden-environmental, .bring-fraktguiden-eta')
			.on('click', function () { $(this).closest('li').find('input').trigger('click');})
	};

	$( document ).on( 'updated_checkout', bind_buttons );
	bind_buttons();
});
