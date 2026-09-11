/**
 * The Bring booking box of the order screen.
 *
 * The browser never builds the form. It collects what the shop worker typed,
 * sends it, and puts the markup that comes back in place of the old box. So the
 * form lives in one place, in PHP.
 */

import { initCustomSelects } from './custom-select.js';

const SAVE_DELAY = 600;

let saveTimer = null;

/** Return the box element that holds the given node. */
function boxOf(node) {
	return node ? node.closest('[data-bfg-booking]') : null;
}

/** Read the whole form out of the box. */
function readForm(box) {
	const form = {
		additional_services: [],
		packages: [],
	};

	box.querySelectorAll('[data-field]').forEach((input) => {
		form[input.dataset.field] = input.value;
	});

	box.querySelectorAll('[data-vas]').forEach((input) => {
		if (input.checked) {
			form.additional_services.push(input.dataset.vas);
		}
	});

	box.querySelectorAll('[data-bfg-package]').forEach((row) => {
		const line = {};

		row.querySelectorAll('[data-package-field]').forEach((input) => {
			line[input.dataset.packageField] = input.value;
		});

		form.packages.push(line);
	});

	return form;
}

function setBusy(box, busy) {
	const indicator = box.querySelector('[data-bfg-busy]');

	if (indicator) {
		indicator.hidden = !busy;
	}

	box.querySelectorAll('button').forEach((button) => {
		button.disabled = busy;
	});
}

/** Send the box to WordPress and show the markup that comes back. */
async function send(box, action) {
	const token = box.querySelector('[data-bfg-form]')?.dataset.token || '';

	setBusy(box, true);

	try {
		const response = await fetch(box.dataset.url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': box.dataset.nonce,
			},
			body: JSON.stringify({ action, token, form: readForm(box) }),
		});

		const answer = await response.json();

		if (!answer || typeof answer.html !== 'string') {
			throw new Error(answer?.message || 'The server sent no box.');
		}

		replace(box, answer.html);
	} catch (error) {
		setBusy(box, false);
		window.alert(error.message);
	}
}

/**
 * Put the markup that came back in place of the old box.
 *
 * The select widget is built from the markup, so a fresh box needs a fresh
 * widget. initCustomSelects skips a select it has already built.
 */
function replace(box, html) {
	const holder = document.createElement('div');
	holder.innerHTML = html;

	const fresh = holder.firstElementChild;

	box.replaceWith(fresh);
	initCustomSelects(fresh);
}

/** Keep the draft, but wait until the shop worker stops typing. */
function saveLater(box) {
	window.clearTimeout(saveTimer);
	saveTimer = window.setTimeout(() => send(box, 'save'), SAVE_DELAY);
}

document.addEventListener('input', (event) => {
	const box = boxOf(event.target);

	if (box && event.target.matches('[data-field], [data-package-field]')) {
		saveLater(box);
	}
});

document.addEventListener('change', (event) => {
	const box = boxOf(event.target);

	if (!box) {
		return;
	}

	// A new service brings other extra services and other fields, so the box
	// redraws at once instead of waiting for the typing to stop.
	if (event.target.matches('[data-bfg-reload]')) {
		window.clearTimeout(saveTimer);
		send(box, 'save');

		return;
	}

	if (event.target.matches('[data-vas], [data-field], [data-package-field]')) {
		saveLater(box);
	}
});

document.addEventListener('click', (event) => {
	const box = boxOf(event.target);

	if (!box) {
		return;
	}

	const button = event.target.closest('button');

	if (!button) {
		return;
	}

	if (button.hasAttribute('data-bfg-add-package')) {
		const template = box.querySelector('[data-bfg-package-template]');
		const rows = box.querySelector('[data-bfg-packages]');

		if (template && rows) {
			rows.appendChild(template.content.cloneNode(true));
			saveLater(box);
		}

		return;
	}

	if (button.hasAttribute('data-bfg-remove-package')) {
		const rows = box.querySelectorAll('[data-bfg-package]');

		// A booking needs at least one package, so the last row stays.
		if (rows.length > 1) {
			button.closest('[data-bfg-package]').remove();
			saveLater(box);
		}

		return;
	}

	if (button.hasAttribute('data-bfg-book')) {
		window.clearTimeout(saveTimer);
		send(box, 'book');

		return;
	}

	if (button.hasAttribute('data-bfg-reset')) {
		window.clearTimeout(saveTimer);
		send(box, 'reset');

		return;
	}

	if (button.hasAttribute('data-bfg-again')) {
		send(box, 'form');
	}
});

/** Build the select widget of every box already on the page. */
function start() {
	document.querySelectorAll('[data-bfg-booking]').forEach(initCustomSelects);
}

// A module script runs after the document is parsed, so the ready event may
// already be gone.
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', start);
} else {
	start();
}
