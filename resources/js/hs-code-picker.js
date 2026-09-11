/**
 * The HS code picker.
 *
 * Every place that sets an HS code shows a button. The button opens one modal,
 * the modal searches the Norwegian tariff, and the chosen code goes back into
 * the hidden input beside the button.
 *
 * The whole tariff is 4587 codes, so the browser holds it and searches without
 * a call. It keeps the list in localStorage, so a shop downloads it once.
 *
 * PHP passes the route and the texts in window.bringHsCodePicker. See
 * BringFraktguiden\Customs\HsCodePicker.
 */

const STORE_KEY = 'bring-fraktguiden-hs-codes';

// The tariff changes once a year. A month old copy is close enough, and the
// server keeps its own copy for the same time.
const STORE_LIFETIME = 30 * 24 * 60 * 60 * 1000;

// A shorter query matches nearly everything, so the list stays empty until the
// shop worker has typed this much.
const MIN_QUERY = 2;

const MAX_HITS = 50;

const config = window.bringHsCodePicker || {};
const text = config.i18n || {};

/** The index, once it is loaded. */
let index = null;

/** The load in flight, so two buttons share one call. */
let loading = null;

let dialog = null;

/** What the modal writes to when a code is chosen. */
let pending = null;

/**
 * Return the code with dots, as Tolltariffen prints it.
 *
 * Tolltariffen breaks the chapter and the position off, and leaves the rest
 * whole. The code 64031200 reads 64.03.1200.
 */
function dotted(code) {
	return [code.slice(0, 2), code.slice(2, 4), code.slice(4)].filter(Boolean).join('.');
}

/** Return the whole text of a row, for the search and for the label. */
function rowText(row) {
	const position = index.positions[row[1]] || '';

	return row[2] ? `${position}, ${row[2]}` : position;
}

/** Read the index the browser saved, or null. */
function readStore() {
	try {
		const saved = JSON.parse(window.localStorage.getItem(STORE_KEY) || 'null');

		return saved && Date.now() - saved.savedAt < STORE_LIFETIME ? saved.index : null;
	} catch {
		return null;
	}
}

/**
 * Keep the index for the next screen.
 *
 * A browser that refuses to store it still works. The index then lives for the
 * one page, and the next screen fetches it again.
 */
function writeStore(value) {
	try {
		window.localStorage.setItem(STORE_KEY, JSON.stringify({ savedAt: Date.now(), index: value }));
	} catch {
		// The quota is full, or the browser stores nothing.
	}
}

/** Load the index once. */
function load() {
	if (index) {
		return Promise.resolve(index);
	}

	if (!loading) {
		const saved = readStore();

		loading = saved
			? Promise.resolve(saved)
			: fetch(config.url, { credentials: 'same-origin', headers: { 'X-WP-Nonce': config.nonce } })
				.then((response) => response.json())
				.then((value) => {
					writeStore(value);

					return value;
				});

		loading = loading.then((value) => {
			index = value;

			return value;
		});
	}

	return loading;
}

/** Return the rows that match what the shop worker typed. */
function search(query) {
	const digits = query.replace(/\D/g, '');
	const words = query.toLowerCase().split(/\s+/).filter(Boolean);
	const hits = [];

	for (const row of index.codes) {
		if (hits.length >= MAX_HITS) {
			break;
		}

		// A typed number is a code, so it matches from the first digit.
		if (digits && row[0].startsWith(digits)) {
			hits.push(row);

			continue;
		}

		const haystack = rowText(row).toLowerCase();

		if (!digits && words.every((word) => haystack.includes(word))) {
			hits.push(row);
		}
	}

	return hits;
}

