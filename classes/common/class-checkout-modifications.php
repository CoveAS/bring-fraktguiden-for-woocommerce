<?php

namespace Bring_Fraktguiden\Common;

use Bring_Fraktguiden\Actions\CreateAlternativeDeliveryDateFromArray;
use WC_Shipping_Method_Bring;

/**
 * Checkout Modifications
 */
class Checkout_Modifications {

	static function setup() {
		add_action(
			'woocommerce_checkout_order_review',
			__CLASS__ . '::template',
			15
		);

		add_filter(
			'woocommerce_update_order_review_fragments',
			__CLASS__ . '::filter_fragments'
		);

		add_action(
			'wp_enqueue_scripts',
			__CLASS__ . '::enqueue_scripts'
		);

		add_action(
			'woocommerce_checkout_create_order_shipping_item',
			__CLASS__ . '::attach_item_meta',
			10,
			4
		);

		add_action(
			'woocommerce_review_order_before_submit',
			__CLASS__ . '::bag_on_door_consent'
		);

		add_action(
			'woocommerce_checkout_update_order_meta',
			__CLASS__ . '::bag_on_door_order_meta',
			10,
			1
		);

		add_action(
			'woocommerce_admin_order_data_after_billing_address',
			__CLASS__ . '::bag_on_door_admin_value',
			10,
			1
		);

		add_filter(
			'kco_additional_checkboxes',
			__CLASS__ . '::kco_bag_on_door_consent'
		);
	}


	public static function enqueue_scripts() {
		if ( ! is_checkout() ) {
			return;
		}

		$file = 'bring-fraktguiden-checkout.js';
		$url = plugins_url( 'assets/js/' . $file, dirname( __DIR__ ) );
		wp_register_script(
			'fraktguiden-checkout-js',
			$url,
			[ 'jquery' ],
			\Bring_Fraktguiden::VERSION,
			true
		);
		wp_localize_script(
			'fraktguiden-checkout-js',
			'_fraktguiden_checkout',
			[
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'one_column_shipping' => Fraktguiden_Helper::get_option( 'shipping_options_full_width', 'yes' ) === 'yes',
				'map_key'             => Fraktguiden_Helper::get_option( 'pickup_point_map', 'postenMapsLink' ),
			]
		);
		wp_enqueue_script( 'fraktguiden-checkout-js' );
	}

	public static function filter_fragments( $fragments ) {
		ob_start();
		self::template();
		$fragments['.bring-fraktguiden-date-options'] = ob_get_clean();

		return $fragments;
	}

	public static function template() {
		$args = self::get_alternative_date_parameters();
		extract( $args );
		include dirname( dirname( __DIR__ ) ) . '/templates/woocommerce/alternative-dates.php';
	}

	public static function get_alternative_date_parameters() {
		$args                    = [
			'earliest'     => false,
			'range'        => [],
			'alternatives' => [],
			'selected'     => WC()->session->get( 'bring_fraktguiden_time_slot' ),
		];
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		$meta_data               = false;
		foreach ( WC()->shipping()->get_packages() as $i => $package ) {
			if ( ! isset( $chosen_shipping_methods[ $i ], $package['rates'][ $chosen_shipping_methods[ $i ] ] ) ) {
				continue;
			}
			// Get current selected rate.
			$rate      = $package['rates'][ $chosen_shipping_methods[ $i ] ];
			$meta_data = $rate->get_meta_data();
			break;
		}

		if ( empty( $meta_data['alternative_delivery_dates'] ) ) {
			return $args;
		}
		$alternatives = ( new CreateAlternativeDeliveryDateFromArray )(
			$meta_data['alternative_delivery_dates']
		);

		if ( empty( $alternatives ) ) {
			return $args;
		}
		$time_slot_group      = reset( $alternatives );
		$args['earliest']     = reset( $time_slot_group['items'] );
		$args['range']        = self::extract_date_range( $alternatives );
		$args['alternatives'] = $alternatives;

		if ( ! self::validate_selected_time_slot( $args['selected'], $alternatives ) ) {
			$args['selected'] = array_key_first( $time_slot_group['items'] ) . 'T' . $time_slot_group['id'];
		}

		return $args;
	}

