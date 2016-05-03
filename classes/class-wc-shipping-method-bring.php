<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
 * Bring class for calculating and adding rates.
 *
 * License: See license.txt
 *
 * @category    Shipping Method
 * @author      Driv Digital
 * @package     Woocommerce
 */
class WC_Shipping_Method_Bring extends WC_Shipping_Method {

  const SERVICE_URL = 'https://api.bring.com/shippingguide/products/all.json';

  const TEXT_DOMAIN = 'bring-fraktguiden';

  const DEFAULT_MAX_PRODUCTS = 100;

  const DEFAULT_ALT_FLAT_RATE = 200;

  private $from_country = '';
  private $from_zip = '';
  private $post_office = '';
  private $vat = '';
  private $evarsling = '';
  private $services = array();
  private $service_name = '';
  private $display_desc = '';
  private $max_products = '';
  private $alt_flat_rate = '';

  /**
   * @constructor
   */
  public function __construct() {
    $this->id           = 'bring_fraktguiden';
    $this->method_title = __( 'Bring Fraktguiden', self::TEXT_DOMAIN );

    // Load the form fields.
    $this->init_form_fields();

    // Load the settings.
    $this->init_settings();

    // Debug configuration
    $this->debug = $this->settings['debug'];
    $this->log   = new WC_Logger();

    // Define user set variables

    // WC_Shipping_Method
    $this->enabled      = $this->settings['enabled'];
    $this->title        = $this->settings['title'];
    $this->availability = $this->settings['availability'];
    $this->countries    = $this->settings['countries'];
    $this->fee          = $this->settings['handling_fee'];

    // WC_Shipping_Method_Bring
    $this->from_country = array_key_exists( 'from_country', $this->settings ) ? $this->settings['from_country'] : '';
    $this->from_zip     = array_key_exists( 'from_zip', $this->settings ) ? $this->settings['from_zip'] : '';
    $this->post_office  = array_key_exists( 'post_office', $this->settings ) ? $this->settings['post_office'] : '';
    $this->vat          = array_key_exists( 'vat', $this->settings ) ? $this->settings['vat'] : '';
    $this->evarsling    = array_key_exists( 'evarsling', $this->settings ) ? $this->settings['evarsling'] : '';
    $this->services     = array_key_exists( 'services', $this->settings ) ? $this->settings['services'] : '';
    $this->service_name = array_key_exists( 'service_name', $this->settings ) ? $this->settings['service_name'] : '';
    $this->display_desc = array_key_exists( 'display_desc', $this->settings ) ? $this->settings['display_desc'] : '';
    $this->max_products = ! empty( $this->settings['max_products'] ) ? (int)$this->settings['max_products'] : self::DEFAULT_MAX_PRODUCTS;
    // Extra safety, in case shop owner blanks ('') the value.
    if ( ! empty( $this->settings['alt_flat_rate'] ) ) {
      $this->alt_flat_rate = (int)$this->settings['alt_flat_rate'];
    }
    elseif ( empty( $this->settings['alt_flat_rate'] ) ) {
      $this->alt_flat_rate = '';
    }
    else {
      $this->alt_flat_rate = self::DEFAULT_ALT_FLAT_RATE;
    }

    // The packer may make a lot of recursion when the cart contains many items.
    // Make sure xdebug max_nesting_level is raised.
    // See: http://stackoverflow.com/questions/4293775/increasing-nesting-functions-calls-limit
    ini_set( 'xdebug.max_nesting_level', 10000 );

    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );

