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

    if ( ! window._fraktguiden_order_items_data ) {
        return;
    }

    //print_pickup_point_to_view( _fraktguiden_pickup_point );

    // $('#order_shipping_line_items').find('.shipping' ).each(function(i, elem) {
    //
    //     var shipping_row = $(elem);
    //
    //     if (shipping_row.find('select[name^=shipping_method]').val().indexOf('bring_fraktguiden') > -1) {
    //         new Pickup_Point_Item(shipping_row);
    //     }
    //
    // });

    update_row_views();

    init_dom_mutation_observer();

    init_event_listeners();


    // *************************************************************************
    // Functions

    function begin_edit_shipping( edit_row ) {
        var selector = get_shipping_method_selector( edit_row );
        if ( is_fraktguiden_selected( selector ) ) {
            $( document.body ).trigger( 'fraktguiden_shipping_selected', [edit_row] );
        }
    }

    function update_row_views() {
        var items = window._fraktguiden_order_items_data;

        for ( var key in items ) {
            if ( ! items.hasOwnProperty( key ) ) {
                continue;
            }
            var item = items[key];

            var row = $( '[data-order_item_id=' + item.item_id + ']' );
            // if ( ! has_fraktguiden_servicepakke( row ) ) {
            //     return;
            // }

            var pickup_point = item.pickup_point;
            var name_elem = row.find( '.name .view' );
            var name = pickup_point.name;
            var visiting_address = pickup_point.visitingAddress;
            var visiting_postcode = pickup_point.visitingPostalCode;
            var visiting_city = pickup_point.visitingCity;
            var info_html = '<div><b>Pickup point</b>: <br/>' + name + '<br/>' + visiting_address + '<br/>' + visiting_postcode + ', ' + visiting_city + '</div>';

            name_elem.html( name_elem.text() + info_html );
        }
    }

    function create_pickup_point_ui( edit_row ) {
        var title_elem = get_shipping_method_title_elem( edit_row );
        title_elem.hide();

        var service_selector = $( '<select style="width:200px" name="_fraktguiden_services"/>' );
        service_selector.append( '<option>På posten</option>' );
        service_selector.append( '<option>I postkassen (A-Prioritet)</option>' );
        service_selector.append( '<option>I postkassen (B-Økonomi)</option>' );

        title_elem.after( service_selector );
    }

    function destroy_pickup_point_ui( edit_row ) {
        $('[name=_fraktguiden_services]', edit_row).remove();
        get_shipping_method_title_elem( edit_row ).show();
    }

    function get_shipping_method_selector( row ) {
        return row.find( 'select[name^=shipping_method]' );
    }

    function get_shipping_method_title_elem( edit_row ) {
        return edit_row.find( 'input[name^=shipping_method_title]' );
    }

    function is_fraktguiden_selected( select_elem ) {
        return $( select_elem ).val().indexOf( 'fraktguiden' ) > - 1;
    }

    function has_fraktguiden_servicepakke( shipping_row ) {
        return shipping_row.find( 'select[name^=shipping_method]' ).val().indexOf( 'bring_fraktguiden:servicepakke' ) > - 1;
    }

    /**
     * WooCommerce does not trigger a public 'order-items-saved' event.
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

    function init_event_listeners() {
        $( document.body ).on( 'fraktguiden_order_items_updated', update_row_views );

        $( document.body ).on( 'fraktguiden_shipping_selected', function ( evt, edit_row ) {
            console.log( 'on fraktguiden selected', edit_row );
            create_pickup_point_ui( edit_row );
        } );

        $( document.body ).on( 'fraktguiden_shipping_deselected', function ( evt, edit_row ) {
            console.log( 'on fraktguiden deselected', edit_row );
            destroy_pickup_point_ui( edit_row );
        } );

        var shipping_items = $( '#order_shipping_line_items' );

        shipping_items.on( 'click', 'tr.shipping a.edit-order-item', function () {
            begin_edit_shipping( $( this ).closest( 'tr' ) );
        } );

        shipping_items.on( 'change', 'select[name^=shipping_method]', function () {
            var edit_row = $( this ).parents( 'tr.shipping' );
            if ( is_fraktguiden_selected( $( this ) ) ) {
                $( document.body ).trigger( 'fraktguiden_shipping_selected', [edit_row] );
            }
            else {
                $( document.body ).trigger( 'fraktguiden_shipping_deselected', [edit_row] );
            }
        } );
    }

})();