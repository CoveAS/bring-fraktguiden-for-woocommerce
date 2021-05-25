jQuery(
	function( $ ) {
			$( '.get-rates' ).click(
				function() {
					$( '.bring-debug' ).block(
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
					var resource = $.get(
						bring_fraktguiden_debug.ajaxurl,
						{
							action: 'bring_debug_product_rates',
							post_code: $( '.bring-debug__post-code' ).val(),
							country: $( '#bring-debug-country' ).val(),
							id: bring_fraktguiden_debug.id,
						}
					);
					resource.done(
						function( data ) {
							$( '.bring-debug__rates' ).html( data );
							$( '.bring-debug' ).unblock();
						}
					);
				}
			);
	}
);