    if ( ! $this->is_valid_for_use() ) {
      $this->enabled = false;
    }
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
   * Default settings.
   *
   * @return void
   */
  public function init_form_fields() {
    global $woocommerce;
    $services = array(
        'SERVICEPAKKE'               => 'Klimanøytral Servicepakke',
        'PA_DOREN'                   => 'På Døren',
        'BPAKKE_DOR-DOR'             => 'Bedriftspakke',
        'EKSPRESS09'                 => 'Bedriftspakke Ekspress-Over natten 09',
        'MINIPAKKE'                  => 'Minipakken',
        'A-POST'                     => 'A-Prioritert',
        'B-POST'                     => 'B-Økonomi',
        'SMAAPAKKER_A-POST'          => 'Småpakke A-Post',
        'SMAAPAKKER_B-POST'          => 'Småpakke B-Post',
        'EXPRESS_NORDIC_SAME_DAY'    => 'Express Nordic Same Day',
        'EXPRESS_INTERNATIONAL_0900' => 'Express International 09:00',
        'EXPRESS_INTERNATIONAL_1200' => 'Express International 12:00',
        'EXPRESS_INTERNATIONAL'      => 'Express International',
        'EXPRESS_ECONOMY'            => 'Express Economy',
        'CARGO_GROUPAGE'             => 'Cargo',
        'BUSINESS_PARCEL'            => 'Business Parcel',
        'PICKUP_PARCEL'              => 'PickUp Parcel',
        'COURIER_VIP'                => 'Bud VIP',
        'COURIER_1H'                 => 'Bud 1 time',
        'COURIER_2H'                 => 'Bud 2 timer',
        'COURIER_4H'                 => 'Bud 4 timer',
        'COURIER_6H'                 => 'Bud 6 timer',
        'OIL_EXPRESS'                => 'Oil Express',
    );

    $wc_log_dir = '';
    if ( defined( 'WC_LOG_DIR' ) ) {
      $wc_log_dir = WC_LOG_DIR;
    }

    $this->form_fields = array(
        'enabled'       => array(
            'title'   => __( 'Enable', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Bring Fraktguiden', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),
        'title'         => array(
            'title'       => __( 'Title', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'This controls the title which the user sees during checkout.', self::TEXT_DOMAIN ),
            'default'     => __( 'Bring Fraktguiden', self::TEXT_DOMAIN )
        ),
        'handling_fee'  => array(
            'title'       => __( 'Delivery Fee', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', self::TEXT_DOMAIN ),
            'default'     => ''
        ),
        'post_office'   => array(
            'title'       => __( 'Post office', self::TEXT_DOMAIN ),
            'type'        => 'checkbox',
            'label'       => __( 'Shipping from post office', self::TEXT_DOMAIN ),
            'description' => __( 'Flag that tells whether the parcel is delivered at a post office when it is shipped.', self::TEXT_DOMAIN ),
            'default'     => 'no'
        ),
        'from_zip'      => array(
            'title'       => __( 'From zip', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'This is the zip code of where you deliver from. For example, the post office. Should be 4 digits.', self::TEXT_DOMAIN ),
            'default'     => ''
        ),
        'from_country'  => array(
            'title'       => __( 'From country', self::TEXT_DOMAIN ),
            'type'        => 'select',
            'description' => __( 'This is the country of origin where you deliver from (If omitted WooCommerce\'s default location will be used. See WooCommerce - Settings - General)', self::TEXT_DOMAIN ),
            'class'       => 'chosen_select',
            'css'         => 'width: 450px;',
            'default'     => $this->get_selected_from_country(),
            'options'     => $this->get_nordic_countries()
        ),
        'vat'           => array(
            'title'       => __( 'Display price', self::TEXT_DOMAIN ),
            'type'        => 'select',
            'description' => __( 'How to calculate delivery charges', self::TEXT_DOMAIN ),
            'default'     => 'include',
            'options'     => array(
                'include' => __( 'VAT included', self::TEXT_DOMAIN ),
                'exclude' => __( 'VAT excluded', self::TEXT_DOMAIN )
            ),
        ),
        'evarsling'     => array(
            'title'       => __( 'Recipient notification', self::TEXT_DOMAIN ),
            'type'        => 'checkbox',
            'label'       => __( 'Recipient notification over SMS or E-Mail', self::TEXT_DOMAIN ),
            'description' => __( 'If not checked, Fraktguiden will add a fee for paper based recipient notification.<br/>If checked, the recipient will receive notification over SMS or E-mail when the parcel has arrived.<br/>Applies to Bedriftspakke, Kliman&oslash;ytral Servicepakke and Bedriftspakke Ekspress-Over natten 09', self::TEXT_DOMAIN ),
            'default'     => 'no'
        ),
        'availability'  => array(
            'title'   => __( 'Method availability', self::TEXT_DOMAIN ),
            'type'    => 'select',
            'default' => 'all',
            'class'   => 'availability',
            'options' => array(
                'all'      => __( 'All allowed countries', self::TEXT_DOMAIN ),
                'specific' => __( 'Specific Countries', self::TEXT_DOMAIN )
            )
        ),
        'countries'     => array(
            'title'   => __( 'Specific Countries', self::TEXT_DOMAIN ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $woocommerce->countries->countries
        ),
        'services'      => array(
            'title'   => __( 'Services', self::TEXT_DOMAIN ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $services
        ),
        'service_name'  => array(
            'title'       => __( 'Display Service As', self::TEXT_DOMAIN ),
            'type'        => 'select',
            'description' => __( 'The service name displayed to the customer', self::TEXT_DOMAIN ),
            'default'     => 'DisplayName',
            'options'     => array(
                'DisplayName' => __( 'Display Name', self::TEXT_DOMAIN ),
                'ProductName' => __( 'Product Name', self::TEXT_DOMAIN ),
            )
        ),
        'display_desc'  => array(
            'title'       => __( 'Display Description', self::TEXT_DOMAIN ),
            'type'        => 'checkbox',
            'label'       => __( 'Add description after the service', self::TEXT_DOMAIN ),
            'description' => __( 'Show service description after the name of the service', self::TEXT_DOMAIN ),
            'default'     => 'no'
        ),
        'max_products'  => array(
            'title'       => __( 'Max products', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'Maximum of products in the cart before offering a flat rate', self::TEXT_DOMAIN ),
            'default'     => self::DEFAULT_MAX_PRODUCTS
        ),
        'alt_flat_rate' => array(
            'title'       => __( 'Flat rate', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'Offer a flat rate if the cart reaches max products or a product in the cart does not have the required dimensions', self::TEXT_DOMAIN ),
            'default'     => self::DEFAULT_ALT_FLAT_RATE
        ),
        'debug'         => array(
            'title'       => __( 'Debug', self::TEXT_DOMAIN ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable debug logs', self::TEXT_DOMAIN ),
            'description' => __( 'These logs will be saved in', self::TEXT_DOMAIN ) . ' <code>' . $wc_log_dir . '</code>',
            'default'     => 'no'
        ),
    );
  }

  /**
   * Display settings in HTML.
   *
   * @return void
   */
  public function admin_options() {
    global $woocommerce; ?>

    <h3><?php echo $this->method_title; ?></h3>
    <p><?php _e( 'Bring Fraktguiden is a shipping method using Bring.com to calculate rates.', self::TEXT_DOMAIN ); ?></p>

    <table class="form-table">

      <?php if ( $this->is_valid_for_use() ) :
        $this->generate_settings_html();
      else : ?>
        <div class="inline error"><p>
            <strong><?php _e( 'Gateway Disabled', self::TEXT_DOMAIN ); ?></strong>
            <br/> <?php printf( __( 'Bring shipping method requires <strong>weight &amp; dimensions</strong> to be enabled. Please enable them on the <a href="%s">Catalog tab</a>. <br/> In addition, Bring also requires the <strong>Norweigian Krone</strong> currency. Choose that from the <a href="%s">General tab</a>', self::TEXT_DOMAIN ), 'admin.php?page=woocommerce_settings&tab=catalog', 'admin.php?page=woocommerce_settings&tab=general' ); ?>
          </p></div>
      <?php endif; ?>

    </table> <?php
  }

  /**
   * Calculate shipping costs.
   */
  public function calculate_shipping() {
    global $woocommerce;

    include_once( __DIR__ . '/class-packer.php' );
    $packer = new Fraktguiden_Packer();

    // Offer flat rate if the cart contents exceeds max product.
    if ( $woocommerce->cart->get_cart_contents_count() > $this->max_products ) {
      if ( $this->alt_flat_rate == '' ) {
        return;
      }
      $rate = array(
          'id'    => $this->id . ':' . 'alt_flat_rate',
          'cost'  => $this->alt_flat_rate,
          'label' => $this->method_title . ' flat rate',
      );
      $this->add_rate( $rate );
    }
    else {
      // Create an array of 'product boxes' (l,w,h,weight).
      $product_boxes = array();
      foreach ( $woocommerce->cart->get_cart() as $values ) {
        $product = $values['data'];
        if ( ! $product->needs_shipping() ) {
          continue;
        }

        $quantity = $values['quantity'];
        for ( $i = 0; $i < $quantity; $i++ ) {
          if ( ! $product->has_dimensions() ) {
            // If the product has no dimensions, assume the lowest unit 1x1x1 cm
            $dims = array( 0, 0, 0 );
          }
          else {
            $dims = array(
                $product->length,
                $product->width,
                $product->height
            );
          }

          // Workaround weird LAFFPack issue where the dimensions are expected in reverse order.
          rsort( $dims );

          $box = array(
              'length'          => $dims[0],
              'width'           => $dims[1],
              'height'          => $dims[2],
              'weight'          => $product->weight,
              'weight_in_grams' => $packer->get_weight( $product->weight ) // For $packer->exceeds_max_package_values only.
          );

          // Return if product is larger than available Bring packages.
          if ( $packer->exceeds_max_package_values( $box ) ) {
            return;
          }

          $product_boxes[] = $box;
        }
      }

      // Pack product boxes.

      $packer->pack( $product_boxes, true );

      // Create the url.

      // Request parameters.
      $params = array_merge( $this->create_standard_url_params(), $packer->create_coli_params() );
      // Remove any empty elements.
      $params = array_filter( $params );
      // Create url.

      $url = add_query_arg( $params, self::SERVICE_URL );

      // Add all the selected products to the URL
      if ( $this->services && count( $this->services ) > 0 ) {
        foreach ( $this->services as $product ) {
          $url .= '&product=' . $product;
        }
      }

      // Make the request.
      $response = wp_remote_get( $url );
      // If the request fails, just return.
      if ( is_wp_error( $response ) ) {
        return;
      }

      // Decode the JSON data from bring.
      $json = json_decode( $response['body'], true );
      // Filter the response json to get only the selected services from the settings.
      $rates = $this->get_services_from_response( $json );

      if ( $this->debug != 'no' ) {
        $this->log->add( $this->id, 'params: ' . print_r( $params, true ) );

        if ( $rates ) {
          $this->log->add( $this->id, 'Rates found: ' . print_r( $rates, true ) );
        }
        else {
          $this->log->add( $this->id, 'No rates found for params: ' . print_r( $params, true ) );
        }

        $this->log->add( $this->id, 'Request url: ' . print_r( $url, true ) );
      }

      // Calculate rate.
      if ( $rates ) {
        foreach ( $rates as $rate ) {
          $this->add_rate( $rate );
        }
      }
    }
  }

  /**
   * @param array $response The JSON response from Bring.
   * @return array|boolean
   */
  private function get_services_from_response( $response ) {
    if ( ! $response || ( is_array( $response ) && count( $response ) == 0 ) || empty( $response['Product'] ) ) {
      return false;
    }

    $rates = array();

    // Fix for when only one service is found. It's not returned in an array :/
    if ( empty( $response['Product'][0] ) ) {
      $cache = $response['Product'];
      unset( $response['Product'] );
      $response['Product'][] = $cache;
    }

    foreach ( $response['Product'] as $serviceDetails ) {
      if ( ! empty( $this->services ) && ! in_array( $serviceDetails['ProductId'], $this->services ) ) {
        continue;
      }

      $service = $serviceDetails['Price']['PackagePriceWithoutAdditionalServices'];
      $rate    = $this->vat == 'exclude' ? $service['AmountWithoutVAT'] : $service['AmountWithVAT'];

      $rate = array(
          'id'    => $this->id . ':' . sanitize_title( $serviceDetails['ProductId'] ),
          'cost'  => (float)$rate + (float)$this->fee,
          'label' => $serviceDetails['GuiInformation'][$this->service_name] . ( $this->display_desc == 'no' ? '' : ': ' . $serviceDetails['GuiInformation']['DescriptionText'] ),
      );

      array_push( $rates, $rate );
    }
    return $rates;
  }

  /**
   * Standard url params for the Bring http request.
   *
   * @return array
   */
  public function create_standard_url_params() {
    global $woocommerce;
    return apply_filters( 'bring_fraktguiden_standard_url_params', array(
        'clientUrl'           => $_SERVER['HTTP_HOST'],
        'from'                => $this->from_zip,
        'fromCountry'         => $this->get_selected_from_country(),
        'to'                  => $woocommerce->customer->get_shipping_postcode(),
        'toCountry'           => $woocommerce->customer->get_shipping_country(),
        'postingAtPostOffice' => ( $this->post_office == 'no' ) ? 'false' : 'true',
        'additional'          => ( $this->evarsling == 'yes' ) ? 'evarsling' : '',
    ) );
  }


  public function get_selected_from_country() {
    global $woocommerce;
    return isset( $this->from_country ) ? $this->from_country : $woocommerce->countries->get_base_country();
  }

  /**
   * Returns an array with nordic country codes
   *
   * @return array
   */
  public function get_nordic_countries() {
    global $woocommerce;
    $countries = array( 'NO', 'SE', 'DK', 'FI', 'IS' );
    return $this->array_filter_key( $woocommerce->countries->countries, function ( $k ) use ( $countries ) {
      return in_array( $k, $countries );
    } );
  }

  /**
   * Returns an array based on the filter in the callback function.
   * Same as PHP's array_filter but uses the key instead of value.
   *
   * @param array $array
   * @param callable $callback
   * @return array
   */
  private function array_filter_key( $array, $callback ) {
    $matched_keys = array_filter( array_keys( $array ), $callback );
    return array_intersect_key( $array, array_flip( $matched_keys ) );
  }

}
