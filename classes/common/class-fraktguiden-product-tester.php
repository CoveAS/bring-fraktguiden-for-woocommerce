<?php

/**
 * Fraktguiden Product Tester
 */
class Fraktguiden_Product_Tester
{
  static function setup() {
    add_action( 'add_meta_boxes', __CLASS__.'::add_events_metaboxes' );
  }

  static function add_events_metaboxes( $post_type ) {
    if ( 'product' != $post_type ) {
      return;
    }
    add_meta_box(
      'bring_fraktguiden_product_tester',
      'Bring Fraktguiden Product Tester',
      __CLASS__.'::layout_of_meta_box_content'
    );
  }


  static function layout_of_meta_box_content() {

  ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr;">

      <div class="test-plane">
        <h4><?php _e( 'Can this product be shipped with bring?', 'bring-fraktguiden' ); ?></h4>
        <p>Here's some information about your product</p>
        <?php self::render(); ?>
      </div>

      <div class="test-plane">
        <h4>Run the test</h4>
        <p>You can check if Bring accepts this product for shipping</p>
        <?php printf( '<a class="button button-primary button-large" href="%s">%s</a>', '#test', 'Test bring' ); ?>
      </div>
    </div>

  <?php

  }


  static function render() {

    $post = get_post();
    // $cart_id = $this->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
    $product = wc_get_product( $post->ID );
    $package = [
      'destination' => [
        'country' => 'no',
        'state' => '',
        'postcode' => '4848',
      ],
      'contents' => [
        [
          'key'          => 'NAN',
          'product_id'   => $post->ID,
          'variation_id' => null,
          'variation'    => null,
          'quantity'     => 1,
          'data'         => $product,
        ]
      ]
    ];


    $dims = $product->get_length() && $product->get_width() && $product->get_height();
    if ( ! $dims ) {
      echo "No dimensions.\n";
    }
    $weight = $product->get_weight();
    if ( ! $weight ) {
      echo "No weight.\n";
    }
    if ( ! $weight && ! $dims ) {
      echo "The product needs to have either dimensions or weight specified.\n";
      return;
    }

    $zone = wc_get_shipping_zone( $package );

    if ( ! $zone ) {
      echo "There are no shipping zone matches.\n";
      return;
    }
    $bring = false;
    $methods = $zone->get_shipping_methods();
    foreach ( $methods as $method ) {
      if ( get_class( $method ) == 'WC_Shipping_Method_Bring_Pro' ) {
        $bring = $method;
        break;
      }
      if ( get_class( $method ) == 'WC_Shipping_Method_Bring' ) {
        $bring = $method;
        break;
      }
    }
    if ( ! $bring ) {
      echo "Bring is not enabled for the current Zone.\n";
      return;
    }

    $rates = $bring->get_rates_for_package( $package );

    $messages = $bring->get_trace_messages();
    foreach ( $messages as $message ) {
      printf( '<p>%s</p>', $message );
    }

    if ( empty( $rates ) ) {
      echo "<p>Bring did not return any shipping rates.</p>\n";
      return;
    }
    echo "<ul>";
    foreach ( $rates as $rate_id => $rate ) {
      printf( '<li><strong>%s</strong> <span>%s</span></li>', $rate->get_label(), $rate->get_cost() );
    }
    echo "</ul>";
  }
}
