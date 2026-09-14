jQuery(function ($) {
	var form = $( 'form#posts-filter, #wc-orders-filter' );

	if ( ! form.length ) {
		return;
	}

	var bookDialog = document.getElementById( 'bfg-bulk-book' );
	var errorDialog = document.getElementById( 'bfg-bulk-errors' );

	const handleBulkBookResponse = function (data) {
		form.unblock();
		if ( ! data.bring_column ) {
			return;
		}
		if ( data.print_url ) {
			window.open( data.print_url, '_blank' ).focus();
		}
		$.each( data.bring_column, function( id, column_item ) {
			var elem = $( '#post-' + id +',#order-' + id );
			if ( ! elem.length ) {
				return;
			}
			elem.find( '.bring-booking-cell' ).replaceWith( column_item );
		} );
		var error_list = $( '<ul>' );
		$.each( data.report, function( id, record ) {
			var elem = $( '#post-' + id +',#order-' + id );
			if ( elem.length ) {
				// Update the WooCommerce order status.
				elem.find( '.column-order_status' )
					.html( record.order_status );
				// Initialise the tooltip.
				if ( $.prototype.tipTip ) {
					elem.find( '.tips' ).tipTip( {
						'attribute': 'data-tip',
						'fadeIn': 50,
						'fadeOut': 50,
						'delay': 200
					} );
				}
			}
			if ( 'error' !== record.status ) {
				return;
			}
			error_list.append(
				$( '<li>' ).append(
					$( '<a>' ).addClass( 'error-post-id' )
						.attr( 'href', record.url.replace( '&amp;', '&' ) )
						.text( '#' + id ),
					' ',
					$( '<span>' )
						.addClass( 'error-post-message' )
						.text( record.message )
				)
			);
		} );
		if ( error_list.children().length && errorDialog ) {
			$( '#bfg-bulk-errors-list' ).empty().append( error_list.children() );
			errorDialog.showModal();
		}
	};
	const buttons = $('[data-action="bring-book-orders"]');
	const handleClick = function(e, el) {
		e.preventDefault();
		const ids = el.data('order-ids');
		$.post(
			_booking_data.ajaxurl,
			{
				action: 'bring_bulk_book',
				json : true,
				'id[]': (ids + '').split(','),
			},
			handleBulkBookResponse
		);
	};
	buttons.on('click', function (e) { handleClick(e, $(this)); });
	buttons.on('keyup', function(e) {
		if (e.keyCode === 13) {
			handleClick(e, $(this));
		}
	});

	function get_checked_order_ids() {
		var result = [];
		$( '#the-list' ).find( 'input[type=checkbox]:checked' ).each( function ( i, elem ) {
			result.push( elem.value );
		} );
		return result;
	}

	function show_bulk_book_dialog() {
		if ( ! bookDialog ) {
			return;
		}

		var order_ids = get_checked_order_ids();

		$( '#bfg-bulk-book-orders' ).text( order_ids.join( ' - ' ) );
		$( '#bfg-bulk-book-send' ).prop( 'disabled', order_ids.length === 0 );

		bookDialog.showModal();
	}

	function send_bulk_booking() {
		var order_ids = get_checked_order_ids();
		if ( ! order_ids.length ) {
			return;
		}

		// The form asks for one time, and the booking reads the hour and the
		// minute apart.
		var time = ( $( '#bfg-bulk-book-time' ).val() || '' ).split( ':' );

		bookDialog.close();

		form.block(
			{
				message: '',
				css: {
					border: 'none'
				},
				overlayCSS: {
					backgroundColor: '#f9f9f9'
				},
			}
		);

		$.post(
			_booking_data.ajaxurl,
			{
				action: 'bring_bulk_book',
				json: true,
				'id[]': order_ids,
				// The custom select widget moves the id to its trigger button,
				// so the native select answers to its name.
				'_bring-customer-number': $( '#bfg-bulk-book select[name="_bring-customer-number"]' ).val(),
				'_bring-shipping-date': $( '#bfg-bulk-book-date' ).val(),
				'_bring-shipping-date-hour': time[0] || '',
				'_bring-shipping-date-minutes': time[1] || '',
			},
			handleBulkBookResponse
		);
	}

	$( '#bfg-bulk-book-send' ).on( 'click', send_bulk_booking );

	// Run bulk booking or printing actions when selected and clicked
	$( '#doaction, #doaction2' ).on( 'click', function ( evt ) {

		var selected = $(this).closest( '.bulkactions' ).find( 'select[name^="action"]' ).val();

		if ( 'bring_bulk_book' === selected ) {
			show_bulk_book_dialog();
			evt.preventDefault();
		}

		if ( 'bring_bulk_print' === selected ) {
			var url = _booking_data.downloadurl;

			url = url + get_checked_order_ids().join(',');

			window.open(url);
			evt.preventDefault();
		}
	} );
});
