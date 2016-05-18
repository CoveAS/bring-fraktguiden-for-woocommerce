// Checkout
(function () {
    var $ = jQuery;

    // *************************************************************************
    // Setup

    // Store the user's configuration.
    // The pickup point elements needs to be created each time the billing info,
    // shipping info etc. changes.
    var user_selected = {
        postcode:        '',
        country:         '',
        pickup_point_id: ''
    };

    init_event_listeners();

    // *************************************************************************
    // Functions

    /**
     * Adds html to the review order table.
     */
    function add_pickup_point_html() {
        var html = [];
        var postcode = user_selected.postcode != '' ? user_selected.postcode : get_shipping_postcode();

        html.push( '<tr id="fraktguiden-pickup-point">' );
        html.push( '    <th>Pickup point</th>' );
        html.push( '    <td>' );
        html.push( '         <label for="fraktguiden-pickup-point-postcode">Postcode</label>' );
        html.push( '        <div><input type="text" name="_fraktguiden_pickup_point_postcode" id="fraktguiden-pickup-point-postcode" class="input-text" value="' + postcode + '"/></div>' );
        html.push( '        <div>' );
        html.push( '            <select name="_fraktguiden_pickup_point_id" id="fraktguiden-pickup-point-select">' );
        html.push( '                <option id="fraktguiden-pickup-point-placeholder" value="">Select pickup point</option>' );
        html.push( '            </select>' );
        html.push( '        </div>' );
        html.push( '        <div id="fraktguiden-pickup-point-info"><div id="fpp-name"></div><div id="fpp-address"></div><div id="fpp-postal"></div><div id="fpp-opening-hours"></div></div>' );
        html.push( '    </td>' );
        html.push( '</tr>' );
        $( 'tr.shipping' ).after( html.join( '' ) );

        // Add events to the postcode and inputs.
        pickup_point_postcode_elem().keyup( function ( evt ) {
            $( document.body ).trigger( 'fraktguiden_postcode_updated', [this.value], get_shipping_country() );
        } );

        pickup_point_select_elem().change( function () {
            var selected_option = $( this.options[this.selectedIndex] );
            update_info( selected_option.data( 'info' ) );
            user_selected.pickup_point_id = this.value;
        } );
    }

    /**
     * Loads pickup points.
     *
     * @param postcode {String}
     * @param country {String}
     * @param callback {Function}
     */
    function load_pickup_points( postcode, country, callback ) {

        user_selected.postcode = postcode;
        user_selected.country = country;

        $.ajax( {
            url : _fraktguiden_pickup_point.ajaxurl,
            data: {'action': 'fg_get_pickup_point', 'country': country, 'postcode': postcode},
            dataType:   'json',
            beforeSend: function ( xhr ) {
            },
            success:    callback
        } );
    }

    function cb( data, status ) {
        if ( ! data || ! data.pickupPoint ) {
            // todo: handle no result.
            return;
        }

        var pickup_point_selector = pickup_point_select_elem();

        // Remove options from selector.
        pickup_point_selector.find( 'option[id!=fraktguiden-pickup-point-placeholder]' ).remove();
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
            $( '#fraktguiden-pickup-point-placeholder' ).after( option );

        } );

        // Set value in the selector.
        if ( user_selected.pickup_point_id ) {
            pickup_point_selector.val( user_selected.pickup_point_id );
        }
    }

    function init_event_listeners() {

        // After the review has been updated.
        $( document.body ).on( 'updated_checkout', function () {
            if ( has_bring_shipping_rates() ) {
                if ( get_selected_shipping_method() == 'servicepakke' ) {
                    // Create pickup point html.
                    add_pickup_point_html();
                    // Load pickup points.
                    $( document.body ).trigger( 'fraktguiden_postcode_updated', [get_shipping_postcode(), get_shipping_country()] );
                }
            }
        } );

        // When Fraktguiden postcode is updated.
        $( document.body ).on( 'fraktguiden_postcode_updated', function ( evt, postcode, country ) {
            pickup_point_select_elem().val( '' );
            user_selected.pickup_point_id = '';
            update_info( null );
            var code = $.trim( postcode );
            if ( code == '' ) return;
            delay( function () {
                load_pickup_points( code, country, cb );
            }, 700 );
        } );

        // When the Woo billing post code changes.
        woo_billing_postcode_elem().keyup( function ( evt ) {
            pickup_point_postcode_elem().val( this.value );
            $( document.body ).trigger( 'fraktguiden_postcode_updated', [pickup_point_postcode_elem().val(), get_shipping_country()] );
        } );

        // When the Woo shipping post code changes
        woo_shipping_postcode_elem().keyup( function ( evt ) {
            pickup_point_postcode_elem().val( this.value );
            $( document.body ).trigger( 'fraktguiden_postcode_updated', [pickup_point_postcode_elem().val(), get_shipping_country()] );
        } );

        // When the billing country selector changes.
        woo_billing_country_elem().change( function () {
            $( document.body ).trigger( 'fraktguiden_postcode_updated', [pickup_point_postcode_elem().val(), get_shipping_country()] );
        } );

        // When the shipping country selector changes.
        woo_shipping_country_elem().change( function () {
            $( document.body ).trigger( 'fraktguiden_postcode_updated', [pickup_point_postcode_elem().val(), get_shipping_country()] );
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
        if ( ! info ) return;

        $( '#fpp-name' ).text( info.name );
        $( '#fpp-address' ).text( info.visitingAddress );
        $( '#fpp-postal' ).text( info.visitingPostalCode + ' ' + info.visitingCity );
        $( '#fpp-opening-hours' ).text( get_opening_hours_from_pickup_point( info, get_shipping_country() ) );
    }


    /**
     * @returns {boolean}
     */
    function ship_to_different_address() {
        return $( '[name=ship_to_different_address]:checked' ).length > 0;
    }

    /**
     * @returns {String}
     */
    function get_shipping_postcode() {
        return ship_to_different_address() ? woo_shipping_postcode_elem().val() : woo_billing_postcode_elem().val()
    }

    /**
     * @returns {String}
     */
    function get_shipping_country() {
        return ship_to_different_address() ? woo_shipping_country_elem().val() : woo_billing_country_elem().val()
    }

    function get_wp_lang() {
        var lang = $( 'html' ).attr( 'lang' );
        if ( ! lang ) {
            lang = 'en-US';
        }
        return lang;
    }

    /**
     * @param {Object} pickup_point
     * @param {String} country
     * @returns {String}
     */
    function get_opening_hours_from_pickup_point( pickup_point, country ) {
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

    /**
     * @returns {jQuery}
     */
    function woo_billing_postcode_elem() {
        return $( '[name=billing_postcode]' );
    }

    /**
     * @returns {jQuery}
     */
    function woo_shipping_postcode_elem() {
        return $( '[name=shipping_postcode]' );
    }

    /**
     * @returns {jQuery}
     */
    function woo_billing_country_elem() {
        return $( '[name=billing_country]' );
    }

    /**
     * @returns {jQuery}
     */
    function woo_shipping_country_elem() {
        return $( '[name=shipping_country]' );
    }

    /**
     * @returns {jQuery}
     */
    function pickup_point_postcode_elem() {
        return $( '#fraktguiden-pickup-point-postcode' );
    }

    /**
     * @returns {jQuery}
     */
    function pickup_point_select_elem() {
        return $( '#fraktguiden-pickup-point-select' );
    }


    var delay = (function () {
        var timer = 0;
        return function ( callback, ms ) {
            clearTimeout( timer );
            timer = setTimeout( callback, ms );
        };
    })();


})();
