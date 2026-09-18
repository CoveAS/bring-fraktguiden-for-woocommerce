jQuery(function ($) {
	// Assign data from localised js object
	let pickUpPoints = window._fraktguiden_data.pick_up_points;
	let selectedPickUpPoints = window._fraktguiden_data.selected_pick_up_points || {};
	let loadedShippingKey = window._fraktguiden_data.shipping_key;
	let requireUpdate = false;

	/**
	 * The element that carries the busy overlay while points load.
	 * The shipping block holds every picker, so one overlay covers them all.
	 * @returns {jQuery}
	 */
	const busyEl = function () {
		return $('.woocommerce-shipping-totals, .wp-block-woocommerce-checkout-shipping-methods-block');
	};


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
		open(selected) {
			const el = this.shadowRoot.querySelector('.bring-fraktguiden-pick-up-points-modal');
			el.classList.add('open');

			const listItems = $(el).find('.bfg-pupm__item')
			// Find selected pick up point and focus it
			let selectedItems = listItems.filter(function () {
				return selected && $(this).data('id') === selected.id;
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
				const distance = utility.formatDistance(point);
				const pointEl = $(`
					<div class="bfg-pupm__item">
						<div class="bfg-pupm__name">${point.name}</div>
						<div class="bfg-pupm__address">${address}</div>
						${distance ? `<div class="bfg-pupm__distance">${distance}</div>` : ''}
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

	/**
	 * Pick Up Point Picker
	 *
	 * One picker belongs to one shipping rate. The content sits in a shadow
	 * root, so no theme rule reaches it.
	 */
	class PickUpPointPicker extends HTMLElement {
		constructor() {
			super();
			this.attachShadow({mode: 'open'});

			const styles = document.createElement('style');
			styles.textContent = _fraktguiden_data.pick_up_point_picker_css;

			const el = document.createElement('div');
			el.classList.add('bring-fraktguiden-pick-up-point-picker');
			el.innerHTML = `
				<div class="bfg-pup__change" role="button" tabindex="0">${_fraktguiden_data.i18n.PICKER_CHANGE}</div>
				<div class="bfg-pup__name"></div>
				<div class="bfg-pup__address"></div>
				<div class="bfg-pup__opening-hours"></div>
				<div class="bfg-pup__description"></div>
				<a href="#" target="_blank" class="bfg-pup__map">${_fraktguiden_data.i18n.PICKER_MAP}</a>
			`;

			this.shadowRoot.append(styles, el);

			const change = el.querySelector('.bfg-pup__change');
			change.addEventListener('click', () => this.requestChange());
			change.addEventListener('keydown', (e) => {
				if (e.key !== 'Enter' && e.key !== ' ') {
					return;
				}
				e.preventDefault();
				this.requestChange();
			});
		}

		/**
		 * Ask for the modal. The event crosses the shadow boundary, so one
		 * listener on the document serves every picker.
		 */
		requestChange() {
			this.dispatchEvent(new CustomEvent(
				'bfg-change-pick-up-point',
				{bubbles: true, composed: true}
			));
		}

		/**
		 * Write one point into this picker.
		 * @param point
		 */
		render(point) {
			if (!point) {
				return;
			}
			const root = this.shadowRoot;
			root.querySelector('.bfg-pup__name').textContent = point.name;
			root.querySelector('.bfg-pup__address').textContent = utility.formatAddress(point);
			root.querySelector('.bfg-pup__opening-hours').textContent = point.openingHours;
			root.querySelector('.bfg-pup__description').textContent = point.description;

			const map = root.querySelector('.bfg-pup__map');
			if (_fraktguiden_checkout.map_key) {
				map.setAttribute('href', point[_fraktguiden_checkout.map_key]);
			} else {
				map.style.display = 'none';
			}
		}
	}

	customElements.define('bring-fraktguiden-pick-up-point-picker', PickUpPointPicker);

	let getRequest = undefined;

	/**
	 * Utility
	 * General purpose functions used by both the block and classic checkout
	 */
	const utility = {
		refreshPickUpPoints: function () {
			if (!requireUpdate) {
				return;
			}
			if (loadedShippingKey && utility.getShippingKey()  === loadedShippingKey) {
				return;
			}
			requireUpdate = false;
			busyEl().block(blockArgs);
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
			return value in (_fraktguiden_data.pick_up_point_rate_types || {});
		},
		/**
		 * The pickup point type of one rate
		 * @param {string} rateId
		 * @returns {string}
		 */
		typeForRate: function (rateId) {
			return (_fraktguiden_data.pick_up_point_rate_types || {})[rateId] || '';
		},
		/**
		 * The points of one type. An empty type means every point.
		 * @param {string} type
		 */
		pointsForType: function (type) {
			if (!type) {
				return pickUpPoints;
			}
			return pickUpPoints.filter((point) => point.pickupPointType === type);
		},
		/**
		 * The point the customer chose for one rate
		 * @param {string} rateId
		 */
		selectedForRate: function (rateId) {
			return selectedPickUpPoints[utility.typeForRate(rateId)];
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
		 * Format the distance to a point. Bring leaves the distance out when it
		 * knows no origin, and then this returns an empty string.
		 * @param pickUpPoint
		 * @returns {string}
		 */
		formatDistance: function (pickUpPoint) {
			const km = parseFloat(pickUpPoint.distanceInKm);
			if (!km) {
				return '';
			}
			const number = new Intl.NumberFormat(
				document.documentElement.lang || undefined,
				{maximumFractionDigits: 1}
			).format(km);
			return `${number} km`;
		},
		/**
		 * Write one point into one picker. Each rate has its own picker.
		 * @param pickUpPoint
		 * @param {jQuery} picker
		 */
		renderSelectedPickUpPoint: function (pickUpPoint, picker) {
			if (! pickUpPoint || ! picker || ! picker.length) {
				return;
			}
			picker[0].render(pickUpPoint);
		},
		/**
		 * Write the chosen point into every picker on screen
		 */
		renderAllPickers: function () {
			$('bring-fraktguiden-pick-up-point-picker').each(function () {
				const picker = $(this);
				utility.renderSelectedPickUpPoint(
					utility.selectedForRate(picker.data('rate-id') || ''),
					picker
				);
			});
		}
	};

	/**
	 * Handlers
	 */
	const handlers = {
		/**
		 * Select Pickup Point handler for one rate
		 * @param {string} rateId The rate the modal serves
		 * @param {jQuery} picker The picker of that rate
		 * @returns {function} A factory the modal calls per point
		 */
		selectHandlerFor: function (rateId, picker) {
			return function (pickUpPoint) {
				return function (e) {
					e.preventDefault();
					modalEl.close();

					const previous = utility.selectedForRate(rateId);
					if (previous && previous.id === pickUpPoint.id) {
						return;
					}

					const el = busyEl();
					el.block(blockArgs);

					// Ajax select pick up point
					$.post(
						_fraktguiden_checkout.ajaxurl,
						{
							action: 'bfg_select_pick_up_point',
							id: pickUpPoint.id,
							rate_id: rateId,
						}
					).fail(
						function (data) {
							console.error(data);
							el.unblock();
						}
					).done(function () {
						el.unblock();
						utility.renderSelectedPickUpPoint(pickUpPoint, picker);
					});
					selectedPickUpPoints[utility.typeForRate(rateId)] = pickUpPoint;
				};
			};
		},

		fetchPickUpPointsFailed: function () {
			getRequest = undefined;
			const el = document.querySelector('pick-up-points-modal');
			el.setError(_fraktguiden_data.i18n.ERROR_LOADING_PICK_UP_POINTS)
			busyEl().unblock();
		},

		fetchPickUpPointsDone: function (response) {
			getRequest = undefined;
			// Update values from response
			window._fraktguiden_data.selected_pick_up_points = response.selected_pick_up_points;
			selectedPickUpPoints = response.selected_pick_up_points || {};
			window._fraktguiden_data.pick_up_points = response.pick_up_points;
			pickUpPoints = response.pick_up_points;
			window._fraktguiden_data.shipping_key = response.shipping_key;
			loadedShippingKey = response.shipping_key;

			utility.renderAllPickers();
			busyEl().unblock();
		}
	};

	// Define the custom element
	customElements.define('pick-up-points-modal', PickUpPointsModal);

	// Create modal
	const modalEl = document.createElement('pick-up-points-modal');
	document.body.appendChild(modalEl);

	/**
	 * Fill the modal with the points of one rate, then open it
	 * @param {string} rateId
	 * @param {jQuery} picker
	 */
	const openPicker = function (rateId, picker) {
		const type = utility.typeForRate(rateId);
		modalEl.setPickUpPoints(
			utility.pointsForType(type),
			handlers.selectHandlerFor(rateId, picker)
		);
		modalEl.open(selectedPickUpPoints[type]);
	};

	// Every picker asks for the modal through this one listener.
	document.addEventListener('bfg-change-pick-up-point', function (e) {
		const picker = $(e.target);
		openPicker(picker.data('rate-id') || '', picker);
	});

	/**
	 * Block checkout
	 */
	const blockCheckout = function (e) {
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
			let picker = control.find('bring-fraktguiden-pick-up-point-picker');
			if (!picker.length) {
				// Create a new element if the picker is not found
				picker = $(document.createElement('bring-fraktguiden-pick-up-point-picker'));
				control.append(picker);
			}
			// The picker carries its rate, so any reader knows which type it shows.
			picker.data('rate-id', rate.rate_id);
			return picker;
		}

		/**
		 * Show the picker of the chosen rate and hide every other picker.
		 * Each rate with pick up points owns a picker, so a picker left alone
		 * stays on screen under a rate the customer no longer wants.
		 * @param {Array} rates
		 * @returns {string} The rate the customer chose
		 */
		const syncPickers = function (rates) {
			const selected = rates.find((rate) => rate.selected);
			const selectedRateId = selected ? selected.rate_id : '';
			for (let i = 0; i < rates.length; i++) {
				const rate = rates[i];
				if (rate.method_id !== 'bring_fraktguiden' || !utility.usesPickUpPoint(rate.rate_id)) {
					continue;
				}
				const picker = getPicker(rate);
				if (rate.rate_id !== selectedRateId) {
					picker.hide();
					continue;
				}
				utility.renderSelectedPickUpPoint(utility.selectedForRate(rate.rate_id), picker);
				picker.show();
			}
			return selectedRateId;
		};

		syncPickers(shippingRates);

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
				const selectedRateId = syncPickers(bring_fraktguiden_for_woocommerce.getShippingRates());

				if (selectedRateId === currentRateId) {
					// No change
					return;
				}
				currentRateId = selectedRateId;

				if (!utility.usesPickUpPoint(selectedRateId)) {
					return;
				}
				requireUpdate = true;
				utility.refreshPickUpPoints();
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
		const classicCheckout = function () {
			const current = $('#shipping_method .shipping_method:checked').val();
			const picker = $('bring-fraktguiden-pick-up-point-picker');
			if (!picker.length) {
				return;
			}
			picker.data('rate-id', current);

			let changed = current !== previous;
			if (changed) {
				previous = current;
			}
			picker.show();
			requireUpdate = true;
			utility.renderSelectedPickUpPoint(utility.selectedForRate(current), picker)
			loadedShippingKey = ''; // A small hack to force update the pick up points
			utility.refreshPickUpPoints();
		};
		$(document).on(
			'updated_checkout',
			classicCheckout
		);
	})();
});