/** Build the modal. One screen holds one. */
function build() {
	dialog = document.createElement('dialog');
	dialog.className = 'bfg-modal bfg-hs-modal';
	dialog.innerHTML = `
		<form method="dialog" class="bfg-modal__head">
			<h2 class="bfg-modal__title"></h2>
			<button type="submit" class="bfg-modal__close" value=""></button>
		</form>
		<input type="search" class="bfg-hs-modal__search" autocomplete="off">
		<p class="bfg-hs-modal__hint"></p>
		<ul class="bfg-hs-modal__list"></ul>
	`;

	dialog.querySelector('.bfg-modal__title').textContent = text.title || '';
	dialog.querySelector('.bfg-modal__close').textContent = text.close || '';
	dialog.querySelector('.bfg-modal__close').setAttribute('aria-label', text.close || '');
	dialog.querySelector('.bfg-hs-modal__search').placeholder = text.search || '';

	dialog.querySelector('.bfg-hs-modal__search').addEventListener('input', (event) => {
		render(event.target.value.trim());
	});

	dialog.querySelector('.bfg-hs-modal__list').addEventListener('click', (event) => {
		const hit = event.target.closest('[data-code]');

		if (hit) {
			choose(hit.dataset.code);
		}
	});

	// The close button and the escape key both end the dialog without a code.
	dialog.addEventListener('close', () => {
		pending = null;
	});

	document.body.append(dialog);
}

/** Show the hits of one query. */
function render(query) {
	const list = dialog.querySelector('.bfg-hs-modal__list');
	const hint = dialog.querySelector('.bfg-hs-modal__hint');

	list.textContent = '';

	if (!index) {
		hint.textContent = text.loading || '';

		return;
	}

	if (query.length < MIN_QUERY) {
		hint.textContent = text.hint || '';

		return;
	}

	const hits = search(query);

	hint.textContent = hits.length ? '' : text.empty || '';

	for (const row of hits) {
		const item = document.createElement('li');
		const button = document.createElement('button');

		button.type = 'button';
		button.className = 'bfg-hs-modal__hit';
		button.dataset.code = row[0];
		button.innerHTML = '<span class="bfg-hs-modal__hit-code"></span><span class="bfg-hs-modal__hit-text"></span>';
		button.querySelector('.bfg-hs-modal__hit-code').textContent = dotted(row[0]);
		button.querySelector('.bfg-hs-modal__hit-text').textContent = rowText(row);

		item.append(button);
		list.append(item);
	}
}

/** Hand the chosen code to whoever opened the modal. */
function choose(code) {
	const target = pending;

	pending = null;
	dialog.close();

	if (target) {
		target(code);
	}
}

/**
 * Open the modal, and call back with the chosen code.
 *
 * Nothing calls back when the shop worker closes the modal.
 */
export async function pickHsCode(onPick) {
	if (!dialog) {
		build();
	}

	pending = onPick;

	const field = dialog.querySelector('.bfg-hs-modal__search');

	field.value = '';
	render('');

	// The modal opens before the tariff is there, so a click answers at once.
	dialog.showModal();
	field.focus();

	await load();
	render(field.value.trim());
}

/** Return the hidden input a picker button writes to. */
function fieldOf(button) {
	return button.parentElement.querySelector('input[type="hidden"]');
}

/** Show the code and its text on one button. */
function label(button) {
	const code = fieldOf(button)?.value || '';

	// A shop may hold a longer code than the tariff names here, for example an
	// eight digit Norwegian number. The first six digits still name the goods.
	const row = code && index
		? index.codes.find((line) => line[0] === code.slice(0, 6))
		: null;

	button.querySelector('[data-bfg-hs-code]').textContent = code ? dotted(code) : text.choose || '';
	button.querySelector('[data-bfg-hs-text]').textContent = row ? rowText(row) : '';
	button.classList.toggle('bfg-hs-button--empty', !code);
}

/**
 * Show the text of every code already on the screen.
 *
 * PHP prints the code alone, because the tariff lives in the browser.
 */
export function showHsCodeLabels(root = document) {
	const buttons = [...root.querySelectorAll('[data-bfg-hs-pick]')];

	if (!buttons.length) {
		return;
	}

	load().then(() => buttons.forEach(label));
}

/** Write a code into the field of one button, and tell the form about it. */
export function setHsCode(button, code) {
	const field = fieldOf(button);

	if (!field) {
		return;
	}

	field.value = code;
	label(button);
	field.dispatchEvent(new Event('input', { bubbles: true }));
}

document.addEventListener('click', (event) => {
	const button = event.target.closest('[data-bfg-hs-pick]');

	if (button) {
		pickHsCode((code) => setHsCode(button, code));
	}
});

// A code already on the screen needs its text, and the text lives in the index.
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => showHsCodeLabels());
} else {
	showHsCodeLabels();
}
