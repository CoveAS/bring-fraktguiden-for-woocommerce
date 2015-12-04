<?php

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
    $this->enabled      = $this->settings['enabled'];
    $this->title        = $this->settings['title'];
    $this->availability = $this->settings['availability'];
    $this->countries    = $this->settings['countries'];
    $this->fee          = $this->settings['handling_fee'];
    $this->from_zip     = $this->settings['from_zip'];
    $this->post_office  = $this->settings['post_office'];
    $this->vat          = $this->settings['vat'];
    $this->services     = $this->settings['services'];
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
   * @return boolean
   */
  public function is_valid_for_use() {
    $dimensions_unit = get_option( 'woocommerce_dimension_unit' );
    $weight_unit     = get_option( 'woocommerce_weight_unit' );
    $currency        = get_option( 'woocommerce_currency' );
    return $weight_unit && $dimensions_unit && $currency == 'NOK';
  }

  /**
   * Default settings.
   *
   * @return void
   */
  public function init_form_fields() {
    global $woocommerce;
    $services          = array(
        'SERVICEPAKKE'                 => 'Klimanøytral Servicepakke',
        'PA_DOREN'                     => 'På Døren',
        'BPAKKE_DOR-DOR'               => 'Bedriftspakke',
        'EKSPRESS09'                   => 'Bedriftspakke Ekspress-Over natten 09',
        'MINIPAKKE'                    => 'Minipakken',
        'A-POST'                       => 'A-Prioritert 1',
        'B-POST'                       => 'B-Økonomi 2',
        'QUICKPACK_SAMEDAY'            => 'QuickPack SameDay 3',
        'QUICKPACK_OVER_NIGHT_0900'    => 'Quickpack Over Night 0900',
        'QUICKPACK_OVER_NIGHT_1200'    => 'Quickpack Over Night 1200',
        'QUICKPACK_DAY_CERTAIN'        => 'Quickpack Day Certain',
        'QUICKPACK_EXPRESS_ECONOMY'    => 'Quickpack Express Economy',
        'CARGO_GROUPAGE'               => 'Cargo',
        'CARRYON BUSINESS NORWAY'      => 'CarryOn Business Norway',
        'CARRYON BUSINESS SWEDEN'      => 'CarryOn Business Sweden',
        'CARRYON BUSINESS DENMARK'     => 'CarryOn Business Denmark',
        'CARRYON BUSINESS FINLAND'     => 'CarryOn Business Finland',
        'CARRYON HOMESHOPPING NORWAY'  => 'CarryOn Homeshopping Norway',
        'CARRYON HOMESHOPPING SWEDEN'  => 'CarryOn Homeshopping Sweden',
        'CARRYON HOMESHOPPING DENMARK' => 'CarryOn Homeshopping Denmark',
        'CARRYON HOMESHOPPING FINLAND' => 'CarryOn Homeshopping Finland',
        'HOMEDELIVERY_CURBSIDE_DAG'    => 'HomeDelivery CurbSide',
        'COURIER_VIP'                  => 'Bud VIP',
        'COURIER_1H'                   => 'Bud 1 time',
        'COURIER_2H'                   => 'Bud 2 timer',
        'COURIER_4H'                   => 'Bud 4 timer',
        'COURIER_6H'                   => 'Bud 6 timer',
    );
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
            'title'   => __( 'Post office', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Shipping from post office', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),
        'from_zip'      => array(
            'title'       => __( 'From zip', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'This is the zip code of where you deliver from. For example, the post office. Should be 4 digits.', self::TEXT_DOMAIN ),
            'default'     => ''
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
            'description' => __( 'These logs will be saved in <code>wc-logs/</code>', self::TEXT_DOMAIN ),
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
      $query = add_query_arg( $params, self::SERVICE_URL );
      // Make the request.
      $response = wp_remote_get( $query );
      // If the request fails, just return.
      if ( is_wp_error( $response ) ) {
        return;
      }

      // Decode the JSON data from bring.
      $json = json_decode( $response['body'], true );
      // Filter the response json to get only the selected services from the settings.
      $rates = $this->get_services_from_response( $json );
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
          'cost'  => round( $rate ) + (int)$this->fee,
          'label' => $serviceDetails['GuiInformation']['DisplayName'],
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
    return array(
        'clientUrl'           => $_SERVER['HTTP_HOST'],
        'from'                => $this->from_zip,
        'to'                  => $woocommerce->customer->get_shipping_postcode(),
        'toCountry'           => $woocommerce->customer->get_shipping_country(),
        'postingAtPostOffice' => ( $this->post_office == 'no' ) ? 'false' : 'true',
    );
  }

}
