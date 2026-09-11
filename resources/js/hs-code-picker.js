import './dialog.js';

/**
 * The HS code picker.
 *
 * Every place that sets an HS code shows a button. The button opens one modal,
 * the modal searches the Norwegian tariff, and the chosen code goes back into
 * the hidden input beside the button.
 *
 * The whole tariff is 4587 codes, so the browser holds it and searches without
 * a call. The route sends an ETag, so a repeat visit gets a short 304 and the
 * browser reuses the copy in its own cache.
 *
 * PHP passes the route and the texts in window.bringHsCodePicker. See
 * BringFraktguiden\Customs\HsCodePicker.
 */

// A shorter query matches nearly everything, so the list stays empty until the
// shop worker has typed this much.
const MIN_QUERY = 2;

const MAX_HITS = 50;

/** How many rows the empty search adds at a time. */
const PAGE = 100;

/** How near the end of the list the shop worker scrolls before more rows come. */
const PAGE_MARGIN = 400;

/**
 * The search of Tolltariffen, where a shop worker can look further.
 *
 * The site reads the word from `q`, and takes `no` or `en` as the language.
 */
const TARIFF_SEARCH = 'https://tolltariffen.toll.no/import/search';


const config = window.bringHsCodePicker || {};
const text = config.i18n || {};

/** The index, once it is loaded. */
let index = null;

/** The load in flight, so two buttons share one call. */
let loading = null;

let dialog = null;

/** What the modal writes to when a code is chosen. */
let pending = null;

/** The code the field already holds, while the modal is open. */
let current = '';

/** The rows of the empty search that the list does not hold yet. */
let queue = [];

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

/**
 * Return the tariff row of a code, or null.
 *
 * A shop may hold a longer code than the tariff names here, for example an
 * eight digit Norwegian number. The first six digits still name the goods.
 */
function rowOf(code) {
	return code && index ? index.codes.find((line) => line[0] === code.slice(0, 6)) || null : null;
}

/** Load the index once. */
function load() {
	if (index) {
		return Promise.resolve(index);
	}

	if (!loading) {
		loading = fetch(config.url, { credentials: 'same-origin', headers: { 'X-WP-Nonce': config.nonce } })
			.then((response) => response.json())
			.then((value) => {
				index = value;

				return value;
			});
	}

	return loading;
}

/**
 * Return the score of one word in one text, or 0 when the text lacks it.
 *
 * A word start beats a letter inside a word, and an early hit beats a late
 * one.
 *
 * ponytail: Norwegian builds compounds, so "ullgensere" holds "genser" in the
 * same shape "reagenser" does, and both score low. A word list of Norwegian
 * stems would tell them apart.
 */
function score(haystack, word) {
	const at = haystack.indexOf(word);

	if (at < 0) {
		return 0;
	}

	const starts = 0 === at || !/[a-z0-9æøå]/.test(haystack[at - 1]);
	const whole = starts && !/[a-z0-9æøå]/.test(haystack[at + word.length] || '');

	// The later the hit sits, the less it says about the goods.
	const place = Math.max(1, 100 - at);

	return (whole ? 30000 : starts ? 20000 : 10000) + place;
}

/** Return the rows that match what the shop worker typed, best first. */
function search(query) {
	const digits = query.replace(/\D/g, '');
	const words = query.toLowerCase().split(/\s+/).filter(Boolean);
	const hits = [];

	for (const row of index.codes) {
		// A typed number is a code, so it matches from the first digit.
		if (digits) {
			if (row[0].startsWith(digits)) {
				hits.push([row, 0]);
			}

			continue;
		}

		const haystack = rowText(row).toLowerCase();
		let total = 0;

		for (const word of words) {
			const one = score(haystack, word);

			if (!one) {
				total = 0;

				break;
			}

			total += one;
		}

		if (total) {
			hits.push([row, total]);
		}
	}

	hits.sort((a, b) => b[1] - a[1]);

	return hits.slice(0, MAX_HITS).map((hit) => hit[0]);
}

/**
 * Write a text into an element, with every typed word marked.
 *
 * The text comes from the tariff, so it goes in as text nodes and never as
 * HTML.
 */
function highlight(element, value, words) {
	element.textContent = '';

	if (!words.length) {
		element.textContent = value;

		return;
	}

	const pattern = new RegExp(`(${words.map(quote).join('|')})`, 'gi');

	for (const part of value.split(pattern)) {
		if (!part) {
			continue;
		}

		// split keeps the matched words as their own parts, so a part that
		// equals a typed word is a hit.
		const hit = words.includes(part.toLowerCase());

		element.append(hit ? Object.assign(document.createElement('mark'), { textContent: part }) : part);
	}
}

