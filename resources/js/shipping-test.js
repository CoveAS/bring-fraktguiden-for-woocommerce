/**
 * The shipping test block. The setup page and the product screen both use it.
 *
 * The answer comes back as ready made HTML, so this file only sends the values
 * and puts the answer in place.
 */
document.addEventListener('click', async (event) => {
	const box = event.target.closest('.bfg-shipping-test');
	if (!box) return;

	const dialog = box.querySelector('.bfg-shipping-test__dialog');

	if (event.target.closest('.bfg-shipping-test__raw')) {
		dialog.showModal();
		return;
	}

	if (event.target.closest('[data-bfg-dialog-close]')) {
		dialog.close();
		return;
	}

	const tab = event.target.closest('.bfg-shipping-test__tab');
	if (tab) {
		dialog.querySelectorAll('.bfg-shipping-test__tab').forEach((other) => {
			const chosen = other === tab;
			other.setAttribute('aria-selected', String(chosen));
			dialog.querySelector('#' + other.getAttribute('aria-controls')).hidden = !chosen;
		});
		return;
	}

	const button = event.target.closest('.bfg-shipping-test__run');
	if (!button) return;

	const result = box.querySelector('.bfg-shipping-test__result');

	button.disabled = true;
	result.textContent = box.dataset.busy;

	try {
		const response = await fetch(box.dataset.url, {
			method: 'POST',
			credentials: 'same-origin',
			body: new URLSearchParams({
				action: 'bfg_shipping_test',
				_ajax_nonce: box.dataset.nonce,
				product_id: box.dataset.product,
				postcode: box.querySelector('.bfg-shipping-test__postcode').value,
				country: box.querySelector('.bfg-shipping-test__country').value,
			}),
		});

		if (!response.ok) throw new Error(response.status);

		result.innerHTML = await response.text();
	} catch (error) {
		result.textContent = box.dataset.failed;
	}

	button.disabled = false;
});
