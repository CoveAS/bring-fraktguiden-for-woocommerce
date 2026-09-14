/**
 * The HS code table of the bulk booking modal.
 *
 * The modal renders in the page footer, before the shop worker picks an order.
 * So the table arrives from the server when the modal opens, and the browser
 * never builds it.
 *
 * A code is a trait of the product, so the table writes it at once. A worker
 * who closes the modal without a booking keeps the work.
 */

import { showHsCodeLabels } from './hs-code-picker.js';
import './hs-code-panel.js';

const SAVE_DELAY = 600;

let saveTimer = null;
let orders = [];

const holder = document.querySelector('[data-bfg-bulk-hs]');

/** Send the order ids and the codes, and put the fresh table in place. */
async function send(codes) {
	const answer = await fetch(holder.dataset.url, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': holder.dataset.nonce },
		body: JSON.stringify({ orders, codes }),
	});

	if (!answer.ok) {
		return;
	}

	const { html } = await answer.json();

	// A redraw while the worker types would take the caret away, so only the
	// first load writes the markup.
	if (codes) {
		return;
	}

	holder.innerHTML = html;
	showHsCodeLabels(holder);
}

/** Return the code of every row, keyed by product id. */
function readCodes() {
	const codes = {};

	holder.querySelectorAll('[data-hs-product]').forEach((field) => {
		codes[field.dataset.hsProduct] = field.value;
	});

	return codes;
}

function saveLater() {
	window.clearTimeout(saveTimer);
	saveTimer = window.setTimeout(() => send(readCodes()), SAVE_DELAY);
}

if (holder) {
	document.addEventListener('bfg:bulk-open', (event) => {
		orders = event.detail.orders;
		holder.innerHTML = '';
		send(null);
	});

	document.addEventListener('input', (event) => {
		if (holder.contains(event.target) && event.target.matches('[data-hs-product]')) {
			saveLater();
		}
	});
}
