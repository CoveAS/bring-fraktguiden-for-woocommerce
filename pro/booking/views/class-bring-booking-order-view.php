<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

add_action( 'wp_ajax_bring_update_packages',         'Bring_Booking_Order_View::ajax_update_packages' );
add_action( 'wp_ajax_nopriv_bring_update_packages',  'Bring_Booking_Order_View::ajax_update_packages' );
class Bring_Booking_Order_View {

  const TEXT_DOMAIN = Fraktguiden_Helper::TEXT_DOMAIN;

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
        <?php if ( $order->order->get_status() != 'completed' ) { ?>
          <div style="text-align:center;margin-bottom: 1em">
            <?php _e( 'Note: Order is not completed', 'bring-fraktguiden' ); ?>
          </div>
        <?php } ?>
      </div>
      <div>
        <h3 style="margin-top:0"><?php _e('Consignments', 'bring-fraktguiden'); ?></h3 style>
        <?php self::render_consignments( $order ); ?>
      </div>

    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_consignments( $order ) {
    $type = $order->get_consignment_type();
    ?>
    <div class="bring-consignments">
      <?php
      $consignments = $order->get_booking_consignments();
      foreach ( $consignments as $consignment ) {
        require dirname( __DIR__ ) .'/templates/consignment-table-'. $type .'.php';
      }
      ?>
    </div>
    <?php
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


    <?php

    $shipping_items = $order->get_fraktguiden_shipping_items();
    if ( empty( $shipping_items ) ) {
      return;
    }
    $shipping_item = reset( $shipping_items );
    $consignment = new Bring_Booking_Consignment_Request( $shipping_item );
    self::render_parties( $consignment );
    ?>
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
                data-tip="<?php _e( 'Start creating a label to ship this order with MyBring', 'bring-fraktguiden' ); ?>"
                class="button button-primary tips"><?php _e( 'Start booking', 'bring-fraktguiden' ); ?></button>
      <?php } ?>
    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_parties( $consignment ) {
    ?>
    <div class="bring-form-field">
      <a class="bring-show-parties button"
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
        <?php self::render_address_table( $consignment->get_sender_address() ); ?>
      </div>
      <div>
        <h3><?php _e( 'Recipient Address', 'bring-fraktguiden' ); ?></h3>
        <?php self::render_address_table( $consignment->get_recipient_address() ); ?>
      </div>
    </div>
    <?php
  }

