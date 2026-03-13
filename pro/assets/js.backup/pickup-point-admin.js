/* global _fraktguiden_data */
// Admin

(function () {

	/**
	 * The jQuery plugin namespace.
	 *
	 * @external "jQuery.fn"
	 * @see {@link http://learn.jquery.com/plugins/|jQuery Plugins}
	 */
	var $ = jQuery;

	/** @type {Object} */
	var lang = _fraktguiden_data.i18n;

	// ********************************************************************** //
	// Classes

	/**
	 * @namespace
	 *
	 * Model
	 */
	var Data = {

		root: window._fraktguiden_data,

		/**
		 * Load the shipping info object.
		 *
		 * Triggers a 'bring_info_loaded.bring' event.
		 */
		load_bring_shipping_info: function () {
			var self = this;
			$.ajax(
				{
					url: self.ajax_url(),
					data: {
						'action': 'bring_shipping_info_var',
						'country' : get_shipping_address_country(),
						'postcode': get_shipping_address_postcode(),
						'post_id': $( '#post_ID' ).val()
					},
					dataType: 'json',
					success: function ( response, status ) {
						self.set_shipping_order_data( response.bring_shipping_info );
						$( document.body ).trigger( 'bring_info_loaded.bring' );
					}
				}
			);
		},

		/**
		 * Get a rate for the given service.
		 *
		 * @param {String} service Bring product
		 * @param {Function} callback
		 */
		get_rate: function ( service, callback ) {
			var self = this;

			$.ajax(
				{
					url: self.ajax_url(),
					data: {
						'action'  : 'bring_get_rate',
						'post_id' : $( '#post_ID' ).val(),
						'country' : get_shipping_address_country(),
						'postcode': get_shipping_address_postcode(),
						'service' : service
					},
					dataType: 'json',
					success: callback
				}
			);
		},

		/**
		 * @return {String}
		 */
		ajax_url: function () {
			return this.root.ajaxurl;
		},

		/**
		 * Returns the available Fraktguiden services
		 *
		 * @return {Object}
		 */
		get_services: function () {
			return this.root.services;
		},

		/**
		 * @param {Array} data
		 */
		set_shipping_order_data: function ( data ) {
			this.root.bring_shipping_info = data;
		},

		/**
		 * @return {Array}
		 */
		get_shipping_order_data: function () {
			return this.root.bring_shipping_info;
		},

		/**
		 * @param {jQuery} row - Shipping row element
		 * @return {String}
		 */
		get_order_item_id_from_row: function ( row ) {
			return row.attr( 'data-order_item_id' );
		},

		/**
		 * @return {Boolean}
		 */
		make_items_editable: function () {
			return this.root.make_items_editable;
		}
	};

	/**
	 * @namespace
	 *
	 * Keeps track of {Shipping_Item} instances.
	 */
	var Shipping_Items_Manager = {

		/**
		 * @property Array.<Shipping_Item>
		 */
		instances: [],

		/**
		 * Get a instance by row.
		 *
		 * @param {jQuery} row WooCommerce shipping line
		 * @return {Shipping_Item|null}
		 */
		from_row: function ( row ) {
			var order_item_id = Data.get_order_item_id_from_row( row );
			return Shipping_Items_Manager.by_item_id( order_item_id );
		},

		/**
		 * Add a to the instances.
		 *
		 * @param {Shipping_Item} shipping_item
		 */
		add: function ( shipping_item ) {
			this.instances.push( shipping_item );
		},

		/**
		 * Clear all instances.
		 */
		clear: function () {
			this.instances.splice( 0, this.instances.length );
		},

		/**
		 * Get an instance by order item id.
		 *
		 * @param {int} order_item_id
		 * @return {Shipping_Item}
		 */
		by_item_id: function ( order_item_id ) {
			var result = null;
			for ( var key in this.instances ) {
				if ( ! this.instances.hasOwnProperty( key ) ) {
					continue;
				}
				var inst = this.instances[key];
				if ( inst.item_id == order_item_id ) {
					result = inst;
					break;
				}
			}
			return result;
		}
	};

	/**
	 * A class that represents a shipping item.
	 *
	 * @class
	 */
	function Shipping_Item( item_id ) {

		var self     = this;
		this.item_id = item_id;
		this.row     = $( '[data-order_item_id=' + self.item_id + ']' );
		this.elems   = {
			view: function () {
				return $( '.name .view', self.row ).first();
			},
			edit: function () {
				return $( '.name .edit', self.row ).first();
			},
			shipping_title: function () {
				return $( 'input[name^=shipping_method_title]', self.row );
			},
			shipping_selector: function () {
				return $( 'select[name^=shipping_method]', self.row );
			},
			pickup_info: function () {
				return $( '.fraktguiden-pickup-point-info', self.row );
			},
			shipping_cost: function () {
				return $( 'input[name^=shipping_cost]', self.row );
			},
			fraktguiden: {
				wrapper: function () {
					return $( '.fraktguiden-ui', self.row );
				},
				services_selector: function () {
					return $( '[name^=_fraktguiden_services]', self.row );
				},
				get_rate_button: function () {
					return $( '.get-rate-button', self.row );
				},
				packages: function () {
					return $( '[name^=_fraktguiden_packages]', self.row );
				},

				pickup: {
					wrapper: function () {
						return $( '.fraktguiden-pickup-point-ui', self.row )
					},
					postcode: function () {
						return $( '[name^=_fraktguiden_pickup_point_postcode]', self.row );
					},
					selector: function () {
						return $( '[name^=_fraktguiden_pickup_point_selector]', self.row );
					},
					input: function () {
						return $( '[name^=_fraktguiden_pickup_point_id]', self.row );
					},
					info_wrapper: function () {
						return $( '.fraktguiden-pickup-point-selected-info', self.row );
					},
					info_input: function () {
						return $( '[name^=_fraktguiden_pickup_point_info_chache]', self.row );
					}
				}
			}
		};

		// Add actions buttons for shipping lines so they are always available
		// regardless of what order status.
		if ( Data.make_items_editable() ) {
			var edit_line = this.row.find( '.wc-order-edit-line-item' );
			if (edit_line.find( '.wc-order-edit-line-item-actions' ).length == 0) {
				var actions_html = '<div class="wc-order-edit-line-item-actions"><a class="edit-order-item" href="#"></a><a class="delete-order-item" href="#"></a></div>';
				edit_line.append( actions_html );
			}
		}
	}

	Shipping_Item.prototype = {

		/**
		 * When the user starts editing an order item.
		 */
		on_start_edit: function () {
			this.on_shipping_method_changed();

			if ( Data.make_items_editable() ) {
				$( '.wc-order-bulk-actions' ).hide();
				$( '.wc-order-add-item' ).show().find( '.button.add-order-item, .button.add-order-fee' ).hide();
			}
		},

		/**
		 * When shipping method is changed.
		 */
		on_shipping_method_changed: function () {
			if ( this.is_fraktguiden_selected() ) {
				// Alert and return if shipping postcode and shipping country
				// is not set on order.
				var valid = can_use();
				if ( ! valid.status ) {
					alert( valid.text );
					this.elems.shipping_selector().val( '' );
					return;
				}
				// Show fraktguiden.
				this.show_fraktguiden();
				// Update the shipping title element.
				this.elems.shipping_title().val( get_text_from_selector( this.elems.fraktguiden.services_selector() ) );
			} else {
				// Other shipping method is selected.
				// We could now remove service from fraktguiden shipping option.

				// Hide Fraktguiden.
				this.hide_fraktguiden();
				this.elems.shipping_title().val( get_text_from_selector( this.elems.shipping_selector() ) );
			}

		},

		/**
		 * When pickup point post code is changed.
		 *
		 * @param postcode
		 */
		on_pickup_point_postcode_changed: function ( postcode ) {
			if ( postcode.length < 3 ) {
				return;
			}

			this.update_info( '' );

			this.populate_pickup_point_selector( postcode );
		},
		/**
		 * When the pickup point is changed.
		 */
		on_pickup_point_changed: function () {
			var pickup_point_selector = this.elems.fraktguiden.pickup.selector();
			var pickup_point          = pickup_point_selector.find( ':selected' ).data( 'pickup_point' );
			var pickup_point_id       = pickup_point_selector.val();
			if ( pickup_point ) {
				var info = Bring_Common.create_pickup_point_display_html( pickup_point, get_shipping_address_country() );
				this.update_info( info );
			}
			this.elems.fraktguiden.pickup.input().val( pickup_point_id );
		},

		/**
		 * When Fraktguiden service is changed.
		 */
		on_service_changed: function () {
			var service_selector  = this.elems.fraktguiden.services_selector();
			var shipping_selector = this.elems.shipping_selector();
			var selected_service  = service_selector.val();

			// Update shipping method value.
			shipping_selector.find( ':selected' ).attr( 'value', 'bring_fraktguiden:' + selected_service );
			// Update shipping title.
			this.elems.shipping_title().val( get_text_from_selector( service_selector ) );

			if ( selected_service == 'servicepakke' || selected_service == '5800' ) {
				this.show_pickup_point();
			} else {
				this.hide_pickup_point();
			}

			this.on_update_rate();
		},

		/**
		 * Show fraktguiden ui.
		 */
		show_fraktguiden: function () {
			this.elems.fraktguiden.wrapper().show();
			var service_selector = this.elems.fraktguiden.services_selector();
			// Get the selected BFG service from the shipping selector (Woo stores the service/rate there "Fraktguiden:<service>).
			var selected_service = this.get_service_from_shipping_selector();
			// Set the service selector value.
			service_selector.val( selected_service );
			// Show pickup point options if sercvice is Klimanøytral Servicepakke
			if ( selected_service == 'servicepakke' ) {
				this.show_pickup_point();
			}

			this.elems.fraktguiden.get_rate_button().show();

		},

		/**
		 * Hide Fraktguiden ui.
		 */
		hide_fraktguiden: function () {
			this.elems.fraktguiden.wrapper().hide();
			this.elems.fraktguiden.get_rate_button().hide();
		},

		/**
		 * Show Pickup Point ui.
		 */
		show_pickup_point: function () {
			this.elems.fraktguiden.pickup.wrapper().show();
			var data           = get_bring_shipping_info_for_order_item( this.item_id );
			var postcode_input = this.elems.fraktguiden.pickup.postcode();
			var postcode;
			if ( data && data.postcode ) {
				postcode = data.postcode;
				postcode_input.val( postcode );
			} else {
				postcode = get_shipping_address_postcode();
				postcode_input.val( postcode );
			}

			this.populate_pickup_point_selector( postcode );
		},

		/**
		 * Hide Pickup Point ui.
		 */
		hide_pickup_point: function () {
			this.elems.fraktguiden.pickup.wrapper().hide();
			this.elems.fraktguiden.pickup.postcode().val( '' );
			this.elems.fraktguiden.pickup.input().val( '' );
			this.update_info( '' );
		},

		/**
		 * Requests pickup points and populates the selector.
		 *
		 * @param {String} postcode
		 */
		populate_pickup_point_selector: function ( postcode ) {

			var self = this;
			// todo resolve country and postcode. Find country in shipping address.
			var country = get_shipping_address_country();

			if ( ! postcode ) {
				return;
			}

			var pickup_point_selector = this.elems.fraktguiden.pickup.selector();

			var ajax_options = {
				url: Data.ajax_url(),
				before_send: function () {
					pickup_point_selector.find( 'option' ).remove();
					pickup_point_selector.append( '<option value="">' + lang.LOADING_TEXT + '</option>' );
				},
				success: function ( response, status ) {
					var pickup_points = response;

					// @todo: handle no pickup points.

					// Remove existing options in the selector
					pickup_point_selector.find( 'option' ).remove();
					// Create a placeholder option in the selector
					pickup_point_selector.append( '<option value="">--- ' + lang.PICKUP_POINT_PLACEHOLDER + ' ---</option>' );
					// Collect new pickup point ids so we can select the user selected pickup point later.
					var ids = [];
					for ( var key in pickup_points ) {
						if ( ! pickup_points.hasOwnProperty( key ) ) {
							continue;
						}

						var option            = $( '<option>' );
						var pickup_point      = pickup_points[key];
						var name              = pickup_point.name;
						var visiting_address  = pickup_point.visitingAddress;
						var visiting_postcode = pickup_point.visitingPostalCode;
						var visiting_city     = pickup_point.visitingCity;

						option.text( name + ', ' + visiting_address + ', ' + visiting_postcode + ' ' + visiting_city );
						option.attr( 'value', pickup_point.id );
						option.data( 'pickup_point', pickup_point );

						// Add pickup point option
						pickup_point_selector.append( option );
						ids.push( pickup_point.id );
					}

					// Set the selected pickup point.
					var data            = get_bring_shipping_info_for_order_item( self.item_id );
					var pickup_point_id = '';
					if ( data && data.pickup_point && ids.indexOf( data.pickup_point.id ) > -1 ) {
						pickup_point_id = data.pickup_point.id;
						pickup_point_selector.val( pickup_point_id );
					} else {
						pickup_point_selector.prop( 'selectedIndex', 0 );
					}

					self.on_pickup_point_changed();
				}
			};
			Bring_Common.load_pickup_points( country, postcode, ajax_options );
		},

		/**
		 * When a rate is requested.
		 * Loads rate for the selected Fraktguiden service.
		 */
		on_update_rate: function () {
			var self    = this;
			var service = this.elems.fraktguiden.services_selector().val();
			if ( service ) {

				self.row.block(
					{
						message: '',
						css: {
							border: 'none'
						},
						overlayCSS: {backgroundColor: '#f9f9f9'},
						blockMsgClass: 'bring-block-ui'
					}
				);

				Data.get_rate(
					service,
					function ( response, status ) {

						self.row.unblock();

						if ( status == 'success' ) {
							var rate = response.rate;
							if ( rate ) {
								self.elems.shipping_cost().val( rate );
								self.elems.fraktguiden.packages().val( response.packages );
							} else {
								alert( lang.SERVICE + ': ' + service + '\n\n' + lang.RATE_NOT_AVAILABLE );
							}
						} else {
							alert( lang.REQUEST_FAILED + ', ' + status );
						}
					}
				);
			}
		},

		/**
		 * Populate the selected pickup point info elements.
		 *
		 * @param info
		 */
		update_info: function ( info ) {
			this.elems.fraktguiden.pickup.info_wrapper().html( info );
			this.elems.fraktguiden.pickup.info_input().val( Bring_Common.br2pipe( info ) );
		},

		/**
		 * Create the html shown below a shipping item.
		 */
		create_pickup_point_display_info: function () {
			// Return if the element has been created.
			if ( this.elems.pickup_info().length == 1 ) {
				return;
			}

			var info = get_bring_shipping_info_for_order_item( this.item_id );

			if ( info ) {
				var view              = this.elems.view();
				var pickup_point      = info.pickup_point;
				var name              = pickup_point ? pickup_point.name : '';
				var visiting_address  = pickup_point ? pickup_point.visitingAddress : '';
				var visiting_postcode = pickup_point ? pickup_point.visitingPostalCode : '';
				var visiting_city     = pickup_point ? pickup_point.visitingCity : '';
				// @todo: We could get country from eg. WooCommerce base country
				var opening_hours = name != '' ? Bring_Common.get_opening_hours_from_pickup_point( pickup_point, get_shipping_address_country() ) : '';

				if ( pickup_point ) {

					view.append(
						'<div class="fraktguiden-pickup-point-info">' +
						'   <b>' + lang.PICKUP_POINT + '</b>:<br/>' +
						name + '<br/>' +
						visiting_address + '<br/>' +
						visiting_postcode + ', ' + visiting_city + '<br/>' +
						opening_hours +
						'</div>'
					);
				}
			}
		},

		/**
		 * Creates the ui elements for Fraktguiden ui.
		 */
		create_fraktguiden_elements: function () {
			if ( this.elems.fraktguiden.wrapper().length == 1 ) {
				return;
			}
			var editor                   = this.elems.edit();
			var item_id                  = this.item_id;
			var info                     = get_bring_shipping_info_for_order_item( item_id );
			var postcode                 = info ? info.postcode : get_shipping_address_postcode();
			var pickup_point_id          = info && info.pickup_point ? info.pickup_point.id : '';
			var pickup_point_info_cached = info && info.pickup_point ? Bring_Common.br2pipe( Bring_Common.create_pickup_point_display_html( info.pickup_point, get_shipping_address_country() ) ) : '';
			var packages                 = info ? info.packages : '';

			editor.append(
				'<div class="fraktguiden-ui" style="display:none">' +
				'   <textarea style="display: none" name="_fraktguiden_packages[' + item_id + ']">' + packages + '</textarea>' +
				'   <div>' +
				'       <select name="_fraktguiden_services[' + item_id + ']">' +
				'       </select>' +
				'   </div>' +
				'   <div class="fraktguiden-pickup-point-ui" style="display:none">' +
				'       <input type="text" name="_fraktguiden_pickup_point_postcode[' + item_id + ']" value="' + postcode + '" placeholder="' + lang.POSTCODE + '"/>' +
				'       <input type="hidden" name="_fraktguiden_pickup_point_id[' + item_id + ']" value="' + pickup_point_id + '"/>' +
				'       <select name="_fraktguiden_pickup_point_selector[' + item_id + ']">' +
				'       </select>' +
				'       <div><b>' + lang.PICKUP_POINT + ':</b></div>' +
				'       <div class="fraktguiden-pickup-point-selected-info"></div>' +
				'       <input type="hidden" name="_fraktguiden_pickup_point_info_cached[' + item_id + ']" value="' + pickup_point_info_cached + '"/>' +
				'   </div>' +
				'</div>'
			);

			// Populate the service selector with service options.
			var services         = Data.get_services();
			var service_selector = this.elems.fraktguiden.services_selector();
			service_selector.append( '<option value="">--- ' + lang.SERVICE_PLACEHOLDER + ' ---</option>' );
			for ( var key in services ) {
				if ( ! services.hasOwnProperty( key ) ) {
					continue;
				}
				var service_key  = key.toLowerCase();
				var service_name = services[key];
				service_selector.append( '<option value="' + service_key + '">' + service_name + '</option>' );
			}

			// Add recalculate button.
			this.elems.shipping_cost().after( '<a href="" class="get-rate-button" style="display:none">' + lang.GET_RATE + '</a>' );
		},

		/**
		 * Returns true if Fraktguiden shipping method is selected.
		 *
		 * @returns {boolean}
		 */
		is_fraktguiden_selected: function () {
			return this.elems.shipping_selector().val().indexOf( 'fraktguiden' ) > -1;
		},

		/**
		 * Returns the Fraktguiden service from the shipping selector.
		 * WooCommerce stores service/rates in this form 'fraktguiden:servicepakke'
		 *
		 * @returns {string}
		 */
		get_service_from_shipping_selector: function () {
			var parts = this.elems.shipping_selector().val().split( ':' );
			return parts[1] ? parts[1] : '';
		}

	};

	// ********************************************************************** //
	// Init
	add_order_item_event_handlers();
	add_order_items_loaded_handler();
	Data.load_bring_shipping_info();

	// ********************************************************************** //
	// Misc. functions

	function get_bring_shipping_info_for_order_item( order_item_id ) {
		var result = null;
		var items  = Data.get_shipping_order_data();
		for ( var key in items ) {
			var item = items[key];
			if ( item.item_id == order_item_id ) {
				result = item;
				break;
			}
		}
		return result;
	}

	/**
	 * Set up listeners for changes in the UI
	 */
	function add_order_item_event_handlers() {

		var order_items = get_order_items();

		// When the order items box is reloaded
		$( document.body ).on(
			'order_items_loaded.bfg',
			function () {
				Data.load_bring_shipping_info();
			}
		);

		$( document.body ).on(
			'bring_info_loaded.bring',
			function () {
				Shipping_Items_Manager.clear();

				shipping_order_items().each(
					function ( i, elem ) {
						var sl = new Shipping_Item( get_order_item_id( $( elem ) ) );
						sl.create_pickup_point_display_info();
						sl.create_fraktguiden_elements();
						Shipping_Items_Manager.add( sl );
					}
				);
			}
		);

		// When the edit shipping item button (pencil) is clicked.
		order_items.on(
			'click',
			'#order_shipping_line_items a.edit-order-item',
			function () {
				var shipping_item = Shipping_Items_Manager.from_row( $( this ).closest( 'tr' ) );
				shipping_item.on_start_edit();
			}
		);

		// When the WooCommerce shipping method selector change
		order_items.on(
			'change',
			'select[name^=shipping_method]',
			function () {
				var shipping_item = Shipping_Items_Manager.from_row( $( this ).closest( 'tr' ) );
				shipping_item.on_shipping_method_changed();
			}
		);

		// When the Fraktguiden services selector change
		order_items.on(
			'change',
			'select[name^=_fraktguiden_services]',
			function () {
				var shipping_item = Shipping_Items_Manager.from_row( $( this ).closest( 'tr' ) );
				shipping_item.on_service_changed();
			}
		);

		// When the pickup point postcode changes.
		order_items.on(
			'keyup',
			'[name^=_fraktguiden_pickup_point_postcode]',
			function () {
				var shipping_item = Shipping_Items_Manager.from_row( $( this ).closest( 'tr' ) );
				shipping_item.on_pickup_point_postcode_changed( this.value );
			}
		);

		// When pickup point selector change
		order_items.on(
			'change',
			'select[name^=_fraktguiden_pickup_point_selector]',
			function () {
				var shipping_item = Shipping_Items_Manager.from_row( $( this ).closest( 'tr' ) );
				shipping_item.on_pickup_point_changed();
			}
		);

		order_items.on(
			'click',
			'.get-rate-button',
			function ( evt ) {
				evt.preventDefault();
				var shipping_item = Shipping_Items_Manager.from_row( $( this ).closest( 'tr' ) );
				shipping_item.on_update_rate();
			}
		);
	}

	/**
	 * Adds a mutation observer
	 * WooCommerce order screen does not trigger a public 'order-items-saved' event.
	 * Listen to when the loading mask element is removed.
	 *
	 * Note: Not available in IE < 11
	 * We may use a polyfill, https://github.com/webcomponents/webcomponentsjs
	 */
	function add_order_items_loaded_handler() {
		// select the target node
		var target = document.querySelector( '#woocommerce-order-items' );
		// create an observer instance
		var mutation_observer = new MutationObserver(
			function ( mutations ) {
					mutations.forEach(
						function ( mutation ) {
							var loader_removed = mutation.removedNodes.length == 1 && mutation.removedNodes[0].className == 'blockUI blockMsg blockElement';
							if ( loader_removed ) {
								  $( document.body ).trigger( 'order_items_loaded.bfg' );
							}
						}
					);
			}
		);

		var config = {attributes: true, childList: true, characterData: true};
		mutation_observer.observe( target, config );
	}

	/**
	 * Returns an object literal with info if the order is ready for Fraktguiden usage.
	 *
	 * @todo: move to Data?
	 *
	 * @returns {{status: boolean, text: string}}
	 */
	function can_use() {
		var result = {
			status: true,
			text: ''
		};

		var postcode = get_shipping_address_postcode();
		var country  = get_shipping_address_country();

		if ( ! country || ! postcode ) {
			result.status = false;
			result.text  += lang.VALIDATE_SHIPPING1 + ':\n\n';

			if ( ! postcode ) {
				result.text += '  * ' + lang.VALIDATE_SHIPPING_POSTCODE;
			}

			if ( ! country ) {
				result.text += '\n  * ' + lang.VALIDATE_SHIPPING_COUNTRY;
			}

			result.text += '\n\n' + lang.VALIDATE_SHIPPING2;
		}

		return result;
	}

	/**
	 * Returns the order items table.
	 *
	 * @return {jQuery}
	 */
	function get_order_items() {
		return $( '#woocommerce-order-items' );
	}

	/**
	 * Returns the order item rows.
	 *
	 * @return {jQuery}
	 */
	function shipping_order_items() {
		return get_order_items().find( 'tr.shipping' );
	}

	/**
	 * Returns the order item id from the the given row
	 *
	 * @param {jQuery} row
	 * @return {String}
	 */
	function get_order_item_id( row ) {
		return row.attr( 'data-order_item_id' );
	}

	/**
	 * Returns the WooCommerce shipping address postcode.
	 *
	 * @returns {String}
	 */
	function get_shipping_address_postcode() {
		return $( '[name=_shipping_postcode]' ).val();
	}

	/**
	 * Returns the WooCommerce shipping address country.
	 *
	 * @returns {String}
	 */
	function get_shipping_address_country() {
		return $( '[name=_shipping_country]' ).val();
	}

	/**
	 * Returns the option text from the given selector.
	 *
	 * @param {jQuery} selector
	 * @returns {String}
	 */
	function get_text_from_selector( selector ) {
		return selector.val() != '' ? selector.find( ':selected' ).text() : '';
	}

})();
