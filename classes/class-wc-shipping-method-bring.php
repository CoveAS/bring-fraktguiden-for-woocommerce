<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

require_once 'common/http/class-wp-bring-request.php';
require_once 'common/class-fraktguiden-helper.php';
require_once 'common/class-fraktguiden-packer.php';
require_once 'common/class-fraktguiden-minimum-dimensions.php';

/**
 * Bring class for calculating and adding rates.
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
  protected $services = array();
  protected $service_name = '';
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
   *
   * @param int $instance_id
   */
  public function __construct( $instance_id = 0 ) {

    $this->id           = self::ID;
    $this->method_title = __( 'Bring Fraktguiden', self::TEXT_DOMAIN );
    $this->method_description    = __( 'Automatically calculate shipping rates using brings fraktguiden api.', self::TEXT_DOMAIN );
    $this->supports              = array(
      'shipping-zones',
      'settings',
      'instance-settings',
      // 'instance-settings-modal',
    );
    if ( $instance_id ) {
      parent::__construct( $instance_id );
    }

    // Load the form fields.
    $this->init_form_fields();

    // Load the settings.
    $this->init_settings();

    // Debug configuration
    $this->debug = $this->settings['debug'];
    $this->log   = new WC_Logger();

    // Define user set variables

    // WC_Shipping_Method
    if ( isset( $this->settings['enabled'] ) ) {
      $this->enabled = $this->settings['enabled'];
    } else {
      // With shipping zones the method should always be enabled
      $this->enabled = true;
    }
    $this->title        = $this->get_setting( 'title' );
    $this->availability = $this->get_setting( 'availability' );
    $this->countries    = $this->get_setting( 'countries' );
    $this->fee          = $this->get_setting( 'handling_fee' );

    // WC_Shipping_Method_Bring
    $this->from_country = $this->get_setting( 'from_country' );
    $this->from_zip     = $this->get_setting( 'from_zip' );
    $this->post_office  = $this->get_setting( 'post_office' );
    $this->vat          = $this->get_setting( 'vat' );
    $this->evarsling    = $this->get_setting( 'evarsling' );
    $this->services     = $this->get_setting( 'services' );
    $this->service_name = $this->get_setting( 'service_name', 'DisplayName' );
    $this->display_desc = $this->get_setting( 'display_desc', 'no' );
    $this->max_products = (int) $this->get_setting( 'max_products', self::DEFAULT_MAX_PRODUCTS );

    // The packer may make a lot of recursion when the cart contains many items.
    // Make sure xdebug max_nesting_level is raised.
    // See: http://stackoverflow.com/questions/4293775/increasing-nesting-functions-calls-limit
    ini_set( 'xdebug.max_nesting_level', 10000 );

    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_admin_options' ) );

    add_action( 'admin_enqueue_scripts', __CLASS__ .'::admin_enqueue_scripts' );

    if ( ! $this->is_valid_for_use() ) {
      $this->enabled = false;
    }
  }
  /**
   * Get setting
   * @param  string $key
   * @param  string|mixed $default
   * @return mixed
   */
  public function get_setting( $key, $default = '' ) {
    return array_key_exists(  $key, $this->settings ) ? $this->settings[ $key] : $default;
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

    if ( $this->instance_id ) {
        $this->init_instance_form_fields();
        return;
    }

    $this->form_fields = [

        /**
         * Plugin settings
         */
        'plugin_settings' => [
            'type'  => 'title',
            'title' => __( 'Bring Settings', self::TEXT_DOMAIN ),
            'class'       => 'separated_title_tab',
        ],
        'enabled'               => array(
            'title'   => __( 'Enable', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Bring Fraktguiden', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),


        /**
         *  Required information
         */

        'title'                 => array(
            'title'    => __( 'Title', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'desc_tip' => __( 'This controls the title which the user sees during checkout.', self::TEXT_DOMAIN ),
            'default'  => __( 'Bring Fraktguiden', self::TEXT_DOMAIN )
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
            'placeholder' => __( 'ie: 0159', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'This is the zip code of where you deliver from. For example, the post office.', self::TEXT_DOMAIN ),
            'css'      => 'width: 8em;',
            'default'  => ''
        ),

        'from_country'          => array(
            'title'    => __( 'From country', self::TEXT_DOMAIN ),
            'type'     => 'select',
            'desc_tip' => __( 'This is the country of origin where you deliver from (If omitted WooCommerce\'s default location will be used. See WooCommerce - Settings - General)', self::TEXT_DOMAIN ),
            'class'    => 'chosen_select',
            'css'      => 'width: 400px;',
            'default'  => $woocommerce->countries->get_base_country(),
            'options'  => Fraktguiden_Helper::get_nordic_countries()
        ),

        'handling_fee'          => array(
            'title'    => __( 'Delivery Fee', self::TEXT_DOMAIN ),
            'type'     => 'number',
            'placeholder' => __( '0', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', self::TEXT_DOMAIN ),
            'css'      => 'width: 8em;',
            'default'  => '',
            'custom_attributes'     => [
              'min'       => '0'
            ]
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
            'description' => __( '<strong>Note:</strong> If not enabled, Fraktguiden will add a fee for paper based recipient notification.<br/>
              If enabled, the recipient will receive notification over SMS or E-mail when the parcel has arrived.<br/>
              This only applies to <u>Bedriftspakke</u>, <u>Kliman&oslash;ytral Servicepakke</u> and <u>Bedriftspakke Ekspress-Over natten 09</u>', self::TEXT_DOMAIN ),
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
            'css'     => 'width: 400px;',
            'default' => '',
            'options' => $woocommerce->countries->countries
        ),

        /**
         * Pro enabling
         */
        'pro_mode_settings' => [
            'type'  => 'title',
            'title' => __( 'Bring Fraktguiden Pro', self::TEXT_DOMAIN ),
            'description' => Fraktguiden_Helper::get_pro_description(),
            'class'       => 'bring-separate-admin-section',
        ],
        'pro_enabled'               => array(
            'title'   => __( 'Bring Fraktguiden Pro', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable PRO features to extend Bring Fraktguiden', self::TEXT_DOMAIN ),
        ),

        'test_mode'               => array(
            'title'   => __( 'Enable test mode', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Use PRO in test-mode. Used for development', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),

        /**
         * General options setting
         */
        'general_options_title' => [
            'type'        => 'title',
            'title'       => __( 'Shipping Options', self::TEXT_DOMAIN ),
            'description' => __( 'Set the default prices for shipping rates and allow free shipping options on those services. You can also set the free shipping limit for each shipping service.', self::TEXT_DOMAIN ),
            'class'       => 'separated_title_tab',
        ],
        'service_name' => array(
            'title'       => __( 'Display Service As', self::TEXT_DOMAIN ),
            'type'        => 'select',
            'desc_tip'    => __( 'The service name displayed to the customer on the cart / checkout', self::TEXT_DOMAIN ),
            'description' => __( 'Display name: <strong>"At the post office"</strong>,<br/>Product name: <strong>"Climate Neutral Service Pack"</strong>', self::TEXT_DOMAIN ),
            'default'     => 'DisplayName',
            'options'     => array(
                'DisplayName' => __( 'Display Name', self::TEXT_DOMAIN ),
                'ProductName' => __( 'Product Name', self::TEXT_DOMAIN ),
            )
        ),
        'display_desc'  => array(
            'title'    => __( 'Display Description', self::TEXT_DOMAIN ),
            'type'     => 'checkbox',
            'label'    => __( 'Add description after the service', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'Show service description after the name of the service', self::TEXT_DOMAIN ),
            'default'  => 'no'
        ),
        'services'              => array(
            'title'   => __( 'Services', self::TEXT_DOMAIN ),
            'type'    => 'services_table',
            'class'   => 'chosen_select',
            'css'     => 'width: 400px;',
            'default' => '',
            'options' => $services
        ),

        /**
         * Sizing is important when packing products to ship.
         * - Dimensions are limited and we need to use 23 x 13 x 1.
         * - The weight should be at least 0.01
         */
        'fallback_options' => [
            'type'        => 'title',
            'title'       => __( 'Fallback options', self::TEXT_DOMAIN ),
            'description' => __( 'With scenarios that fall outside of what Bring can handle, you are able to set prices for cases such as oversized items, minimum sized items, how many items you allow in one shipment and what should happen if Bring is not accessible.', self::TEXT_DOMAIN ),
            'class'       => 'separated_title_tab',
        ],
        'minimum_sizing_params' => [
            'type'        => 'title',
            'title'       => __( 'Minimum shipping dimensions', self::TEXT_DOMAIN ),
            'description' => __( 'Bring needs a default shipping size for when products do not contain any dimension information.', self::TEXT_DOMAIN ),
            'class'       => 'bring-section-started',
        ],
        'minimum_length'  => array(
            'title'       => __( 'Minimum Length in cm', self::TEXT_DOMAIN ),
            'type'        => 'number',
            'css'         => 'width: 8em;',
            'placeholder' => __( 'Must be at least 23cm', self::TEXT_DOMAIN ),
            'desc_tip'       => __( 'The lowest length for a consignment', self::TEXT_DOMAIN ),
            'default'     => '23',
            'custom_attributes'     => [
              'min'       => '1'
            ]
        ),
        'minimum_width'  => array(
            'title'       => __( 'Minimum Width in cm', self::TEXT_DOMAIN ),
            'type'        => 'number',
            'css'         => 'width: 8em;',
            'placeholder' => __( 'Must be at least 13cm', self::TEXT_DOMAIN ),
            'desc_tip'    => __( 'The lowest width for a consignment', self::TEXT_DOMAIN ),
            'default'     => '13',
            'custom_attributes'     => [
              'min'     => '1'
            ]
        ),
        'minimum_height'  => array(
            'title'       => __( 'Minimum Height in cm', self::TEXT_DOMAIN ),
            'type'        => 'number',
            'css'         => 'width: 8em;',
            'placeholder' => __( 'Must be at least 1cm', self::TEXT_DOMAIN ),
            'desc_tip'    => __( 'The lowest height for a consignment', self::TEXT_DOMAIN ),
            'default'     => '1',
            'custom_attributes'     => [
              'min'       => '1'
            ]
        ),
        'minimum_weight'  => array(
            'title'       => __( 'Minimum Weight in kg', self::TEXT_DOMAIN ),
            'type'        => 'number',
            'css'         => 'width: 8em;',
            'desc_tip'    => __( 'The lowest weight in kilograms for a consignment', self::TEXT_DOMAIN ),
            'default'     => '0.01',
        ),

        /**
         * Lost / no connection section
         */
        'no_connection_title' => [
            'type'        => 'title',
            'title'       => __( 'Bring API offline / No connection', self::TEXT_DOMAIN ),
            'description' => __( 'If Bring has any technical difficulties, it won\'t be able to fetch prices from the bring server.<br>In these cases, shipping will default to these settings:', self::TEXT_DOMAIN ),
            'class'       => 'bring-separate-admin-section',
        ],
        'no_connection_handling'          => array(
            'title'       => __( 'No API connection handling', self::TEXT_DOMAIN ),
            'type'        => 'select',
            'desc_tip'    => __( 'What pricing should be used if no connection can be made to the bring API', self::TEXT_DOMAIN ),
            'default'     => 'no_rate',
            'options'  => [
              'no_rate'   => __( 'Do nothing', self::TEXT_DOMAIN ),
              'flat_rate' => __( 'Custom flat rate', self::TEXT_DOMAIN )
            ]
        ),
        'no_connection_flat_rate_label'          => array(
            'title'    => __( 'Shipping method Label to replace \'API Error\'', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'default'  => __( 'Shipping', self::TEXT_DOMAIN ),
        ),
        'no_connection_flat_rate'          => array(
            'title'    => __( 'Shipping method cost for \'API Error\'', self::TEXT_DOMAIN ),
            'css'      => 'width: 8em;',
            'type'     => 'number',
            'placeholder' => __( 'ie: 500', self::TEXT_DOMAIN ),
            'default'  => '0',
        ),
        'no_connection_rate_id'          => array(
            'title'    => __( 'Service to use for booking', self::TEXT_DOMAIN ),
            'css'      => '',
            'type'     => 'select',
            'default'  => '0',
            'options'  => $this->get_service_id_options(),
        ),

        /**
         * Heavy items section
         */
        'exceptions_title' => [
            'type'        => 'title',
            'title'       => __( 'Heavy and oversized items', self::TEXT_DOMAIN ),
            'description' => __( 'Set a flat rate for packages that exceed the maximum measurements allowed by Bring.', self::TEXT_DOMAIN ),
            'class'       => 'bring-separate-admin-section',
        ],
        'exception_handling' => array(
            'title'    => __( 'Heavy item handling', self::TEXT_DOMAIN ),
            'type'     => 'select',
            'desc_tip' => __( 'What method should be used to calculate post rates for items that exceeds the limits set by bring', self::TEXT_DOMAIN ),
            'default'  => 'no_rate',
            'options'  => [
              'no_rate'   => __( 'Do nothing', self::TEXT_DOMAIN ),
              'flat_rate' => __( 'Custom flat rate', self::TEXT_DOMAIN )
            ]
        ),
        'exception_flat_rate_label' => array(
            'title'       => __( 'Shipping method Label for Heavy Items', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'placeholder' => __( 'ie: Cargo shipping', self::TEXT_DOMAIN ),
            'default'     => __( 'Shipping', self::TEXT_DOMAIN ),
        ),
        'exception_flat_rate' => array(
            'title'    => __( 'Shipping method cost for heavy items', self::TEXT_DOMAIN ),
            'css'      => 'width: 8em;',
            'type'     => 'number',
            'placeholder' => __( 'ie: 500', self::TEXT_DOMAIN ),
            'default'  => '0',
        ),
        'exception_rate_id' => array(
            'title'    => __( 'Service to use for booking', self::TEXT_DOMAIN ),
            'css'      => '',
            'type'     => 'select',
            'default'  => '0',
            'options'  => $this->get_service_id_options(),
        ),

        /**
         * Max products section
         */
        'max_products_title' => [
            'type'        => 'title',
            'title'       => __( 'Product quantity limit for cart', self::TEXT_DOMAIN ),
            'description' => __( 'When a cart reaches this limit, you can enable this shipping method.<br><em>For example, when ordering in bulk, the price for a shipping container may be a flat rate</em>', self::TEXT_DOMAIN ),
            'class'       => 'bring-separate-admin-section',
        ],
        'alt_handling' => array(
            'title'    => __( 'Maximum product handling', self::TEXT_DOMAIN ),
            'type'     => 'select',
            'desc_tip' => __( 'We use a packing algorithm to pack items in three dimensions. This algorithm is computationally heavy and to prevent against DDoS attacks we\'ve implemented setting to control the maximum number of items that can be packed per order.', self::TEXT_DOMAIN ),
            'default'  => 'no_rate',
            'options'  => [
              'no_rate'   => __( 'Do nothing', self::TEXT_DOMAIN ),
              'flat_rate' => __( 'Custom flat rate', self::TEXT_DOMAIN )
            ]
        ),
        'max_products'  => array(
            'title'    => __( 'Maximum product limit', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'css'      => 'width: 8em;',
            'placeholder' => __( 'ie: 1500', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'Maximum total quantity of products in the cart before offering a custom price', self::TEXT_DOMAIN ),
            'default'  => self::DEFAULT_MAX_PRODUCTS
        ),
        'alt_flat_rate_label' => array(
            'title'       => __( 'Shipping method label', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'placeholder' => __( 'ie: Cargo shipping', self::TEXT_DOMAIN ),
            'default'     => __( 'Shipping', self::TEXT_DOMAIN ),
        ),
        'alt_flat_rate' => array(
            'title'    => __( 'Shipping method cost', self::TEXT_DOMAIN ),
            'type'     => 'text',
            'css'      => 'width: 8em;',
            'placeholder' => __( 'ie: 1500', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'Offer a flat rate if the cart reaches max products or a product in the cart does not have the required dimensions', self::TEXT_DOMAIN ),
            'default'  => self::DEFAULT_ALT_FLAT_RATE
        ),
        'alt_flat_rate_id'          => array(
            'title'    => __( 'Service to use for booking', self::TEXT_DOMAIN ),
            'css'      => '',
            'type'     => 'select',
            'default'  => '0',
            'options'  => $this->get_service_id_options(),
        ),

        /**
         * Developer settings
         */
        'developer_settings' => [
            'type'  => 'title',
            'title' => __( 'Developer', self::TEXT_DOMAIN ),
            'description' => __( 'For debugging and testing the plugin', self::TEXT_DOMAIN ),
            'class'       => 'separated_title_tab',
        ],
        'disable_stylesheet'               => array(
            'title'   => __( 'Disable stylesheet', self::TEXT_DOMAIN ),
            'type'    => 'checkbox',
            'label'   => __( 'Remove fraktguiden styling from the checkout', self::TEXT_DOMAIN ),
            'default' => 'no'
        ),
        'debug'         => array(
            'title'       => __( 'Debug mode', self::TEXT_DOMAIN ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable debug logs', self::TEXT_DOMAIN ),
            'desc_tip'    => __( 'Issues from the Bring API will be logged here', self::TEXT_DOMAIN ),
            'description' => __( 'Bring Fraktguiden logs will be saved in', self::TEXT_DOMAIN ) . ' <code>' . $wc_log_dir . '</code>',
            'default'     => 'no'
        ),
        'system_information'         => array(
            'title'       => __( 'Debug System information', self::TEXT_DOMAIN ),
            'type'        => 'hidden',
            'label'       => __( 'Enable debug logs', self::TEXT_DOMAIN ),
            'desc_tip' => __( 'We may ask for this information if you require support', self::TEXT_DOMAIN ),
            'description' => sprintf( '<a href="%s" target="_blank">%s</a>', admin_url( 'admin-ajax.php?action=bring_system_info' ), __( 'View system info', self::TEXT_DOMAIN ) )
        ),

        /**
         * MyBring API settings
         */
        'mybring_title' => [
            'title'       => __( 'Mybring.com API', self::TEXT_DOMAIN ),
            'description' => __( 'If you are a Mybring user you can enter your API credentials for additional features. API authentication is required for some services such as "Package in mailbox (PAKKE_I_POSTKASSEN)".', self::TEXT_DOMAIN ),
            'class'       => 'separated_title_tab',
            'type'        => 'title',
        ],
        'mybring_api_uid' => [
            'title'       => __( 'API User ID', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'label'       => __( 'API User ID', self::TEXT_DOMAIN ),
            'placeholder' => __( 'API User ID', 'Email address, eg: post@example.com', self::TEXT_DOMAIN ),
        ],
        'mybring_api_key' => [
            'title'       => __( 'API Key', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'label'       => __( 'API Key', self::TEXT_DOMAIN ),
            'placeholder' => '4abcdef1-4a60-4444-b9c7-9876543219bf',
        ],
        'mybring_customer_number' => [
            'title'       => __( 'Customer number', self::TEXT_DOMAIN ),
            'type'        => 'text',
            'label'       => __( 'Customer number', self::TEXT_DOMAIN ),
            'placeholder' => 'PARCELS_NORWAY-100########',
        ],
    ];

    if ( class_exists( 'WC_Shipping_Zones' ) ) {
      unset( $this->form_fields['availability'] );
      unset( $this->form_fields['enabled'] );
      unset( $this->form_fields['countries'] );
    }
  }

  public function init_instance_form_fields() {
    $this->form_fields = [
    ];
  }

  /**
   * Display settings in HTML.
   *
   * @return void
   */
  public function admin_options() {
    global $woocommerce; ?>
    <!-- -->
    <h3><?php echo $this->method_title; ?></h3>
    <p><?php _e( 'Bring Fraktguiden is a shipping method using Bring.com to calculate rates.', self::TEXT_DOMAIN ); ?></p>

    <!-- -->
    <div class="hash-tabs fraktguiden-options" style="display:none;">
      <article class="tab-container">
        <nav class="tab-nav" role="tablist"><ul></ul><div style="clear:both;"></div></nav>
        <div class="tab-pane-container"></div>
      </article>
    </div>

    <table class="form-table">
      <?php if ( $this->is_valid_for_use() ) :?>
        <?php $this->generate_settings_html();?>
      <?php else : ?>
        <tr><td><div class="inline error"><p>
            <strong><?php _e( 'Gateway Disabled', self::TEXT_DOMAIN ); ?></strong>
            <br/> <?php printf( __( 'Bring shipping method requires <strong>weight &amp; dimensions</strong> to be enabled. Please enable them on the <a href="%s">Catalog tab</a>. <br/> In addition, Bring also requires the <strong>Norweigian Krone</strong> currency. Choose that from the <a href="%s">General tab</a>', self::TEXT_DOMAIN ), 'admin.php?page=woocommerce_settings&tab=catalog', 'admin.php?page=woocommerce_settings&tab=general' ); ?>
          </p></div></td></tr>
      <?php endif; ?>
    </table>

    <script>
    jQuery( function( $ ) {
      // Move settings into tabs
      $( '.separated_title_tab' ).each( function() {
        var id = $( this ).attr('id');
        var text = $( this ).text();
        // Create a new tab/list item
        var elem = $('<li>').append( $( '<a>' ).attr( {
          'href': '#' + id
        } ).text( text ) );
        // Append it to the tab-navigation
        $( '.hash-tabs .tab-nav ul' ).append( elem );

        // Create a new tab-panel
        elem = $( '<section>' ).attr( {
          'id': id
        } ).hide();


        // Find the content for this panel
        // It's always the next p's and <table>
        var content = $( this ).nextUntil( '.separated_title_tab' );
        elem.append( $(this), content );

        // Place the panel in the panels container
        $( '.hash-tabs .tab-pane-container' ).append( elem );
      } );


      // Make the tabs work
      $( '.fraktguiden-options' ).hashTabs().show();

      var save = $( 'p.submit');
      $( '.fraktguiden-options' ).after( save );

    } );

    jQuery( function( $ ) {
      function toggle_test_mode() {
        var is_checked = $( '#woocommerce_bring_fraktguiden_pro_enabled' ).prop( 'checked' );
        $( '#woocommerce_bring_fraktguiden_test_mode' ).closest( 'tr' ).toggle( is_checked );
        // Toggle the menu items for pickup points and bring booking
        $( '#5, #6' ).toggle( is_checked );
      }
      $( '#woocommerce_bring_fraktguiden_pro_enabled' ).change( toggle_test_mode );
      toggle_test_mode();
    } );
    </script>
    <?php
  }

  /**
   * Admin enqueue script
   * Add custom styling and javascript to the admin options
   * @param  string $hook
   */
  static function admin_enqueue_scripts( $hook ) {
    if ('woocommerce_page_wc-settings' !== $hook) {
        return;
    }
    wp_enqueue_script( 'hash-tables', plugin_dir_url( __DIR__ ) .'/assets/js/jquery.hash-tabs.min.js', [], '1.0.4' );
    wp_enqueue_style( 'bring-fraktguiden-styles', plugin_dir_url( __DIR__ ) .'/assets/css/bring-fraktguiden.css', [], '1.0.0' );
  }

  /**
   * Validate the service table field
   * @param  string $key
   * @param  mixed $value
   * @return array
   */
  public function validate_services_table_field( $key, $value = null ) {
    if ( isset( $value ) ) {
      return $value;
    }
    $sanitized_services = [];
    $field_key = $this->get_field_key( $key );
    if ( ! isset( $_POST[ $field_key ] ) ) {
      return $sanitized_services;
    }
    foreach ( $_POST[ $field_key ] as $service ) {
      if ( preg_match( '/^[A-Za-z_\-]+$/', $service ) ) {
        $sanitized_services[] = $service;
      }
    }
    return $sanitized_services;
  }

  /**
   * Process admin options
   * Add custom processing to handle the services field
   */
  public function process_admin_options() {
    parent::process_admin_options();

    if ( $this->instance_id ) {
        $instance_key = $this->get_instance_option_key();
    }

    // Process services table
    $services_field               = $this->get_field_key( 'services' );
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

    // Process services table
    $services  = Fraktguiden_Helper::get_services_data();
    $field_key = $this->get_field_key( 'services' );
    $vars      = [
        'custom_prices',
        'free_shipping_checks',
        'free_shipping_thresholds',
    ];
    $options = [];
    // Only process options for enabled services
    foreach ( $services as $key => $service ) {
      foreach ( $vars as $var ) {
        $data_key = "{$field_key}_{$var}";
        if ( ! isset( $options[$data_key] ) ) {
          $options[$data_key] = [];
        }
        if ( isset( $_POST[$data_key][$key] ) ) {
          $options[$data_key][$key] = $_POST[$data_key][$key];
        }
      }
    }

    foreach ($options as $data_key => $value) {
      update_option( $data_key, $value );
    }
  }

  /**
   * Generate services field
   * @return string html
   */
  public function generate_services_table_html() {
    $services                 = Fraktguiden_Helper::get_services_data();
    $selected                 = $this->services;
    $field_key                = $this->get_field_key( 'services' );
    $custom_prices            = get_option( $field_key . '_custom_prices' );
    $free_shipping_checks     = get_option( $field_key . '_free_shipping_checks' );
    $free_shipping_thresholds = get_option( $field_key . '_free_shipping_thresholds' );

    ob_start();
    ?>

    <tr valign="top">
      <th scope="row" class="titledesc">
        <label
            for="<?php echo $field_key ?>"><?php _e( 'Services', self::TEXT_DOMAIN ); ?></label>
      </th>
      <td class="forminp">
        <table class="wc_shipping widefat fraktguiden-services-table">
          <thead>
          <tr>
            <th class="fraktguiden-services-table-col-enabled">
              <?php _e( 'Active', self::TEXT_DOMAIN ); ?>
            </th>
            <th class="fraktguiden-services-table-col-service">
              <?php _e( 'Service', self::TEXT_DOMAIN ); ?>
            </th>
            <?php if ( Fraktguiden_Helper::pro_activated() ) : ?>
            <th class="fraktguiden-services-table-col-custom-price">
              <?php _e( 'Custom price', self::TEXT_DOMAIN ); ?>
            </th>
            <th class="fraktguiden-services-table-col-free-shipping">
              <?php _e( 'Free shipping', self::TEXT_DOMAIN ); ?>
            </th>
            <th class="fraktguiden-services-table-col-free-shipping-threshold">
              <?php _e( 'Free shipping limit', self::TEXT_DOMAIN ); ?>
            </th>
            <?php endif; ?>
          </tr>
          </thead>
          <tbody>

          <?php
          foreach ( $services as $key => $service ) {
            $id   = $field_key . '_' . $key;
            $vars = [
                'custom_price'            => 'custom_prices',
                'free_shipping'           => 'free_shipping_checks',
                'free_shipping_threshold' => 'free_shipping_thresholds',
            ];
            // Extract variables from the settings data
            foreach ( $vars as $var => $data_var ) {
              // Eg.: ${custom_price_id} = 'woocommerce_bring_fraktguiden_services_custom_prices[SERVICEPAKKE]';
              ${$var . '_id'} = "{$field_key}_{$data_var}[{$key}]";
              $$var           = '';
              if ( isset( ${$data_var}[$key] ) ) {
                // Eg.: $custom_price = $custom_prices['SERVICEPAKKE'];
                $$var = esc_html( ${$data_var}[$key] );
              }
            }
            $enabled = ! empty( $selected ) ? in_array( $key, $selected ) : false;
            ?>
            <tr>
              <td class="fraktguiden-services-table-col-enabled">
                <label for="<?php echo $id; ?>"
                       style="display:inline-block; width: 100%">
                  <input type="checkbox"
                         id="<?php echo $id; ?>"
                         name="<?php echo $field_key; ?>[]"
                         value="<?php echo $key; ?>" <?php echo( $enabled ? 'checked' : '' ); ?> />
                </label>
              </td>
              <td class="fraktguiden-services-table-col-name">
                <span data-tip="<?php echo $service['HelpText']; ?>"
                      class="woocommerce-help-tip"></span>
                <label class="fraktguiden-service"
                       for="<?php echo $id; ?>"
                       data-ProductName="<?php echo $service['ProductName']; ?>"
                       data-DisplayName="<?php echo $service['DisplayName']; ?>">
                  <?php echo $service[$this->service_name]; ?>
                </label>
              </td>
              <?php if ( Fraktguiden_Helper::pro_activated() ) : ?>
              <td class="fraktguiden-services-table-col-custom-price">
                <input type="text"
                       placeholder="<?= __( '...', self::TEXT_DOMAIN );?>"
                       name="<?php echo $custom_price_id; ?>"
                       value="<?php echo $custom_price; ?>"
                />
              </td>
              <td class="fraktguiden-services-table-col-free-shipping">
                <label style="display:inline-block; width: 100%">
                  <input type="checkbox"
                         name="<?php echo $free_shipping_id; ?>"
                      <?php echo $free_shipping ? 'checked' : ''; ?>>
                </label>
              </td>
              <td class="fraktguiden-services-table-col-free-shipping-threshold">
                <input type="text"
                       placeholder="<?= __( '...', self::TEXT_DOMAIN );?>"
                       name="<?php echo $free_shipping_threshold_id; ?>"
                       value="<?php echo $free_shipping_threshold; ?>"
                       placeholder="0"
                />
              </td>
              <?php endif; ?>
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
   * Pack order
   * @param  array      $contents Package contents
   * @return bool|array           Parameters for each box on success
   */
  public function pack_order( $contents ) {
      $packer = new Fraktguiden_Packer();
      $product_boxes = $packer->create_boxes( $contents );
      if ( ! $product_boxes ) {
        return false;
      }
      // Pack product boxes.
      $packer->pack( $product_boxes, true );
      // Create the url.
      return $packer->create_packages_params();
  }

  /**
   * Calculate shipping costs.
   *
   * @todo: in 2.6, the package param was added. Investigate this!
   */
  public function calculate_shipping( $package = array() ) {
    global $woocommerce;
    // include_once( 'common/class-fraktguiden-packer.php' );
    // Offer flat rate if the cart contents exceeds max product.
    // @TODO: Use the package instead of the cart
    if ( $woocommerce->cart->get_cart_contents_count() > $this->max_products ) {
      $alt_handling = $this->get_setting( 'alt_handling' );
      if ( 'flat_rate' == $alt_handling ) {
        $rate = array(
          'id'    => $this->id . ':' . 'alt_flat_rate',
          'cost'  => floatval( $this->get_setting( 'alt_flat_rate' ) ),
          'label' => $this->get_setting( 'alt_flat_rate_label', __( 'Shipping', self::TEXT_DOMAIN ) ),
        );
        $this->add_rate( $rate );
      }
      return;
    }
    else {
      $cart = $package[ 'contents' ];
      try {
        $this->packages_params = $this->pack_order( $cart );
      }
      catch ( PackingException $e ) {
        if ( $e->getMessage() == 'exceeds_max_package_values' ) {
          $exception_handling = $this->get_setting( 'exception_handling' );
          if ( 'flat_rate' == $exception_handling ) {
            $this->add_rate( [
                'id'    => $this->id . ':' . $this->get_setting( 'exception_rate_id', 'servicepakke' ),
                'cost'  => floatval( $this->get_setting( 'exception_flat_rate' ) ),
                'label' => $this->get_setting( 'exception_flat_rate_label', __( 'Shipping', self::TEXT_DOMAIN ) ),
            ] );
          }
        }
        return;
      }

      if ( ! $this->packages_params ) {
        return;
      }

      if ( is_checkout() ) {
        $_COOKIE['_fraktguiden_packages'] = json_encode( $this->packages_params );
      }

      if ( ! $package[ 'destination' ][ 'postcode' ] ) {
        // Postcode must be specified
        return;
      }

      // Request parameters.
      $params = array_merge( $this->create_standard_url_params( $package ), $this->packages_params );
      // Remove any empty elements.
      $params = array_filter( $params );

      $url = add_query_arg( $params, self::SERVICE_URL );

      // Add all the selected services to the URL
      $service_count = 0;
      if ( ! empty( $this->services ) ) {
        foreach ( $this->services as $service ) {
          $url .= '&product=' . $service;
        }
      }

      $customer_number = Fraktguiden_Helper::get_option( 'mybring_customer_number' );
      if ( $customer_number ) {
        $url .= '&customerNumber='. $customer_number;
      }
      $options = [
        'headers' => [
          'Content-Type'       => 'application/json',
          'Accept'             => 'application/json',
        ]
      ];
      $mybring_api_uid = Fraktguiden_Helper::get_option( 'mybring_api_uid' );
      $mybring_api_key = Fraktguiden_Helper::get_option( 'mybring_api_key' );
      if ( $mybring_api_key && $mybring_api_uid) {
        $options['headers']['X-MyBring-API-Uid'] = $mybring_api_uid;
        $options['headers']['X-MyBring-API-Key'] = $mybring_api_key;
      }

      // Make the request.
      $request  = new WP_Bring_Request();
      $response = $request->get( $url, [], $options );

      if ( $response->status_code != 200 ) {
        $no_connection_handling = $this->get_setting( 'no_connection_handling' );
        if ( 'flat_rate' == $no_connection_handling ) {
          $this->add_rate( [
            'id'    => $this->id . ':' . $this->get_setting( 'no_connection_rate_id', 'servicepakke' ),
            'cost'  => floatval( $this->get_setting('no_connection_flat_rate') ),
            'label' => $this->get_setting( 'no_connection_flat_rate_label', __( 'Shipping', self::TEXT_DOMAIN ) ),
          ] );
        }
        return;
      }

      // Decode the JSON data from bring.
      $json = json_decode( $response->get_body(), true );

      // Filter the response json to get only the selected services from the settings.
      $rates = $this->get_services_from_response( $json );
      $rates = apply_filters( 'bring_shipping_rates', $rates );

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
   * @param $package
   *
   * @return array
   */
  public function create_standard_url_params( $package ) {
    global $woocommerce;
    if ( ! $this->from_zip ) {
      wc_add_notice( 'Bring requires a postal code from which packages are being sent. Please check the settings page.', 'error' );
    }
    return apply_filters( 'bring_fraktguiden_standard_url_params', array(
        'clientUrl'           => $_SERVER['HTTP_HOST'],
        'from'                => $this->from_zip,
        'fromCountry'         => $this->get_selected_from_country(),
        'to'                  => $package[ 'destination' ][ 'postcode' ],
        'toCountry'           => $package[ 'destination' ][ 'country' ],
        'postingAtPostOffice' => ( $this->post_office == 'no' ) ? 'false' : 'true',
        'additional'          => ( $this->evarsling == 'yes' ) ? 'evarsling' : '',
        'language'            => $this->get_bring_language()
    ) );
  }

  public function get_bring_language() {
    $language = substr(get_bloginfo ( 'language' ), 0, 2);

    $languages = [
        'dk' => 'da',
        'fi' => 'fi',
        'nb' => 'no',
        'nn' => 'no',
        'sv' => 'se'
    ];

    return array_key_exists($language, $languages) ? $languages[$language] : 'en';
  }

  public function get_selected_from_country() {
    global $woocommerce;
    return isset( $this->from_country ) ?
        $this->from_country : $woocommerce->countries->get_base_country();
  }

  public function get_service_id_options() {
    return Fraktguiden_Helper::get_all_services();
  }
}
