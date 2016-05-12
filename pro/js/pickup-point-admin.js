/* global _fraktguiden_pickup_point */

// Admin
(function () {
    var $ = jQuery;

    // *************************************************************************
    // Setup

    // Listen for click on the edit-shipping-order-line button
    $( '#woocommerce-order-items' ).on( 'click', 'tr.shipping a.edit-order-item', on_edit_shipping );

    print_pickup_point_to_view(_fraktguiden_pickup_point);

    // *************************************************************************
    // Functions

    function print_pickup_point_to_view(data) {
        if ( ! (data && data.pickupPoint && data.pickupPoint[0]) ) return;

        var pickup_point = data.pickupPoint[0];

        $( '#order_shipping_line_items' ).find( '.shipping' ).each( function ( i, elem ) {
            var name_elem = $( elem ).find( '.name .view' );

            var name = pickup_point.name;
            var visiting_address = pickup_point.visitingAddress;
            var visiting_postcode = pickup_point.visitingPostalCode;
            var visiting_city = pickup_point.visitingCity;

            var pickup_point_info = '<div>Pickup point: ' + name + ', ' + visiting_address + ', ' + visiting_postcode + ', ' + visiting_city + '</div>';

            name_elem.html( name_elem.text() + pickup_point_info )
        } )
    }

    function on_edit_shipping() {
        var edit_row = $( this ).closest( 'tr' );
        hide_original_options( edit_row );

        get_shipping_method_title_elem(edit_row).after('<select style="width:200px"><option>1</option></select>');



    }

    function hide_original_options( edit_row ) {
        get_shipping_method_title_elem( edit_row ).hide();
    }

    function get_shipping_method_title_elem( edit_row ) {
        return edit_row.find( 'input[name^=shipping_method_title]' );
    }

})();