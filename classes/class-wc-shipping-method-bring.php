<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once 'traits/settings.php';
require_once 'common/http/class-wp-bring-request.php';
require_once 'common/class-fraktguiden-packer.php';
require_once 'common/class-fraktguiden-minimum-dimensions.php';
require_once 'common/class-fraktguiden-service-table.php';
require_once 'common/class-fraktguiden-service.php';
require_once 'common/class-updater.php';

// Value Added Services classes.
require_once 'vas/class-vas.php';
require_once 'vas/class-vas-checkbox.php';


/**
 * Bring class for calculating and adding rates
 */
class WC_Shipping_Method_Bring extends WC_Shipping_Method {

	use Bring_Fraktguiden\Settings;

	const SERVICE_URL = 'https://api.bring.com/shippingguide/v2/products';

	const ID = Fraktguiden_Helper::ID;

	/**
	 * Trace messages
	 *
	 * @var array
	 */
	private $trace_messages = [];

	/**
	 * 'From country' field
	 *
	 * @var string
	 */
	private $from_country = '';

	/**
	 * 'From zip' code field
	 *
	 * @var string
	 */
	private $from_zip = '';

	/**
	 * Shipping from post office
	 *
	 * @var string
	 */
	private $post_office = '';

	/**
	 * Recipient notification over SMS or E-Mail
	 *
	 * @var string
	 */
	private $evarsling = '';

	/**
	 * Services
	 *
	 * @var array
	 */
	public $services = [];

	/**
	 * Switch for showing a service description after the name of the service
	 *
	 * @var string
	 */
	private $display_desc = '';

	/**
	 * Maximum total quantity of products in the cart before offering a custom price
	 *
	 * @var int
	 */
	private $max_products = '';

	/**
	 * Switch to turn on a debugging mode
	 *
	 * @var string
	 */
	private $debug = '';

	/**
	 * WooCommerce logger
	 *
	 * @var WC_Logger
	 */
	private $log;

	/**
	 * Packages params
	 *
	 * @var array
	 */
	protected $packages_params = [];

	/**
	 * Validation messages
	 *
	 * @var string
	 */
	public $validation_messages;

	/**
	 * Field key
	 *
	 * @var string
	 */
	static public $field_key;

	/**
	 * Initialize the instance
	 *
	 * @param int $instance_id ID of the instance.
	 */
	public function __construct( $instance_id = 0 ) {

		$this->id                 = self::ID;
		$this->method_title       = __( 'Bring Fraktguiden', 'bring-fraktguiden-for-woocommerce' );
		$this->method_description = __( 'Automatically calculate shipping rates using Bring Fraktguiden API.', 'bring-fraktguiden-for-woocommerce' );
		$this->supports           = array(
			'shipping-zones',
			'settings',
			'instance-settings',
		);

		if ( $instance_id ) {
			parent::__construct( $instance_id );
		}

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Debug configuration.
		$this->debug = $this->get_setting( 'debug' );
		$this->log   = new WC_Logger();

		// Define user set variables.
		// With shipping zones the method should always be enabled.
		$this->enabled = true;

		// WC_Shipping_Method.
		if ( isset( $this->settings['enabled'] ) ) {
			$this->enabled = $this->settings['enabled'];
		}

		$this->title        = $this->get_setting( 'title' );
		$this->availability = $this->get_setting( 'availability' );
		$this->countries    = $this->get_setting( 'countries' );
		$this->fee          = $this->get_setting( 'handling_fee' );

		// WC_Shipping_Method_Bring.
		$this->from_country = $this->get_setting( 'from_country' );
		$this->from_zip     = $this->get_setting( 'from_zip' );
		$this->post_office  = $this->get_setting( 'post_office' );
		$this->evarsling    = $this->get_setting( 'evarsling' );
		self::$field_key    = $this->get_field_key( 'services' );
		$this->services     = $this->get_services();

		$this->display_desc = $this->get_setting( 'display_desc', 'no' );

		if ( filter_var( $this->display_desc, FILTER_VALIDATE_BOOLEAN ) ) {
			// Replace a default WooCommerce's cart/cart-shipping.php template.
			add_filter( 'wc_get_template', [ $this, 'get_enhanced_shipping_information_template' ], 10, 2 );
		}

		$max_products       = (int) $this->get_setting( 'max_products', 1000 );
		$this->max_products = $max_products ? $max_products : 1000;

		// The packer may make a lot of recursion when the cart contains many items.
		// Make sure xdebug max_nesting_level is raised.
		// See: http://stackoverflow.com/questions/4293775/increasing-nesting-functions-calls-limit.
		ini_set( 'xdebug.max_nesting_level', 10000 );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = false;
		}

		$this->service_table = new Fraktguiden_Service_Table( $this, 'services' );

