
(function ($) {
	// Assign data from localised js object
	let pickUpPoints = window._fraktguiden_data.pick_up_points;
	let selectedPickUpPoint = window._fraktguiden_data.selected_pick_up_point;
	let loadedShippingKey = window._fraktguiden_data.shipping_key;
	let requireUpdate = false;

	// Global picker element variable
	let pickerEl;


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


	/**
	 * Pick Up Point Modal
	 */
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
			jqEl.find('.bfg-pupm__close').on('click', (e) => {
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

	let getRequest = undefined;

	/**
	 * Utility
	 * General purpose functions used by both the block and classic checkout
	 */
	const utility = {
		refreshPickUpPoints: function () {
			if (!requireUpdate || utility.getShippingKey() === loadedShippingKey) {
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
			if (! pickUpPoint) {
				return;
			}
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

				const el = $('.woocommerce-shipping-totals, .wp-block-woocommerce-checkout-shipping-methods-block');
				el.block(blockArgs);

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
						el.unblock();
					}
				).done(function () {
					el.unblock();
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
			window._fraktguiden_data.shipping_key = response.shipping_key;
			loadedShippingKey = response.shipping_key;

			if (selectedPickUpPoint) {
				utility.renderSelectedPickUpPoint(selectedPickUpPoint);
			}
			if (pickUpPoints) {
				modalEl.setPickUpPoints(pickUpPoints, handlers.selectPickUpPointHandler);
			}
			pickerEl.unblock();
		}
	};

	// Define the custom element
	customElements.define('pick-up-points-modal', PickUpPointsModal);

	// Create modal
	const modalEl = document.createElement('pick-up-points-modal');
	document.body.appendChild(modalEl);

	// Bind modal clicks and key-presses
	if (_fraktguiden_data.pick_up_points) {
		modalEl.setPickUpPoints(
			_fraktguiden_data.pick_up_points,
			handlers.selectPickUpPointHandler
		);
	}

	/**
	 * Block checkout
	 */
	const blockCheckout = function (e) {
		// Set items
		pickerEl = $('.bring-fraktguiden-pick-up-point-picker').first().clone();

		const shippingOptionsEl = e.detail.element;
		const inputs = $(shippingOptionsEl).find('input')
		const shippingRates = bring_fraktguiden_for_woocommerce.getShippingRates();

		/**
		 * Get picker on shippingRate
		 * @param rate
		 * @returns {*}
		 */
		const getPicker = function (rate) {
			const inputEl = $('[value="' + rate.rate_id + '"]')
			const control = inputEl.parent();
			pickerEl = control.find('.bring-fraktguiden-pick-up-point-picker');
			if (!pickerEl.length) {
				// Create a new element if the picker is not found
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

		let timeout = undefined;

		monitorNetworkRequests(() => {
			if (timeout) {
				clearTimeout(timeout);
			}
			timeout = setTimeout(utility.refreshPickUpPoints, 100);
		});

		let monitorTimeout = undefined;
		let shippingKey = utility.getShippingKey();
		wp.data.subscribe(() => {
			if (window.localStorage.getItem('WOOCOMMERCE_CHECKOUT_IS_CUSTOMER_DATA_DIRTY') === 'true') {
				// Don't do me dirty
				return;
			}

			const currentShippingKey = utility.getShippingKey();
			if (shippingKey === currentShippingKey) {
				return;
			}
			shippingKey = currentShippingKey;
			requireUpdate = true;

			// Give the monitor a time limit to detect changes
			if (monitorTimeout) {
				clearTimeout(monitorTimeout);
			}
			monitorTimeout = setTimeout(
				function () {
					if (!requireUpdate) {
						return;
					}
					console.warn('Timeout on detect wc/store/batch update');
					utility.refreshPickUpPoints();
				},
				5000
			);
		});

		// Ensure pick-up points are updated whenever WooCommerce triggers a reload in the checkout.
		// This can occur when the country is changed or if changes are made after `refreshPickUpPoints`
		// has started but before it finishes. We use a timeout to debounce updates and avoid conflicts.
		let refreshTimeout;
		document.addEventListener(
			'bfg-block-shipping-rates-updating',
			function () {
				if (refreshTimeout) {
					clearTimeout(refreshTimeout);
				}
				refreshTimeout = setTimeout(utility.refreshPickUpPoints, 5000);
			}
		);

		/**
		 * Monitor all state changes
		 * React to changes in selected rate
		 */
		let currentRateId = '';
		wp.data.subscribe(
			function () {
				let rates = bring_fraktguiden_for_woocommerce.getShippingRates();
				let shouldHide = true;
				for (let i = 0; i < rates.length; i++) {
					const rate = rates[i];
					if (!rate.selected || rate.method_id !== 'bring_fraktguiden') {
						// Not selected, or not a bring method
						continue;
					}

					if (! currentRateId) {
						currentRateId = rate.rate_id;
					}


					if (!utility.usesPickUpPoint(rate.rate_id)) {
						// Doesn't support pick up points
						continue;
					}
					shouldHide = false;

					if (rate.rate_id === currentRateId) {
						// No change
						continue;
					}
					currentRateId = rate.rate_id;
					// Selected rate supports pick up points
					getPicker(rate).show();

					requireUpdate = true;
					utility.refreshPickUpPoints();
					return;
				}

				// No rate selected that supports pick up points
				if (shouldHide) {
					pickerEl.hide();
				}
			}
		);
	}

	// Initialise block checkout
	document.addEventListener('bfg-block-shipping-rates-loaded', blockCheckout);

	let lastStartTime = 0;

	function monitorNetworkRequests(callback) {
		const observer = new PerformanceObserver((list, ob) => {
			if (!requireUpdate) {
				return;
			}
			const entries = list.getEntries();
			for (const entry of entries) {
				if (entry.initiatorType !== 'fetch') {
					continue;
				}
				if (
					!entry.name.includes('%2Fwc%2Fstore%2Fv1%2Fbatch') &&
					!entry.name.includes('wp-json/wc/store/v1/batch')
				) {
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

	/**
	 * Classic checkout
	 */
	(() => {
		let previous = $('#shipping_method .shipping_method:checked').val();
		$(document).on(
			'updated_checkout',
			function (event, data) {
				const current = $('#shipping_method .shipping_method:checked').val();
				pickerEl = $('.bring-fraktguiden-pick-up-point-picker');
				pickerEl.find('.bfg-pup__change').on('click', () => modalEl.open());

				let changed = current !== previous;
				if (changed) {
					previous = current;
				}
				pickerEl.show();
				utility.renderSelectedPickUpPoint(selectedPickUpPoint)
				utility.refreshPickUpPoints();
			}
		);
	})();

})(jQuery);
