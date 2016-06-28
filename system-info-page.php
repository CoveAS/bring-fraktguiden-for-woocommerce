<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( 'Fraktguiden_Helper' ) ) {
  include_once 'classes/common/class-fraktguiden-helper.php';
}

class Fraktguiden_System_Info {

  static function generate() {
    global $woocommerce, $wp_version;
    ?>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Fraktguiden - System Info</title>
      <style>
        body {
          font-family: "Open Sans", sans-serif;
          color: #333;
        }

        body, td {
          font-size: 13px;
          line-height: 17px;
        }

        div.main {
          width: 55%;
          margin-left: auto;
          margin-right: auto;
          text-align: center;
        }

        table {
          border-collapse: collapse;
          border: none;
        }

        td, th {
          padding: 3px 6px;
          vertical-align: top;
          border: 1px solid lightgray;
          text-align: left;
        }

        th {
          padding-top: 15px;
          padding-bottom: 15px;
        }

        table.properties td, table.properties th {
          border: 1px solid white;
        }

        table.properties td:first-child {
          background-color: lightgray;
          text-align: right;
          width: 130px;
        }

        ul {
          margin: 0;
          padding: 0 15px;
        }

      </style>
    </head>
    <body>
    <div class="main">
      <h1>System Info</h1>
      <table>
        <?php
        $bfg_plugin_data = get_plugin_data( dirname( __DIR__ ) . '/bring-fraktguiden-for-woocommerce/bring-fraktguiden-for-woocommerce.php' );
        $bfg_options     = get_option( 'woocommerce_' . WC_Shipping_Method_Bring::ID . '_settings' );

        self::create_header( 'WordPress ' . $wp_version );
        self::create_row( 'active_plugins', self::create_active_plugins_info() );

        self::create_header( 'WooCommerce ' . $woocommerce->version );
        self::create_row( 'base_country', $woocommerce->countries->get_base_country() );
        self::create_row( 'woocommerce_dimension_unit', get_option( 'woocommerce_dimension_unit' ) );
        self::create_row( 'woocommerce_weight_unit', get_option( 'woocommerce_weight_unit' ) );
        self::create_row( 'woocommerce_currency', get_option( 'woocommerce_currency' ) );
        self::create_row( 'shipping_countries', self::create_shipping_countries( $woocommerce->countries->get_shipping_countries() ) );

        self::create_header( $bfg_plugin_data['Name'] . ' ' . $bfg_plugin_data['Version'] );
        self::generate_fraktguiden_options( $bfg_options );

        self::create_header( 'Bring Fraktguiden Services' );
        self::generate_fraktguiden_services_info( Fraktguiden_Helper::get_all_services_with_customer_types(), $bfg_options );
        ?>
      </table>
    </div>

    <?php self::generate_script(); ?>

    </body>
    </html>
    <?php
    die();
  }

  private static function create_header( $header_text ) {
    echo '<thead>';
    echo '<tr>';
    echo '<th colspan="2">' . $header_text . '</th>';
    echo '</tr>';
    echo '</thead>';
  }

  private static function create_row( $key, $val ) {
    echo '<tr>';
    echo '<td>' . $key . '</td>';
    echo '<td>' . $val . '</td>';
    echo '</tr>';
  }

  private static function generate_fraktguiden_options( $options ) {
    $is_pro = class_exists( 'WC_Shipping_Method_Bring_Pro' );
    self::create_row( 'pro', ( $is_pro ? 'yes' : 'no' ) );
    foreach ( $options as $key => $option ) {
      $val = '';
      if ( gettype( $option ) == 'array' ) {
        $val .= '<ul>';

        foreach ( $option as $o ) {
          $val .= '<li>' . $o . '</li>';
        }
        $val .= '</ul>';
      }
      else {
        $val = $option;
      }

      if ( $key == 'mybring_api_key' ) {
        $val = '*******';
      }

      self::create_row( $key, $val );
    }
    if ( $is_pro ) {
      self::create_row( 'labels_directory', Bring_Booking_Labels::get_local_dir() );
    }
  }

  private static function create_active_plugins_info() {
    $plugins_text = '<ul>';
    $plugins      = get_plugins();
    foreach ( $plugins as $key => $plugin ) {
      if ( is_plugin_active( $key ) ) {
        $plugins_text .= '<li>' . $plugin['Name'] . ' ' . $plugin['Version'] . '</li>';
      }
    }
    $plugins_text .= '</ul>';
    return $plugins_text;
  }

  private static function create_shipping_countries( $countries ) {
    $html = '<div class="shipping-countries">';
    $html .= '<div>';

    $i = 0;
    foreach ( $countries as $country ) {
      if ( $i == 0 ) {
        $html .= '<ul>';
      }
      if ( $i < 5 ) {
        $html .= '<li>' . $country . '</li>';
      }
      if ( $i == 5 ) {
        $html .= '</ul>';
        $html .= '<ul class="js-hidden" style="display: none">';
      }
      if ( $i > 5 ) {
        $html .= '<li>' . $country . '</li>';
      }

      if ( $i == count( $countries ) ) {
        $html .= '</ul>';
      }

      $i++;
    }
    $html .= '</div>';
    $html .= '<div><a href="#" class="js-more">More...</a></div>';

    $html .= '</div>';

    return $html;
  }

  private static function generate_fraktguiden_services_info( $all_services, $bfg_options ) {
    foreach ( $all_services as $key => $service ) {
      $info_table = '<table class="properties">';
      foreach ( $service as $k => $v ) {
        if ( gettype( $v ) == 'array' ) {
          $val_html = '';
          foreach ( $v as $n ) {
            $val_html .= '<li>' . $n . '</li>';
          }
          $val_html .= '</ul>';
        }
        else {
          $val_html = $v;
        }
        $info_table .= "    
        <tr>
          <td>$k</td>
          <td>$val_html</td>
        </tr>";
      }
      $info_table .= '</table>';

      $text = $key;
      foreach ( $bfg_options['services'] as $k => $selected_service ) {
        if ( $key == $selected_service ) {
          $text .= ' (selected)';
        }
      }

      self::create_row( $text, $info_table );
    }
  }

  private static function generate_script() {
    ?>
    <script>
      var more_elem = document.querySelector( '.js-more' );
      var shipping_countries_elem = document.querySelector( '.shipping-countries' );
      var hidden_elem = document.querySelector( '.js-hidden' );
      shipping_countries_elem.addEventListener( 'click', function ( evt ) {
        hidden_elem.style.display = hidden_elem.style.display == 'none' ? '' : 'none';
        more_elem.textContent = hidden_elem.style.display == 'none' ? 'More...' : 'Less';
      } );

      more_elem.addEventListener( 'click', function ( evt ) {
        evt.preventDefault();
      } );
    </script>
    <?php
  }

}