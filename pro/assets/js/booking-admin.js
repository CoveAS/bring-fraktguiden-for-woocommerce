
(function () {
	var $ = jQuery;

	$( document ).ready( function () {

	//@todo: does this need to be global?
	var modal = $( {} );

	var form = $( 'form#posts-filter' );

	// Add input for form filter submit from modal.
	var customer_number = $( '<input type="hidden" name="_bring-customer-number" value="">' );
	var shipping_date = $( '<input type="hidden" name="_bring-shipping-date" value="">' );
	var shipping_date_hour = $( '<input type="hidden" name="_bring-shipping-date-hour" value="">' );
	var shipping_date_minutes = $( '<input type="hidden" name="_bring-shipping-date-minutes" value="">' );

	form.append( customer_number );
	form.append( shipping_date );
	form.append( shipping_date_hour );
	form.append( shipping_date_minutes );

	function get_checked_order_ids() {
		var result = [];
		$( '#the-list' ).find( 'input[type=checkbox]:checked' ).each( function ( i, elem ) {
			result.push( elem.value );
		} );
		return result;
	}


	function show_bulk_book_dialog() {
		// Open dialog.
		modal.WCBackboneModal( {
			template: 'bring-modal-bulk'
		} );

		// Initialize data picker.
		$( "[name=_bring-modal-shipping-date]" ).datepicker( {
			minDate: 0,
			dateFormat: 'yy-mm-dd'
		} );

		// Disable dialog submit button if no orders are checked.
		var order_ids = get_checked_order_ids();
		if ( order_ids.length == 0 ) {
			$( '#btn-ok' ).attr( 'disabled', 'true' );
		}
		else {
			$( '#btn-ok' ).removeAttr( 'disabled' );
		}

		// Print order ids in dialog.
		$( '.bring-modal-selected-orders-list' ).text( order_ids.join( ' - ' ) );
	}

	var display_errors = function() {
		modal.WCBackboneModal( {
			template: 'bring-modal-bulk-errors'
		} );
	}

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

	$( document.body ).on( 'wc_backbone_modal_response', function ( e ) {
		customer_number.val( $( '[name=_bring-modal-customer-selector]:checked' ).val() );
		shipping_date.val( $( '[name=_bring-modal-shipping-date]' ).val() );
		shipping_date_hour.val( $( '[name=_bring-modal-shipping-date-hour]' ).val() );
		shipping_date_minutes.val( $( '[name=_bring-modal-shipping-date-minutes]' ).val() );

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

		var url = location.origin + location.pathname;
		$.get( url + '?json=true&' + form.serialize(), function( data ) {
			form.unblock();
			if ( ! data.bring_column ) {
				return;
			}
			var error_messages = [];
			$.each( data.report, function( id, record ) {
				if ( record.status === 'ok' ) {
					return;
				}
				error_messages.push( record.message );
			} );
			$.each( data.bring_column, function( id, column_item ) {
				var elem = $( '#post-' + id );
				if ( ! elem.length ) {
					return;
				}
				elem.find( '.bring-area-icon span' )
				.removeClass( 'dashicons-minus dashicons-yes dashicons-warning' )
				.addClass( column_item.icon );
				elem.find( '.bring-area-info' )
				.text( column_item.text );
			} );
			var error_list = $( '<ul>' );
			$.each( data.report, function( id, record ) {
				var elem = $( '#post-' + id );
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
			if ( error_list.children().length ) {
				modal.WCBackboneModal( {
					template: 'bring-modal-bulk-errors'
				} );
				$( '#bring-error-modal-content' ).html( error_list );
			}
		} );
	} );

} );
})();
