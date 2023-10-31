(function ( parent ) {
	var $ = jQuery;

	/**
	 * @namespace
	 */
	parent.Bring_Common = parent.Bring_Common || {};

	parent.Bring_Common = {

		/**
		 * Loads pickup point for country/postcode
		 *
		 * @param {String} country
		 * @param {String} postcode
		 * @param {Object} options
		 */
		load_pickup_points: function ( country, postcode, options ) {
			$.ajax(
				{
					url:        options.url,
					data:       {
						'action':   'bring_get_pickup_points',
						'country':  country,
						'postcode': postcode
					},
					beforeSend: options.before_send || null,
					dataType:   'json',
					success:    options.success
				}
			);
		},

		/**
		 * Creates pickup point html
		 *
		 * @param {String} pickup_point
		 * @param {String} country
		 * @returns {String}
		 */
		create_pickup_point_display_html: function ( pickup_point, country ) {
			var info              = '';
			var name              = pickup_point.name;
			var visiting_address  = pickup_point.visitingAddress;
			var visiting_postcode = pickup_point.visitingPostalCode;
			var visiting_city     = pickup_point.visitingCity;
			var opening_hours     = this.get_opening_hours_from_pickup_point( pickup_point, country );

			info += name + '<br/>';
			info += visiting_address + '<br/>';
			info += visiting_postcode + ', ' + visiting_city + '<br/>';
			info += opening_hours;

			return info;
		},

		/**
		 * Returns translated opening hours for given pickup point and country.
		 *
		 * @param {Object} pickup_point
		 * @param {String} country
		 * @returns {String}
		 */
		get_opening_hours_from_pickup_point: function ( pickup_point, country ) {
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
		},

		/**
		 * Replaces BR elements with pipe symbol
		 *
		 * @param {String} str
		 * @returns {String|void|*|XML|{style, text, priority, click}}
		 */
		br2pipe: function ( str ) {
			return str.replace( /(<|&lt;)br\s*\/*(>|&gt;)/g, '|' );
		},


		erase_cookie: function ( name ) {
			create_cookie( name, "", -1 );
		}
	};

})( window );