/** Return a word that means itself inside a regular expression. */
function quote(word) {
	return word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

/** Build the modal. One screen holds one. */
function build() {
	dialog = document.createElement('dialog');
	dialog.className = 'bfg bfg-modal bfg-hs-modal';
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
	// The button is a small square, so it shows a cross and says its name to a
	// screen reader.
	dialog.querySelector('.bfg-modal__close').textContent = '\u00d7';
	dialog.querySelector('.bfg-modal__close').setAttribute('aria-label', text.close || '');
	dialog.querySelector('.bfg-hs-modal__search').placeholder = text.search || '';

	dialog.querySelector('.bfg-hs-modal__search').addEventListener('input', (event) => {
		render(event.target.value.trim());
	});

	// The empty search holds the whole tariff, so the list takes the next rows
	// as the shop worker comes near its end.
	dialog.querySelector('.bfg-hs-modal__list').addEventListener('scroll', (event) => {
		const list = event.target;

		if (list.scrollHeight - list.scrollTop - list.clientHeight < PAGE_MARGIN) {
			addPage();
		}
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
	queue = [];

	if (!index) {
		hint.textContent = text.loading || '';

		return;
	}

	// An empty search shows the whole tariff, with the code the field already
	// holds on top. The list takes it a page at a time, because 4587 rows at
	// once hold the browser up.
	if (query.length < MIN_QUERY) {
		hint.textContent = text.hint || '';

		const chosen = rowOf(current);

		queue = chosen ? [chosen, ...index.codes.filter((row) => row !== chosen)] : [...index.codes];
		list.scrollTop = 0;
		addPage();

		return;
	}

	const hits = search(query);

	// A typed number is a code, and a code carries no word to mark.
	const words = /\d/.test(query) ? [] : query.toLowerCase().split(/\s+/).filter(Boolean);

	hint.textContent = hits.length ? '' : text.empty || '';

	if (!hits.length) {
		hint.append(' ', tariffLink(query));
	}

	for (const row of hits) {
		addHit(list, row, words);
	}
}

/** Add one tariff row to a list or a fragment, and return its button. */
function addHit(into, row, words) {
	const item = document.createElement('li');
	const button = document.createElement('button');

	button.type = 'button';
	button.className = 'bfg-hs-modal__hit';
	button.dataset.code = row[0];
	button.innerHTML = '<span class="bfg-hs-modal__hit-code"></span><span class="bfg-hs-modal__hit-text"></span>';
	button.querySelector('.bfg-hs-modal__hit-code').textContent = dotted(row[0]);
	highlight(button.querySelector('.bfg-hs-modal__hit-text'), rowText(row), words);

	item.append(button);
	into.append(item);

	return button;
}

/**
 * Move the next page of the empty search into the list.
 *
 * The code the field already holds is the first row of the queue, so it is
 * marked on the first page and never again.
 */
function addPage() {
	if (!queue.length) {
		return;
	}

	const list = dialog.querySelector('.bfg-hs-modal__list');
	const chosen = rowOf(current);
	const batch = document.createDocumentFragment();

	for (const row of queue.splice(0, PAGE)) {
		const hit = addHit(batch, row, []);

		if (row === chosen) {
			hit.classList.add('bfg-hs-modal__hit--current');
			hit.setAttribute('aria-current', 'true');
		}
	}

	list.append(batch);
}

/** Return a link that runs the same search on Tolltariffen. */
function tariffLink(query) {
	// wp-admin writes the locale of the shop here, and every Norwegian locale
	// starts with an n.
	const language = document.documentElement.lang.startsWith('n') ? 'no' : 'en';
	const link = document.createElement('a');

	link.href = `${TARIFF_SEARCH}?q=${encodeURIComponent(query)}&language=${language}`;
	link.target = '_blank';
	link.rel = 'noopener';
	link.textContent = text.tariff || '';

	return link;
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
 *
 * @param {Function} onPick Takes the chosen code.
 * @param {string}   code   The code the field already holds, if it holds one.
 */
export async function pickHsCode(onPick, code = '') {
	if (!dialog) {
		build();
	}

	pending = onPick;
	current = code;

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
	const row = rowOf(code);

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
		pickHsCode((code) => setHsCode(button, code), fieldOf(button)?.value || '');
	}
});

// A code already on the screen needs its text, and the text lives in the index.
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', () => showHsCodeLabels());
} else {
	showHsCodeLabels();
}
