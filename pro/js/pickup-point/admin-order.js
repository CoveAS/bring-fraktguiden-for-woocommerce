/* global _fraktguiden_pickup_point */
// Admin

(function () {

    // *************************************************************************
    // Setup

    var $ = jQuery;

    var mutation_observer = null;

    if ( $( '.order_data_column:nth-child(3) .address' ).find( 'p.none_set' ).length > 0 ) {
        alert( 'Fraktguiden: Shipping address is not set.' );
        return;
    }

    if ( ! (window._fraktguiden_pickup_point && window._fraktguiden_pickup_point.items) ) {
        return;
    }

    update_lines();

    init_dom_mutation_observer();

    init_event_listeners();

    // *************************************************************************
    // Functions

    function begin_edit_shipping( edit_line ) {
        var selector = get_shipping_method_selector( edit_line );
        if ( is_fraktguiden_selected( selector ) ) {
            $( document.body ).trigger( 'fraktguiden_shipping_selected', [edit_line] );
        }
    }

    function update_lines() {
        var items = window.window._fraktguiden_pickup_point.items;

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
            url:        _fraktguiden_pickup_point.ajaxurl,
            data:       {'action': 'fg_get_services'},
            dataType:   'json',
            beforeSend: function ( xhr ) {
            },
            success:    function ( data, status ) {
                var service_selector = $( '<select style="width:200px" name="_fraktguiden_services"/>' );
                for ( var key in data ) {
                    service_selector.append( '<option value="' + key + '">' + data[key] + '</option>' );
                }
                title_elem.after( service_selector );
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

    /**
     * WooCommerce order screen does not trigger a public 'order-items-saved' event.
     * Listen to when the loading mask element is removed.
     */
    function init_dom_mutation_observer() {
        // select the target node
        var target = document.querySelector( '#woocommerce-order-items' );
        // create an observer instance
        mutation_observer = new MutationObserver( function ( mutations ) {
            mutations.forEach( function ( mutation ) {
                var loader_removed = mutation.removedNodes.length == 1 && mutation.removedNodes[0].className == 'blockUI blockMsg blockElement';
                if ( loader_removed ) {
                    $( document.body ).trigger( 'fraktguiden_order_items_updated' );
                }
            } );
        } );

        var config = {attributes: true, childList: true, characterData: true};
        mutation_observer.observe( target, config );
    }

    /**
     * Set up listeners for changes in the UI
     */
    function init_event_listeners() {

        var shipping_line_items = $( '#order_shipping_line_items' );

        // When the order items box is reloaded
        $( document.body ).on( 'fraktguiden_order_items_updated', update_lines );

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
            begin_edit_shipping( $( this ).closest( 'tr' ) );
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
    }

})();