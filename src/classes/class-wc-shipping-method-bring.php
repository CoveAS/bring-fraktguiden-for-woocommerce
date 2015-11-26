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

    $this->dim_unit    = get_option( 'woocommerce_dimension_unit' );
    $this->weight_unit = get_option( 'woocommerce_weight_unit' );

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

    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );

    if ( ! $this->is_valid_for_use() ) {
      $this->enabled = false;
    }
  }

  /**
   * Check if weight or dimensions are enabled.
   *
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
        'enabled'      => array(
            'title'   => __( 'Enable', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Bring Fraktguiden', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),
        'title'        => array(
            'title'       => __( 'Title', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'This controls the title which the user sees during checkout.', self::TEXT_DOMAIN ),
            'default'     => __( 'Bring Fraktguiden', self::TEXT_DOMAIN )
        ),
        'handling_fee' => array(
            'title'       => __( 'Delivery Fee', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', self::TEXT_DOMAIN ),
            'default'     => ''
        ),
        'post_office'  => array(
            'title'   => __( 'Post office', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Shipping from post office', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),
        'from_zip'     => array(
            'title'       => __( 'From zip', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'description' => __( 'This is the zip code of where you deliver from. For example, the post office. Should be 4 digits.', self::TEXT_DOMAIN ),
            'default'     => ''
        ),
        'vat'          => array(
            'title'       => __( 'Display price', self::TEXT_DOMAIN ),
            'type'        => 'select',
            'description' => __( 'How to calculate delivery charges', self::TEXT_DOMAIN ),
            'default'     => 'include',
            'options'     => array(
                'include' => __( 'VAT included', self::TEXT_DOMAIN ),
                'exclude' => __( 'VAT excluded', self::TEXT_DOMAIN )
            ),
        ),
        'availability' => array(
            'title'   => __( 'Method availability', self::TEXT_DOMAIN ),
            'type'    => 'select',
            'default' => 'all',
            'class'   => 'availability',
            'options' => array(
                'all'      => __( 'All allowed countries', self::TEXT_DOMAIN ),
                'specific' => __( 'Specific Countries', self::TEXT_DOMAIN )
            )
        ),
        'countries'    => array(
            'title'   => __( 'Specific Countries', self::TEXT_DOMAIN ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $woocommerce->countries->countries
        ),
        'services'     => array(
            'title'   => __( 'Services', self::TEXT_DOMAIN ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $services
        ),
        'debug'        => array(
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
   * Called by WooCommerce.
   */
  public function calculate_shipping() {

    // Request params.
    $params = array_merge( $this->create_standard_params(), $this->create_dimension_params() );
    // Remove empty parameters.
    $params = array_filter( $params );
    // Run the query.
    $query = add_query_arg( $params, self::SERVICE_URL );

    // Get the response.
    $response = wp_remote_get( $query );
    if ( is_wp_error( $response ) ) {
      return;
    }
    // Decode the JSON data from bring.
    $decoded = json_decode( $response['body'], true );
    // Filter the data to get the selected services from the admin settings.
    $rates = $this->get_services_from_response( $decoded );
    // Calculate rate.
    if ( $rates ) {
      foreach ( $rates as $rate ) {
        $this->add_rate( $rate );
      }
    }
    return;
  }

  /**
   * @param array $response .
   * @return array|boolean
   *
   * Fixme: always return array.
   */
  private function get_services_from_response( $response ) {
    if ( ! $response || ( is_array( $response ) && count( $response ) == 0 ) || empty( $response['Product'] ) ) {
      return false;
    }

    $rates = array();

    // Fix for when only one product exists. It's not returned in an array :/
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
          'cost'  => round( $rate ),
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
  public function create_standard_params() {
    global $woocommerce;
    return array(
        'clientUrl'           => $_SERVER['HTTP_HOST'],
        'from'                => $this->from_zip,
        'to'                  => $woocommerce->customer->get_shipping_postcode(),
        'toCountry'           => $woocommerce->customer->get_shipping_country(),
        'postingAtPostOffice' => ( $this->post_office == 'no' ) ? 'false' : 'true',
    );
  }

  public function create_dimension_params() {
    global $woocommerce;

    $result    = array();
    $param_num = 0;

    foreach ( $woocommerce->cart->get_cart() as $values ) {
      $simple_prod = $values['data'];
      if ( ! $simple_prod->needs_shipping() ) {
        continue;
      }

      for ( $i = 0; $i < $values['quantity']; $i++ ) {
        if ( ! $simple_prod->has_dimensions() ) {
          // If the product has no dimensions, assume the lowest unit 1x1x1 cm
          $dims = array( 0, 0, 0 );
        }
        else {
          // Use defined product dimensions
          $dims = array(
              $simple_prod->length,
              $simple_prod->width,
              $simple_prod->height
          );
        }

        $result['length' . $param_num]        = $this->get_dimension( $dims[0] );
        $result['width' . $param_num]         = $this->get_dimension( $dims[1] );
        $result['height' . $param_num]        = $this->get_dimension( $dims[2] );
        $result['weightInGrams' . $param_num] = $this->get_weight( $simple_prod->weight );

        $param_num++;
      }
    }
    return $result;
  }

  /**
   * Return weight in grams.
   *
   * @param float $weight
   * @return float
   */
  public function get_weight( $weight ) {
    switch ( $this->weight_unit ) {

      case 'g' :
        return $weight;

      case 'kg' :
        return $weight / 0.0010000;

      case 'lbs' :
        return $weight / 0.0022046;

      case 'oz' :
        return $weight / 0.035274;

      /* Unknown weight unit */
      default :
        return false;
    }
  }

  /**
   * Return dimension in centimeters.
   *
   * @param float $dimension
   * @return float
   */
  public function get_dimension( $dimension ) {
    switch ( $this->dim_unit ) {
      case 'mm' :
        $dimension = $dimension / 10.000;
        break;
      case 'in' :
        $dimension = $dimension / 0.39370;
        break;
      case 'yd' :
        $dimension = $dimension / 0.010936;
        break;
      case 'cm' :
        $dimension = $dimension;
        break;
      case 'm' :
        $dimension = $dimension / 0.010000;
        break;
      /* Unknown dimension unit */
      default :
        return false;
    }

    if ( 1 > $dimension ) {
      // Minimum 1 cm
      $dimension = 1;
    }

    return $dimension;
  }

  /**
   * Return volume in dm.
   *
   * @param $dimension
   * @return float
   */
  public function get_volume( $dimension ) {
    switch ( $this->dim_unit ) {
      case 'mm' :
        return $dimension / 100;

      case 'in' :
        return $dimension * 0.254;

      case 'yd' :
        return $dimension * 9.144;

      case 'cm' :
        return $dimension / 1000;

      case 'm' :
        return $dimension / 10;
      /* Unknown dimension unit */
      default :
        return false;
    }
  }
}

