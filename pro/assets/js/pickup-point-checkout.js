/* global _fraktguiden_data */

/**
 * Checkout
 */
(function () {
    var $ = jQuery;

    var lang = _fraktguiden_data.i18n;
    var checkout_div;

    // *************************************************************************
    // Events

    function events() {
        return $( document.body );
    }

    events.CHECKOUT_REVIEW_UPDATED = 'updated_checkout';
    events.POST_CODE_UPDATED = 'postcode_updated.bring';
    events.PICKUP_POINT_CHANGED = 'pickup_point_updated.bring';


    // *************************************************************************
    // Init

    // Create an object where the user's choices can be stored each time the
    // checkout review reloads.
    var user_selected = {
        postcode: '',
        country: '',
        pickup_point_id: ''
    };

    // Add event handlers to the document.
    add_order_review_event_handlers();

    if ( has_klarna_widget() ) {
        // Get the post code
        user_selected.postcode = $( '.bring-enter-postcode .input-text' ).val();
        // Update the cart
        events().trigger( events.CHECKOUT_REVIEW_UPDATED );
        // Determine the checkout div
        checkout_div = $( '#klarna-checkout-cart' ).parent();
        // Add change event handler for Fraktguiden shipping rates.
        $( 'body' ).on( 'change', 'input[type=radio][value^=bring_fraktguiden].shipping_method', function () {
            // Hide the pickup point html if service pakke is not selected
            if ( ! is_servicepakke_selected() ) {
                $( '.fraktguiden-pickup-point' ).hide();
                return;
            }
        } );

        if ( ! has_bring_shipping_rates() ) {
            // Hide the checkout itself
            $( '.klarna_checkout' ).hide();
        }
    }

    function post_kco_delivery_post_code( post_code ) {
        if ( !is_servicepakke_selected() ) {
            $( '.fraktguiden-pickup-point' ).hide();
        }
        $.post(
            kcoAjax.ajaxurl,
            {
                action: 'kco_iframe_shipping_address_change_cb',
                postal_code: post_code,
                // country: 'NO',
                nonce: kcoAjax.klarna_checkout_nonce
            },
            function( response ) {
                // Copy paste from klarna code
                $( '#klarna-checkout-widget' ).html( response.data.widget_html );
                check_shipping_rate_selection();
                if ( window._klarnaCheckout ) {
                    // Reload the klarna payment window
                    window._klarnaCheckout( function ( api ) {
                        api.resume();
                    } );
                }
            }
        );
    }

    // *************************************************************************
    // Functions

    /**
     * Adds html to the review order table.
     */
    function add_pickup_point_html() {
        if ( $( '.fraktguiden-pickup-point' ).length > 0 ) {
            // Select shipping method must either not exist or not be disabled
            if ( ! $( '.select-shipping-method' ).length || ! $( '.select-shipping-method.disabled' ).length  ) {
                $( '.fraktguiden-pickup-point' ).show();
            }
            return;
        }
        var html = [];
        var postcode = user_selected.postcode != '' ? user_selected.postcode : get_shipping_postcode();

        html.push( '<tr class="fraktguiden-pickup-point">' );
        html.push( '    <th>' + lang.PICKUP_POINT + '</th>' );
        html.push( '    <td>' );
        html.push( '         <label for="fraktguiden-pickup-point-postcode">' + lang.POSTCODE + '</label>' );
        html.push( '        <div><input type="text" name="_fraktguiden_pickup_point_postcode" class="input-text fraktguiden-pickup-point-postcode" value="' + postcode + '"/></div>' );
        html.push( '        <div>' );
        html.push( '            <select name="_fraktguiden_pickup_point_id" class="fraktguiden-pickup-point-select">' );
        html.push( '                <option value="">--- ' + lang.ADD_POSTCODE + ' ---</option>' );
        html.push( '            </select>' );
        html.push( '        </div>' );
        html.push( '        <div class="fraktguiden-selected-text"></div>' );
        html.push( '        <div class="fraktguiden-pickup-point-display"></div>' );
        html.push( '        <input type="hidden" name="_fraktguiden_pickup_point_info_cached"/>' );
        html.push( '    </td>' );
        html.push( '</tr>' );

        if ( has_klarna_widget() ) {
            if ( !is_servicepakke_selected() ) {
                return;
            }
            $( '#kco-page-shipping' ).after( html.join( '' ) );
        }
        else {
            $( 'tr.shipping' ).after( html.join( '' ) );
        }
    }

    /**
     * Updates the pickup point selector.
     *
     * @param postcode {String}
     * @param country {String}
     */
    function update_pickup_point_selector( postcode, country ) {

        user_selected.postcode = postcode;
        user_selected.country = country;

        var ajax_options = {
            url: _fraktguiden_data.ajaxurl,
            before_send: function () {
                var pickup_point_selector = pickup_point_select_elem();
                pickup_point_selector.find( 'option' ).remove();
                pickup_point_selector.append( '<option value="">' + lang.LOADING_TEXT + '</option>' );
            },

            success: function ( data, status ) {
                //pickup_point_postcode_elem().focus();

                if ( !data || !data.pickupPoint ) {
                    // todo: handle no result.
                    return;
                }

                var pickup_point_selector = pickup_point_select_elem();

                // Remove options from selector.
                pickup_point_selector.find( 'option' ).remove();
                // Add the placeholder.
                pickup_point_selector.append( '<option value="">--- ' + lang.PICKUP_POINT_PLACEHOLDER + ' ---</option>' );

                // Create new options.
                $.each( data.pickupPoint, function ( key, pickup_point ) {
                    var name = pickup_point.name;
                    var visiting_address = pickup_point.visitingAddress;
                    var visiting_postcode = pickup_point.visitingPostalCode;
                    var visiting_city = pickup_point.visitingCity;

                    var option = $( '<option>' );
                    option.text( name + ', ' + visiting_address + ', ' + visiting_postcode + ' ' + visiting_city );
                    option.attr( 'value', pickup_point.id );
                    option.data( 'pickup_point', pickup_point );

                    pickup_point_selector.append( option );
                } );

                // Set value in the selector.
                if ( user_selected.pickup_point_id ) {
                    pickup_point_selector.val( user_selected.pickup_point_id );
                    events().trigger( events.PICKUP_POINT_CHANGED, [pickup_point_selector.get( 0 )] );
                }
                else {

                    pickup_point_select_elem().prop( 'selectedIndex', 0 );
                }
                pickup_point_select_elem().show();
            }
        };

        Bring_Common.load_pickup_points( country, postcode, ajax_options );
    }

    function check_shipping_rate_selection() {
        if ( has_bring_shipping_rates() ) {
            if ( is_servicepakke_selected() ) {
                // Create pickup point html.
                add_pickup_point_html();

                user_selected.postcode = user_selected.postcode ? user_selected.postcode : get_shipping_postcode();
                user_selected.country = user_selected.country ? user_selected.country : get_shipping_country();
                events().trigger( events.POST_CODE_UPDATED, [user_selected.postcode, user_selected.country] );
            }
            if ( has_klarna_widget() ) {
                if ( is_servicepakke_selected() && !pickup_point_select_elem().val() ) {
                    $( '.klarna_checkout' ).hide();
                }
                else{
                    $( '.klarna_checkout' ).show();
                }
            }
        }
    }

    /**
     * Add event handlers to the document.
     */
    function add_order_review_event_handlers() {

        // Listen for global ajax success events and find ajax success from Klarna's requests.
        if ( has_klarna_widget() ) {
            // Hook for ajax success.
            $( document ).ajaxSuccess( function ( event, xhr, settings ) {
                var data = settings.data;
                if ( data && (data.indexOf( 'action=kco_' ) > -1 || data.indexOf( 'action=klarna_' )) > -1 ) {
                    events().trigger( events.CHECKOUT_REVIEW_UPDATED );
                }
            } );
        }

        // Each time the order review box is updated.
        events().on( events.CHECKOUT_REVIEW_UPDATED, function () {
            if ( has_klarna_widget() ) {
                $( '.bring-enter-postcode form' ).submit( function( e ) {
                    e.preventDefault();
                    $( this ).addClass( 'loading' );
                    $( this ).find( 'input' ).prop( 'disabled', true );
                    post_kco_delivery_post_code( $( this ). find( '.input-text' ).val() );
                } );
            }
            check_shipping_rate_selection();
        } );

        // Each time Pickup point postcode is updated.
        events().on( events.POST_CODE_UPDATED, function ( evt, postcode, country ) {

            var id = Bring_Common.read_cookie( '_fraktguiden_pickup_point_id' );

            update_cookies();

            update_display_info( null );

            var code = $.trim( postcode );
            // Return if post code char length is lesser than 3 chars.
            if ( code.length < 3 ) return;

            if ( ! has_klarna_widget() ) {
                // When klarna is used then the pickup points are provided with the ajax for the widget html
                pickup_point_select_elem().val( id );
                update_pickup_point_selector( code, country );
            }
            else {
                $( '#'+ id ).prop( 'checked', true );
                setTimeout( function() {
                    $( '#'+ id ).prop( 'checked', true );
                }, 500 );
            }

            // delay( function () {
            //     update_pickup_points( code, country );
            // }, 700 );
        } );

        events().on( events.PICKUP_POINT_CHANGED, function ( evt, pickup_point_selector ) {
            if ( has_klarna_widget ) {
                // Klarna uses radio buttons. Meaning the input is the data source
                update_display_info( $( pickup_point_selector ).data( 'pickup_point' ) );
            }
            else {
                // Normal wo Klarna uses a select dropdown which means an <option> of the select is the data source
                var selected_option = $( pickup_point_selector.options[pickup_point_selector.selectedIndex] );
                update_display_info(selected_option.data( 'pickup_point' ));
            }
            update_cookies();
        } );

        get_order_review_wrapper_elem().on( 'keyup', '.fraktguiden-pickup-point-postcode', function () {
            user_selected.postcode = this.value;
            events().trigger( events.POST_CODE_UPDATED, [this.value, get_shipping_country()] );
        } );

        get_order_review_wrapper_elem().on( 'change', '.fraktguiden-pickup-point-select, .fraktguiden-pickup-point-list input', function () {
            user_selected.pickup_point_id = this.value;
            events().trigger( events.PICKUP_POINT_CHANGED, [this] );
        } );
    }


    /**
     * Update the display info html.
     *
     * @param pickup_point Bring pickup point object.
     */
    function update_display_info( pickup_point ) {
        if ( !pickup_point ) {
            $( '.fraktguiden-selected-text' ).text( '' );
            $( '.fraktguiden-pickup-point-display' ).html( '' );
            $( '[name=_fraktguiden_pickup_point_info_cached]' ).val( '' );
            if ( has_klarna_widget ) {
                $( '.klarna_checkout' ).hide();
            }
        }
        else {
            var html = Bring_Common.create_pickup_point_display_html( pickup_point, get_shipping_country() );

            $( '.fraktguiden-selected-text' ).text( lang.SELECTED_TEXT + ':' );
            $( '.fraktguiden-pickup-point-display' ).html( html );
            $( '[name=_fraktguiden_pickup_point_info_cached]' ).val( Bring_Common.br2pipe( html ) );
            if ( has_klarna_widget ) {
                $( '.klarna_checkout' ).show();
            }
        }
    }

    /**
     * Returns true if the Klarna Checkout widget exists in the document.
     *
     * @returns {boolean}
     */
    function has_klarna_widget() {
        return get_klarna_checkout_widget_elem().length > 0;
    }

    /**
     * Return all Bring shipping rates radio buttons.
     *
     * @returns {jQuery}
     */
    function get_bring_shipping_radio_buttons() {
        return $( 'input[type=radio][value^=bring_fraktguiden].shipping_method' );
    }

    /**
     * Returns true if shipping methods has Bring shipping rates.
     *
     * @returns {boolean}
     */
    function has_bring_shipping_rates() {
        return get_bring_shipping_radio_buttons().length > 0;
    }

    /**
     * Returns true if selected shipping rate is servicepakke.
     *
     * @returns {boolean}
     */
    function is_servicepakke_selected() {
        return get_selected_shipping_rate() == 'servicepakke';
    }

    /**
     * Returns the selected shipping rate.
     *
     * @returns {null}
     */
    function get_selected_shipping_rate() {
        var selected = $( 'input[type=radio][value^=bring_fraktguiden].shipping_method:checked' );
        return selected.length > 0 ? selected.val().split( ':' )[1] : null;
    }

    /**
     * @returns {boolean}
     */
    function ship_to_different_address() {
        return $( '[name=ship_to_different_address]:checked' ).length > 0;
    }

    /**
     * Returns the user's shipping post code.
     *
     * @returns {String}
     */
    function get_shipping_postcode() {
        // Return empty string for Klarna checkout.
        if ( has_klarna_widget() ) {
            return '';
        }
        return ship_to_different_address() ? woo_shipping_postcode_elem().val() : woo_billing_postcode_elem().val()
    }

    /**
     * Returns the user's shipping country.
     *
     * @returns {String}
     */
    function get_shipping_country() {
        // Country is not available when Klarna Checkout exists.
        // Get it from our _fraktguiden_data source which assumes WooCommerce base country is used.
        if ( has_klarna_widget() ) {
            var country = _fraktguiden_data.from_country;
            // Should never happen.
            if ( !country ) {
                console.log( 'From country not set' );
            }
            return country;
        }

        // Otherwise return shipping country from WooCommerce checkout form.
        return ship_to_different_address() ? woo_shipping_country_elem().val() : woo_billing_country_elem().val()
    }

    /**
     * Returns the element that wraps the review box.
     *
     * @returns {jQuery}
     */
    function get_order_review_wrapper_elem() {
        if ( has_klarna_widget() ) {
            return get_klarna_checkout_widget_elem();
        }
        return $( '#order_review' );
    }

    /**
     * Returns WooCommerce's billing post code element.
     *
     * @returns {jQuery}
     */
    function woo_billing_postcode_elem() {
        return $( '[name=billing_postcode]' );
    }

    /**
     * Returns WooCommerce's post code element.
     *
     * @returns {jQuery}
     */
    function woo_shipping_postcode_elem() {
        return $( '[name=shipping_postcode]' );
    }

    /**
     * Returns WooCommerce's billing country element.
     *
     * @returns {jQuery}
     */
    function woo_billing_country_elem() {
        return $( '[name=billing_country]' );
    }

    /**
     * Returns WooCommerce's shipping country element.
     *
     * @returns {jQuery}
     */
    function woo_shipping_country_elem() {
        return $( '[name=shipping_country]' );
    }

    /**
     * Returns Pickup Point's post code element.
     *
     * @returns {jQuery}
     */
    function pickup_point_postcode_elem() {
        return $( '.fraktguiden-pickup-point-postcode' );
    }

    /**
     * Returns Pickup Point's select element.
     *
     * @returns {jQuery}
     */
    function pickup_point_select_elem() {
        return $( '.fraktguiden-pickup-point-select, .fraktguiden-pickup-point-list input' );
    }

    /**
     * Returns Klarna CO wdiget element.
     *
     * @returns {jQuery}
     */
    function get_klarna_checkout_widget_elem() {
        return $( '#klarna-checkout-widget' );
    }

    var delay = (function () {
        var timer = 0;
        return function ( callback, ms ) {
            clearTimeout( timer );
            timer = setTimeout( callback, ms );
        };
    })();

    /**
     * Updates cookies with the user's data.
     */
    function update_cookies() {
        var pickup_id;
        if ( has_klarna_widget() ) {
            pickup_id = pickup_point_select_elem().filter( ':checked' ).val();
        }
        else {
            pickup_id = pickup_point_select_elem().val();
        }
        if ( pickup_id ) {
            Bring_Common.create_cookie( '_fraktguiden_pickup_point_id', pickup_id );
        }
        Bring_Common.create_cookie( '_fraktguiden_pickup_point_postcode', user_selected.postcode );
        Bring_Common.create_cookie( '_fraktguiden_pickup_point_info_cached', $( '[name=_fraktguiden_pickup_point_info_cached]' ).val() );
        // console.log( 'read cookie', Bring_Common.read_cookie( '_fraktguiden_pickup_point_id' ) );
        // console.log( 'read cookie', Bring_Common.read_cookie( '_fraktguiden_pickup_point_postcode' ) );
        // console.log( 'read cookie', Bring_Common.read_cookie( '_fraktguiden_pickup_point_info_cached' ) );
    }

})();
