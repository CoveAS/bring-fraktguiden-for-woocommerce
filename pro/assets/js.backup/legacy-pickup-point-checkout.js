/* global _fraktguiden_data */

/**
 * Checkout
 */
(function () {
	var $ = jQuery;

	var lang = _fraktguiden_data.i18n;
	var checkout_div;

	// *************************************************************************
	// Events

	/**
	 * As the checkout is dynamic (ajax) events are delegated to the body element.
	 * in order to not
	 *
	 * @returns {jQuery}
	 */
	function events() {
		return $( document.body );
	}

	/**
	 * Event name when the checkout review is updated.
	 *
	 * @static
	 * @type {string}
	 */
	events.CHECKOUT_REVIEW_UPDATED = 'updated_checkout';

	/**
	 * Event name when the pickup point post code is updated.
	 *
	 * @static
	 * @type {string}
	 */
	events.POST_CODE_UPDATED = 'postcode_updated.bring';

	/**
	 * Event name when pickup point is updated.
	 *
	 * @static
	 * @type {string}
	 */
	events.PICKUP_POINT_SELECTOR_CHANGED = 'pickup_point_updated.bring';

	/**
	 * Event name when pickup point <select> is updated.
	 *
	 * @static
	 * @type {string}
	 */
	events.PICKUP_POINT_SELECT_UPDATED = 'pickup_point_select_updated.bring';

	// *************************************************************************
	// Init

	// Create an object where the user's choices can be stored each time the
	// checkout review reloads.
	var user_selected = {
		postcode: '',
		country: '',
		pickup_point_id: ''
	};

	// Add event handlers to the document.
	add_order_review_event_handlers();

	// *************************************************************************
	// Functions

	function check_shipping_rate_selection() {
		if ( has_bring_shipping_rates() ) {
			if ( is_servicepakke_selected() ) {
				// Create pickup point html.
				user_selected.postcode = user_selected.postcode ? user_selected.postcode : get_shipping_postcode();
				user_selected.country  = user_selected.country ? user_selected.country : get_shipping_country();
				events().trigger( events.POST_CODE_UPDATED, [user_selected.postcode, user_selected.country] );
			}
		}
	}

	/**
	 * Add event handlers to the document.
	 */
	function add_order_review_event_handlers() {

		get_order_review_wrapper_elem().on(
			'keyup',
			'.fraktguiden-pickup-point-postcode',
			function () {
				user_selected.postcode = this.value;
				events().trigger( events.POST_CODE_UPDATED, [this.value, get_shipping_country()] );
			}
		);

		get_order_review_wrapper_elem().on(
			'change',
			'.fraktguiden-pickup-point-select',
			function () {
				user_selected.pickup_point_id = this.value;
				events().trigger( events.PICKUP_POINT_SELECTOR_CHANGED, [this] );
			}
		);
	}

	/**
	 * Clone shipping methods
	 *
	 * Move the shipping method options from their original container
	 */
	function clone_shipping_methods( options_target ) {
		// @TODO: Only if enabled
		if ( options_target.length ) {
			// Clone the original shipping rates
			return;
		}
		// When there is only one option then it's just a hidden input field
		var lone_option = $( '#shipping_method_0' );
		if ( lone_option.length ) {
			var elem = $( '<p>' ).addClass( 'only-one-shipping-option' );
			elem.append( lone_option.parent().text() );
			options_target.append( elem );
			options_target.parent().show();
			return;
		}

		var shipping_method_clone = $( '#shipping_method' ).clone();
		shipping_method_clone.attr( 'id', 'shipping_method_clone' );
		// Hide the original
		$( '#shipping_method' ).hide();
		// Link the clones to the original. A change in the clone will be a change in the original
		shipping_method_clone.find( 'input' ).each(
			function() {
					// Se the inputs id to a data id and remove the original id
					$( this ).data( 'id', $( this ).attr( 'id' ) );
					$( this ).removeAttr( 'id' );
					$( this ).attr( 'name', 'cloned_' + $( this ).attr( 'name' ) );
			}
		).change(
			function() {
					var id = $( this ).data( 'id' );
					$( '#' + id ).prop( 'checked', $( this ).prop( 'checked' ) ).trigger( 'change' );
			}
		);
		options_target.append( shipping_method_clone );
		options_target.parent().show();
	}

	/**
	 * Return all Bring shipping rates radio buttons.
	 *
	 * @returns {jQuery}
	 */
	function get_bring_shipping_radio_buttons() {
		return $( 'input[type=radio][value^=bring_fraktguiden].shipping_method, .only-one-shipping-option' );
	}

	/**
	 * Returns true if shipping methods has Bring shipping rates.
	 *
	 * @returns {boolean}
	 */
	function has_bring_shipping_rates() {
		return get_bring_shipping_radio_buttons().length > 0;
	}

	/**
	 * Returns true if selected shipping rate is servicepakke.
	 *
	 * @returns {boolean}
	 */
	function is_servicepakke_selected() {
		return get_selected_shipping_rate() == 'servicepakke';
	}

	/**
	 * Returns the selected shipping rate.
	 *
	 * @returns {null}
	 */
	function get_selected_shipping_rate() {
		var selected = $( 'input[type=radio][value^=bring_fraktguiden].shipping_method:checked' );
		if ( ! selected.length ) {
			return null;
		}
		var matches = selected.val().match( /^.*:([a-z_\-]+[a-z])\-?(\d+)?$/ );
		if ( ! matches ) {
			return null;
		}
		return matches[1];
	}

	/**
	 * @returns {boolean}
	 */
	function ship_to_different_address() {
		return $( '[name=ship_to_different_address]:checked' ).length > 0;
	}

	/**
	 * Returns the user's shipping post code.
	 *
	 * @returns {String}
	 */
	function get_shipping_postcode() {
		return ship_to_different_address() ? woo_shipping_postcode_elem().val() : woo_billing_postcode_elem().val()
	}

	/**
	 * Returns the user's shipping country.
	 *
	 * @returns {String}
	 */
	function get_shipping_country() {
		// Return shipping country from WooCommerce checkout form.
		return ship_to_different_address() ? woo_shipping_country_elem().val() : woo_billing_country_elem().val()
	}

	/**
	 * Returns the element that wraps the review box.
	 *
	 * @returns {jQuery}
	 */
	function get_order_review_wrapper_elem() {
		return $( '#order_review' );
	}

	/**
	 * Returns WooCommerce's billing post code element.
	 *
	 * @returns {jQuery}
	 */
	function woo_billing_postcode_elem() {
		return $( '[name=billing_postcode]' );
	}

	/**
	 * Returns WooCommerce's post code element.
	 *
	 * @returns {jQuery}
	 */
	function woo_shipping_postcode_elem() {
		return $( '[name=shipping_postcode]' );
	}

	/**
	 * Returns WooCommerce's billing country element.
	 *
	 * @returns {jQuery}
	 */
	function woo_billing_country_elem() {
		return $( '[name=billing_country]' );
	}

	/**
	 * Returns WooCommerce's shipping country element.
	 *
	 * @returns {jQuery}
	 */
	function woo_shipping_country_elem() {
		return $( '[name=shipping_country]' );
	}

	/**
	 * Returns Pickup Point's post code element.
	 *
	 * @returns {jQuery}
	 */
	function pickup_point_postcode_elem() {
		return $( '.fraktguiden-pickup-point-postcode' );
	}

	var delay = (function () {
		var timer = 0;
		return function ( callback, ms ) {
			clearTimeout( timer );
			timer = setTimeout( callback, ms );
		};
	})();

	function create_cookie( name, value, days ) {
		var expires;
		if ( days ) {
			var date = new Date();
			date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
			expires = "; expires=" + date.toGMTString();
		} else {
			expires = "";
		}

		document.cookie = name + "=" + value + expires + "; path=/";
	}

	/**
	 * Updates cookies with the user's data.
	 */
	function update_cookies() {
		create_cookie( '_fraktguiden_pickup_point_postcode', user_selected.postcode );
		create_cookie( '_fraktguiden_pickup_point_info_cached', $( '[name=_fraktguiden_pickup_point_info_cached]' ).val() );
	}
})();
