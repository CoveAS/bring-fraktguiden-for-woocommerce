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
    $this->method_title = __( 'Bring Fraktguiden', 'bring-fraktguiden' );
    $this->supports              = array(
      'shipping-zones',
      'settings',
      // 'instance-settings', // @TODO - Settings per zone
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

    $this->form_fields = [
        'plugin_settings' => [
            'type'  => 'title',
            'title' => __( 'Bring Settings', 'bring-fraktguiden' ),
        ],
        'enabled'               => array(
            'title'   => __( 'Enable', 'bring-fraktguiden' ),
            'type'    => 'checkbox',
            'label'   => __( 'Enable Bring Fraktguiden', 'bring-fraktguiden' ),
            'default' => 'no'
        ),
        'pro_enabled'               => array(
            'title'   => __( 'Enable PRO', 'bring-fraktguiden' ),
            'type'    => 'checkbox',
            'label'   => __( 'Activate PRO features.', 'bring-fraktguiden' ),
            'description' => Fraktguiden_Helper::get_pro_description(),
        ),
        'test_mode'               => array(
            'title'   => __( 'Test mode', 'bring-fraktguiden' ),
            'type'    => 'checkbox',
            'label'   => __( 'Use PRO in test-mode. Used for development', 'bring-fraktguiden' ),
            'default' => 'no'
        ),
        'disable_stylesheet'               => array(
            'title'   => __( 'Disable stylesheet', 'bring-fraktguiden' ),
            'type'    => 'checkbox',
            'label'   => __( 'Remove fraktguiden styling from the checkout', 'bring-fraktguiden' ),
            'default' => 'no'
        ),
        'title'                 => array(
            'title'    => __( 'Title', 'bring-fraktguiden' ),
            'type'     => 'text',
            'desc_tip' => __( 'This controls the title which the user sees during checkout.', 'bring-fraktguiden' ),
            'default'  => __( 'Bring Fraktguiden', 'bring-fraktguiden' )
        ),
        'handling_fee'          => array(
            'title'    => __( 'Delivery Fee', 'bring-fraktguiden' ),
            'type'     => 'number',
            'desc_tip' => __( 'What fee do you want to charge for Bring, disregarded if you choose free. Leave blank to disable.', 'bring-fraktguiden' ),
            'css'      => 'width: 5rem;',
            'default'  => ''
        ),
        'post_office'           => array(
            'title'    => __( 'Post office', 'bring-fraktguiden' ),
            'type'     => 'checkbox',
            'label'    => __( 'Shipping from post office', 'bring-fraktguiden' ),
            'desc_tip' => __( 'Flag that tells whether the parcel is delivered at a post office when it is shipped.', 'bring-fraktguiden' ),
            'default'  => 'no'
        ),
        'from_zip'              => array(
            'title'    => __( 'From zip', 'bring-fraktguiden' ),
            'type'     => 'text',
            'desc_tip' => __( 'This is the zip code of where you deliver from. For example, the post office.', 'bring-fraktguiden' ),
            'css'      => 'width: 5rem;',
            'default'  => ''
        ),
        'from_country'          => array(
            'title'    => __( 'From country', 'bring-fraktguiden' ),
            'type'     => 'select',
            'desc_tip' => __( 'This is the country of origin where you deliver from (If omitted WooCommerce\'s default location will be used. See WooCommerce - Settings - General)', 'bring-fraktguiden' ),
            'class'    => 'chosen_select',
            'css'      => 'width: 450px;',
            'default'  => $woocommerce->countries->get_base_country(),
            'options'  => Fraktguiden_Helper::get_nordic_countries()
        ),
        'vat'                   => array(
            'title'    => __( 'Display price', 'bring-fraktguiden' ),
            'type'     => 'select',
            'desc_tip' => __( 'How to calculate delivery charges', 'bring-fraktguiden' ),
            'default'  => 'include',
            'options'  => array(
                'include' => __( 'VAT included', 'bring-fraktguiden' ),
                'exclude' => __( 'VAT excluded', 'bring-fraktguiden' )
            ),
        ),
        'evarsling'             => array(
            'title'    => __( 'Recipient notification', 'bring-fraktguiden' ),
            'type'     => 'checkbox',
            'label'    => __( 'Recipient notification over SMS or E-Mail', 'bring-fraktguiden' ),
            'desc_tip' => __( 'If not checked, Fraktguiden will add a fee for paper based recipient notification.<br/>If checked, the recipient will receive notification over SMS or E-mail when the parcel has arrived.<br/>Applies to Bedriftspakke, Kliman&oslash;ytral Servicepakke and Bedriftspakke Ekspress-Over natten 09', 'bring-fraktguiden' ),
            'default'  => 'no'
        ),
        'availability'          => array(
            'title'   => __( 'Method availability', 'bring-fraktguiden' ),
            'type'    => 'select',
            'default' => 'all',
            'class'   => 'availability',
            'options' => array(
                'all'      => __( 'All allowed countries', 'bring-fraktguiden' ),
                'specific' => __( 'Specific Countries', 'bring-fraktguiden' )
            )
        ),
        'countries'             => array(
            'title'   => __( 'Specific Countries', 'bring-fraktguiden' ),
            'type'    => 'multiselect',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $woocommerce->countries->countries
        ),
        'debug'         => array(
            'title'       => __( 'Debug', 'bring-fraktguiden' ),
            'type'        => 'checkbox',
            'label'       => __( 'Enable debug logs', 'bring-fraktguiden' ),
            'description' => __( 'These logs will be saved in', 'bring-fraktguiden' ) . ' <code>' . $wc_log_dir . '</code>',
            'default'     => 'no'
        ),
        'general_options_title' => [
            'type'  => 'title',
            'title' => __( 'Shipping Options', 'bring-fraktguiden' ),
        ],
        'services'              => array(
            'title'   => __( 'Services', 'bring-fraktguiden' ),
            'type'    => 'services_table',
            'class'   => 'chosen_select',
            'css'     => 'width: 450px;',
            'default' => '',
            'options' => $services
        ),
        'service_name' => array(
            'title'    => __( 'Display Service As', 'bring-fraktguiden' ),
            'type'     => 'select',
            'desc_tip' => __( 'The service name displayed to the customer', 'bring-fraktguiden' ),
            'default'  => 'DisplayName',
            'options'  => array(
                'DisplayName' => __( 'Display Name', 'bring-fraktguiden' ),
                'ProductName' => __( 'Product Name', 'bring-fraktguiden' ),
            )
        ),
        'display_desc'  => array(
            'title'    => __( 'Display Description', 'bring-fraktguiden' ),
            'type'     => 'checkbox',
            'label'    => __( 'Add description after the service', 'bring-fraktguiden' ),
            'desc_tip' => __( 'Show service description after the name of the service', 'bring-fraktguiden' ),
            'default'  => 'no'
        ),
        'exception_handling'          => array(
            'title'    => __( 'Heavy item handling', 'bring-fraktguiden' ),
            'type'     => 'select',
            'desc_tip' => __( 'What method should be used to calculate post rates for items that exceeds the limits set by bring', 'bring-fraktguiden' ),
            'default'  => 'no_rates',
            'options'  => [ 'no_rate' => 'No rate', 'flat_rate' => 'Flat rate']
        ),
        'exception_flat_rate_label'          => array(
            'title'    => __( 'Label for heavy item rate', 'bring-fraktguiden' ),
            'type'     => 'text',
            'default'  => __( 'Shipping', 'bring-fraktguiden' ),
        ),
        'exception_flat_rate'          => array(
            'title'    => __( 'Flat rate for heavy items', 'bring-fraktguiden' ),
            'css'      => 'width: 5rem;',
            'type'     => 'number',
            'default'  => '0',
        ),
        'max_products'  => array(
            'title'    => __( 'Max products', 'bring-fraktguiden' ),
            'type'     => 'text',
            'desc_tip' => __( 'Maximum of products in the cart before offering a flat rate', 'bring-fraktguiden' ),
            'default'  => self::DEFAULT_MAX_PRODUCTS
        ),
        'alt_flat_rate' => array(
            'title'    => __( 'Flat rate', 'bring-fraktguiden' ),
            'type'     => 'text',
            'desc_tip' => __( 'Offer a flat rate if the cart reaches max products or a product in the cart does not have the required dimensions', 'bring-fraktguiden' ),
            'default'  => self::DEFAULT_ALT_FLAT_RATE
        ),
    ];

    if ( class_exists( 'WC_Shipping_Zones' ) ) {
      unset( $this->form_fields['availability'] );
      unset( $this->form_fields['enabled'] );
      unset( $this->form_fields['countries'] );
    }

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
    <p>
      <a href="<?php echo admin_url(); ?>admin-ajax.php?action=bring_system_info"
         target="_blank"><?php echo __( 'View system info', 'bring-fraktguiden' ) ?></a>
    </p>
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
            <strong><?php _e( 'Gateway Disabled', 'bring-fraktguiden' ); ?></strong>
            <br/> <?php printf( __( 'Bring shipping method requires <strong>weight &amp; dimensions</strong> to be enabled. Please enable them on the <a href="%s">Catalog tab</a>. <br/> In addition, Bring also requires the <strong>Norweigian Krone</strong> currency. Choose that from the <a href="%s">General tab</a>', 'bring-fraktguiden' ), 'admin.php?page=woocommerce_settings&tab=catalog', 'admin.php?page=woocommerce_settings&tab=general' ); ?>
          </p></div></td></tr>
      <?php endif; ?>
    </table>

    <script>
    jQuery( function( $ ) {
      // Move settings into tabs
      $( '.wc-settings-sub-title' ).each( function() {
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
        var table = $( this ).next();
        var description = [];

        // Sometimes titles have descriptions which come before the table
        while ( 'P' == table.prop( 'nodeName' ) ) {
          description.push( table );
          table = table.next();
        }
        // Remove the id from the title because we gave it to the tab instead
        $( this ).removeAttr( 'id' );

        // Put the content into the panel
        elem.append( $( this ) );
        for( var i = 0; i < description.length; i++ ) {
          elem.append( description[i] );
        }
        elem.append( table );

        // Place the panel in the panels container
        $( '.hash-tabs .tab-pane-container' ).append( elem );
      } );

      // Make the tabs work
      $( '.fraktguiden-options' ).hashTabs().show();
    } );

    jQuery( function( $ ) {
      function toggle_test_mode() {
        var is_checked = $( '#woocommerce_bring_fraktguiden_pro_enabled' ).prop( 'checked' );
        $( '#woocommerce_bring_fraktguiden_test_mode' ).closest( 'tr' ).toggle( is_checked );
        // Toggle the menu items for pickup points and bring booking
        $( '#2, #3' ).toggle( is_checked );
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
            for="<?php echo $field_key ?>"><?php _e( 'Services', 'bring-fraktguiden' ); ?></label>
      </th>
      <td class="forminp">
        <table class="wc_shipping widefat fraktguiden-services-table">
          <thead>
          <tr>
            <th class="fraktguiden-services-table-col-enabled">
              Aktiv
            </th>
            <th class="fraktguiden-services-table-col-service">
              Tjeneste
            </th>
            <?php if ( Fraktguiden_Helper::pro_activated() ) : ?>
            <th class="fraktguiden-services-table-col-custom-price">
              Egendefinert pris
            </th>
            <th class="fraktguiden-services-table-col-free-shipping">
              Gratis frakt
            </th>
            <th class="fraktguiden-services-table-col-free-shipping-threshold">
              Fraktfri grense
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
      $cart = $package[ 'contents' ];
      try {
        $this->packages_params = $this->pack_order( $cart );
      }
      catch ( PackingException $e ) {
        if ( $e->getMessage() == 'exceeds_max_package_values' ) {
          $exception_handling = $this->get_setting( 'exception_handling' );
          if ( 'flat_rate' == $exception_handling ) {
            $rate = array(
                'id'    => $this->id . ':' . 'exception_rate',
                'cost'  => floatval( $this->get_setting('exception_flat_rate') ),
                'label' => $this->get_setting( 'exception_flat_rate', __( 'Shipping', 'bring-fraktguiden' ) ),
            );
            $this->add_rate( $rate );
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

      // Request parameters.
      $params = array_merge( $this->create_standard_url_params( $package ), $this->packages_params );
      // Remove any empty elements.
      $params = array_filter( $params );

      $url = add_query_arg( $params, self::SERVICE_URL );

      // Add all the selected services to the URL
      $service_count = 0;
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

}
