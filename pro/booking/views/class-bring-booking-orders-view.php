<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Orders_View {

  static function init() {
    add_action( 'admin_footer-edit.php', array( __CLASS__, 'add_bulk_admin_footer' ) );
    add_filter( 'manage_edit-shop_order_columns', array( __CLASS__, 'booking_status_column' ), 15 );
    add_action( 'manage_shop_order_posts_custom_column', array( __CLASS__, 'booking_column_value' ), 10, 2 );
    add_action( 'admin_action_bring_bulk_book', array( __CLASS__, 'bulk_send_booking' ) );
  }

  /**
   * @param $columns
   * @return mixed
   */
  static function booking_status_column( $columns ) {
    $columns['bring_booking_status'] = __( 'Booking', 'bring-fraktguiden' );
    return $columns;
  }

  /**
   * @param $column
   */
  static function booking_column_value( $column ) {
    global $the_order;

    if ( $column == 'bring_booking_status' ) {
      $order = new Bring_WC_Order_Adapter( $the_order );
      $info  = Bring_Booking_Common_View::get_booking_status_info( $order );

      echo '<div clas="bring-area-icon">';
      echo Bring_Booking_Common_View::create_status_icon( $info, 16 );
      echo '</div>';
      echo '<div class="bring-area-info">';
      echo $info['text'];
      echo '</div>';
    }
  }

  /**
   *
   */
  static function add_bulk_admin_footer() {
    global $post_type;
    if ( $post_type == 'shop_order' ) {
      ?>
      <script type="text/template" id="tmpl-bring-modal-bulk">
        <div class="wc-backbone-modal">
          <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
              <header class="wc-backbone-modal-header">
                <h1 class="bgf-modal-header"><?php _e( 'MyBring Booking', 'bring-fraktguiden' ); ?></h1>
                <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                  <span class="screen-reader-text"><?php _e( 'Close modal panel', 'bring-fraktguiden' ); ?></span>
                </button>
              </header>
              <article>
                <div class="bring-form-field" style="margin-top:0">
                  <?php _e( 'This will only book orders that has not been booked.', 'bring-fraktguiden' ) ?>
                </div>
                <div class="bring-form-field">
                  <label><?php _e( 'Selected orders', 'bring-fraktguiden' ) ?>:</label>
                  <span class="bring-modal-selected-orders-list"></span>
                </div>
                <div class="bring-form-field">
                  <label><?php _e( 'MyBring Customer', 'bring-fraktguiden' ); ?>:</label>
                  <?php Bring_Booking_Common_View::render_customer_selector( '_bring-modal-customer-selector' ); ?>
                </div>
                <div class="bring-form-field">
                  <label><?php _e( 'Shipping Date', 'bring-fraktguiden' ); ?>:</label>
                  <?php Bring_Booking_Common_View::render_shipping_date_time( '_bring-modal-shipping-date' ); ?>
                </div>
              </article>
              <footer>
                <div class="inner">
                  <button id="btn-ok" class="button button-primary button-large"><?php echo Bring_Booking_Common_View::booking_label( true ); ?></button>
                </div>
              </footer>
            </section>
          </div>
        </div>
        <div class="wc-backbone-modal-backdrop modal-close"></div>
      </script>

      <script type="text/javascript">
        (function () {
          var $ = jQuery;

          $( document ).ready( function () {

            // Add bulk booking to action selector
            $( '<option>' ).val( 'bring_bulk_book' ).text( '<?php echo Bring_Booking_Common_View::booking_label( true ); ?>' ).appendTo( "select[name='action']" );
            $( '<option>' ).val( 'bring_bulk_book' ).text( '<?php echo Bring_Booking_Common_View::booking_label( true );?>' ).appendTo( "select[name='action2']" );

            // Add bulk print to action selector
            $( '<option>' ).val( 'bring_print_labels' ).text( '<?php _e( 'Bring - Print labels', 'bring-fraktguiden' ); ?>' ).appendTo( "select[name='action']" );
            $( '<option>' ).val( 'bring_print_labels' ).text( '<?php _e( 'Bring - Print labels', 'bring-fraktguiden' ) ;?>' ).appendTo( "select[name='action2']" );

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


            $( '#doaction' ).click( function ( evt ) {
              if ( $( "select[name='action']" ).val() == 'bring_bulk_book' ) {
                show_bulk_book_dialog();
                evt.preventDefault();
              }

              if ( $( "select[name='action']" ).val() == 'bring_print_labels' ) {
                var url = '<?php echo Bring_Booking_Labels::create_download_url(''); ?>';

                url = url + get_checked_order_ids().join(',');

                window.open(url);
                evt.preventDefault();
              }
            } );


            $( '#doaction2' ).click( function ( evt ) {
              if ( $( "select[name='action2']" ).val() == 'bring_bulk_book' ) {
                show_bulk_book_dialog();
                evt.preventDefault();
              }

              if ( $( "select[name='action']" ).val() == 'bring_print_labels' ) {
                evt.preventDefault();
              }
            } );

            $( document.body ).on( 'wc_backbone_modal_response', function ( e ) {
              customer_number.val( $( '[name=_bring-modal-customer-selector]' ).val() );
              shipping_date.val( $( '[name=_bring-modal-shipping-date]' ).val() );
              shipping_date_hour.val( $( '[name=_bring-modal-shipping-date-hour]' ).val() );
              shipping_date_minutes.val( $( '[name=_bring-modal-shipping-date-minutes]' ).val() );
              form.submit();
            } );

          } );
        })();
      </script>
      <?php
    }
  }

  static function bulk_send_booking() {
    if ( isset( $_REQUEST['post'] ) ) {
      Bring_Booking::bulk_send_booking( $_REQUEST['post'] );
    }
  }

}