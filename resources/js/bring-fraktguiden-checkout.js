
window.bring_fraktguiden_for_woocommerce = {
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
		const packages = bring_fraktguiden_for_woocommerce.getPackages();
		let shippingRates = [];
		for (let i = 0; i < packages.length; i++) {
			const rates = packages[i].shipping_rates;
			shippingRates = shippingRates.concat(rates);
		}
		return shippingRates;
	},
};

jQuery(function ($) {

	const unblock_options = () => {
		$ ( '.bring-fraktguiden-date-options' ).unblock();
	};
	const block_options = () => {
		$ ( '.bring-fraktguiden-date-options' ).block(
			{
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			}
		);
	};
	$(document).on( 'update_checkout', block_options );

	function oneColumnShipping() {
		const tr = $('.woocommerce-shipping-totals');
		if (tr.children.length !== 2) {
			return;
		}
		const header = tr.find('th');
		const cell = tr.find('td');
		if (! header.length || ! cell.length) {
			return;
		}
		// Move the header and give it colspan 2
		header.attr('colspan', 2)
		tr.before(
			$('<tr>').append(header)
		);
		// Give cell colspan 2
		cell.attr('colspan', 2)
	}
	if (_fraktguiden_checkout.one_column_shipping) {
		$(document).on( 'updated_checkout', oneColumnShipping );
		oneColumnShipping();
	}

	let busy = false;
	const select_time_slot = function () {
		if (busy) {
			return;
		}
		busy = true;
		const elem = $( this );
		$('.alternative-date-item--chosen')
			.removeClass( 'alternative-date-item--chosen alternative-date-item--selected' );
		elem.addClass( 'alternative-date-item--chosen alternative-date-item--selected' );
		block_options();
		$.post(
			_fraktguiden_checkout.ajaxurl,
			{
				action: 'bring_select_time_slot',
				time_slot: elem.data( 'time_slot' ),
			},
			function () {
				busy = false;
				unblock_options();
			}
		);
	};

	const bind_buttons = () => {
		$( '.bring-fraktguiden-date-options .alternative-date-item--choice' ).on(
			'click',
			select_time_slot
		);

		$('.bring-fraktguiden-logo, .bring-fraktguiden-description, .bring-fraktguiden-environmental, .bring-fraktguiden-eta')
			.on('click', function () { $(this).closest('li').find('input').trigger('click');})
	};

	$( document ).on( 'updated_checkout', bind_buttons );
	bind_buttons();

	const transformData = (metaData) => {
		const metaObject = metaData.reduce((acc, meta) => {
			if (meta.key && meta.value !== undefined) {
				acc[meta.key] = meta.value;
			}
			return acc;
		}, {});
		return metaObject;
	};

	/*
	@TODO
	// Block checkout:
	document.addEventListener(
		'bfg-block-shipping-rates-loaded',
		function (e) {
			const el = $(e.detail.element);
			console.log(el);
			const shippingRates = bring_fraktguiden_for_woocommerce.getShippingRates();
			for (let i = 0; i < shippingRates.length; i++) {
				const rate = shippingRates[i];

				const inputEl = $('[value="' + rate.rate_id + '"]')
				const control = inputEl.parent();
				const layout = control.find('.wc-block-components-radio-control__option-layout');


				if (rate.method_id !== 'bring_fraktguiden') {
					console.log('not bring rate')
					continue;
				}

				const decoration = $('<div class="bring-fraktguiden-rate">');
				decoration.append(layout.children())

				layout.append(decoration);

				const metaData = (transformData(rate.meta_data))
				console.log(metaData)
				if (metaData.bring_eta) {
					decoration.append($(`
						<div class="bring-fraktguiden-eta">${_fraktguiden_checkout.i18n.expected_delivery}: ${metaData.bring_eta}</div>',
					`));
				}
				if (metaData.bring_logo_url) {
					decoration.append($(`
						<img class="bring-fraktguiden-logo" alt="${metaData.bring_logo_alt}" src="${metaData.bring_logo_url}">
					`));
				}
				if (metaData.bring_description) {
					decoration.append($(`
						<div className="bring-fraktguiden-description">${metaData.bring_description}</div>
					`));
				}

				if (metaData.bring_environmental_logo_url && metaData.bring_environmental_description) {
					decoration.append($(`
						<div class="bring-fraktguiden-environmental">
							<img class="environmental-logo" src="${metaData.bring_environmental_logo_url}">
							<span>${metaData.bring_environmental_description}</span>
						</div>
					`));
				}


			}
			console.log(shippingRates);
		}
	);
	 */


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
				if (!loaded) {
					document.dispatchEvent(new CustomEvent('bfg-block-shipping-rates-loaded', {detail: {element: el}}));
					loaded = true;
				}
				const mask = document.querySelector(
					// Trust that WooCommerce never changes this?
					'.wc-block-components-loading-mask'
				);
				if (!mask) {
					continue;
				}
				document.dispatchEvent(new CustomEvent('bfg-block-shipping-rates-updating', {detail: {element: el}}));
			}
		}
	);

	const checkoutBlock = document.querySelector('.wc-block-checkout');
	if (checkoutBlock) {
		observer.observe(checkoutBlock, {childList: true, subtree: true});
	}

});
