(function ($) {
	// Assign data from localised js object
	let pickUpPoints = window._fraktguiden_data.pick_up_points;
	let selectedPickUpPoint = window._fraktguiden_data.selected_pick_up_point;
	let requireUpdate = false;

	/**
	 * jQuery UI block arguments
	 * @type {{overlayCSS: {background: string, opacity: number}, message: null}}
	 */
	const blockArgs = {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	};


	const modalEl = document.createElement('pick-up-points-modal');
	document.body.appendChild(modalEl);

	let pickerEl;
	let listEl;
	let getRequest = undefined;

	const utility = {
		refreshPickUpPoints: function () {
			if (! requireUpdate) {
				return;
			}
			requireUpdate = false;
			pickerEl.block(blockArgs);
			if (getRequest) {
				getRequest.cancel();
			}
			getRequest = $.get(
				_fraktguiden_checkout.ajaxurl,
				{action: 'bfg_get_pick_up_points'}
			).done(
				handlers.fetchPickUpPointsDone
			).fail(handlers.fetchPickUpPointsFailed);
			return getRequest;
		},
		getShippingKey: function () {
			const cart = wp.data.select('wc/store/cart');
			// Check if cart is valid
			if (!cart || typeof cart.getCustomerData !== 'function') {
				console.warn('Cart store or customer data is unavailable.');
				return '';
			}
			const cartData = cart.getCartData();
			// Check if customer data is valid
			if (!cartData || !cartData.shippingAddress) {
				console.warn('Customer data or shipping address is missing.');
				return '';
			}
			const {country, postcode} = cartData.shippingAddress;
			// Ensure country and postcode are valid strings
			if (typeof country !== 'string' || typeof postcode !== 'string') {
				console.warn('Shipping address country or postcode is invalid.');
				return '';
			}
			return `${country}${postcode}`;
		},
		/**
		 * Get packages
		 * @returns {*[]}
		 */
		getPackages: function () {
			if (!window.wc || !window.wp) {
				return [];
			}
			const storeKey = wc.wcBlocksData.CART_STORE_KEY;
			const store = wp.data.select(storeKey);
			return store.getShippingRates();
		},
		/**
		 * Get shipping rates
		 * @returns {*[]}
		 */
		getShippingRates: function () {
			const packages = utility.getPackages();
			let shippingRates = [];
			for (let i = 0; i < packages.length; i++) {
				const rates = packages[i].shipping_rates;
				shippingRates = shippingRates.concat(rates);
			}
			return shippingRates;
		},

		/**
		 * @param {string} value
		 * @returns {boolean}
		 */
		usesPickUpPoint: function (value) {
			for (let j = 0; j < _fraktguiden_data.pick_up_point_rate_ids.length; j++) {
				if (value === _fraktguiden_data.pick_up_point_rate_ids[j]) {
					return true;
				}
			}
			return false;
		},
		/**
		 * Format Address
		 * @param pickUpPoint
		 * @returns {string}
		 */
		formatAddress: function (pickUpPoint) {
			return pickUpPoint.address + ', ' + pickUpPoint.postalCode + ' ' + pickUpPoint.city;
		},
		/**
		 * @param pickUpPoint
		 */
		renderSelectedPickUpPoint: function (pickUpPoint) {
			$('.bfg-pup__name').text(pickUpPoint.name);
			$('.bfg-pup__address').text(utility.formatAddress(pickUpPoint));
			$('.bfg-pup__opening-hours').text(pickUpPoint.openingHours);
			$('.bfg-pup__description').text(pickUpPoint.description);
			if (_fraktguiden_checkout.map_key) {
				$('.bfg-pup__map').attr('href', pickUpPoint[_fraktguiden_checkout.map_key]);
			} else {
				$('.bfg-pup__map').hide();
			}
		}
	};

	/**
	 * Handlers
	 */
	const handlers = {
		/**
		 * Select Pickup Point handler
		 * @param pickUpPoint
		 * @returns {(function(*): void)|*}
		 */
		selectPickUpPointHandler: function (pickUpPoint) {
			return function (e) {
				e.preventDefault();
				modalEl.close();

				if (selectedPickUpPoint.id === pickUpPoint.id) {
					return;
				}

				const tr = $('.woocommerce-shipping-totals, .wp-block-woocommerce-checkout-shipping-methods-block');
				tr.block(blockArgs);

				// Ajax select pick up point
				$.post(
					_fraktguiden_checkout.ajaxurl,
					{
						action: 'bfg_select_pick_up_point',
						id: pickUpPoint.id,
					}
				).error(
					function (data) {
						console.error(data);
						tr.unblock();
					}
				).done(function () {
					tr.unblock();
					utility.renderSelectedPickUpPoint(pickUpPoint);
				});
				selectedPickUpPoint = pickUpPoint;
			};
		},

		fetchPickUpPointsFailed: function () {
			getRequest = undefined;
			const el = document.querySelector('pick-up-points-modal');
			el.setError(_fraktguiden_data.i18n.ERROR_LOADING_PICK_UP_POINTS)
			pickerEl.unblock();
		},

		fetchPickUpPointsDone: function (response) {
			getRequest = undefined;
			// Update values from response
			window._fraktguiden_data.selected_pick_up_point = response.selected_pick_up_point;
			selectedPickUpPoint = response.selected_pick_up_point;
			window._fraktguiden_data.pick_up_points = response.pick_up_points;
			pickUpPoints = response.pick_up_points;

			utility.renderSelectedPickUpPoint(selectedPickUpPoint);

			const el = document.querySelector('pick-up-points-modal');
			el.setPickUpPoints(pickUpPoints, handlers.selectPickUpPointHandler);
			pickerEl.unblock();
		}
	};

	const init = function () {
		// Set items
		pickerEl = $('.bring-fraktguiden-pick-up-point-picker').first().clone();

		// Bind modal clicks and key-presses
		modalEl.setPickUpPoints(
			_fraktguiden_data.pick_up_points,
			handlers.selectPickUpPointHandler
		);
	};


	/**
	 * Block checkout
	 */
	const blockCheckout = function (e) {
		init();

		const shippingOptionsEl = e.detail.element;
		const inputs = $(shippingOptionsEl).find('input')
		const shippingRates = utility.getShippingRates();

		const getPicker = function(rate) {
			const inputEl = $('[value="' + rate.rate_id + '"]')
			const control = inputEl.parent();
			pickerEl = control.find('.bring-fraktguiden-pick-up-point-picker');
			if (!pickerEl.length) {
				pickerEl = $('.bring-fraktguiden-pick-up-point-picker').first().clone();
				pickerEl.find('.bfg-pup__change').on('click', () => modalEl.open());
				control.append(pickerEl);
			}
			return pickerEl;
		}

		for (let i = 0; i < shippingRates.length; i++) {
			const shippingRate = shippingRates[i];
			if (!utility.usesPickUpPoint(shippingRate.rate_id)) {
				continue;
			}
			utility.renderSelectedPickUpPoint(_fraktguiden_data.selected_pick_up_point);

			getPicker(shippingRate).show();
		}


		// Bind callback to the set-selected-shipping-rate action
		// This is triggered when selecting a shipping option
		wp.hooks.addAction(
			'woocommerce_blocks-checkout-set-selected-shipping-rate',
			'bring-fraktguiden-for-woocommerce',
			function () {
				console.log('updated')
			}
		);

		// 26712 - wrong list?
		wp.hooks.addAction(
			'experimental__woocommerce_blocks-checkout-set-selected-shipping-rate',
			'bring-fraktguiden-for-woocommerce',
			function () {
				console.log('updated')
			}
		);
		let timeout = undefined;

		monitorNetworkRequests(() => {
			if (timeout) {
				clearTimeout(timeout);
			}
			timeout = setTimeout(utility.refreshPickUpPoints, 100);
		});

		let monitorTimeout = undefined;
		let shippingKey = utility.getShippingKey();
		wp.data.subscribe(
			function () {
				if (window.localStorage.getItem('WOOCOMMERCE_CHECKOUT_IS_CUSTOMER_DATA_DIRTY') === 'true') {
					// Don't do me dirty
					return;
				}

				const key = utility.getShippingKey();
				if (shippingKey === key) {
					return;
				}
				shippingKey = key;
				requireUpdate = true;

				// Give the monitor a time limit to detect changes
				if (monitorTimeout) {
					clearTimeout(monitorTimeout);
				}
				monitorTimeout = setTimeout(
					function () {
						if (! requireUpdate) {
							return;
						}
						console.warn('Timeout on detect wc/store/batch update');
						utility.refreshPickUpPoints();
					},
					5000
				);
			}
		);

		wp.data.subscribe(
			function () {
				let rates = utility.getShippingRates();
				let showPicker = false;
				for (let i = 0; i < rates.length; i++) {
					const rate = rates[i];
					if (! rate.selected) {
						continue;
					}
					if (rate.method_id !== 'bring_fraktguiden') {
						continue;
					}

					if (! utility.usesPickUpPoint(rate.rate_id)) {
						continue;
					}
					getPicker(rate).show();
					requireUpdate = true;
					showPicker = true;
					break;
				}

				if (! showPicker) {
					pickerEl.hide();
					return;
				}
			}
		);

		// The checkout block is empty on load and it takes a few seconds to load before it's ready.
		// Poll to check for the existance of the woocommerce shipping option or label element.
		// let pollInterval = setInterval(
		// 	function () {
		// 		const el = $('.wc-block-components-shipping-rates-control');
		// 		if (! el.length) {
		// 			return;
		// 		}
		// 		const result = el.find('.wc-block-components-radio-control__option, .wc-block-components-radio-control__label-group')
		// 		if (! result.length) {
		// 			return;
		// 		}
		// 		clearInterval(pollInterval);
		// 		console.log('render the location picker here')
		// 		console.log(getShippingRates())
		// 	},
		// 	5
		// );
	}
	document.addEventListener('bfg-shipping-rates-loaded', blockCheckout);

	/**
	 * Classic checkout
	 */
	const classicCheckout = function () {
		let previous = $('#shipping_method .shipping_method:checked').val();
		$(document).on(
			'updated_checkout',
			function (event, data) {
				const current = $('#shipping_method .shipping_method:checked').val();
				let changed = current !== previous;
				if (changed) {
					previous = current;
				}
				pickerEl.show();
				pickerEl.block(blockArgs);

				utility.refreshPickUpPoints();
			}
		);
	}



	class PickUpPointsModal extends HTMLElement {
		constructor() {
			super();
			this.attachShadow({mode: 'open'});

			// Create styles for the modal
			const styles = document.createElement('style');
			styles.textContent = _fraktguiden_data.pick_up_point_modal_css;

			const el = document.createElement('div');
			el.classList.add('bring-fraktguiden-pick-up-points-modal');
			el.innerHTML = `
			  <div class="bfg-pupm__wrap">
				<div class="bfg-pupm__inner">
					<div class="bfg-pupm__header">
						<div class="bfg-pupm__instruction">
							${_fraktguiden_data.i18n.MODAL_INSTRUCTIONS}
						</div>
						<div class="bfg-pupm__close" tabindex="0">&times;</div>
					</div>
					<div class="bfg-pupm__list"></div>
				</div>
			  </div>
			`;

			// Append styles and modal to the shadow DOM
			this.shadowRoot.append(styles, el);

			const jqEl = $(el);
			// Create the modal structure
			// Close modal when clicking on the close button ✖️
			jqEl.find('.bfg-pupm__close').on('click',  (e) => {
				e.preventDefault();
				this.close();
			}).on('keyup', (e) => {
				if (e.key !== 'Enter' && e.key !== ' ') {
					return;
				}
				e.preventDefault();
				this.close();
			})

			// Close modal when clicking on backdrop or the [esc] key
			jqEl.on('click', (e) => {
				e.preventDefault();
				this.close();
			}).on('keyup', (e) => {
				if (e.key !== 'Escape') {
					return;
				}
				e.preventDefault();
				this.close();
			});

			// Prevent closing when clicking on inner elements
			jqEl.find('.bfg-pupm__inner').on(
				'click',
				function (e) {
					e.preventDefault();
					e.stopPropagation();
				}
			);
		}

		// Open the modal
		open() {
			const el = this.shadowRoot.querySelector('.bring-fraktguiden-pick-up-points-modal');
			el.classList.add('open');

			const listItems = $(el).find('.bfg-pupm__item')
			// Find selected pick up point and focus it
			let selectedItems = listItems.filter(function () {
				return $(this).data('id') === selectedPickUpPoint.id;
			});
			if (!selectedItems.length) {
				selectedItems = listItems.first().focus();
			}
			selectedItems.focus();
			setTimeout(function () {
				selectedItems.focus();
			}, 100);
		}

		// Close the modal
		close() {
			this.shadowRoot.querySelector('.bring-fraktguiden-pick-up-points-modal').classList.remove('open');
		}

		setError(text) {
			$(this.shadowRoot.querySelector('.bfg-pupm__list')).text(text);
		}

		// Add pick-up points dynamically
		setPickUpPoints(points, callback) {
			const listContainer = this.shadowRoot.querySelector('.bfg-pupm__list');
			listContainer.innerHTML = '';

			points.forEach((point) => {
				const address = utility.formatAddress(point);
				const pointEl = $(`
					<div class="bfg-pupm__item">
						<div class="bfg-pupm__name">${point.name}</div>
						<div class="bfg-pupm__address">${address}</div>
					</div>
				`);
				pointEl.data('id', point.id);
				pointEl.on('click', callback(point));
				pointEl.on('keyup', function (e) {
					if (e.key === 'ArrowUp') {
						clone.prev().focus();
						e.preventDefault();
						return;
					}
					if (e.key === 'ArrowDown') {
						clone.next().focus();
						e.preventDefault();
						return;
					}

					if (e.key !== 'Enter' && e.key !== ' ') {
						return;
					}
					callback(point)(e);
				});
				listContainer.appendChild(pointEl[0]);
			});
		}
	}

	// Define the custom element
	customElements.define('pick-up-points-modal', PickUpPointsModal);

	const checkoutBlock = document.querySelector(
		'.wc-block-checkout'
	);

	// Because WordPress is now 50% react we have to use space age technology to implement stone age methods to figure out
	// when the element we want is ready to be interacted with...
	// Believe me, I spent 3 days trying to figure out a "correct" way, but there is nothing to hook into and not a single
	// event we can use. The new block system is a shit-show start to finish.
	let loaded = false;
	let refreshTimeout = undefined;
	const observer = new MutationObserver(
		function (mutationsList, observer) {
			for (const mutation of mutationsList) {
				if (mutation.type !== 'childList') {
					continue;
				}
				// Check if the .wc-block-components-shipping-rates-control element exists
				const el = document.querySelector(
					// Trust that WooCommerce never changes this?
					'.wc-block-components-shipping-rates-control'
				);
				if (!el || el.children.length <= 0) {
					continue;
				}
				if (! loaded) {
					document.dispatchEvent(new CustomEvent('bfg-shipping-rates-loaded', {detail: {element: el}}));
					loaded = true;
				}
				const mask = document.querySelector(
					// Trust that WooCommerce never changes this?
					'.wc-block-components-loading-mask'
				);
				if (! mask) {
					continue;
				}
				if (refreshTimeout) {
					clearTimeout(refreshTimeout);
				}
				refreshTimeout = setTimeout(utility.refreshPickUpPoints, 1000);
			}
		}
	);
	if (checkoutBlock) {
		observer.observe(checkoutBlock, {childList: true, subtree: true});
	}

	let lastStartTime = 0;
	function monitorNetworkRequests(callback) {
		const observer = new PerformanceObserver((list, ob) => {
			if (! requireUpdate) {
				return;
			}
			const entries = list.getEntries();
			for (const entry of entries) {
				if (entry.initiatorType !== 'fetch') {
					continue;
				}
				if (! entry.name.includes('%2Fwc%2Fstore%2Fv1%2Fbatch')) {
					console.log('wrong name: ' + entry.name)
					continue;
				}
				if (entry.startTime <= lastStartTime) {
					continue;
				}
				lastStartTime = entry.startTime + 1;
				// Stop monitoring after detecting the request
				callback();
			}
		});

		observer.observe({type: 'resource', buffered: true});
	}

})(jQuery);
