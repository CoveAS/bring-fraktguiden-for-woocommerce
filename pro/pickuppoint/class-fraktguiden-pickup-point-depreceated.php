<?php
class Fraktguiden_Pickup_Point_Depreceated {
  static function checkout_save_pickup_point( $order ) {
    $expire = time() - 300;
    if ( isset( $_COOKIE['_fraktguiden_pickup_point_id'] ) && isset( $_COOKIE['_fraktguiden_pickup_point_postcode'] ) && isset( $_COOKIE['_fraktguiden_pickup_point_info_cached'] ) ) {
      $order->checkout_update_pickup_point_data(
          $_COOKIE['_fraktguiden_pickup_point_id'],
          $_COOKIE['_fraktguiden_pickup_point_postcode'],
          $_COOKIE['_fraktguiden_pickup_point_info_cached']
      );

      // Unset cookies.
      // This does not work at the moment as headers has already been sent.
      // @todo: Find an earlier hook
      setcookie( '_fraktguiden_pickup_point_id', '', $expire );
      setcookie( '_fraktguiden_pickup_point_postcode', '', $expire );
      setcookie( '_fraktguiden_pickup_point_info_cached', '', $expire );
    }
  }
}
