import TextValidator from './components/text-validator.vue';

var create_text_validator = function( callback ) {
	return function( createElement ) {
		return createElement( TextValidator, {
			props: {
				original_el: this.$el,
				validator: callback,
			}
		} );
	};
};

var validate_email = function( email ) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

var i18n = bring_fraktguiden_settings.i18n;

var api_uid = new Vue( {
	el: '#woocommerce_bring_fraktguiden_mybring_api_uid',
	render: create_text_validator( function( value ) {
		var error_messages = [];
		if ( value.match( /\s/ ) ) {
			error_messages.push( i18n.error_spaces + ' ' + i18n.api_email );
			return error_messages;
		}
		if ( ! validate_email( value ) ) {
			error_messages.push( i18n.error_api_uid );
		}
		return error_messages;
	} )
} );

var api_key = new Vue( {
	el: '#woocommerce_bring_fraktguiden_mybring_api_key',
	render: create_text_validator( function( value ) {
		var error_messages = [];
		if ( value.match( /\s/ ) ) {
			error_messages.push( i18n.error_spaces + ' ' + i18n.api_key );
			return error_messages;
		}
		if ( ! value.match( /^[A-Za-z\-\d]*$/ ) ) {
			error_messages.push( i18n.error_api_key );
		}
		return error_messages;
	} )
} );

var api_customer_number = new Vue( {
	el: '#woocommerce_bring_fraktguiden_mybring_customer_number',
	render: create_text_validator( function( value ) {
		var error_messages = [];
		if ( value.match( /\s/ ) ) {
			error_messages.push( i18n.error_spaces + ' ' + i18n.customer_number );
			return error_messages;
		}
		if ( ! value.match( /^[A-Za-z_]+\-\d+$/ ) && ! value.match( /^\d{6,}$/ ) ) {
			error_messages.push( i18n.error_customer_number );
		}
		return error_messages;
	} )
} );
