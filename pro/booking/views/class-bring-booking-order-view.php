<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

namespace BringFraktguidenPro\Booking\Views;

use Bring_Fraktguiden\Common\Fraktguiden_Helper;
use BringFraktguidenPro\Booking\Actions\Get_First_Enabled_Bring_Product;
use BringFraktguidenPro\Booking\Bring_Booking;
use BringFraktguidenPro\Booking\Bring_Booking_Customer;
use BringFraktguidenPro\Booking\Consignment_Request\Bring_Booking_Consignment_Request;
use BringFraktguidenPro\Order\Bring_WC_Order_Adapter;
use DateTime;
use Exception;
use WC_Order;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wp_ajax_bring_update_packages', [Bring_Booking_Order_View::class, 'ajax_update_packages'] );
add_action( 'wp_ajax_nopriv_bring_update_packages', [Bring_Booking_Order_View::class, 'ajax_update_packages'] );

/**
 * Bring_Booking_Order_View class
 */
class Bring_Booking_Order_View {

	const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', __CLASS__ . '::add_booking_meta_box', 1, 2 );
		add_action( 'woocommerce_order_action_bring_book_with_bring', __CLASS__ . '::send_booking' );
		add_action( 'save_post', __CLASS__ . '::redirect_page' );
	}

	/**
	 * Add booking meta box
	 *
	 * @param string $post_type Post type.
	 * @param WP_Post $post Post.
	 */
	public static function add_booking_meta_box( $post_type, $post ) {
		if ( 'shop_order' !== $post_type && 'woocommerce_page_wc-orders' !== $post_type ) {
			return;
		}

		// Do not show if the order does not use fraktguiden shipping.
		$order = new Bring_WC_Order_Adapter( wc_get_order($post) );
		if ( Fraktguiden_Helper::get_option( 'booking_without_bring' ) !== 'yes' && ! $order->has_bring_shipping_methods() ) {
			return;
		}
		add_meta_box(
				'woocommerce-order-bring-booking',
				__( 'Bring Booking', 'bring-fraktguiden-for-woocommerce' ),
				[ __CLASS__, 'render_booking_meta_box' ],
				$post_type,
				'normal',
				'high'
		);
	}

	/**
	 * Render booking meta box
	 *
	 * @param WP_Post $post Post.
	 */
	public static function render_booking_meta_box( $post ) {
		$wc_order = wc_get_order($post);
		$order    = new Bring_WC_Order_Adapter( $wc_order );
		?>

		<div class="bring-booking-meta-box-content">
			<?php
			if ( ! $order->is_booked() ) {
				self::render_progress_tracker( $order );
			}

			?>
			<div class="bring-booking-meta-box-content-body">
				<?php
				self::render_booking_meta_box_content($order);
				?>

			</div>
		</div>
		<?php
	}

	/**
	 * Render start
	 *
	 * @param Bring_WC_Order_Adapter $order Order.
	 */
	public static function render_start( Bring_WC_Order_Adapter $order ): void {
		if ( empty( $order->order->get_shipping_methods() ) ) {
			?>
			<div>
				<?php esc_html_e( 'Please add a shipping item to the order and reload the page to enable booking', 'bring-fraktguiden-for-woocommerce' ); ?>
			</div>
			<?php
			return;
		}
		if ( ! $order->has_booking_errors() ) {
			?>
			<div>
				<?php esc_html_e( 'Press start to start booking', 'bring-fraktguiden-for-woocommerce' ); ?>
				<br>
				<?php
				$next_status = Fraktguiden_Helper::get_option( 'auto_set_status_after_booking_success' );
				if ( 'none' !== $next_status ) {
					$order_statuses = wc_get_order_statuses();
					printf( __( 'Order status will be set to %s upon successful booking', 'bring-fraktguiden-for-woocommerce' ), mb_strtolower( $order_statuses[ $next_status ] ) );
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * Render booking success screen
	 *
	 * @param Bring_WC_Order_Adapter $order Order.
	 */
	public static function render_booking_success_screen( $order ) {
		?>
		<div class="bring-info-box">
			<div>
				<?php
				$status = Bring_Booking_Common_View::get_booking_status_info( $order );
				echo Bring_Booking_Common_View::create_status_icon( $status, 90 );
				?>
				<h3><?php echo $status['text']; ?></h3>
				<?php if ( 'completed' !== $order->order->get_status() ) { ?>
					<div style="text-align:center;margin-bottom:1em;">
						<?php esc_html_e( 'Note: Order is not completed', 'bring-fraktguiden-for-woocommerce' ); ?>
					</div>
				<?php } ?>
			</div>
			<div>
				<h3 style="margin-top:0"><?php esc_html_e( 'Consignments', 'bring-fraktguiden-for-woocommerce' ); ?></h3>
				<?php self::render_consignments( $order ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render consignments
	 *
	 * @param Bring_WC_Order_Adapter $order Order.
	 */
	public static function render_consignments( $order ) {
		$type = $order->get_consignment_type();
		?>
		<div class="bring-consignments">
			<?php
			$consignments = $order->get_booking_consignments();
			foreach ( $consignments as $consignment ) {
				require dirname( __DIR__ ) . '/templates/consignment-table-' . $type . '.php';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render step 2 screen
	 *
	 * @param Bring_WC_Order_Adapter $order Order.
	 *
	 * @throws Exception
	 */
	public static function render_step2_screen( $order ) {
		$bring_product = $order->bring_product ?: (new Get_First_Enabled_Bring_Product())();;
		$service_data = Fraktguiden_Helper::get_service_data_for_key( $bring_product );
		?>
		<div class="bring-form-field">
			<label><?php esc_html_e( 'Customer Number', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>
			<?php Bring_Booking_Common_View::render_customer_selector( '_bring-customer-number', $order ); ?>
		</div>

		<div class="bring-form-field">
			<label><?php esc_html_e( 'Shipping Date', 'bring-fraktguiden-for-woocommerce' ); ?>:</label>

			<div>
				<?php Bring_Booking_Common_View::render_shipping_date_time(); ?>
			</div>
		</div>

		<?php if ( in_array( $order->bring_product, [ 5600, 'PA_DOREN' ] ) ): ?>

			<?php
			$date      = false;
			$time_slot = $order->shipping_item->get_meta( 'bring_fraktguiden_time_slot' );
			if ( $time_slot ) {
				$date = new DateTime( $time_slot );
			}
			?>
			<div class="bring-form-field">
				<label><?php esc_html_e( 'Customer requested delivery date', 'bring-fraktguiden-for-woocommerce' ); ?>
					:</label>

				<div>
					<?php
					Bring_Booking_Common_View::render_shipping_date_time(
							'_bring-delivery-date',
							[
									'date'   => $date ? $date->format( 'Y-m-d' ) : '',
									'hour'   => $date ? $date->format( 'H' ) : '',
									'minute' => $date ? $date->format( 'i' ) : '',
							]
					);
					?>
				</div>
			</div>

		<?php endif; ?>

		<script>
			jQuery(document).ready(function ($) {
				$("[name=_bring-shipping-date], [name=_bring-delivery-date]").datepicker({
					minDate: 0,
					dateFormat: 'yy-mm-dd'
				});

				$(".bring_shipping_services").select2();
			});
		</script>
		<?php

		$shipping_items = $order->get_fraktguiden_shipping_items();
		if ( empty( $shipping_items ) ) {
			return;
		}
		$shipping_item = reset( $shipping_items );
		$consignment   = Bring_Booking_Consignment_Request::create( $shipping_item );
		self::render_parties( $consignment );
		?>
		<div class="bring-form-field">
			<label for="_bring_additional_info_sender">
				<?php esc_html_e( 'Additional Info', 'bring-fraktguiden-for-woocommerce' ); ?>
				(<?php esc_html_e( 'Sender', 'bring-fraktguiden-for-woocommerce' ); ?>)
			</label>
			<textarea name="_bring_additional_info_sender" id="_bring_additional_info_sender"></textarea>
		</div>
		<div class="bring-form-field">
			<label for="_bring_additional_info_recipient">
				<?php esc_html_e( 'Additional Info', 'bring-fraktguiden-for-woocommerce' ); ?>
				(<?php esc_html_e( 'Recipient', 'bring-fraktguiden-for-woocommerce' ); ?>)
			</label>
			<textarea
					name="_bring_additional_info_recipient"
					id="_bring_additional_info_recipient"
			  <?php if ( $service_data['home_delivery'] ?? false ) : ?>
				  required="required"
			  <?php endif; ?>
		></textarea>
		</div>
		<?php if ( $order->order->get_customer_note() ) : ?>
			<div class="bring-customer-note">
		  <span class="bring-customer-note__label">
			<?php esc_html_e( 'Customer note from the order', 'bring-fraktguiden-for-woocommerce' ); ?>:
		  </span>
				<span class="bring-customer-note__value">
			<?php echo esc_html( $order->order->get_customer_note() ); ?>
		  </span>
			</div>
		<?php endif; ?>

		<div class="bring-form-field" style="margin-bottom:25px">
			<label>
				<?php esc_html_e( 'Packages', 'bring-fraktguiden-for-woocommerce' ); ?>:
			</label>
			<?php self::render_packages( $order ); ?>
		</div>
		<?php
	}

	/**
	 * @param bool $is_step2
	 */
	public static function render_footer( bool $is_step2, Bring_WC_Order_Adapter $adapter ): void
	{
		$missing_params  = false;
		$required_params = [
				'booking_address_store_name',
				'booking_address_street1',
				'booking_address_postcode',
				'booking_address_city',
				'booking_address_country',
		];
		foreach ( $required_params as $field ) {
			if ( ! Fraktguiden_Helper::get_option( $field ) ) {
				$missing_params = true;
			}
		}
		?>
		<div class="bring-booking-footer">
			<?php if ( $is_step2 ) { ?>
				<!-- @todo: use a real link / not history back -->
				<button type="button" onclick="window.history.back()"
						class="button"
						style="margin-right:1em"><?php _e( 'Cancel', 'bring-fraktguiden-for-woocommerce' ); ?></button>
				<button type="submit" name="wc_order_action"
						value="bring_book_with_bring"
						data-tip="<?php _e( 'Update order and send consignment to Bring', 'bring-fraktguiden-for-woocommerce' ); ?>"
						class="button button-primary tips">
					<?php echo Bring_Booking_Common_View::booking_label(); ?>
				</button>
			<?php } elseif ( ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) && $missing_params ) { ?>
				<a href="<?php echo Fraktguiden_Helper::get_settings_url(); ?>#woocommerce_bring_fraktguiden_booking_title"
				   data-tip="<?php _e( 'Update your store address.', 'bring-fraktguiden-for-woocommerce' ); ?>"
				   class="button button-primary tips"><?php _e( 'Update store information', 'bring-fraktguiden-for-woocommerce' ); ?></a>
			<?php } elseif ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) { ?>
				<button type="submit" name="_bring-start-booking"
						<?php if ( empty( $adapter->order->get_shipping_methods() ) ) { echo 'disabled="disabled"'; } ?>
						data-tip="<?php _e( 'Start creating a label to ship this order with Mybring', 'bring-fraktguiden-for-woocommerce' ); ?>"
						class="button button-primary tips"><?php _e( 'Start booking', 'bring-fraktguiden-for-woocommerce' ); ?></button>
			<?php } else { ?>
				<a href="<?php echo Fraktguiden_Helper::get_settings_url(); ?>"
				   data-tip="<?php _e( 'You have to upgrade to PRO in order to use this feature.', 'bring-fraktguiden-for-woocommerce' ); ?>"
				   class="button button-primary tips"><?php _e( 'Activate PRO', 'bring-fraktguiden-for-woocommerce' ); ?></a>
			<?php } ?>
		</div>
		<?php
	}

	static function render_parties( $consignment ): void
	{
		?>
		<div class="bring-form-field">
			<a class="bring-show-parties button"
			   href="#"><?php _e( 'Show Parties', 'bring-fraktguiden-for-woocommerce' ); ?></a>
		</div>
		<script type="text/javascript">
			(function () {
				jQuery('.bring-show-parties').click(function (evt) {
					evt.preventDefault();
					jQuery('.bring-booking-parties').toggle();
				});
			})();
		</script>

		<div class="bring-booking-parties bring-form-field bring-flex-box"
			 style="display:none">
			<div>
				<h3><?php _e( 'Sender Address', 'bring-fraktguiden-for-woocommerce' ); ?></h3>
				<?php self::render_address_table( $consignment->get_sender_address() ); ?>
			</div>
			<div>
				<h3><?php _e( 'Recipient Address', 'bring-fraktguiden-for-woocommerce' ); ?></h3>
				<?php self::render_address_table( $consignment->get_recipient_address() ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param Bring_WC_Order_Adapter $order
	 */
	public static function render_packages( Bring_WC_Order_Adapter $order ) {
		echo '<div id="bring-fraktguiden-booking-packages"></div>';
	}

	/**
	 * @param string $label
	 * @param string $value
	 */
	public static function render_table_row( $label, $value ) {
		?>
		<tr>
			<td>
				<?php echo $label; ?>:
			</td>
			<td>
				<?php echo $value; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * @param array $address
	 */
	public static function render_address_table( $address ) {
		?>
		<table>
			<tbody>
			<?php
			self::render_table_row( __( 'Name', 'bring-fraktguiden-for-woocommerce' ), $address['name'] );
			self::render_table_row( __( 'Street Address 1', 'bring-fraktguiden-for-woocommerce' ), $address['addressLine'] );
			self::render_table_row( __( 'Street Address 2', 'bring-fraktguiden-for-woocommerce' ), $address['addressLine2'] );
			self::render_table_row( __( 'Postcode', 'bring-fraktguiden-for-woocommerce' ), $address['postalCode'] );
			self::render_table_row( __( 'City', 'bring-fraktguiden-for-woocommerce' ), $address['city'] );
			self::render_table_row( __( 'Country', 'bring-fraktguiden-for-woocommerce' ), $address['countryCode'] );
			if ( $address['reference'] ) {
				self::render_table_row( __( 'Reference', 'bring-fraktguiden-for-woocommerce' ), $address['reference'] );
			}
			if ( $address['additionalAddressInfo'] ) {
				self::render_table_row( __( 'Additional Address Info', 'bring-fraktguiden-for-woocommerce' ), $address['additionalAddressInfo'] );
			}
			?>
			<tr>
				<td colspan="2">
					<h4><?php _e( 'Contact', 'bring-fraktguiden-for-woocommerce' ); ?></h4>
				</td>
			</tr>
			<?php
			self::render_table_row( __( 'Name', 'bring-fraktguiden-for-woocommerce' ), $address['contact']['name'] ?? $address['name'] );
			self::render_table_row( __( 'Email', 'bring-fraktguiden-for-woocommerce' ), $address['contact']['email'] );
			self::render_table_row( __( 'Phone Number', 'bring-fraktguiden-for-woocommerce' ), $address['contact']['phoneNumber'] );
			?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * @param Bring_WC_Order_Adapter $order
	 */
	public static function render_progress_tracker( $order ) {
		$step2  = Bring_Booking_Common_View::is_step2();
		$booked = $order->is_booked();
		?>
		<div class="bring-progress-tracker bring-flex-box">
	  <span class="<?php echo( ( ! $step2 && ! $booked ) ? 'bring-progress-active' : '' ); ?>">
		1. <?php _e( 'Create a new booking', 'bring-fraktguiden-for-woocommerce' ); ?>
	  </span>
			<span class="<?php echo( ( $step2 ) ? 'bring-progress-active' : '' ); ?>">
		2. <?php _e( 'Confirm and submit consignment', 'bring-fraktguiden-for-woocommerce' ); ?>
	  </span>
			<span class="<?php echo( ( $booked ) ? 'bring-progress-active' : '' ); ?>">
		3. <?php _e( 'Sucessfully booked', 'bring-fraktguiden-for-woocommerce' ); ?>
	  </span>
		</div>
		<?php
	}

	/**
	 * @param Bring_WC_Order_Adapter $order
	 *
	 * @return string
	 */
	public static function render_errors( $order ) {
		$errors = $order->get_booking_errors();
		?>
		<div class="bring-info-box">
			<div>
				<?php
				$status = Bring_Booking_Common_View::get_booking_status_info( $order );
				echo Bring_Booking_Common_View::create_status_icon( $status );
				?>
				<h3><?php echo $status['text']; ?></h3>
			</div>

			<div class="bring-booking-errors">
				<div><?php _e( 'Previous booking request failed with the following errors:', 'bring-fraktguiden-for-woocommerce' ); ?></div>
				<ul>
					<?php foreach ( $errors as $error ) { ?>
						<li><?php echo $error; ?></li>
					<?php } ?>
				</ul>
				<div><?php _e( 'Press Start to try again', 'bring-fraktguiden-for-woocommerce' ); ?></div>
			</div>
		</div>
		<?php
	}

	public static function redirect_page() {
		global $post_ID;
		$type = get_post_type();

		if ( $type == 'shop_order' && isset( $_POST['_bring-start-booking'] ) ) {
			$url = admin_url() . 'post.php?post=' . $post_ID . '&action=edit&booking_step=2';
			wp_redirect( $url );
			exit;
		}
	}

	/**
	 * @param WC_Order $wc_order
	 */
	public static function send_booking( $wc_order ) {
		Bring_Booking::send_booking( $wc_order );
	}

	public static function ajax_update_packages() {
		if ( ! isset( $_POST['order_id'] ) ) {
			die( '{ "error": "Missing order id" }' );
		}
		if ( ! isset( $_POST['packages'] ) || ! is_array( $_POST['packages'] ) || empty( $_POST['packages'] ) ) {
			die( '{ "error": "Empty packages" }' );
		}
		$packages        = $_POST['packages'];
		$expected_fields = [
				'service_id',
				'height',
				'length',
				'order_item_id',
				'weight',
				'width',
		];
		foreach ( $packages as $package ) {
			foreach ( $expected_fields as $key ) {
				if ( ! isset( $package[ $key ] ) ) {
					die( '{ "error": "Missing package field ' . $key . '" }' );
				}
				if ( ! is_string( $package[ $key ] ) ) {
					die( '{ "error": "Package field is not a string ' . $key . '" }' );
				}
			}
		}
		$order_id = $_POST['order_id'];
		if ( ! $order_id ) {
			die( 'testing' );
		}

		$wc_order       = new WC_Order( $order_id );
		$order          = new Bring_WC_Order_Adapter( $wc_order );
		$shipping_items = $order->order->get_shipping_methods();
		$existing       = [];
		// Get the existing packages
		foreach ( $shipping_items as $item_id => $method ) {
			$meta_packages        = wc_get_order_item_meta( $item_id, '_fraktguiden_packages_v2', true );
			$existing[ $item_id ] = $meta_packages;
		}

		$fields       = [ 'weight_in_grams', 'length', 'width', 'height' ];
		$new_packages = [];
		// Create the package array that bring needs
		foreach ( $packages as $package ) {
			$order_item_id = $package['order_item_id'];
			if (! isset($new_packages[$order_item_id])) {
				$new_packages[$order_item_id] = [];
			}
			$new_packages[$order_item_id][] = [
				'weight_in_grams' => floatval($package['weight']) * 1000,
				'length' => $package['length'],
				'width' => $package['width'],
				'height' => $package['height'],
			];
		}

		// Save the new fields
		foreach ( $new_packages as $item_id => $new_package ) {
			wc_update_order_item_meta( $item_id, '_fraktguiden_packages_v2', $new_package );
		}

		$service_ids = [];
		foreach ( $packages as $package ) {
			$service_ids[ $package['order_item_id'] ] = $package['service_id'];
		}

		foreach ( $service_ids as $order_item_id => $service_id ) {
			foreach ( $shipping_items as $item ) {
				if ( $item->get_id() !== $order_item_id ) {
					continue;
				}
				$item->update_meta_data( 'bring_product', $service_id );
				$item->save();
			}
		}
		// @TODO: with multiple shipping items, remove the metadata for items no longer used
		die;
	}

	private static function render_booking_meta_box_content(Bring_WC_Order_Adapter $adapter)
	{

		try {
			$customers = Bring_Booking_Customer::get_customer_numbers_formatted();
		} catch ( Exception $e ) {
			printf( '<p class="error">%s</p>', esc_html( $e->getMessage() ) );
			return;
		}

		$step2    = Bring_Booking_Common_View::is_step2();
		if ( $adapter->has_booking_errors() && ! $step2 ) {
			self::render_errors( $adapter );
		}

		if ( ! $step2 && ! $adapter->is_booked() ) {
			self::render_start( $adapter );
		}

		if ( $step2 && ! $adapter->is_booked() ) {
			self::render_step2_screen( $adapter );
		}

		if ( $adapter->is_booked() ) {
			self::render_booking_success_screen( $adapter );
		}

		if ( ! $adapter->is_booked() ) {
			self::render_footer( $step2, $adapter );
		}
	}
}
