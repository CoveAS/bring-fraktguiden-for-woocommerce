<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Bring_Booking_Order_View {

  static function init() {
    add_action( 'add_meta_boxes', array( __CLASS__, 'add_booking_meta_box' ), 1, 2 );
    //add_action( 'woocommerce_order_actions', array( __CLASS__, 'add_order_meta_box_actions' ) );
    add_action( 'woocommerce_order_action_bring_book_with_bring', array( __CLASS__, 'send_booking' ) );
    add_action( 'save_post', array( __CLASS__, 'redirect_page' ) );
  }

  /**
   * @param array $actions
   * @return array
   */
//  static function add_order_meta_box_actions( $actions ) {
//    /** @var WP_Post */
//    global $post;
//    if ( $post && $post->post_type == 'shop_order' ) {
//      $order = new Bring_WC_Order_Adapter( new WC_Order( $post->ID ) );
//      if ( ! $order->is_booked() ) {
//        if ( ! Bring_Booking_Common_View::is_step2() ) {
//          $actions['bring_book_with_bring'] = Bring_Booking_Common_View::booking_label();
//        }
//      }
//    }
//    return $actions;
//  }

  /**
   * @param string $post_type
   * @param WP_Post $post
   */
  static function add_booking_meta_box( $post_type, $post ) {
    if ( $post_type != 'shop_order' ) {
      return;
    }
    // Do not show if the order does not use fraktguiden shipping.
    $order = new Bring_WC_Order_Adapter( new WC_Order( $post->ID ) );
    if ( ! $order->has_bring_shipping_methods() ) {
      return;
    }
    add_meta_box(
        'woocommerce-order-bring-booking',
        __( 'Bring Booking', 'bring-fraktguiden' ),
        array( __CLASS__, 'render_booking_meta_box' ),
        'shop_order',
        'normal',
        'high'
    );
  }

  /**
   * @param WP_Post $post
   */
  static function render_booking_meta_box( $post ) {
    $wc_order = new WC_Order( $post->ID );
    $order    = new Bring_WC_Order_Adapter( $wc_order );
    $step2    = Bring_Booking_Common_View::is_step2();
    ?>

    <div class="bring-booking-meta-box-content">
      <?php
      if ( ! $order->is_booked() ) {
        self::render_progress_tracker( $order );
      }

      ?>
      <div class="bring-booking-meta-box-content-body">
        <?php
        if ( $order->has_booking_errors() && ! $step2 ) {
          self::render_errors( $order );
        }

        if ( ! $step2 && ! $order->is_booked() ) {
          self::render_start( $order );
        }

        if ( $step2 && ! $order->is_booked() ) {
          self::render_step2_screen( $order );
        }

        if ( $order->is_booked() ) {
          self::render_booking_success_screen( $order );
        }

        if ( ! $order->is_booked() ) {
          self::render_footer( $step2 );
        }
        ?>

      </div>
    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_start( $order ) {
    ?>
    <?php
    if ( ! $order->has_booking_errors() ) {
      ?>
      <div>
        <?php _e( 'Press start to start booking', 'bring-fraktguiden' ); ?>
        <br>
        <?php
        $next_status = Fraktguiden_Helper::get_option( 'auto_set_status_after_booking_success' );
        if ( $next_status != 'none' ) {
          $order_statuses = wc_get_order_statuses();
          printf( __( 'Order status will be set to %s upon successful booking', 'bring-fraktguiden' ), strtolower( $order_statuses[$next_status] ) );
        }
        ?>
      </div>
      <?php
    }
    ?>

    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_booking_success_screen( $order ) {
    ?>
    <div class="bring-info-box">
      <div>
        <?php
        $status = Bring_Booking_Common_View::get_booking_status_info( $order );
        echo Bring_Booking_Common_View::create_status_icon( $status, 90 );
        ?>
        <h3><?php echo $status['text']; ?></h3>
        <div style="text-align:center;margin-bottom: 1em">
          <?php _e( 'Note: Order is not completed', 'bring-fraktguiden' ); ?>
        </div>
      </div>
      <div>
        <h3 style="margin-top:0"><?php _e('Consignments', 'bring-fraktguiden'); ?></h3 style>
        <?php
        self::render_consignments( $order );
        ?>
      </div>

    </div>
    <?php if ( $order->order->get_status() != 'completed' ) { ?>
    <?php } ?>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_consignments( $order ) {
    $consignments = $order->get_booking_consignments();
    if ( $consignments ) {
      ?>
      <div class="bring-consignments">
        <?php

        foreach ( $consignments as $consignment ) {
          //$correlation_id     = $consignment->correlationId;
          $errors             = $consignment->errors;
          $confirmation       = $consignment->confirmation;
          $consignment_number = $confirmation->consignmentNumber;
          $links              = $confirmation->links;
          $tracking           = $links->tracking;
          $date_and_times     = $confirmation->dateAndTimes;
          $earliest_pickup    = $date_and_times->earliestPickup ? date_i18n( wc_date_format(), $date_and_times->earliestPickup / 1000 ) : 'N/A';
          $expected_delivery  = $date_and_times->expectedDelivery ? date_i18n( wc_date_format(), $date_and_times->expectedDelivery / 1000 ) : 'N/A';
          $packages           = $confirmation->packages;
          $labels_url         = Bring_Booking_Labels::create_download_url( $order->order->get_id() );
          ?>
          <div>
            <table>
              <tr>
                <th colspan="2"><?php printf( 'NO: %s', $consignment_number ) ?></th>
              </tr>
              <tr>
                <td><?php _e( 'Earliest Pickup', 'bring-fraktguiden' ); ?>:</td>
                <td><?php echo $earliest_pickup; ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Expected delivery', 'bring-fraktguiden' ); ?>:</td>
                <td><?php echo $expected_delivery; ?></td>
              </tr>
              <tr>
                <td><?php _e( 'Labels', 'bring-fraktguiden' ); ?>:</td>
                <td>
                  <a href="<?php echo $labels_url; ?>"
                     target="_blank"><?php _e( 'Download', 'bring-fraktguiden' ); ?></a>
                </td>
              </tr>
              <tr>
                <td><?php _e( 'Tracking', 'bring-fraktguiden' ); ?>:</td>
                <td>
                  <a href="<?php echo $tracking; ?>"
                     target="_blank"><?php _e( 'View', 'bring-fraktguiden' ); ?>
                    →</a>
                </td>
              </tr>
              <tr>
                <td>
                  <?php _e( 'Packages', 'bring-fraktguiden' ); ?>:
                </td>
                <td>
                  <ul>
                    <?php
                    foreach ( $packages as $package ) {
                      //$correlation_id = property_exists( $package, 'correlationId' ) ? $package->correlationId : 'N/A';
                      ?>
                      <li style="position: relative">
                        <span class="bring-package"></span>
                        <?php
                        printf( 'NO: %s', $package->packageNumber );
                        ?>
                      </li>
                    <?php } ?>
                  </ul>
                </td>
              </tr>
            </table>
          </div>
          <?php
        }
        ?>
      </div>
      <?php
    }
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_step2_screen( $order ) {
    ?>
    <div class="bring-form-field">
      <label><?php _e( 'Customer Number', 'bring-fraktguiden' ); ?>:</label>
      <?php Bring_Booking_Common_View::render_customer_selector(); ?>
    </div>

    <div class="bring-form-field">
      <label>
        <?php _e( 'Shipping Date', 'bring-fraktguiden' ); ?>:
      </label>

      <div>
        <?php Bring_Booking_Common_View::render_shipping_date_time(); ?>
      </div>

      <script type="text/javascript">
        jQuery( document ).ready( function () {
          jQuery( function () {
            jQuery( "[name=_bring-shipping-date]" ).datepicker( {
              minDate: 0,
              dateFormat: 'yy-mm-dd'
            } );
          } );
        } );
      </script>
    </div>


    <?php self::render_parties( $order ); ?>
    <div class="bring-form-field">
      <label
          for="_bring_additional_info"><?php _e( 'Additional Info', 'bring-fraktguiden' ); ?>
        :</label>
      <textarea name="_bring_additional_info" id="_bring_additional_info"
                style="width:20em"></textarea>
    </div>

    <div class="bring-form-field" style="margin-bottom:25px">
      <label>
        <?php _e( 'Packages', 'bring-fraktguiden' ); ?>:
      </label>
      <?php self::render_packages( $order ); ?>
    </div>
    <?php
  }

  /**
   * @param bool $is_step2
   */
  static function render_footer( $is_step2 ) {
    ?>
    <div class="bring-booking-footer">
      <?php if ( $is_step2 ) { ?>
        <!-- @todo: use a real link / not history back -->
        <button type="button" onclick="window.history.back()"
                class="button"
                style="margin-right:1em"><?php _e( 'Cancel', 'bring-fraktguiden' ); ?></button>
        <button type="submit" name="wc_order_action"
                value="bring_book_with_bring"
                data-tip="<?php _e( 'Update order and send consignment to Bring', 'bring-fraktguiden' ); ?>"
                class="button button-primary tips">
          <?php echo Bring_Booking_Common_View::booking_label() ?>
        </button>
      <?php }
      else { ?>
        <button type="submit" name="_bring-start-booking"
                data-tip="<?php _e( 'Update order and start booking', 'bring-fraktguiden' ); ?>"
                class="button button-primary tips"><?php _e( 'Start', 'bring-fraktguiden' ); ?>
          &gt;</button>
      <?php } ?>
    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_parties( $order ) {
    ?>
    <div class="bring-form-field">
      <a class="bring-show-parties"
         href="#"><?php _e( 'Show Parties', 'bring-fraktguiden' ); ?></a>
    </div>
    <script type="text/javascript">
      (function () {
        jQuery( '.bring-show-parties' ).click( function ( evt ) {
          evt.preventDefault();
          jQuery( '.bring-booking-parties' ).toggle();
        } );
      })();
    </script>

    <div class="bring-booking-parties bring-form-field bring-flex-box"
         style="display:none">
      <div>
        <h3><?php _e( 'Sender Address', 'bring-fraktguiden' ); ?></h3>
        <?php self::render_address_table( Bring_Booking::get_sender_address( $order->order ) ); ?>
      </div>
      <div>
        <h3><?php _e( 'Recipient Address', 'bring-fraktguiden' ); ?></h3>
        <?php self::render_address_table( $order->get_recipient_address_formatted() ); ?>
      </div>
    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_packages( $order ) {
    $shipping_item_tip = __( 'Shipping item id', 'bring-fraktguiden' );
    ?>
    <table class="bring-booking-packages">
      <thead>
      <tr>
        <th title="<?php echo $shipping_item_tip; ?>"><?php _e( '#', 'bring-fraktguiden' ); ?></th>
        <th><?php _e( 'Product', 'bring-fraktguiden' ); ?></th>
        <th><?php _e( 'Width', 'bring-fraktguiden' ); ?> (cm)</th>
        <th><?php _e( 'Height', 'bring-fraktguiden' ); ?> (cm)</th>
        <th><?php _e( 'Length', 'bring-fraktguiden' ); ?> (cm)</th>
        <th><?php _e( 'Weight', 'bring-fraktguiden' ); ?> (kg)</th>
      </tr>
      </thead>
      <tbody>
      <?php
      foreach ( $order->get_packages_formatted( false, true ) as $key => $package ) { ?>
        <?php
        $shipping_item_id = $package['shipping_item_info']['item_id'];
        $service_data     = Fraktguiden_Helper::get_service_data_for_key( $package['shipping_item_info']['shipping_method']['service'] ); ?>
        <tr>
          <td title="<?php echo $shipping_item_tip; ?>">
            <?php echo $shipping_item_id; ?>
          </td>
          <td>
            <?php echo $service_data[Fraktguiden_Helper::get_option( 'service_name' )]; ?>
            <?php
            $pickup_point = $order->get_pickup_point_for_shipping_item( $shipping_item_id );
            if ( ! empty( $pickup_point ) ) {
              ?>
              <span class="tips"
                    data-tip="<?php echo str_replace( '|', '<br/>', $pickup_point['cached'] ); ?>">
                [<?php _e( 'Pickup Point', 'bring-fraktguiden' ) ?>]
              </span>
              <?php
            }
            ?>
          </td>
          <td>
            <?php echo $package['dimensions']['widthInCm']; ?>
          </td>
          <td>
            <?php echo $package['dimensions']['heightInCm']; ?>
          </td>
          <td>
            <?php echo $package['dimensions']['lengthInCm']; ?>
          </td>
          <td>
            <?php echo $package['weightInKg']; ?>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
    <?php
  }

  /**
   * @param string $label
   * @param string $value
   */
  static function render_table_row( $label, $value ) {
    ?>
    <tr>
      <td>
        <?php echo $label; ?>:
      </td>
      <td>
        <?php echo $value; ?>
      </td>
    </tr>
    <?php
  }

  /**
   * @param array $address
   */
  static function render_address_table( $address ) {
    ?>
    <table>
      <tbody>
      <?php
      self::render_table_row( __( 'Name', 'bring-fraktguiden' ), $address['name'] );
      self::render_table_row( __( 'Street Address 1', 'bring-fraktguiden' ), $address['addressLine'] );
      self::render_table_row( __( 'Street Address 2', 'bring-fraktguiden' ), $address['addressLine2'] );
      self::render_table_row( __( 'Postcode', 'bring-fraktguiden' ), $address['postalCode'] );
      self::render_table_row( __( 'City', 'bring-fraktguiden' ), $address['city'] );
      self::render_table_row( __( 'Country', 'bring-fraktguiden' ), $address['countryCode'] );
      if ( $address['reference'] ) {
        self::render_table_row( __( 'Reference', 'bring-fraktguiden' ), $address['reference'] );
      }
      if ( $address['additionalAddressInfo'] ) {
        self::render_table_row( __( 'Additional Address Info', 'bring-fraktguiden' ), $address['additionalAddressInfo'] );
      }
      ?>
      <tr>
        <td colspan="2">
          <h4><?php _e( 'Contact', 'bring-fraktguiden' ); ?></h4>
        </td>
      </tr>
      <?php
      self::render_table_row( __( 'Name', 'bring-fraktguiden' ), $address['contact']['name'] );
      self::render_table_row( __( 'Email', 'bring-fraktguiden' ), $address['contact']['email'] );
      self::render_table_row( __( 'Phone Number', 'bring-fraktguiden' ), $address['contact']['phoneNumber'] );
      ?>
      </tbody>
    </table>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_progress_tracker( $order ) {
    $step2  = Bring_Booking_Common_View::is_step2();
    $booked = $order->is_booked();
    ?>
    <div class="bring-progress-tracker bring-flex-box">

      <span
          class="<?php echo( ( ! $step2 && ! $booked ) ? 'bring-progress-active' : '' ); ?>">1 ) <?php _e( 'Start', 'bring-fraktguiden' ); ?></span>
      <span class="<?php echo( ( $step2 ) ? 'bring-progress-active' : '' ); ?>">2 ) <?php _e( 'Create and submit consignment', 'bring-fraktguiden' ); ?></span>
      <span
          class="<?php echo( ( $booked ) ? 'bring-progress-active' : '' ); ?>">3 ) <?php _e( 'Submitted', 'bring-fraktguiden' ); ?></span>
    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   * @return string
   */
  static function render_errors( $order ) {
    $errors = $order->get_booking_errors();
    ?>
    <div class="bring-info-box">
      <div>
        <?php
        $status = Bring_Booking_Common_View::get_booking_status_info( $order );
        echo Bring_Booking_Common_View::create_status_icon( $status ); ?>
        <h3><?php echo $status['text'] ?></h3>
      </div>

      <div class="bring-booking-errors">
        <div><?php _e( 'Previous booking request failed with the following errors:', 'bring-fraktguiden' ); ?></div>
        <ul>
          <?php foreach ( $errors as $error ) { ?>
            <li><?php echo $error; ?></li>
          <?php } ?>
        </ul>
        <div><?php _e( 'Press Start to try again', 'bring-fraktguiden' ); ?></div>
      </div>
    </div>
    <?php
  }

  static function redirect_page() {
    global $post_ID;
    $type = get_post_type();

    if ( $type == 'shop_order' && isset( $_POST['_bring-start-booking'] ) ) {
      $url = admin_url() . 'post.php?post=' . $post_ID . '&action=edit&booking_step=2';
      wp_redirect( $url );
      exit;
    }
  }

  /**
   * @param WC_Order $wc_order
   */
  static function send_booking( $wc_order ) {
    Bring_Booking::send_booking( $wc_order );
  }

}
