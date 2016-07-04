<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Bring_Request' ) ) {
  include_once 'common/http/class-wp-bring-request.php';
}
if ( ! class_exists( 'Fraktguiden_Helper' ) ) {
  include_once 'common/class-fraktguiden-helper.php';
}

if ( ! class_exists( 'Fraktguiden_Packer' ) ) {
  include_once( 'common/class-fraktguiden-packer.php' );
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

  const ID = Fraktguiden_Helper::ID;

  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

  const DEFAULT_MAX_PRODUCTS = 100;

  const DEFAULT_ALT_FLAT_RATE = 200;

  private $from_country = '';
  private $from_zip = '';
  private $post_office = '';
  private $vat = '';
  private $evarsling = '';
  private $services = array();
  private $services2 = array();
  private $service_name = '';
  private $display_desc = '';
  private $max_products = '';
  private $alt_flat_rate = '';

  private $debug = '';

  /** @var WC_Logger */
  private $log;

  /** @var array */
  protected $packages_params = [ ];

  /**
   * @constructor
   */
  public function __construct() {
    $this->id           = self::ID;
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
    $this->services2    = array_key_exists( 'services2', $this->settings ) ? $this->settings['services2'] : [ ];
    $this->service_name = array_key_exists( 'service_name', $this->settings ) ? $this->settings['service_name'] : 'DisplayName';
    $this->display_desc = array_key_exists( 'display_desc', $this->settings ) ? $this->settings['display_desc'] : 'no';
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
    $services = Fraktguiden_Helper::get_all_services();

    // @todo
    $wc_log_dir = '';
    if ( defined( 'WC_LOG_DIR' ) ) {
      $wc_log_dir = WC_LOG_DIR;
    }

    $this->form_fields = [
        'general_options_title' => [
            'type'  => 'title',
            'title' => __( 'Shipping Options', self::TEXT_DOMAIN ),
        ],
        'enabled'               => array(
            'title'   => __( 'Enable', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Bring Fraktguiden', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),
        'title'                 => array(
            'title'    => __( 'Title', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'desc_tip' => __( 'This controls the title which the user sees during checkout.', self::TEXT_DOMAIN ),
            'default'  => __( 'Bring Fraktguiden', self::TEXT_DOMAIN )
        ),
        'handling_fee'          => array(
            'title'    => __( 'Delivery Fee', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'desc_tip' => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', self::TEXT_DOMAIN ),
            'default'  => ''
        ),
        'post_office'           => array(
            'title'    => __( 'Post office', self::TEXT_DOMAIN ),
            'type'     => 'checkbox',
            'label'    => __( 'Shipping from post office', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'Flag that tells whether the parcel is delivered at a post office when it is shipped.', self::TEXT_DOMAIN ),
            'default'  => 'no'
        ),
        'from_zip'              => array(
            'title'    => __( 'From zip', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'desc_tip' => __( 'This is the zip code of where you deliver from. For example, the post office.', self::TEXT_DOMAIN ),
            'default'  => ''
        ),
        'from_country'          => array(
            'title'    => __( 'From country', self::TEXT_DOMAIN ),
            'type'     => 'select',
            'desc_tip' => __( 'This is the country of origin where you deliver from (If omitted WooCommerce\'s default location will be used. See WooCommerce - Settings - General)', self::TEXT_DOMAIN ),
            'class'    => 'chosen_select',
            'css'      => 'width: 450px;',
            'default'  => $woocommerce->countries->get_base_country(),
            'options'  => Fraktguiden_Helper::get_nordic_countries()
        ),
        'vat'                   => array(
            'title'    => __( 'Display price', self::TEXT_DOMAIN ),
            'type'     => 'select',
            'desc_tip' => __( 'How to calculate delivery charges', self::TEXT_DOMAIN ),
            'default'  => 'include',
            'options'  => array(
                'include' => __( 'VAT included', self::TEXT_DOMAIN ),
                'exclude' => __( 'VAT excluded', self::TEXT_DOMAIN )
            ),
        ),
        'evarsling'             => array(
            'title'    => __( 'Recipient notification', self::TEXT_DOMAIN ),
            'type'     => 'checkbox',
            'label'    => __( 'Recipient notification over SMS or E-Mail', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'If not checked, Fraktguiden will add a fee for paper based recipient notification.<br/>If checked, the recipient will receive notification over SMS or E-mail when the parcel has arrived.<br/>Applies to Bedriftspakke, Kliman&oslash;ytral Servicepakke and Bedriftspakke Ekspress-Over natten 09', self::TEXT_DOMAIN ),
            'default'  => 'no'
        ),
        'availability'          => array(
            'title'   => __( 'Method availability', self::TEXT_DOMAIN ),
            'type'    => 'select',
            'default' => 'all',
            'class'   => 'availability',
            'options' => array(
                'all'      => __( 'All allowed countries', self::TEXT_DOMAIN ),
                'specific' => __( 'Specific Countries', self::TEXT_DOMAIN )
            )
        ),
        'countries'             => array(
            'title'   => __( 'Specific Countries', self::TEXT_DOMAIN ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $woocommerce->countries->countries
        ),
        'services'              => array(
            'title'   => __( 'Services', self::TEXT_DOMAIN ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $services
        ),

        'service_name' => array(
            'title'    => __( 'Display Service As', self::TEXT_DOMAIN ),
            'type'     => 'select',
            'desc_tip' => __( 'The service name displayed to the customer', self::TEXT_DOMAIN ),
            'default'  => 'DisplayName',
            'options'  => array(
                'DisplayName' => __( 'Display Name', self::TEXT_DOMAIN ),
                'ProductName' => __( 'Product Name', self::TEXT_DOMAIN ),
            )
        ),

//        'services2' => array(
//            'type' => 'services_table'
//        ),

        'display_desc'  => array(
            'title'    => __( 'Display Description', self::TEXT_DOMAIN ),
            'type'     => 'checkbox',
            'label'    => __( 'Add description after the service', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'Show service description after the name of the service', self::TEXT_DOMAIN ),
            'default'  => 'no'
        ),
        'max_products'  => array(
            'title'    => __( 'Max products', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'desc_tip' => __( 'Maximum of products in the cart before offering a flat rate', self::TEXT_DOMAIN ),
            'default'  => self::DEFAULT_MAX_PRODUCTS
        ),
        'alt_flat_rate' => array(
            'title'    => __( 'Flat rate', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'desc_tip' => __( 'Offer a flat rate if the cart reaches max products or a product in the cart does not have the required dimensions', self::TEXT_DOMAIN ),
            'default'  => self::DEFAULT_ALT_FLAT_RATE
        ),
        'debug'         => array(
            'title'       => __( 'Debug', self::TEXT_DOMAIN ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable debug logs', self::TEXT_DOMAIN ),
            'description' => __( 'These logs will be saved in', self::TEXT_DOMAIN ) . ' <code>' . $wc_log_dir . '</code>',
            'default'     => 'no'
        )
    ];

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
    <p>
      <a href="<?php echo admin_url(); ?>admin-ajax.php?action=bring_system_info"
         target="_blank"><?php echo __( 'View system info', self::TEXT_DOMAIN ) ?></a>
    </p>

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

  public function validate_services_table_field( $key, $value ) {
    return isset( $value ) ? $value : array();
  }

  public function process_admin_options() {
    parent::process_admin_options();

    // Process services table
    $services_field               = $this->get_field_key( 'services2' );
    $services_custom_prices_field = $services_field . '_custom_prices';
    $custom_prices                = [ ];
    if ( isset( $_POST[$services_field] ) ) {
      $checked_services = $_POST[$services_field];
      foreach ( $checked_services as $key => $service ) {

        if ( isset( $_POST[$services_custom_prices_field][$service] ) ) {
          $custom_prices[$service] = $_POST[$services_custom_prices_field][$service];
        }
      }
    }

    update_option( $services_custom_prices_field, $custom_prices );
  }


  public function generate_services_table_html() {
    $services      = Fraktguiden_Helper::get_services_data();
    $selected      = $this->services2;
    $field_key     = $this->get_field_key( 'services2' );
    $custom_prices = get_option( $field_key . '_custom_prices' );

    ob_start();
    ?>

    <tr valign="top">
      <th scope="row" class="titledesc">
        <label
            for="<?php echo $field_key ?>"><?php _e( 'Services 2', self::TEXT_DOMAIN ); ?></label>
      </th>
      <td class="forminp">
        <table class="wc_shipping widefat fraktguiden-services-table">
          <thead>
          <tr>
            <th class="fraktguiden-services-table-col-enabled">Enabled</th>
            <th class="fraktguiden-services-table-col-service">Service</th>
            <th class="fraktguiden-services-table-col-custom-price">Egendefinert pris</th>
          </tr>
          </thead>
          <tbody>

          <?php
          foreach ( $services as $key => $service ) {
            $id               = $field_key . '_' . $key;
            $prices_field_key = $field_key . '_custom_prices[' . $key . ']';
            $custom_price     = isset( $custom_prices[$key] ) ? $custom_prices[$key] : '';
            $checked          = in_array( $key, $selected );
            ?>
            <tr>
              <td class="fraktguiden-services-table-col-enabled">
                <label for="<?php echo $id; ?>"
                       style="display:inline-block; width: 100%">
                  <input type="checkbox" id="<?php echo $id; ?>"
                         name="<?php echo $field_key; ?>[]"
                         value="<?php echo $key; ?>" <?php echo( $checked ? 'checked' : '' ); ?> />
                </label>
              </td>
              <td class="fraktguiden-services-table-col-name">
                <span data-tip="<?php echo $service['HelpText']; ?>"
                      class="woocommerce-help-tip"></span>
                <label class="fraktguiden-service" for="<?php echo $id; ?>"
                       data-ProductName="<?php echo $service['ProductName']; ?>"
                       data-DisplayName="<?php echo $service['DisplayName']; ?>">
                  <?php echo $service[$this->service_name]; ?>
                </label>
              </td>
              <td class="fraktguiden-services-table-col-custom-price">
                <input type="text" name="<?php echo $prices_field_key; ?>"
                       value="<?php echo $custom_price; ?>"/>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
        <script>
          jQuery( document ).ready( function () {
            var $ = jQuery;
            $( '#woocommerce_bring_fraktguiden_service_name' ).change( function () {
              console.log( 'change', this.value );
              var val = this.value;
              $( '.fraktguiden-services-table' ).find( 'label.fraktguiden-service' ).each( function ( i, elem ) {

                var label = $( elem );
                label.text( label.attr( 'data-' + val ) );
              } );
            } );

          } );
        </script>
      </td>
    </tr>

    <?php
    return ob_get_clean();
  }

  /**
   * Calculate shipping costs.
   *
   * @todo: in 2.6, the package param was added. Investigate this!
   */
  public function calculate_shipping( $package = array() ) {
    global $woocommerce;

    //include_once( 'common/class-fraktguiden-packer.php' );
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
      $c             = $woocommerce->cart->get_cart();
      $product_boxes = $packer->create_boxes( $woocommerce->cart->get_cart() );
//      // Create an array of 'product boxes' (l,w,h,weight).
//      $product_boxes = array();
//
//      /** @var WC_Cart $cart */
//      $cart = $woocommerce->cart;
//      foreach ( $cart->get_cart() as $values ) {
//
//        /** @var WC_Product $product */
//        $product = $values['data'];
//
//        if ( ! $product->needs_shipping() ) {
//          continue;
//        }
//        $quantity = $values['quantity'];
//        for ( $i = 0; $i < $quantity; $i++ ) {
//          if ( ! $product->has_dimensions() ) {
//            // If the product has no dimensions, assume the lowest unit 1x1x1 cm
//            $dims = array( 0, 0, 0 );
//          }
//          else {
//            $dims = array(
//                $product->length,
//                $product->width,
//                $product->height
//            );
//          }
//
//          // Workaround weird LAFFPack issue where the dimensions are expected in reverse order.
//          rsort( $dims );
//
//          $box = array(
//              'length'          => $dims[0],
//              'width'           => $dims[1],
//              'height'          => $dims[2],
//              'weight'          => $product->weight,
//              'weight_in_grams' => $packer->get_weight( $product->weight ) // For $packer->exceeds_max_package_values only.
//          );
//
//          // Return if product is larger than available Bring packages.
//          if ( $packer->exceeds_max_package_values( $box ) ) {
//            return;
//          }
//
//          $product_boxes[] = $box;
//        }
//      }

      if ( ! $product_boxes ) {
        return;
      }

      // Pack product boxes.
      $packer->pack( $product_boxes, true );

      // Create the url.
      $this->packages_params = $packer->create_packages_params();


      if ( is_checkout() && session_status() == PHP_SESSION_NONE ) {
        session_start();
        $_SESSION['_fraktguiden_packages'] = json_encode( $this->packages_params );
      }

      // Request parameters.
      $params = array_merge( $this->create_standard_url_params(), $this->packages_params );
      // Remove any empty elements.
      $params = array_filter( $params );

      $url = add_query_arg( $params, self::SERVICE_URL );

      // Add all the selected services to the URL
      if ( $this->services && count( $this->services ) > 0 ) {
        foreach ( $this->services as $service ) {
          $url .= '&product=' . $service;
        }
      }

      // Make the request.
      $request  = new WP_Bring_Request();
      $response = $request->get( $url );

      if ( $response->status_code != 200 ) {
        return;
      }

      // Decode the JSON data from bring.
      $json = json_decode( $response->get_body(), true );
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
          'label' => $serviceDetails['GuiInformation'][$this->service_name]
              . ( $this->display_desc == 'no' ?
                  '' : ': ' . $serviceDetails['GuiInformation']['DescriptionText'] ),
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
    return isset( $this->from_country ) ?
        $this->from_country : $woocommerce->countries->get_base_country();
  }

}

