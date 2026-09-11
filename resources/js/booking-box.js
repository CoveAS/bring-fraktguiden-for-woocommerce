/**
 * The Bring booking box of the order screen.
 *
 * The browser never builds the form. It collects what the shop worker typed,
 * sends it, and puts the markup that comes back in place of the old box. So the
 * form lives in one place, in PHP.
 *
 * A draft save is the one send that redraws nothing. The shop worker already
 * holds the true form, so the box waits for no answer and keeps the caret.
 */

import { initCustomSelects } from './custom-select.js';

const SAVE_DELAY = 600;

// A save faster than this shows no word at all, so the status line does not
// blink on every pause in the typing.
const SLOW_SAVE = 300;

let saveTimer = null;
let slowTimer = null;

// The order screen holds one box, so one timer and one pending save are enough.
let pendingSave = null;

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

/** Read the HS code of every product row, keyed by product id. */
function readHsCodes(box) {
	const codes = {};

	box.querySelectorAll('[data-hs-product]').forEach((input) => {
		codes[input.dataset.hsProduct] = input.value;
	});

	return codes;
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

/** Post the box to WordPress and return what comes back. */
async function post(box, action) {
	const token = box.querySelector('[data-bfg-form]')?.dataset.token || '';

	const response = await fetch(box.dataset.url, {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': box.dataset.nonce,
		},
		body: JSON.stringify({ action, token, form: readForm(box), hs_codes: readHsCodes(box) }),
	});

	if (!response.ok) {
		throw new Error('The server refused the box.');
	}

	return response.json();
}

