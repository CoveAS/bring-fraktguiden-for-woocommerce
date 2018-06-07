jQuery( function( $ ) {

  function post_kco_delivery_post_code( post_code, country ) {
    $.post(
      _fraktguiden_data.ajaxurl,
      {
        action: 'bring_post_code_validation',
        post_code: post_code,
        country: country,
        nonce:   _fraktguiden_data.klarna_checkout_nonce
      },
      function( response ) {
        if ( ! response.valid ) {
          $( '.bring-enter-postcode input' ).prop( 'disabled', false );
          $( '.bring-enter-postcode .input-text' ).addClass( 'bring-error-input' );
          $( '.bring-enter-postcode' ).addClass( 'bring-error' ).removeClass( 'loading' );
          $( '<p>' ).addClass( 'bring-error-message' ).html( response.result ).appendTo( $( '.bring-enter-postcode' ) );
          return false;
        }
        location.href = location.href;
      }
    );
  }

  function toggle_checkout() {
    var shipping_opts = $( 'input[name^="shipping_method"]' ).length;
    if ( shipping_opts ) {
      $( '.bring-enter-postcode' ).hide();
      $( '#klarna-checkout-container' ).show();
    } else {
      $( '.bring-enter-postcode' ).show();
      $( '#klarna-checkout-container' ).hide();
    }
  }

  $( document ).ajaxSuccess( function ( event, xhr, settings ) {
    var data = settings.data;
    if ( ! settings.url.match( /wc-ajax=kco_wc_iframe_shipping_address_change$/ ) ) {
      return;
    }
    toggle_checkout();
  } );

  $( document.body ).on( 'updated_checkout', function () {
    if ( ! $( '.bring-enter-postcode .input-text' ).length ) {
      return;
    }
    $( '.bring-enter-postcode .input-text' ).on( 'keydown', function() {
      $( '.bring-enter-postcode' ).removeClass( 'bring-error' );
      $( this ).removeClass( 'bring-error-input' );
      $( '.bring-error-message' ).remove();
    } );
    $( '.checkout' ).unbind( 'submit' );
    $( '.checkout' ).submit( function( e ) {
      e.preventDefault();
      e.stopPropagation();
      // console.log( 'submitted' );
      $( this ).addClass( 'loading' );
      $( this ).find( '.bring-enter-postcode .input-text' ).prop( 'disabled', true ).removeClass( 'bring-error-input' );
      $( '.bring-error-message' ).remove();
      post_kco_delivery_post_code(
        $( '#bring-post-code' ).val(),
        $( '#bring-country' ).val()
      );
    } );
    toggle_checkout();
  } );

} );