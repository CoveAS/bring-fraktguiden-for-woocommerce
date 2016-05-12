// Self execute. Avoid leakage to global
(function () {
    var $ = jQuery;

    // Store the user's configuration.
    // The pickup point elements needs to be created each time the billing info, shipping info etc. changes.
    var user_selected = {
        postcode:        '',
        country:         '',
        pickup_point_id: ''
    };

    /**
     * Entry point
     *
     * Set up listeners for checkout events.
     */

    // Before review is updated.
    $( document.body ).on( 'update_checkout', function () {
        //console.log( 'update' );
    } );

    // After review is updated.
    $( document.body ).on( 'updated_checkout', function () {
        if ( has_bring_shipping_rates() ) {

            if ( get_selected_shipping_method() == 'servicepakke' ) {
                // Create pickup point html.
                add_pickup_point_html();
                // Load pickup points.
                load_pickup_points( get_selected_woo_postcode(), get_selected_woo_country() );
            }
        }
    } );

    // When Fraktguiden postcode is updated.
    $( document.body ).on( 'fraktguiden_updated_postcode', function ( evt, postcode, country ) {
        pickuppoint_select_elem().val( '' );
        user_selected.pickup_point_id = '';
        update_info( null );
        var code = $.trim( postcode );
        if ( code == '' ) return;
        delay( function () {
            load_pickup_points( code, country );
        }, 700 );
    } );

    // When the Woo billing post code changes.
    woo_billing_postcode_elem().keyup( function ( evt ) {
        pickuppoint_postcode_elem().val( this.value );
        $( document.body ).trigger( 'fraktguiden_updated_postcode', [pickuppoint_postcode_elem().val(), get_selected_woo_country()] );
    } );

    // When the Woo shipping post code changes
    woo_shipping_postcode_elem().keyup( function ( evt ) {
        pickuppoint_postcode_elem().val( this.value );
        $( document.body ).trigger( 'fraktguiden_updated_postcode', [pickuppoint_postcode_elem().val(), get_selected_woo_country()] );
    } );

    // When the billing country selector changes.
    woo_billing_country_elem().change( function () {
        $( document.body ).trigger( 'fraktguiden_updated_postcode', [pickuppoint_postcode_elem().val(), get_selected_woo_country()] );
    } );

    // When the shipping country selector changes.
    woo_shipping_country_elem().change( function () {
        $( document.body ).trigger( 'fraktguiden_updated_postcode', [pickuppoint_postcode_elem().val(), get_selected_woo_country()] );
    } );

    /**
     * Adds html to the review order table.
     */
    function add_pickup_point_html() {
        var html = [];
        var postcode = user_selected.postcode != '' ? user_selected.postcode : get_selected_woo_postcode();

        html.push( '<tr id="fraktguiden-pickuppoint">' );
        html.push( '    <th>Pickup point</th>' );
        html.push( '    <td>' );
        html.push( '         <label for="fraktguiden-pickuppoint-postcode">Postcode</label>' );
        html.push( '        <div><input type="text" name="fraktguiden_pickuppoint_postcode" id="fraktguiden-pickuppoint-postcode" class="input-text" value="' + postcode + '"/></div>' );
        html.push( '        <div>' );
        html.push( '            <select name="fraktguiden_pickuppoint_select" id="fraktguiden-pickuppoint-select">' );
        html.push( '                <option id="fraktguiden-pickuppoint-placeholder" value="">Select pickup point</option>' );
        html.push( '            </select>' );
        html.push( '        </div>' );
        html.push( '        <div id="fraktguiden-pickuppoint-info"><div id="fpp-name"></div><div id="fpp-address"></div><div id="fpp-postal"></div><div id="fpp-opening-hours"></div></div>' );
        html.push( '    </td>' );
        html.push( '</tr>' );
        $( 'tr.shipping' ).after( html.join( '' ) );

        // Add events to the postcode and inputs.
        pickuppoint_postcode_elem().keyup( function ( evt ) {
            $( document.body ).trigger( 'fraktguiden_updated_postcode', [this.value], get_selected_woo_country() );
        } );

        pickuppoint_select_elem().change( function () {
            var selected_option = $( this.options[this.selectedIndex] );
            update_info( selected_option.data( 'info' ) );
            user_selected.pickup_point_id = this.value;
        } );
    }

    /**
     * @param postcode {String}
     * @param country {String}
     */
    function load_pickup_points( postcode, country ) {

        user_selected.postcode = postcode;
        user_selected.country = country;

        $.ajax( {
            // @todo: should not load from root.
            url:        '/wp-content/plugins/bring-fraktguiden-for-woocommerce/pro/proxy.php?url=https://api.bring.com/pickuppoint/api/pickuppoint/' + country + '/postalCode/' + postcode + '.json',
            dataType:   'json',
            beforeSend: function ( xhr ) {

            },
            success:    function ( data, status ) {
                if ( ! data || ! data.pickupPoint ) {
                    // todo: handle no result.
                    return;
                }

                var pickup_point_selector = pickuppoint_select_elem();

                // Remove options from selector.
                pickup_point_selector.find( 'option[id!=fraktguiden-pickuppoint-placeholder]' ).remove();
                // Create new options.
                $.each( data.pickupPoint, function ( key, pickup_point ) {
                    var name = pickup_point.name;
                    var visiting_address = pickup_point.visitingAddress;
                    var visiting_postcode = pickup_point.visitingPostalCode;
                    var visiting_city = pickup_point.visitingCity;

                    var option = $( '<option>' );
                    option.text( name + ', ' + visiting_address + ', ' + visiting_postcode + ', ' + visiting_city );
                    option.attr( 'value', pickup_point.id );
                    option.data( 'info', pickup_point );
                    $( '#fraktguiden-pickuppoint-placeholder' ).after( option );

                } );

                // Set value in the selector.
                if ( user_selected.pickup_point_id ) {
                    pickup_point_selector.val( user_selected.pickup_point_id );
                }
            }
        } );
    }

    /**
     * Returns true if shipping methods has any bring shipping rates.
     *
     * @returns {boolean}
     */
    function has_bring_shipping_rates() {
        return $( '.shipping input[type=radio][value^=bring_fraktguiden].shipping_method' ).length > 0;
    }

    function get_selected_shipping_method() {
        var selected = $( '.shipping input[type=radio][value^=bring_fraktguiden].shipping_method:checked' );
        return selected.length > 0 ? selected.val().split( ':' )[1] : null;
    }

    function update_info( info ) {
        // var html = '<p>Valgt hentested</p><div>' + info.name + '<br/>' + info.visitingAddress + '<br/>' + info.visitingPostalCode + ' ' + info.visitingCity + '</div><div>' + get_opening_hours( info ) + '</div>';
        // $( '#fraktguiden-pickuppoint-info' ).html( html );
        if ( !info ) return;

        $( '#fpp-name' ).text( info.name );
        $( '#fpp-address' ).text( info.visitingAddress );
        $( '#fpp-postal' ).text( info.visitingPostalCode + ' ' + info.visitingCity );
        $( '#fpp-opening-hours' ).text( get_opening_hours( info ) );
    }

    function woo_billing_postcode_elem() {
        return $( '[name=billing_postcode]' );
    }

    function woo_shipping_postcode_elem() {
        return $( '[name=shipping_postcode]' );
    }

    function woo_billing_country_elem() {
        return $( '[name=billing_country]' );
    }

    function woo_shipping_country_elem() {
        return $( '[name=shipping_country]' );
    }

    function pickuppoint_postcode_elem() {
        return $( '#fraktguiden-pickuppoint-postcode' );
    }

    function pickuppoint_select_elem() {
        return $( '#fraktguiden-pickuppoint-select' );
    }

    function ship_to_different_address() {
        return $( '[name=ship_to_different_address]:checked' ).length > 0;
    }

    function get_selected_woo_postcode() {
        return ship_to_different_address() ? woo_shipping_postcode_elem().val() : woo_billing_postcode_elem().val()
    }

    function get_selected_woo_country() {
        return ship_to_different_address() ? woo_shipping_country_elem().val() : woo_billing_country_elem().val()
    }

    function get_wp_lang() {
        var lang = $( 'html' ).attr( 'lang' );
        if ( ! lang ) {
            lang = 'en-US';
        }
        return lang;
    }

    function get_opening_hours( pickup_point ) {
        var country = get_selected_woo_country();
        var lang = '';
        switch ( country ) {
            case 'DK':
                lang = 'Danish';
                break;
            case 'EN':
                lang = 'English';
                break;
            case 'FI':
                lang = 'Finish';
                break;
            case 'NO':
                lang = 'Norwegian';
                break;
            case 'SE':
                lang = 'Swedish';
                break;
            default:
                lang = 'English'
        }

        return pickup_point['openingHours' + lang];
    }

    var delay = (function () {
        var timer = 0;
        return function ( callback, ms ) {
            clearTimeout( timer );
            timer = setTimeout( callback, ms );
        };
    })();


})();
