/**
 * Custom Select — a listbox widget built from <select class="bfg-custom-select">.
 *
 * The native select stays in the form and holds the value. The widget renders a
 * trigger button and a list, and copies every pick back to the native select.
 *
 * The widget aims at the keyboard behaviour of a native select. The arrow keys
 * move a highlight inside the open list. A pick happens on Enter, on Space or
 * on a click, and only then does the native select fire a change event.
 */

const CHECK_SVG = `<svg class="bfg-custom-select__check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
const ARROW_SVG = `<svg class="bfg-custom-select__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>`;

const TYPE_AHEAD_RESET_MS = 700;

let idCounter = 0;

function buildWidget(nativeSelect) {
	const options = Array.from(nativeSelect.options).filter(o => !(o.disabled && o.value === ''));
	const placeholderOption = Array.from(nativeSelect.options).find(o => o.disabled && o.value === '');
	const selectedOption = options.find(o => o.selected);
	const listId = `bfg-select-list-${++idCounter}`;

	const wrapper = document.createElement('div');
	wrapper.className = 'bfg-custom-select';
	wrapper.setAttribute('data-bfg-select', '');

	// The label points at the id of the native select. Move that id to the
	// trigger, so a click on the label focuses the control the user can see.
	const trigger = document.createElement('button');
	trigger.type = 'button';
	trigger.className = 'bfg-custom-select__trigger';
	if (nativeSelect.id) {
		trigger.id = nativeSelect.id;
		nativeSelect.removeAttribute('id');
	}
	trigger.setAttribute('aria-haspopup', 'listbox');
	trigger.setAttribute('aria-expanded', 'false');
	trigger.setAttribute('aria-controls', listId);

	const valueSpan = document.createElement('span');
	valueSpan.className = 'bfg-custom-select__value';
	trigger.appendChild(valueSpan);
	trigger.insertAdjacentHTML('beforeend', ARROW_SVG);

	const dropdown = document.createElement('div');
	dropdown.className = 'bfg-custom-select__dropdown';
	dropdown.id = listId;
	dropdown.setAttribute('role', 'listbox');

	const optionEls = options.map((opt, index) => {
		const optEl = document.createElement('div');
		optEl.className = 'bfg-custom-select__option';
		optEl.id = `${listId}-${index}`;
		optEl.dataset.value = opt.value;
		optEl.setAttribute('role', 'option');
		optEl.setAttribute('aria-selected', 'false');

		const textSpan = document.createElement('span');
		textSpan.className = 'bfg-custom-select__option-text';
		textSpan.textContent = opt.text;
		optEl.appendChild(textSpan);

		dropdown.appendChild(optEl);
		return optEl;
	});

	// Hide the native select from the mouse, the keyboard and the screen reader.
	// The widget speaks for it.
	nativeSelect.classList.remove('bfg-custom-select');
	nativeSelect.classList.add('bfg-custom-select__native');
	nativeSelect.setAttribute('tabindex', '-1');
	nativeSelect.setAttribute('aria-hidden', 'true');

	nativeSelect.parentNode.insertBefore(wrapper, nativeSelect);
	wrapper.append(nativeSelect, trigger, dropdown);

	const select = {
		wrapper, trigger, valueSpan, dropdown, nativeSelect, optionEls,
		placeholderText: placeholderOption ? placeholderOption.text : '',
		highlighted: null,
		typed: '',
		typedAt: 0,
	};

	showSelection(select, selectedOption ? optionEls[options.indexOf(selectedOption)] : null);
	wireEvents(select);
	return select;
}

/** Paint the picked option and the trigger text. */
function showSelection(select, optEl) {
	select.optionEls.forEach(el => {
		const picked = el === optEl;
		el.classList.toggle('is-selected', picked);
		el.setAttribute('aria-selected', picked ? 'true' : 'false');
		el.querySelector('.bfg-custom-select__check')?.remove();
		if (picked) el.insertAdjacentHTML('beforeend', CHECK_SVG);
	});

	const text = optEl ? optEl.textContent.trim() : select.placeholderText;
	select.valueSpan.textContent = text;
	select.valueSpan.classList.toggle('is-placeholder', !optEl);
}

/** Move the highlight and scroll it into view. */
function highlight(select, optEl) {
	if (!optEl) return;
	select.highlighted?.classList.remove('is-active');
	select.highlighted = optEl;
	optEl.classList.add('is-active');
	select.trigger.setAttribute('aria-activedescendant', optEl.id);
	reveal(select.dropdown, optEl);
}

/**
 * Scroll the list so the option is visible. The list scrolls, the page does not,
 * which is why this does not use scrollIntoView.
 */
