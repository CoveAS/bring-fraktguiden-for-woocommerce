/* global _fraktguiden_pickup_point */
// Admin

(function () {

    // *************************************************************************
    // Setup

    var $ = jQuery;

    var ajax_url = _fraktguiden_pickup_point.ajaxurl;

    // Check if order has a shipping address.
    // if ( $( '.order_data_column:nth-child(3) .address' ).find( 'p.none_set' ).length > 0 ) {
    //     alert( 'Fraktguiden: Shipping address is not set.' );
    //     return;
    // }

    update_shipping_item_lines();

    init_event_listeners();

    // *************************************************************************
    // Functions

    /**
     * Adds pickup point info to shipping items.
     */
    function update_shipping_item_lines() {
        var items = window._fraktguiden_pickup_point.items;

        for ( var key in items ) {
            if ( ! items.hasOwnProperty( key ) ) {
                continue;
            }
            var item = items[key];

            var line_elem = $( '[data-order_item_id=' + item.item_id + ']' );

            var pickup_point = item.info;
            var name_elem = line_elem.find( '.name .view' );
            var name = pickup_point.name;
            var visiting_address = pickup_point.visitingAddress;
            var visiting_postcode = pickup_point.visitingPostalCode;
            var visiting_city = pickup_point.visitingCity;
            var info_html = '<div><b>Pickup point</b>: <br/>' + name + '<br/>' + visiting_address + '<br/>' + visiting_postcode + ', ' + visiting_city + '</div>';

            console.log( item.postcode );

            name_elem.html( name_elem.text() + info_html );
        }
    }

    function create_pickup_point_ui( edit_line ) {
        var title_elem = get_shipping_method_title_elem( edit_line );
        title_elem.hide();

        $.ajax( {
            url:        ajax_url,
            data:       {'action': 'fg_get_services'},
            dataType:   'json',
            beforeSend: function ( xhr ) {
            },
            success:    function ( data, status ) {
                var service_selector = $( '<select style="width:200px" name="_fraktguiden_services"/>' );
                for ( var key in data ) {
                    service_selector.append( '<option value="' + key + '">' + data[key] + '</option>' );
                }

                var service = get_fraktguiden_service_from_shipping_selector( edit_line );
                service_selector.val( service );

                //var postcode_elem = $( '<input type="text" name="_pickup_point_postcode" value="' + _fraktguiden_pickup_point.postcode + '" />' );

                title_elem.after( service_selector );
                //service_selector.after( postcode_elem );
            }
        } );
    }

    function destroy_pickup_point_ui( edit_line ) {
        $( '[name=_fraktguiden_services]', edit_line ).remove();
        get_shipping_method_title_elem( edit_line ).show();
    }

    function get_shipping_method_selector( edit_line ) {
        return edit_line.find( 'select[name^=shipping_method]' );
    }

    function get_shipping_method_title_elem( edit_line ) {
        return edit_line.find( 'input[name^=shipping_method_title]' );
    }

    function is_fraktguiden_selected( shipping_selector ) {
        return shipping_selector.val().indexOf( 'fraktguiden' ) > - 1;
    }

    function get_fraktguiden_service_from_shipping_selector( edit_line ) {
        var shipping_selector = get_shipping_method_selector( edit_line );
        var parts = shipping_selector.val().split( ':' );
        return parts[1] ? parts[1].toUpperCase() : 'SERVICEPAKKE';
    }

    /**
     * Set up listeners for changes in the UI
     */
    function init_event_listeners() {
        init_order_items_updated_listener();

        var shipping_line_items = $( '#order_shipping_line_items' );

        // When the order items box is reloaded
        $( document.body ).on( 'fraktguiden_order_items_updated', update_shipping_item_lines );

        // When Fraktguiden is selected in the shipping method selector.
        $( document.body ).on( 'fraktguiden_shipping_selected', function ( evt, edit_line ) {
            console.log( 'on fraktguiden selected', edit_line );
            create_pickup_point_ui( edit_line );
        } );

        // When Fraktguiden is de selected in the shipping method selector.
        $( document.body ).on( 'fraktguiden_shipping_deselected', function ( evt, edit_line ) {
            console.log( 'on fraktguiden deselected', edit_line );
            destroy_pickup_point_ui( edit_line );
        } );

        // When the edit-shipping-item button is clicked.
        shipping_line_items.on( 'click', 'tr.shipping a.edit-order-item', function () {
            var edit_line = $( this ).closest( 'tr' );
            var shipping_selector = get_shipping_method_selector( edit_line );
            if ( is_fraktguiden_selected( shipping_selector ) ) {
                $( document.body ).trigger( 'fraktguiden_shipping_selected', [edit_line] );
            }
        } );

        // Listen for changes in the shipping method selector
        shipping_line_items.on( 'change', 'select[name^=shipping_method]', function () {
            var edit_line = $( this ).parents( 'tr.shipping' );
            if ( is_fraktguiden_selected( $( this ) ) ) {
                $( document.body ).trigger( 'fraktguiden_shipping_selected', [edit_line] );
            }
            else {
                $( document.body ).trigger( 'fraktguiden_shipping_deselected', [edit_line] );
            }
        } );

        // Listen for changes in the Fraktguiden service selector
        shipping_line_items.on( 'change', 'select[name^=_fraktguiden_services]', function () {
            var edit_line = $( this ).parents( 'tr.shipping' );
            if ($( this ).val() == 'SERVICEPAKKE') {
                $( document.body ).trigger( 'fraktguiden_servicepakke_selected', [edit_line] );
                console.log('service pakkke selected');
            }
            else {
                $( document.body ).trigger( 'fraktguiden_servicepakke_deselected', [edit_line] );
                console.log('service pakkke deselected');
            }

        } );
    }

    /**
     * WooCommerce order screen does not trigger a public 'order-items-saved' event.
     * Listen to when the loading mask element is removed.
     */
    function init_order_items_updated_listener() {
        // select the target node
        var target = document.querySelector( '#woocommerce-order-items' );
        // create an observer instance
        var mutation_observer = new MutationObserver( function ( mutations ) {
            mutations.forEach( function ( mutation ) {
                var loader_removed = mutation.removedNodes.length == 1 && mutation.removedNodes[0].className == 'blockUI blockMsg blockElement';
                if ( loader_removed ) {
                    console.log( 'Order items updated' );
                    $( document.body ).trigger( 'fraktguiden_order_items_updated' );
                }
            } );
        } );

        var config = {attributes: true, childList: true, characterData: true};
        mutation_observer.observe( target, config );
    }


    // *************************************************************************
    // Prototypes
    // var Shipping_Lines_Manager = {
    //     shipping_lines: [],
    //
    //     add: function(line) {
    //
    //     }
    // };
    //
    // function Shipping_Line( edit_line ) {
    //     this.edit_line = edit_line;
    // }
    // Shipping_Line.prototype = {};
    //
    // $( '#order_shipping_line_items .shipping' ).each( function ( i, elem ) {
    //
    //     Shipping_Lines_Manager.add(elem);
    //     console.log(elem)
    // } );
})();