  /**
   * @param Bring_WC_Order_Adapter $order
   */
  static function render_packages( $order ) {
    $shipping_item_tip = __( 'Shipping item id', 'bring-fraktguiden' );
    $all_services = Fraktguiden_Helper::get_all_services();
    $order_item_ids = array_keys( $order->get_fraktguiden_shipping_items() );
    ?>
    <form class="bring-booking-packages-form">
    <input type="hidden" id="bring_order_id" name="bring_order_id" value="<?php echo $order->order->get_id(); ?>">
    <table class="bring-booking-packages">
      <thead>
      <tr>
        <th title="<?php echo $shipping_item_tip; ?>"><?php _e( 'Order ID', 'bring-fraktguiden' ); ?></th>
        <th><?php _e( 'Product', 'bring-fraktguiden' ); ?></th>
        <th><?php _e( 'Width', 'bring-fraktguiden' ); ?> (cm)</th>
        <th><?php _e( 'Height', 'bring-fraktguiden' ); ?> (cm)</th>
        <th><?php _e( 'Length', 'bring-fraktguiden' ); ?> (cm)</th>
        <th><?php _e( 'Weight', 'bring-fraktguiden' ); ?> (kg)</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <?php
      // YO! This shit breaks NOW!
      //
      foreach ( $order->get_fraktguiden_shipping_items() as $item_id => $shipping_method ) {

      // 1. Create Booking Consignment
      $consignment = new Bring_Booking_Consignment_Request( $shipping_method );

      // 2. Get packages from that consignment
      foreach ( $consignment->create_packages( true ) as $key => $package ) { ?>
        <?php
        $shipping_item_id = $package['shipping_item_info']['item_id'];
        $key              = $package['shipping_item_info']['shipping_method']['service'];
        $service_data     = Fraktguiden_Helper::get_service_data_for_key( $key );
        $pickup_point     = $package['shipping_item_info']['shipping_method']['pickup_point_id'];
        ?>
        <tr>
          <td title="<?php echo $shipping_item_tip; ?>">
            <select class="order-item-id" name="order_item_id[]">
              <?php foreach ( $order_item_ids as $id ): ?>
                <?php if ( $id == $shipping_item_id ): ?>
                  <option value="<?php echo $id; ?>" selected="selected"><?php echo $id; ?></option>
                <?php else: ?>
                  <option value="<?php echo $id; ?>"><?php echo $id; ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
              <?php echo $service_data['ProductName']; ?>
              <?php if ( ! empty( $pickup_point ) ): ?>
                <span
                  class="tips"
                  data-tip="<?php echo str_replace( '|', '<br/>', $pickup_point ); ?>">
                   [<?php _e( 'Pickup point', 'bring-fraktguiden' ); ?>]
                </span>
              <?php endif;?>
          </td>
          <td>
            <input name="width[]" class="dimension" type="text" value="<?php echo $package['dimensions']['widthInCm']; ?>">
          </td>
          <td>
            <input name="height[]" class="dimension" type="text" value="<?php echo $package['dimensions']['heightInCm']; ?>">
          </td>
          <td>
            <input name="length[]" class="dimension" type="text" value="<?php echo $package['dimensions']['lengthInCm']; ?>">
          </td>
          <td>
            <input name="weight[]" class="dimension" type="text" value="<?php echo $package['weightInKg']; ?>">
          </td>
          <td align="right">
            <span class="button-link button-link-delete delete"><?php echo __( 'Delete', 'bring-fraktguiden' ); ?></span>
          </td>
        </tr>
      <?php } } ?>
        <tr class="bring-package-template" style="display: none">
          <td title="<?php echo $shipping_item_tip; ?>">
            <select class="order-item-id" name="order_item_id[]">
              <?php foreach ( $order_item_ids as $id ): ?>
                <option value="<?php echo $id; ?>"><?php echo $id; ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
              <?php echo $service_data['ProductName']; ?>
              <?php if ( ! empty( $pickup_point ) ): ?>
                <span
                  class="tips"
                  data-tip="<?php echo str_replace( '|', '<br/>', $pickup_point ); ?>"
                  >
                   [<?php _e( 'Pickup point', 'bring-fraktguiden' ); ?>]
                </span>
              <?php endif;?>
          </td>
          <td>
            <input name="width[]" class="dimension" type="text" value="0">
          </td>
          <td>
            <input name="height[]" class="dimension" type="text" value="0">
          </td>
          <td>
            <input name="length[]" class="dimension" type="text" value="0">
          </td>
          <td>
            <input name="weight[]" class="dimension" type="text" value="0">
          </td>
          <td align="right">
            <span class="button-link button-link-delete delete"><?php echo __( 'Delete', 'bring-fraktguiden' ); ?></span>
          </td>
        </tr>
        <tr>
          <td colspan="6"></td>
          <td align="right">
            <span class="button add"><?php echo __( 'Add', 'bring-fraktguiden' ); ?></span>
          </td>
      </tr>
      </tbody>
    </table>
    </form>
    <script type="text/javascript">
      jQuery( function( $ ) {


        /**
         * Debounce
         * @param  function callback
         * @param  int      timeout
         * @param  string   id
         * @return function
         */
        var _timers = {};
        var debounce = function( callback, timeout, id ) {
          return function() {
            if ( _timers[id] ) {
              clearTimeout( _timers[id] );
            }
            _timers[id] = setTimeout( callback, timeout );
          };
        };

        /**
         * Get val
         * Helper functino to quickly find an element in the row
         * @param  object row
         * @param  string name
         * @param  string default
         * @return string
         */
        var get_val = function( row, name, _default ) {
          var elem = row.find( '[name="'+ name +'[]"]' );
          if ( ! elem.length ) {
            return _default;
          }
          return elem.val();
        };

        /**
         * Ajax Update
         */
        var ajax_update = function() {
          var order_id = $( '#bring_order_id' ).val();
          if ( ! order_id ) {
            return;
          }
          var data = {
            action   : 'bring_update_packages',
            order_id : order_id,
            packages : []
          };
          $( '.bring-booking-packages tr:visible' ).each( function() {
            var row = $( this );
            var order_item_id = get_val( row, 'order_item_id' );
            if ( ! order_item_id ) {
              return;
            }
            data.packages.push( {
              order_item_id: order_item_id,
              service_id:    get_val( row, 'service_id' ),
              height:        get_val( row, 'height' ),
              length:        get_val( row, 'length' ),
              width:         get_val( row, 'width' ),
              weight:        get_val( row, 'weight' ),
            } );
          } );

          $.post( ajaxurl, data, function( result ) {
            console.log( data );
            console.log( 'Returned from AJAX:' );
            console.log( result );
          } );
        }

        /**
         * Delete row
         * Button handler
         */
        var delete_row = function() {
          $( this ).closest( 'tr' ).remove();
          debounce( ajax_update, 500, 'ajax_update' )();
        };

        /**
         * Hook row
         * For each row/tr run this function to hook buttons and changes
         * @param  object
         */
        var hook_row = function( row ) {
          row.find( '.delete' ).click( delete_row );
          row.find( '.service-id, .order-item-id, .dimension' ).on(
            'change keyup',
            debounce( ajax_update, 500, 'ajax_update' )
          );
        };

        /**
         * Fix pickup point id options
         * @param  object clone
         */
        var fix_pickup_point_id_options = function( clone ) {
          var input_elems = clone.find('[name^="pickup_point_id"]');
          input_elems.each( function () {
            var elem = $( this );
            var index = elem.closest( 'tr' ).index();
            var li_index = elem.parent().index();
            var name = 'pickup_point_id['+ ( index + 1 ) + ']';
            elem.attr( 'name', name );
            var id = 'pickup_point_id_' + ( index + 1 ) + '_' + li_index;
            elem.attr( 'id', id );
            elem.next().attr( 'for', id );
          } );
        };

        // Button handler for "Add"
        $( '.bring-booking-packages .add' ).click( function() {
          var clone = $( '.bring-package-template' ).clone();
          clone.removeClass( 'bring-package-template' );
          clone.insertBefore( '.bring-package-template' );
          fix_pickup_point_id_options( clone );
          clone.show();
          // Hook the new row
          hook_row( clone );
          // Trigger an ajax update
          debounce( ajax_update, 500, 'ajax_update' )();
        } );

        // Hook all rows
        $( '.bring-booking-packages tr' ).each( function() {
          hook_row( $( this ) );
        } );

      } )
    </script>
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
      <span class="<?php echo( ( ! $step2 && ! $booked ) ? 'bring-progress-active' : '' ); ?>">
        1. <?php _e( 'Create a new booking', 'bring-fraktguiden' ); ?>
      </span>
      <span class="<?php echo( ( $step2 ) ? 'bring-progress-active' : '' ); ?>">
        2. <?php _e( 'Confirm and submit consignment', 'bring-fraktguiden' ); ?>
      </span>
      <span class="<?php echo( ( $booked ) ? 'bring-progress-active' : '' ); ?>">
        3. <?php _e( 'Sucessfully booked', 'bring-fraktguiden' ); ?>
      </span>
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

  static function ajax_update_packages() {
    if ( ! isset( $_POST['order_id'] ) ) {
      die( '{ "error": "Missing order id" }' );
    }
    if ( ! isset( $_POST['packages'] ) || ! is_array( $_POST['packages'] ) || empty( $_POST['packages'] ) ) {
      die( '{ "error": "Empty packages" }' );
    }
    $packages = $_POST['packages'];
    $expected_fields = [
      'height', 'length', 'order_item_id', 'weight', 'width',
    ];
    foreach ( $packages as $package ) {
      foreach ( $expected_fields as $key ) {
        if ( ! isset( $package[ $key ] ) ) {
          die( '{ "error": "Missing package field '. $key .'" }' );
        }
        if ( ! is_string( $package[ $key ] ) ) {
          die( '{ "error": "Package field is not a string '. $key .'" }' );
        }
      }
    }
    $order_id = $_POST[ 'order_id' ];
    if ( ! $order_id ) {
      die( 'testing');
    }

    $wc_order = new WC_Order( $order_id );
    $order    = new Bring_WC_Order_Adapter( $wc_order );
    $shipping_methods = $order->order->get_shipping_methods();
    $existing = [];
    // Get the existing packages
    foreach ( $shipping_methods as $item_id => $method ) {
      $meta_packages = wc_get_order_item_meta( $item_id, '_fraktguiden_packages', true );
      $existing[ $item_id ] = $meta_packages;
    }

    $fields = [ 'weightInGrams', 'length', 'width', 'height' ];
    $new_packages = [];
    // Create the package array that bring needs
    // with eg. [ length0 = 10, length1 = 10 ] etc..
    foreach ( $packages as $index => $package ) {
      $package['weightInGrams'] = $package['weight'] * 1000;
      if ( ! isset( $new_packages[ $package['order_item_id'] ] ) ) {
        $new_packages[ $package['order_item_id'] ] = [];
      }
      foreach ( $fields as $field ) {
        // Assign the field + number as the key
        // eg height0, height1, height2 etc...
        $new_packages[ $package['order_item_id'] ][ $field . $index ] = $package[ $field ];
      }
    }

    // Save the new fields
    foreach ( $new_packages as $item_id => $new_package ) {
      wc_update_order_item_meta( $item_id, '_fraktguiden_packages', $new_package );
    }

    // @TODO: with multiple shipping items, remove the metadata for items no longer used
    die;
  }
}