function reveal(dropdown, optEl) {
	const top = optEl.offsetTop;
	const bottom = top + optEl.offsetHeight;
	if (top < dropdown.scrollTop) {
		dropdown.scrollTop = top;
	} else if (bottom > dropdown.scrollTop + dropdown.clientHeight) {
		dropdown.scrollTop = bottom - dropdown.clientHeight;
	}
}

function open(select) {
	closeAll(select.wrapper);
	select.wrapper.classList.add('is-open');
	select.trigger.setAttribute('aria-expanded', 'true');
	// The list may be taller than its box, so show the picked option first.
	highlight(select, select.dropdown.querySelector('.bfg-custom-select__option.is-selected') || select.optionEls[0]);
}

function close(select) {
	select.wrapper.classList.remove('is-open');
	select.trigger.setAttribute('aria-expanded', 'false');
	select.trigger.removeAttribute('aria-activedescendant');
	select.highlighted?.classList.remove('is-active');
	select.highlighted = null;
}

function closeAll(except) {
	document.querySelectorAll('[data-bfg-select].is-open').forEach(w => {
		if (w === except) return;
		w.classList.remove('is-open');
		const trigger = w.querySelector('.bfg-custom-select__trigger');
		trigger.setAttribute('aria-expanded', 'false');
		trigger.removeAttribute('aria-activedescendant');
		w.querySelector('.bfg-custom-select__option.is-active')?.classList.remove('is-active');
	});
}

/** Take an option as the new value and tell the form. */
function pick(select, optEl) {
	if (optEl && select.nativeSelect.value !== optEl.dataset.value) {
		select.nativeSelect.value = optEl.dataset.value;
		showSelection(select, optEl);
		select.nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
	}
	close(select);
	select.trigger.focus();
}

/** Find the next option whose text starts with the typed letters. */
function typeAhead(select, key) {
	const now = Date.now();
	select.typed = now - select.typedAt > TYPE_AHEAD_RESET_MS ? key : select.typed + key;
	select.typedAt = now;

	const needle = select.typed.toLowerCase();
	const start = select.optionEls.indexOf(select.highlighted) + 1;
	const count = select.optionEls.length;
	for (let i = 0; i < count; i++) {
		const optEl = select.optionEls[(start + i) % count];
		if (optEl.textContent.trim().toLowerCase().startsWith(needle)) return optEl;
	}
	return null;
}

function wireEvents(select) {
	const { wrapper, trigger, dropdown, optionEls } = select;

	trigger.addEventListener('click', e => {
		e.preventDefault();
		wrapper.classList.contains('is-open') ? close(select) : open(select);
	});

	// Keep the focus on the trigger, so the blur below does not close the list
	// before the click on an option lands.
	dropdown.addEventListener('mousedown', e => e.preventDefault());

	dropdown.addEventListener('click', e => {
		const optEl = e.target.closest('.bfg-custom-select__option');
		if (optEl) pick(select, optEl);
	});

	dropdown.addEventListener('mousemove', e => {
		const optEl = e.target.closest('.bfg-custom-select__option');
		if (optEl && optEl !== select.highlighted) highlight(select, optEl);
	});

	trigger.addEventListener('keydown', e => {
		const isOpen = wrapper.classList.contains('is-open');
		const index = optionEls.indexOf(select.highlighted);

		switch (e.key) {
			case 'Enter':
			case ' ':
				e.preventDefault();
				isOpen ? pick(select, select.highlighted) : open(select);
				return;
			case 'Escape':
				if (isOpen) {
					e.preventDefault();
					close(select);
				}
				return;
			case 'Tab':
				if (isOpen) close(select);
				return;
			case 'ArrowDown':
			case 'ArrowUp':
			case 'Home':
			case 'End':
				e.preventDefault();
				if (!isOpen) {
					open(select);
					return;
				}
				if (e.key === 'Home') highlight(select, optionEls[0]);
				else if (e.key === 'End') highlight(select, optionEls[optionEls.length - 1]);
				else if (e.key === 'ArrowDown') highlight(select, optionEls[index + 1]);
				else highlight(select, optionEls[index - 1]);
				return;
		}

		// A single printable character jumps to an option, as a native select does.
		if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
			const match = typeAhead(select, e.key);
			if (!match) return;
			e.preventDefault();
			if (!isOpen) open(select);
			highlight(select, match);
		}
	});

	trigger.addEventListener('blur', () => close(select));
}

export function initCustomSelects(root = document) {
	root.querySelectorAll('select.bfg-custom-select').forEach(buildWidget);
}

document.addEventListener('DOMContentLoaded', () => initCustomSelects());