	public static function validate_selected_time_slot( $selected, $alternatives ) {
		foreach ( $alternatives as $time_slot_group ) {
			foreach ( $time_slot_group['items'] as $key => $alternative ) {
				if ( $selected === $key . 'T' . $time_slot_group['id'] ) {
					return true;
				}
			}
		}

		return false;
	}

	public static function extract_date_range( $alternatives ) {
		$range      = [];
		$first_date = null;
		$last_date  = null;

		// Find the first and last dates in the available alternatives
		foreach ( $alternatives as $time_slot_group ) {
			$last = end( $time_slot_group['items'] );
			if ( ! $last_date || $last->expected_delivery_date > $last_date ) {
				$last_date = clone $last->expected_delivery_date;
			}
			$first = reset( $time_slot_group['items'] );
			if ( ! $first_date || $first->expected_delivery_date < $first_date ) {
				$first_date = clone $first->expected_delivery_date;
			}
		}
		// Create a time period.
		$interval = \DateInterval::createFromDateString( '1 day' );
		$period   = new \DatePeriod(
			$first_date,
			$interval,
			$last_date->modify( '+1 day' )
		);

		// Fill the range array.
		foreach ( $period as $date ) {
			$key           = $date->format( "Y-m-d" );
			$range[ $key ] = [
				'd'    => $date->format( 'D' ),
				'day'  => ucfirst( wp_date( 'D', $date->getTimestamp(), $date->getTimezone() ) ),
				'date' => str_replace(
					' ',
					'&nbsp;',
					wp_date( 'j. M', $date->getTimestamp(), $date->getTimezone() )
				),
			];
		}

		return $range;
	}

	/**
	 * Attach item meta
	 *
	 * @param \WC_Order_Item_Shipping $item        Shipping item.
	 * @param string                  $package_key Package key.
	 * @param array                   $package     Package.
	 * @param \WC_Order               $order       Order Instance.
	 */
	public static function attach_item_meta( $item, $package_key, $package, $order ) {
		$bring_product = $item->get_meta( 'bring_product' );
		if ( empty( $bring_product ) ) {
			return;
		}
		$shipping_methods   = \WC_Shipping::instance()->get_shipping_methods();
		$shipping_method_id = $item->get_method_id();
		if ( empty( $shipping_methods[ $shipping_method_id ] ) ) {
			return;
		}
		$shipping_method = $shipping_methods[ $shipping_method_id ];
		$field_key       = $shipping_method->get_field_key( 'services' );
		if ( ! Fraktguiden_Service::vas_for( $field_key, $bring_product, [ 'alternative_delivery_dates' ] ) ) {
			return;
		}
		$time_slot = WC()->session->get( 'bring_fraktguiden_time_slot' );
		$item->add_meta_data( 'bring_fraktguiden_time_slot', $time_slot, true );
		$order->add_order_note(
			__( 'Customer requested time slot: ' ) . $time_slot
		);

		add_action(
			'woocommerce_checkout_update_order_meta',
			__CLASS__ . '::attach_order_note'
		);
	}

	/**
	 * Attach item meta
	 *
	 * @param integer   $order_id    Order id.
	 * @param string    $package_key Package key
	 * @param array     $package     Package.
	 * @param \WC_Order $order       Order Instance.
	 */
	public static function attach_order_note( $order_id ) {
		$order     = wc_get_order( $order_id );
		$time_slot = WC()->session->get( 'bring_fraktguiden_time_slot' );
		$order->add_order_note(
			__( 'Customer requested delivery time:', 'bring-fraktguiden-for-woocommerce' ) . " $time_slot"
		);
	}