/** Send the box to WordPress and show the markup that comes back. */
async function send(box, action) {
	setBusy(box, true);

	try {
		const answer = await post(box, action);

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
 * Keep the draft without a redraw.
 *
 * The shop worker holds the true form, so the answer carries no markup. The box
 * stays as it is, and the caret and the scroll position stay with it.
 */
function saveDraft(box) {
	window.clearTimeout(slowTimer);
	slowTimer = window.setTimeout(() => setStatus(box, 'saving'), SLOW_SAVE);

	pendingSave = post(box, 'save').then(
		() => endSave(box, 'saved'),
		() => endSave(box, 'failed')
	);

	return pendingSave;
}

function endSave(box, state) {
	window.clearTimeout(slowTimer);
	setStatus(box, state);
}

/**
 * Tell the shop worker where the draft stands.
 *
 * The state is 'saving', 'saved' or 'failed'. A booking of a failed draft is
 * refused, because the shop worker cannot see what the order now holds.
 */
function setStatus(box, state) {
	const line = box.querySelector('[data-bfg-status]');
	const book = box.querySelector('[data-bfg-book]');
	const failed = state === 'failed';

	if (line) {
		const when = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

		line.textContent = state === 'saved'
			? line.dataset.saved.replace('%s', when)
			: line.dataset[state];
		line.classList.toggle('bfg-booking-form__status--failed', failed);
		line.hidden = false;
	}

	// A save in flight settles nothing, so the button waits for the answer.
	if (!book || state === 'saving') {
		return;
	}

	// The button is also off when the order carries no Bring shipping line, so
	// only a block this code put on comes off again.
	if (failed && !book.disabled) {
		book.disabled = true;
		book.dataset.bfgBlocked = '';
	} else if (!failed && 'bfgBlocked' in book.dataset) {
		book.disabled = false;
		delete book.dataset.bfgBlocked;
	}
}

/**
 * Book the order, but let the save in flight finish first.
 *
 * A booking clears the draft, and a save that lands after it would write a
 * draft back onto a booked order.
 */
async function book(box) {
	setBusy(box, true);
	await pendingSave;
	send(box, 'book');
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
	const line = box.querySelector('[data-bfg-status]');

	// What the line says is true of the last save, not of the new typing.
	if (line) {
		line.hidden = true;
	}

	window.clearTimeout(saveTimer);
	window.clearTimeout(slowTimer);
	saveTimer = window.setTimeout(() => saveDraft(box), SAVE_DELAY);
}

// The product id of the last marked row. A shift click marks from here.
let hsAnchor = null;

/** Mark a row of the HS code list, or the whole range down from the anchor. */
function markRow(box, mark, extend) {
	const marks = [...box.querySelectorAll('[data-bfg-hs-mark]')];
	const from = marks.findIndex((row) => row.dataset.bfgHsMark === hsAnchor);
	const to = marks.indexOf(mark);

	if (extend && from !== -1) {
		marks.slice(Math.min(from, to), Math.max(from, to) + 1).forEach((row) => {
			row.checked = mark.checked;
		});

		// A shift click also selects the text between the two rows.
		window.getSelection().removeAllRanges();
	}

	hsAnchor = mark.dataset.bfgHsMark;
	syncToggle(box);
}

/** Show whether every product now has an HS code. */
function syncHsStatus(box) {
	const panel = box.querySelector('[data-bfg-hs-panel]');

	if (!panel) {
		return;
	}

	const missing = [...box.querySelectorAll('[data-hs-product]')]
		.filter((field) => '' === field.value.trim()).length;
	const count = panel.querySelector('[data-bfg-hs-count]');

	panel.classList.toggle('bfg-customs-products--ok', 0 === missing);

	if (count) {
		count.textContent = missing
			? count.dataset.missing.replace('%d', missing)
			: count.dataset.done;
	}
}

/** Tell the toggle button what it does next. */
function syncToggle(box) {
	const button = box.querySelector('[data-bfg-hs-toggle]');

	if (button) {
		button.textContent = allMarked(box) ? button.dataset.deselect : button.dataset.select;
	}
}

/** Is every row of the HS code list marked? */
function allMarked(box) {
	const marks = [...box.querySelectorAll('[data-bfg-hs-mark]')];

	return marks.length > 0 && marks.every((mark) => mark.checked);
}

/** Write the typed code into every marked row of the HS code list. */
function setMarkedCodes(box) {
	const code = box.querySelector('[data-bfg-hs-bulk]')?.value.trim() ?? '';
	let written = false;

	box.querySelectorAll('[data-bfg-hs-mark]').forEach((mark) => {
		const field = box.querySelector(`[data-hs-product="${mark.dataset.bfgHsMark}"]`);

		if (mark.checked && field) {
			field.value = code;
			written = true;
		}
	});

	if (written) {
		syncHsStatus(box);
		saveLater(box);
	}
}

document.addEventListener('input', (event) => {
	const box = boxOf(event.target);

	if (box && event.target.matches('[data-field], [data-package-field], [data-hs-product]')) {
		if (event.target.matches('[data-hs-product]')) {
			syncHsStatus(box);
		}

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
		send(box, 'reload');

		return;
	}

	if (event.target.matches('[data-vas], [data-field], [data-package-field], [data-hs-product]')) {
		saveLater(box);
	}
});

document.addEventListener('click', (event) => {
	const box = boxOf(event.target);

	if (!box) {
		return;
	}

	const mark = event.target.closest('[data-bfg-hs-mark]');

	if (mark) {
		markRow(box, mark, event.shiftKey);

		return;
	}

	const button = event.target.closest('button');

	if (!button) {
		return;
	}

	if (button.hasAttribute('data-bfg-hs-toggle')) {
		const checked = !allMarked(box);

		box.querySelectorAll('[data-bfg-hs-mark]').forEach((mark) => {
			mark.checked = checked;
		});
		hsAnchor = null;
		syncToggle(box);

		return;
	}

	if (button.hasAttribute('data-bfg-hs-set')) {
		setMarkedCodes(box);

		return;
	}

	if (button.hasAttribute('data-bfg-add-package')) {
		const rows = box.querySelectorAll('[data-bfg-package]');
		const last = rows[rows.length - 1];

		if (last) {
			const row = last.cloneNode(true);
			const sources = last.querySelectorAll('[data-package-field]');

			// A clone carries the attribute value, so copy the typed value over it.
			row.querySelectorAll('[data-package-field]').forEach((input, index) => {
				input.value = sources[index].value;
			});

			last.after(row);
			row.querySelector('[data-package-field]').focus();
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
		book(box);

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
