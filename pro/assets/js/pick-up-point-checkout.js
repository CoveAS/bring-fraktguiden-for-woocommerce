(function ($) {
	// Assign data from localised js object
	let pickUpPoints = window._fraktguiden_data.pick_up_points;
	let selectedPickUpPoint = window._fraktguiden_data.selected_pick_up_point;

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

	const utility = {
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
	const init = function () {
		// Set items
		pickerEl = $('.bring-fraktguiden-pick-up-point-picker');
		// modalEl = $('.bring-fraktguiden-pick-up-points-modal');
		// listEl = modalEl.find('.bfg-pupm__list');

		// Move modal to end of body
		// $('body').append(modalEl);


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
				listEl.text(_fraktguiden_data.i18n.ERROR_LOADING_PICK_UP_POINTS);
			},
			fetchPickUpPointsDone: function (response) {
				// Update values from response
				window._fraktguiden_data.selected_pick_up_point = response.selected_pick_up_point;
				selectedPickUpPoint = response.selected_pick_up_point;
				window._fraktguiden_data.pick_up_points = response.pick_up_points;
				pickUpPoints = response.pick_up_points;

				utility.renderSelectedPickUpPoint(selectedPickUpPoint);

				let modal = document.querySelector('pick-up-points-modal');
				modal.setPickUpPoints(pickUpPoints, handlers.selectPickUpPointHandler);
				listEl.unblock();

				// if (modalEl.is(':visible')) {
				// 	focusItem();
				// }
			}
		};

		// Bind modal clicks and key-presses


		modalEl.setPickUpPoints(
			_fraktguiden_data.pick_up_points,
			handlers.selectPickUpPointHandler
		);
		modalEl.open();

	};


	/**
	 * Block checkout
	 */
	const blockCheckout = function (e) {
		init();

		const shippingOptionsEl = e.detail.element;
		const inputs = $(shippingOptionsEl).find('input')

		console.log(inputs, $(shippingOptionsEl));

		console.log(utility.getShippingRates())
		const shippingRates = utility.getShippingRates();

		for (let i = 0; i < shippingRates.length; i++) {
			const shippingRate = shippingRates[i];
			console.log(shippingRate);
			if (!utility.usesPickUpPoint(shippingRate.rate_id)) {
				continue;
			}
			const inputEl = $('[value="' + shippingRate.rate_id + '"]')
			utility.renderSelectedPickUpPoint(_fraktguiden_data.selected_pick_up_point);


			$(inputEl).parent().append(pickerEl);
			pickerEl.show();


			console.log(inputEl.value);
		}

		$('.bfg-pup__change').on('click', () => modalEl.open());

		// Bind callback to the set-selected-shipping-rate action
		// This is triggered when selecting a shipping option
		wp.hooks.addAction(
			'woocommerce_blocks-checkout-set-selected-shipping-rate',
			'bring-fraktguiden-for-woocommerce',
			function () {
				console.log('updated')
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

	$(document).on(
		'updated_checkout',
		function () {
			console.log('updated checkout');
		}
	);

	/**
	 * Classic checkout
	 */
	const classicCheckout = function () {
		let previous = $('#shipping_method .shipping_method:checked').val();
		$(document).on(
			'updated_checkout',
			function (event, data) {
				const el = pickerEl();
				const current = $('#shipping_method .shipping_method:checked').val();
				let changed = current !== previous;
				if (changed) {
					previous = current;
				}
				if (!el.length || !modalEl.length) {
					return;
				}
				pickerEl.show();
				pickerEl.block(blockArgs);


				const focusItem = function () {
					// find selected pick up point and focus it
					let selected = listEl.find('.bfg-pupm__item').filter(function () {
						return $(this).data('id') === selectedPickUpPoint.id;
					});
					if (!selected.length) {
						selected = listEl.find('.bfg-pupm__item').first().focus();
					}
					selected.focus();
					setTimeout(function () {
						selected.focus();
					}, 100);
					console.log(selected);
				}
				const showModal = function () {
					modalEl.open();
					focusItem();
				};
				if (changed) {
					// Show the picker when selecting method with pickup points
					showModal();
				}


				// Delete items
				listEl.html('').block(blockArgs);

				$.get(
					_fraktguiden_checkout.ajaxurl,
					{action: 'bfg_get_pick_up_points'}
				).done(
					handlers.fetchPickUpPointsDone
				).fail(handlers.fetchPickUpPointsFailed);
			}
		);
	}


	// Because WordPress is now 50% react we have to use space age technology to implement stone age methods to figure out
	// when the element we want is ready to be interacted with...
	// Believe me, I spent 3 days trying to figure out a "correct" way, but there is nothing to hook into and not a single
	// event we can use. The new block system is a shit-show start to finish.
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
				document.dispatchEvent(new CustomEvent('bfg-shipping-rates-loaded', {detail: {element: el}}));
				observer.disconnect();
				break;
			}
		}
	);
	observer.observe(document.body, {childList: true, subtree: true});

	class PickUpPointsModal extends HTMLElement {
		constructor() {
			super();
			this.attachShadow({mode: 'open'});

			// Create styles for the modal
			const styles = document.createElement('style');
			styles.textContent = `

.bring-fraktguiden-pick-up-points-modal {
	display: none;
	position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 99999;
	background: #FFFFFF;
	color: #5f5f5f;
}
.bring-fraktguiden-pick-up-points-modal.open {
	display: block;
}

.bfg-pupm__header {
	display: flex;
	border-bottom: 1px solid #EEEEEE;
	margin-bottom: -1px;
	position: sticky;
	top: 0;
	background: white;
}

.bfg-pupm__instruction {
	font-size: 1rem;
	padding: 16px;
}

.bfg-pupm__close {
	margin-left: auto;
	font-size: 32px;
	line-height: 0.8;
	cursor: pointer;
	padding: 16px;
	border-left: 1px solid #EEEEEE;
	user-select: none;
	transition: color 0.2s;
	color: #5f5f5f;
}

.bfg-pupm__close:focus,
.bfg-pupm__close:hover {
	color: #c12a2a;
}
.bfg-pupm__close:focus {
	outline: 0;
	box-shadow: inset 0 0 0 2px #c12a2a;
}

.bfg-pupm__wrap {
	height: 100%;
}

.bfg-pupm__inner {
	max-height: 100%;
	display: flex;
	flex-direction: column;
}

.bfg-pupm__template {
	display: none;
}

.bfg-pupm__list {
	height: 100%;
	background: white;
	overflow: auto;
}

.bfg-pupm__item {
	user-select: none;
	cursor: pointer;
	padding: 8px 16px;
	border-top: 1px solid #EEEEEE;
	line-height: 1.5;
}

.bfg-pupm__item:focus {
	outline: 0;
	box-shadow: inset 0 0 0 2px #6c97c3;
	background: #f4f9fc;
	color: #163a5f;
}
.bfg-pupm__item:hover {
	background: #f4f9fc;
	color: #163a5f;
}

.bfg-pupm__name {
	font-weight: 600;
}
.bfg-pupm__item:hover .bfg-pupm__name {
	text-decoration: underline;
}

@media screen and (min-width: 767px) {
	.bring-fraktguiden-pick-up-points-modal {
		background: rgba(0, 0, 0, 0.2);
	}
	.bfg-pupm__wrap {
		display: flex;
		align-items: center;
	}
	.bfg-pupm__inner {
		border-radius: 12px;
		overflow: hidden;
		background: #FFFFFF;
		width: 100%;
		min-height: 320px;
		max-height: 90%;
		max-height: min(90%, 960px);
		max-width: 640px;
		margin-right: auto;
		margin-left: auto;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	}

}
		`;

			const modal = document.createElement('div');
		modal.classList.add('bring-fraktguiden-pick-up-points-modal');

		modal.innerHTML = `
		  <div class="bfg-pupm__wrap">
			<div class="bfg-pupm__inner">
			  <div class="bfg-pupm__header">
				<div class="bfg-pupm__instruction">
				${_fraktguiden_data.i18n.MODAL_INSTRUCTIONS}
				</div>
				<div class="bfg-pupm__close" tabindex="0">&times;</div>
			  </div>
			  <div class="bfg-pupm__list">
				<!-- Dynamically populated list goes here -->
			  </div>
			</div>
		  </div>
		`;

			// Append styles and modal to the shadow DOM
			this.shadowRoot.append(styles, modal);

			// Create the modal structure
			const el = $(modal);
			// Close modal when clicking on the close button ✖️
			el.find('.bfg-pupm__close').on('click',  (e) => {
				e.preventDefault();
				this.close();
			}).on('keyup', (e) => {
				if (e.key !== 'Enter' && e.key !== ' ') {
					return;
				}
				e.preventDefault();
				this.close();
			});

			// Close modal when clicking on backdrop or the [esc] key
			el.on('click', (e) => {
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
			el.find('.bfg-pupm__inner').on(
				'click',
				function (e) {
					e.preventDefault();
					e.stopPropagation();
				}
			);
		}

		// Open the modal
		open() {
			this.shadowRoot.querySelector('.bring-fraktguiden-pick-up-points-modal').classList.add('open');
		}

		// Close the modal
		close() {
			this.shadowRoot.querySelector('.bring-fraktguiden-pick-up-points-modal').classList.remove('open');
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

})(jQuery);
