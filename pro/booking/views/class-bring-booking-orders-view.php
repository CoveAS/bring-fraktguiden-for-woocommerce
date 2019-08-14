<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Bring_Booking_Orders_View class
 */
class Bring_Booking_Orders_View {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_footer-edit.php', [ __CLASS__, 'add_bulk_admin_footer' ] );
		add_filter( 'manage_edit-shop_order_columns', [ __CLASS__, 'booking_status_column' ], 15 );
		add_action( 'manage_shop_order_posts_custom_column', [ __CLASS__, 'booking_column_value' ], 10, 2 );
		add_action( 'admin_action_bring_bulk_book', [ __CLASS__, 'bulk_send_booking' ] );
		add_filter( 'bulk_actions-edit-shop_order', [ __CLASS__, 'add_bring_bulk_actions' ] );
	}

	/**
	 * Add bulk booking and printing to actions selector
	 *
	 * @param  array $actions Actions.
	 * @return array
	 */
	public static function add_bring_bulk_actions( $actions ) {
		$actions['bring_bulk_book']  = Bring_Booking_Common_View::booking_label( true );
		$actions['bring_bulk_print'] = __( 'Bring - Print labels', 'bring-fraktguiden-for-woocommerce' );

		return $actions;
	}

	/**
	 * Get booking status column
	 *
	 * @param array $columns Columns.
	 *
	 * @return mixed
	 */
	public static function booking_status_column( $columns ) {
		$columns['bring_booking_status'] = __( 'Booking', 'bring-fraktguiden-for-woocommerce' );

		return $columns;
	}

	/**
	 * Get booking column value
	 *
	 * @param string $column Column.
	 */
	public static function booking_column_value( $column ) {
		global $the_order;

		if ( 'bring_booking_status' === $column ) {
			$order = new Bring_WC_Order_Adapter( $the_order );
			$info  = Bring_Booking_Common_View::get_booking_status_info( $order );
			?>

			<div clas="bring-area-icon">
				<?php echo Bring_Booking_Common_View::create_status_icon( $info, 16 ); ?>
			</div>

			<div class="bring-area-info">
				<?php echo $info['text']; ?>
			</div>

			<?php
		}
	}

	/**
	 * Add bulk admin footer
	 */
	public static function add_bulk_admin_footer() {
		global $post_type;

		if ( 'shop_order' === $post_type ) {
			?>

	  <script type="text/template" id="tmpl-bring-modal-bulk">
		<div class="wc-backbone-modal">
		  <div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
			  <header class="wc-backbone-modal-header">
				<h1 class="bgf-modal-header"><?php _e( 'Mybring Booking', 'bring-fraktguiden-for-woocommerce' ); ?></h1>
				<button class="modal-close modal-close-link dashicons dashicons-no-alt">
				  <span class="screen-reader-text"><?php _e( 'Close modal panel', 'bring-fraktguiden-for-woocommerce' ); ?></span>
				</button>
			  </header>
			  <article>
				<div class="bring-form-field" style="margin-top:0">
				  <?php _e( 'This will only book orders that has not been booked.', 'bring-fraktguiden-for-woocommerce' ); ?>
				</div>
				<div class="bring-form-field">
				  <label><?php _e( 'Selected orders', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
				  <span class="bring-modal-selected-orders-list"></span>
				</div>
				<div class="bring-form-field">
				  <label><?php _e( 'Mybring Customer', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
				  <?php Bring_Booking_Common_View::render_customer_selector( '_bring-modal-customer-selector' ); ?>
				</div>
				<div class="bring-form-field">
				  <label><?php _e( 'Shipping Date', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
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

	  <script>
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

			// Run bulk booking or printing actions when selected and clicked
			$( '#doaction, #doaction2' ).on( 'click', function ( evt ) {

				var selected = $(this).closest( '.bulkactions' ).find( 'select[name^="action"]' ).val();

				if ( 'bring_bulk_book' === selected ) {
					show_bulk_book_dialog();
					evt.preventDefault();
				}

				if ( 'bring_bulk_print' === selected ) {
					var url = '<?php echo Bring_Booking_Labels::create_download_url( '' ); ?>';

					url = url + get_checked_order_ids().join(',');

					window.open(url);
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

	/**
	 * Send booking in bulk
	 *
	 * @return void
	 */
	public static function bulk_send_booking() {
		$post = filter_input( Fraktguiden_Helper::get_input_request_method(), 'post' );

		if ( empty( $post ) || ! is_array( $post ) ) {
			return;
		}

		Bring_Booking::bulk_send_booking( $post );
	}
}