		$field_key = $this->get_field_key( 'services' );
		Bring_Fraktguiden\updater::setup( $field_key );
	}

	/**
	 * Get services
	 *
	 * @return array Services.
	 */
	public function get_services() {
		$services = $this->get_setting( 'services' );
		if ( ! is_array( $services ) ) {
			$services = [];
		}
		return $services;
	}

	/**
	 * Calculate price excluding VAT
	 *
	 * @param  int $line_price Price.
	 * @return int
	 */
	public function calculate_excl_vat( $line_price ) {
		if ( wc_prices_include_tax() ) {
			$tax_rates    = WC_Tax::get_shipping_tax_rates();
			$remove_taxes = WC_Tax::calc_tax( $line_price, $tax_rates, true );
			return $line_price - array_sum( $remove_taxes );

		}
		return $line_price;
	}

	/**
	 * Returns true if the required options are set
	 *
	 * @return boolean
	 */
	public function is_valid_for_use() {
		$dimensions_unit = get_option( 'woocommerce_dimension_unit' );
		$weight_unit     = get_option( 'woocommerce_weight_unit' );
		$currency        = get_option( 'woocommerce_currency' );
		return $weight_unit && $dimensions_unit && $currency;
	}

	/**
	 * Process admin options
	 *
	 * Note: do not use `Fraktguiden_Helper::update_option` within the process option. It will override the $_POST data!
	 *
	 * Add custom processing to handle the services field
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		$instance_key = null;
		if ( $this->instance_id ) {
			$instance_key = $this->get_instance_option_key();
		}
		$this->service_table->process_services_field( $instance_key );
		$this->process_mybring_api_credentials();
	}

	/**
	 * Pack order
	 *
	 * @param  array $contents Package contents.
	 * @return bool|array      Parameters for each box on success
	 */
	public function pack_order( $contents ) {
		$packer        = new Fraktguiden_Packer();
		$product_boxes = $packer->create_boxes( $contents );
		if ( ! $product_boxes ) {
			return false;
		}
		$multipack = $this->get_setting( 'enable_multipack', 'yes' ) === 'yes';
		// Pack product boxes.
		$packer->pack( $product_boxes, $multipack );

		// Create the url.
		return $packer->create_packages_params();
	}

	/**
	 * Push rate
	 * Validate and add
	 *
	 * @param array $args Arguments.
	 * @throws Exception Exception.
	 */
	public function push_rate( $args ) {
		$required_fields = [ 'id', 'bring_product', 'cost', 'label' ];
		foreach ( $required_fields as $field ) {
			if ( ! isset( $args[ $field ] ) ) {
				throw new Exception( "Missing $field on the shipping rate" );
			}
		}
		if ( strpos( $args['id'], ':' ) === false ) {
			$args['id'] .= ":{$args['bring_product']}";
		}
		if ( ! isset( $args['meta_data'] ) ) {
			$args['meta_data'] = [];
		}
		$args['meta_data']['bring_product'] = $args['bring_product'];
		unset( $args['bring_product'] );
		$this->add_rate( $args );
	}

	/**
	 * Calculate shipping costs
	 *
	 * @param array $package Package.
	 * @todo: in 2.6, the package param was added. Investigate this!
	 */
	public function calculate_shipping( $package = [] ) {
		$this->trace_messages = [];

		// include_once( 'common/class-fraktguiden-packer.php' );
		// Offer flat rate if the cart contents exceeds max product.
		// @TODO: Use the package instead of the cart.
		if ( WC()->cart && WC()->cart->get_cart_contents_count() > $this->max_products ) {
			$alt_handling = $this->get_setting( 'alt_handling' );
			if ( 'flat_rate' === $alt_handling ) {
				$rate = array(
					'id'            => $this->id,
					'bring_product' => $this->get_setting( 'alt_flat_rate_id' ),
					'cost'          => $this->get_price_setting( 'alt_flat_rate' ),
					'label'         => $this->get_setting( 'alt_flat_rate_label', __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ) ),
				);
				$this->push_rate( $rate );
			}
			return;
		}

		$cart                  = $package['contents'];
		$this->packages_params = $this->pack_order( $cart );
		if ( ! $this->packages_params ) {
			return;
		}

		if ( is_checkout() ) {
			$_COOKIE['_fraktguiden_packages'] = wp_json_encode( $this->packages_params );
		}

		if ( ! $package['destination']['postcode'] ) {
			// Postcode must be specified.
			return;
		}

		// Request parameters.
		$params = array_merge( $this->create_standard_url_params( $package ), $this->packages_params );
		// Remove any empty elements.
		$params =  array_filter( $params );

		if ( Fraktguiden_Helper::get_option( 'calculate_by_weight' ) === 'yes' ) {
			// Calculate packages based on weight.
			foreach ( $params as $key => $value ) {
				// Remove dimensions.
				if ( preg_match( '/^(?:length|width|height)\d+$/', $key ) ) {
					unset( $params[ $key ] );
				}
			}
		}

		$params = apply_filters( 'bring_fraktguiden_api_parameters', $params, $this );

		$url = add_query_arg( $params, self::SERVICE_URL );

		// Add all the selected services to the URL.
		$field_key = $this->get_field_key( 'services' );
		$services  = \Fraktguiden_Service::all( $field_key, true );
		if ( ! empty( $services ) ) {
			foreach ( $services as $service ) {
				$url .= $service->getUrlParam();
			}
		}

		$options = [
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			],
		];

		// Make the request.
		$request  = new WP_Bring_Request();
		$response = $request->get( $url, [], $options );

		echo '<pre>';
		var_dump($response->status_code, json_decode( $response->get_body(), true ));
		die;

		if ( 400 == $response->status_code ) {
			$json = json_decode( $response->get_body(), true );
			$this->set_trace_messages( $json['fieldErrors'] );
		}
		if ( 200 != $response->status_code ) {
			$no_connection_handling = $this->get_setting( 'no_connection_handling' );
			if ( 'flat_rate' === $no_connection_handling ) {
				$this->push_rate(
					[
						'id'            => $this->id,
						'bring_product' => $this->get_setting( 'no_connection_rate_id', 'servicepakke' ),
						'cost'          => $this->get_price_setting( 'no_connection_flat_rate' ),
						'label'         => $this->get_setting( 'no_connection_flat_rate_label', __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ) ),
					]
				);
			}
			return;
		}

		// Decode the JSON data from bring.
		$json = json_decode( $response->get_body(), true );
		if ( isset( $json['traceMessages'] ) ) {
			$this->set_trace_messages( $json['traceMessages'] );
		}
		$exception_handling = $this->get_setting( 'exception_handling' );

		// Filter the response json to get only the selected services from the settings.
		$rates = $this->get_services_from_response( $json );
		$rates = apply_filters( 'bring_shipping_rates', $rates, $this );

		// Only push the heavy rate when there are no other bring rates.
		if ( 'flat_rate' === $exception_handling && empty( $rates ) ) {
			// Check if any package exeeds the max settings.
			$messages = $this->get_trace_messages();
			foreach ( $messages as $message ) {
				if ( false !== strpos( $message, 'INVALID_MEASUREMENTS' ) ) {
					$this->push_rate(
						[
							'id'            => $this->id,
							'bring_product' => $this->get_setting( 'exception_rate_id', 'servicepakke' ),
							'cost'          => $this->get_price_setting( 'exception_flat_rate' ),
							'label'         => $this->get_setting( 'exception_flat_rate_label', __( 'Shipping', 'bring-fraktguiden-for-woocommerce' ) ),
						]
					);
					break;
				}
			}
		}

		if ( 'yes' === $this->debug ) {
			$this->log->add( $this->id, 'Request url: ' . print_r( $url, true ) );
			$this->log->add( $this->id, 'Parameters: ' . PHP_EOL . print_r( $params, true ) );
			$this->log->add( $this->id, 'Response: ' . PHP_EOL . json_encode( $json, JSON_PRETTY_PRINT ) );
			if ( $rates ) {
				$this->log->add( $this->id, 'Rates found: ' . print_r( $rates, true ) );
			} else {
				$this->log->add( $this->id, 'No rates found for params' );
			}
		}

		// Calculate rate.
		if ( $rates ) {
			foreach ( $rates as $rate ) {
				$this->push_rate( $rate );
			}
		}
	}

	/**
	 * Get services from respone
	 *
	 * @param array $response The JSON response from Bring.
	 * @return array|boolean
	 */
	public function get_services_from_response( $response ) {

		if ( ! $response || ( is_array( $response ) && count( $response ) === 0 ) || empty( $response['consignments'] ) ) {
			return [];
		}

		$rates = [];
		$services  = \Fraktguiden_Service::all( self::$field_key );

		foreach ( $response['consignments'][0]['products'] as $service_details ) {

			$bring_product = $service_details['id'];
			if ( empty( $services[ $bring_product ] ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( $this->id, 'Unidentified bring product: ' . $bring_product );
				}
				continue;
			}

			if ( ! empty( $service_details['errors'] ) ) {
				// Most likely an error.
				$this->add_trace_messages( $service_details['errors'] );
				continue;
			}
			if ( ! empty( $service_details['price']['netPrice']['priceWithoutAdditionalServices'] ) ) {
				$service = $service_details['price']['netPrice']['priceWithoutAdditionalServices'];
			} elseif ( ! empty( $service_details['price']['listPrice']['priceWithoutAdditionalServices'] ) ) {
				$service = $service_details['price']['listPrice']['priceWithoutAdditionalServices'];
			} else {
				$this->add_trace_messages( [ 'No price provided for ' . $service_details['id'], $service_details ] );
				continue;
			}
			$rate = $service['amountWithoutVAT'];

			$label = $service_details['guiInformation']['productName'];

			$rate = apply_filters(
				'bring_product_api_rate',
				array(
					'id'            => $this->id,
					'bring_product' => sanitize_title( $service_details['id'] ),
					'cost'          => (float) $rate + (float) $this->fee,
					'label'         => $label . ( 'no' === $this->display_desc ? '' : ': ' . $service_details['guiInformation']['descriptionText'] ),
				),
				$service_details
			);

			$rates[] = $rate;
		}

		return $rates;
	}

	/**
	 * Standard url params for the Bring HTTP request
	 *
	 * @param array $package Package.
	 * @return array
	 */
	public function create_standard_url_params( $package = null ) {
		if ( null === $package ) {
			$package = [
				'destination' => [
					'postcode' => $this->from_zip,
					'country'  => $this->get_selected_from_country(),
				],
			];
		}

		// WBF-106 Fixed shipping rates when sending to Svalbard and Jan Mayen.
		$country = $package['destination']['country'];
		if ( 'SJ' === $package['destination']['country'] ) {
			$country = 'NO';
		}

		// Remove spaces in post code.
		$postcode = preg_replace( '/\s/', '', $package['destination']['postcode'] );

		$enabled_services = Fraktguiden_Service::all( self::$field_key, true );

		$additional_service = false;
		foreach ( $enabled_services as $service ) {
			$additional_service = $service->vas_match( [ '2084', 'EVARSLING' ] );
			if ( $additional_service ) {
				break;
			}
		}

		return apply_filters(
			'bring_fraktguiden_standard_url_params',
			[
				'clientUrl'           => Fraktguiden_Helper::get_client_url(),
				'frompostalcode'      => $this->from_zip,
				'fromcountry'         => $this->get_selected_from_country(),
				'topostalcode'        => $postcode,
				'tocountry'           => $country,
				'postingatpostoffice' => ( 'no' === $this->post_office ) ? 'false' : 'true',
				'additionalservice'   => $additional_service ?? '',
				'language'            => $this->get_bring_language(),
			],
			$package
		);
	}

	/**
	 * Get Bring language
	 *
	 * @return string
	 */
	public function get_bring_language() {
		$language = substr( get_bloginfo( 'language' ), 0, 2 );

		$languages = [
			'dk' => 'da',
			'fi' => 'fi',
			'nb' => 'no',
			'nn' => 'no',
			'sv' => 'se',
		];

		return array_key_exists( $language, $languages ) ? $languages[ $language ] : 'en';
	}

	/**
	 * Get Trace Messages
	 *
	 * @return array
	 */
	public function get_trace_messages() {
		return $this->trace_messages;
	}

	/**
	 * Set Trace Messages
	 *
	 * @param array $messages Bring trace messages.
	 * @return array
	 */
	public function set_trace_messages( $messages ) {
		$this->trace_messages = [];
		$this->add_trace_messages( $messages );
		return $this;
	}

	/**
	 * Add Trace Messages
	 *
	 * @param array $messages Bring trace messages.
	 * @return void
	 */
	public function add_trace_messages( $messages ) {
		if ( isset( $messages['Message'] ) ) {
			$messages = $messages['Message'];
		}

		if ( ! is_array( $messages ) ) {
			$messages = [];
		}

		foreach ( $messages as &$message ) {
			if ( empty( $message['code'] ) ) {
				continue;
			}
			$description = '';
			if ( ! empty( $message['description'] ) ) {
				$description = $message['description'];
			} elseif ( ! empty( $message['message'] ) && ! empty( $message['field'] ) ) {
				$description = "<strong>{$message['field']}</strong> {$message['message']}";
			}

			$message = "{$message['code']}: {$description}";

		}
		$this->trace_messages = array_merge( $this->trace_messages, $messages );
	}

	/**
	 * Get selected 'From country' option
	 */
	public function get_selected_from_country() {
		global $woocommerce;

		return isset( $this->from_country ) ? $this->from_country : $woocommerce->countries->get_base_country();
	}

	/**
	 * Get enhanced shipping information template
	 *
	 * @param string $template      Template path.
	 * @param string $template_name Template name.
	 *
	 * @return string
	 */
	public function get_enhanced_shipping_information_template( $template, $template_name ) {
		if ( 'cart/cart-shipping.php' !== $template_name ) {
			return $template;
		}

		return dirname( __DIR__ ) . '/templates/woocommerce/cart-shipping.php';
	}
}
