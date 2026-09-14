/**
 * The HS code table of the customs products panel.
 *
 * The booking box of the order screen and the bulk booking modal of the orders
 * list show the same table, so the marks, the two buttons and the count live
 * here.
 *
 * Every listener starts from the panel the event happened in, so a host that
 * redraws its markup needs no new binding.
 */

import { pickHsCode, setHsCode } from './hs-code-picker.js';

// The product id of the last marked row. A shift click marks from here.
let anchor = null;

/** Return the panel that holds the given node. */
function panelOf(node) {
	return node ? node.closest('[data-bfg-hs-panel]') : null;
}

/** Mark a row, or the whole range down from the anchor. */
function markRow(panel, mark, extend) {
	const marks = marksOf(panel);
	const from = marks.findIndex((row) => row.dataset.bfgHsMark === anchor);
	const to = marks.indexOf(mark);

	if (extend && from !== -1) {
		marks.slice(Math.min(from, to), Math.max(from, to) + 1).forEach((row) => {
			row.checked = mark.checked;
		});

		// A shift click also selects the text between the two rows.
		window.getSelection().removeAllRanges();
	}

	anchor = mark.dataset.bfgHsMark;
	syncToggle(panel);
}

/** Show whether every product now has an HS code. */
function syncHsStatus(panel) {
	if (!panel) {
		return;
	}

	const missing = [...panel.querySelectorAll('[data-hs-product]')]
		.filter((field) => '' === field.value.trim()).length;
	const count = panel.querySelector('[data-bfg-hs-count]');

	panel.classList.toggle('bfg-customs-products--ok', 0 === missing);

	if (count) {
		count.textContent = missing
			? count.dataset.missing.replace('%d', missing)
			: count.dataset.done;
	}
}

/** Tell the toggle button what it does next, and show the bulk code button. */
function syncToggle(panel) {
	const button = panel.querySelector('[data-bfg-hs-toggle]');
	const bulk = panel.querySelector('[data-bfg-hs-set]');

	if (button) {
		button.textContent = allMarked(panel) ? button.dataset.deselect : button.dataset.select;
	}

	// The button writes into the marked rows, so it waits for the first mark.
	if (bulk) {
		bulk.hidden = !panel.querySelector('[data-bfg-hs-mark]:checked');
	}
}

/** Return the mark of every row the worker can see. */
function marksOf(panel) {
	return [...panel.querySelectorAll(
		'.bfg-customs-products__row:not(.bfg-customs-products__row--filtered) [data-bfg-hs-mark]'
	)];
}

/** Hide every row that already has a code, or show all rows again. */
function filterRows(panel, only) {
	panel.querySelectorAll('[data-hs-product]').forEach((field) => {
		const row = field.closest('.bfg-customs-products__row');
		const hide = only && '' !== field.value.trim();

		// The list is picked once, so a row the worker fills stays in place.
		row.classList.toggle('bfg-customs-products__row--filtered', hide);

		// A hidden mark would send a code into a row nobody sees.
		if (hide) {
			row.querySelector('[data-bfg-hs-mark]').checked = false;
		}
	});

	anchor = null;
	syncToggle(panel);
}

/** Is every row of the table marked? */
function allMarked(panel) {
	const marks = marksOf(panel);

	return marks.length > 0 && marks.every((mark) => mark.checked);
}

/** Ask for one code, and write it into every marked row. */
function setMarkedCodes(panel) {
	pickHsCode((code) => {
		panel.querySelectorAll('[data-bfg-hs-mark]:checked').forEach((mark) => {
			const field = panel.querySelector(`[data-hs-product="${mark.dataset.bfgHsMark}"]`);

			// The picker writes through the button, so the button shows the
			// code it now carries.
			if (field) {
				setHsCode(field.parentElement.querySelector('[data-bfg-hs-pick]'), code);
			}
		});
	});
}

document.addEventListener('input', (event) => {
	if (event.target.matches('[data-hs-product]')) {
		syncHsStatus(panelOf(event.target));
	}
});

document.addEventListener('click', (event) => {
	const panel = panelOf(event.target);

	if (!panel) {
		return;
	}

	const filter = event.target.closest('[data-bfg-hs-filter]');

	if (filter) {
		filterRows(panel, filter.checked);

		return;
	}

	const mark = event.target.closest('[data-bfg-hs-mark]');

	if (mark) {
		markRow(panel, mark, event.shiftKey);

		return;
	}

	const button = event.target.closest('button');

	if (!button) {
		return;
	}

	if (button.hasAttribute('data-bfg-hs-toggle')) {
		const checked = !allMarked(panel);

		marksOf(panel).forEach((row) => {
			row.checked = checked;
		});
		anchor = null;
		syncToggle(panel);

		return;
	}

	if (button.hasAttribute('data-bfg-hs-set')) {
		setMarkedCodes(panel);
	}
});
