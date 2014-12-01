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
class WC_Bring_Fraktguiden extends WC_Shipping_Method {

  /**
   *
   */
  const SERVICE_URL = 'http://fraktguide.bring.no/fraktguide/products/all.json';

  /**
   * @constructor
   */
  public function __construct() {
    global $woocommerce;

    $this->id           = 'bring_fraktguiden';
    $this->method_title = __( 'Bring Fraktguiden', 'bring-fraktguiden' );

    // Load the form fields.
    $this->init_form_fields();

    // Load the settings.
    $this->init_settings();

    // Debug configuration
    $this->debug       = $this->settings['debug'];
    $this->log         = new WC_Logger();
    $this->weight_unit = get_option( 'woocommerce_weight_unit' );
    $this->dimens_unit = get_option( 'woocommerce_dimension_unit' );

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

    if ( !$this->is_valid_for_use() ) {
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
    $services = array(
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
            'title'   => __( 'Enable', 'bring-fraktguiden' ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Bring Fraktguiden', 'bring-fraktguiden' ),
            'default' => 'no'
        ),
        'title'        => array(
            'title'       => __( 'Title', 'bring-fraktguiden' ),
            'type'        => 'text',
            'description' => __( 'This controls the title which the user sees during checkout.', 'bring-fraktguiden' ),
            'default'     => __( 'Bring Fraktguiden', 'bring-fraktguiden' )
        ),
        'handling_fee' => array(
            'title'       => __( 'Delivery Fee', 'bring-fraktguiden' ),
            'type'        => 'text',
            'description' => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', 'bring-fraktguiden' ),
            'default'     => ''
        ),
        'post_office'  => array(
            'title'   => __( 'Post office', 'bring-fraktguiden' ),
            'type'    => 'checkbox',
            'label'   => __( 'Shipping from post office', 'bring-fraktguiden' ),
            'default' => 'no'
        ),
        'from_zip'     => array(
            'title'       => __( 'From zip', 'bring-fraktguiden' ),
            'type'        => 'text',
            'description' => __( 'This is the zip code of where you deliver from. For example, the post office. Should be 4 digits.', 'bring-fraktguiden' ),
            'default'     => ''
        ),
        'vat'          => array(
            'title'       => __( 'Display price', 'bring-fraktguiden' ),
            'type'        => 'select',
            'description' => __( 'How to calculate delivery charges', 'bring-fraktguiden' ),
            'default'     => 'include',
            'options'     => array(
                'include' => __( 'VAT included', 'bring-fraktguiden' ),
                'exclude' => __( 'VAT excluded', 'bring-fraktguiden' )
            ),
        ),
        'availability' => array(
            'title'   => __( 'Method availability', 'bring-fraktguiden' ),
            'type'    => 'select',
            'default' => 'all',
            'class'   => 'availability',
            'options' => array(
                'all'      => __( 'All allowed countries', 'bring-fraktguiden' ),
                'specific' => __( 'Specific Countries', 'bring-fraktguiden' )
            )
        ),
        'countries'    => array(
            'title'   => __( 'Specific Countries', 'bring-fraktguiden' ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $woocommerce->countries->countries
        ),
        'services'     => array(
            'title'   => __( 'Services', 'bring-fraktguiden' ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $services
        ),
        'debug'        => array(
            'title'       => __( 'Debug', 'bring-fraktguiden' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable debug logs', 'bring-fraktguiden' ),
            'description' => __( 'These logs will be saved in <code>wc-logs/</code>', 'bring-fraktguiden' ),
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
    <p><?php _e( 'Bring Fraktguiden is a shipping method using Bring.com to calculate rates.', 'bring-fraktguiden' ); ?></p>

    <table class="form-table">

      <?php if ( $this->is_valid_for_use() ) :
        $this->generate_settings_html();
      else : ?>
        <div class="inline error"><p>
            <strong><?php _e( 'Gateway Disabled', 'bring-fraktguiden' ); ?></strong>
            <br/> <?php printf( __( 'Bring shipping method requires <strong>weight &amp; dimensions</strong> to be enabled. Please enable them on the <a href="%s">Catalog tab</a>. <br/> In addition, Bring also requires the <strong>Norweigian Krone</strong> currency. Choose that from the <a href="%s">General tab</a>', 'bring-fraktguiden' ), 'admin.php?page=woocommerce_settings&tab=catalog', 'admin.php?page=woocommerce_settings&tab=general' ); ?>
          </p></div>
      <?php endif; ?>

    </table> <?php
  }

  /**
   * Calculate shipping costs.
   *
   * @return mixed Value.
   */
  public function calculate_shipping() {
    global $woocommerce;
    $titles            = array();
    $shipping_required = false;
    $weight            = $length = $width = $height = 0;
    foreach ( $woocommerce->cart->get_cart() as $values ) {
      $_product = $values['data'];

      // Check if the product has shipping enabled.
      if ( !$_product->needs_shipping() ) {
        continue;
      }

      // Does the product have dimensions?
      if ( $_product->has_dimensions() ) {

        $shipping_required = true;

        // Weight of current product.
        $weight += $_product->weight * $values['quantity'];

        $length += $_product->length * $values['quantity'];
        $width += $_product->width * $values['quantity'];
        $height += $_product->height * $values['quantity'];

        if ( $this->debug != 'no' ) {
          $titles[] = $_product->get_title();
        }

      } else {

        if ( $this->debug != 'no' ) {
          $this->log->add( $this->id, __( 'Cannot calculate Bring shiping as a product added is missing dimensions. Product: ' . $_product->get_title(), 'bring-fraktguiden' ) );
        }
        return false;
      }
    }

    // No products are shippable, or no products have dimensions.
    if ( empty( $length ) ) {

      if ( $shipping_required ) {
        if ( $this->debug != 'no' ) {
          $this->log->add( $this->id, __( 'The products that were added to the cart do not have dimensions and therefore Bring cannot calculate shipping. Products: ' . print_r( $titles, true ), 'bring-fraktguiden' ) );
        }
      }
      return false;
    }

    $params = array(
        'from'                => $this->from_zip,
        'to'                  => $woocommerce->customer->get_shipping_postcode(),
        'toCountry'           => $woocommerce->customer->get_shipping_country(),
        'length'              => $this->get_dimension( $length ),
        'width'               => $this->get_dimension( $width ),
        'height'              => $this->get_dimension( $height ),
        'weightInGrams'       => $this->get_weight( $weight ),
        'priceAdjustment'     => 'PA_DOREN_89',
        'postingAtPostOffice' => ( $this->post_office == 'no' ) ? 'false' : 'true',
    );

    $params   = array_filter( $params );
    $query    = add_query_arg( $params, self::SERVICE_URL );
    $response = wp_remote_get( $query );
    $decoded  = $this->convert_xml_to_array( $response['body'] );
    $rates    = $this->get_services_from_response( $decoded );

    if ( $this->debug != 'no' ) {
      if ( $rates ) {
        $this->log->add( $this->id, __( 'Rates were found: ' . print_r( $rates, true ), 'bring-fraktguiden' ) );
      } else {
        $this->log->add( $this->id, __( 'No rates were found for params: ' . print_r( $params, true ), 'bring-fraktguiden' ) );
      }
      $this->log->add( $this->id, __( 'Request url: ' . print_r( $query, true ), 'bring-fraktguiden' ) );
    }

    if ( $rates ) {
      foreach ( $rates as $rate ) {
        $this->add_rate( $rate );
      }
    }
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
        if ( $this->debug != 'no' ) {
          $this->log->add( $this->id, __( sprintf( 'Could not calculate weight unit for %s', $this->weight_unit ), 'bring-fraktguiden' ) );
        }
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

    switch ( $this->dimens_unit ) {

      case 'cm' :
        return $dimension;

      case 'in' :
        return $dimension / 0.39370;

      case 'yd' :
        return $dimension / 0.010936;

      case 'mm' :
        return $dimension / 10.000;

      case 'm' :
        return $dimension / 0.010000;

      /* Unknown dimension unit */
      default :
        if ( $this->debug != 'no' ) {
          $this->log->add( $this->id, __( sprintf( 'Could not calculate dimension unit for %s', $this->dimens_unit ), 'bring-fraktguiden' ) );
        }
        return false;

    }
  }

  /**
   * Convert the Bring XML response to an array.
   *
   * @param string $response
   * @return array
   */
  private function convert_xml_to_array( $response ) {
    return json_decode( $response, true );
  }

  /**
   *
   *
   * @param array $response .
   * @return array|boolean
   *
   * Fixme: always return array.
   */
  private function get_services_from_response( $response ) {

    if ( !$response || ( is_array( $response ) && count( $response ) == 0 ) || empty( $response['Product'] ) ) {
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
      if ( !empty( $this->services ) && !in_array( $serviceDetails['ProductId'], $this->services ) ) {
        continue;
      }

      $service = $serviceDetails['Price']['PackagePriceWithoutAdditionalServices'];
      $rate    = $this->vat == 'exclude' ? $service['AmountWithoutVAT'] : $service['AmountWithVAT'];
      if ( 'PA_DOREN' == $serviceDetails['ProductId'] && 89 == $service['AmountWithoutVAT'] ) {
        $rate = $service['AmountWithoutVAT'] / 1.25;
      }

      $rate = array(
          'id'    => $this->id . ':' . sanitize_title( $serviceDetails['ProductId'] ),
          'cost'  => $rate,
          'label' => $serviceDetails['GuiInformation']['DisplayName'],
      );

      array_push( $rates, $rate );
    }
    return $rates;
  }

}

/**
 * add_bring_method function.
 *
 * @package  WooCommerce/Classes/Shipping
 * @access public
 * @param array $methods
 * @return array
 */
function add_bring_method( $methods ) {
  $methods[] = 'WC_Bring_Fraktguiden';
  return $methods;
}

// Add the method to WooCommerce.
add_filter( 'woocommerce_shipping_methods', 'add_bring_method' );