	/**
	 * Bag on door consent
	 */
	public static function bag_on_door_consent() {
		$current_shipping_method = WC()->session->get( 'chosen_shipping_methods' );

		if ( empty( $current_shipping_method )
		     || ! (
				in_array( 'bring_fraktguiden:3584', $current_shipping_method )
				|| in_array( 'bring_fraktguiden:3570', $current_shipping_method )
			)
		     || WC()->session->get( 'chosen_payment_method' ) === 'kco' ) {
			return;
		}

		if ( ! self::is_bag_on_door_enabled( $current_shipping_method ) ) {
			return;
		}

		$label   = esc_html__(
			"Deliver the package in a bag on my door if it doesn't fit in the mailbox",
			'bring-fraktguiden-for-woocommerce'
		);
		$checked = checked(
			isset( $_POST['bag_on_door_consent'] ), // WPCS: input var ok, csrf ok.
			true,
			false
		);
		echo <<<HTML
			<p class="form-row">
				<label
					class="
						woocommerce-form__label
						woocommerce-form__label-for-checkbox
						checkbox
					"
				>
					<input
						type="checkbox"
						class="
							woocommerce-form__input
							woocommerce-form__input-checkbox
							input-checkbox
						"
						name="bag_on_door_consent"
						$checked
					/>
					<span class="bag-on-door-text">$label</span></span>
				</label>
				<input type="hidden" name="bag-on-door-field" value="1" />
			</p>
		HTML;
	}

	/**
	 * Save user selected bag on door value to order meta
	 */
	public static function bag_on_door_order_meta( $order_id ) {
		$consent_value = filter_input( INPUT_POST, 'bag_on_door_consent', FILTER_VALIDATE_BOOLEAN );

		if ( $consent_value ) {
			update_post_meta( $order_id, '_bag_on_door_consent', $consent_value );
		}
	}

	/**
	 * Display order related bag on door value in WC order admin page
	 */
	public static function bag_on_door_admin_value( $order ) {
		$consent = get_post_meta( $order->get_id(), '_bag_on_door_consent', true );

		if ( ! $consent ) {
			return;
		}
		echo '<p><strong>';
		esc_html_e( 'Bag on door consent', 'bring-fraktguiden-for-woocommerce' );
		echo ':</strong><br>';
		esc_html_e( 'Yes', 'bring-fraktguiden-for-woocommerce' );
		echo '</p>';
	}

	/**
	 * Bag on door option for Klarna checkout
	 */
	public static function kco_bag_on_door_consent( array $additional_checkboxes ) {
		$current_shipping_method = WC()->session->get( 'chosen_shipping_methods' );

		if ( empty( $current_shipping_method )
		     || ! (
				in_array( 'bring_fraktguiden:3584', $current_shipping_method )
				|| in_array( 'bring_fraktguiden:3570', $current_shipping_method )
			)
		) {
			return $additional_checkboxes;
		}

		if ( ! self::is_bag_on_door_enabled( $current_shipping_method ) ) {
			return $additional_checkboxes;
		}
		$additional_checkboxes[] = array(
			'id'       => 'klarna_bag_on_door_consent',
			'text'     => __( "Deliver the package in a bag on my door if it doesn't fit in the mailbox",
				'bring-fraktguiden-for-woocommerce' ),
			'checked'  => false,
			'required' => true,
		);

		return $additional_checkboxes;
	}

	private static function is_bag_on_door_enabled( array $current_shipping_method ): bool {
		$service_key = ( new WC_Shipping_Method_Bring() )->get_field_key( 'services' );
		foreach ( $current_shipping_method as $shipping_method_key ) {
			$parts         = explode( ':', $shipping_method_key );
			$bring_product = array_pop( $parts );
			if ( Fraktguiden_Service::vas_for( $service_key, $bring_product, [ '1081' ] ) ) {
				return true;
			}
		}

		return false;
	}
}
