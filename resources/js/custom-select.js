/**
 * Custom Select — progressive enhancement for <select class="bfg-custom-select">
 *
 * Transforms native selects rendered by the bfg-field.select component into
 * the full custom widget expected by the CSS (trigger + dropdown).
 *
 * The old jQuery-based initCustomSelects() in bring-fraktguiden-admin.js handles
 * selects that are already rendered as the full widget (old pages). This module
 * handles selects rendered as plain native elements (new BFG component pages).
 */

const CHECK_SVG = `<svg class="bfg-custom-select__check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
const ARROW_SVG = `<svg class="bfg-custom-select__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>`;

function buildWidget(nativeSelect) {
	const options = Array.from(nativeSelect.options);
	const selectedOption = options.find(o => o.selected && !o.disabled) || options.find(o => !o.disabled);
	const isPlaceholder = !selectedOption || selectedOption.value === '';
	const displayText = selectedOption ? selectedOption.text : '';

	// Wrapper
	const wrapper = document.createElement('div');
	wrapper.className = 'bfg-custom-select';
	wrapper.setAttribute('data-bfg-select', '');

	// Move native select inside wrapper, hide it from AT / tab order
	nativeSelect.classList.remove('bfg-custom-select');
	nativeSelect.classList.add('bfg-custom-select__native');
	nativeSelect.setAttribute('tabindex', '-1');
	nativeSelect.setAttribute('aria-hidden', 'true');
	nativeSelect.parentNode.insertBefore(wrapper, nativeSelect);
	wrapper.appendChild(nativeSelect);

	// Trigger button
	const trigger = document.createElement('button');
	trigger.type = 'button';
	trigger.className = 'bfg-custom-select__trigger';
	trigger.setAttribute('aria-haspopup', 'listbox');
	trigger.setAttribute('aria-expanded', 'false');

	const valueSpan = document.createElement('span');
	valueSpan.className = 'bfg-custom-select__value' + (isPlaceholder ? ' is-placeholder' : '');
	valueSpan.textContent = displayText;

	trigger.appendChild(valueSpan);
	trigger.insertAdjacentHTML('beforeend', ARROW_SVG);
	wrapper.appendChild(trigger);

	// Dropdown
	const dropdown = document.createElement('div');
	dropdown.className = 'bfg-custom-select__dropdown';
	dropdown.setAttribute('role', 'listbox');

	options.forEach(opt => {
		if (opt.disabled && opt.value === '') return; // skip placeholder option
		const isSelected = opt.selected && opt.value !== '';
		const optEl = document.createElement('div');
		optEl.className = 'bfg-custom-select__option' + (isSelected ? ' is-selected' : '');
		optEl.dataset.value = opt.value;
		optEl.setAttribute('role', 'option');
		optEl.setAttribute('aria-selected', isSelected ? 'true' : 'false');

		const textSpan = document.createElement('span');
		textSpan.className = 'bfg-custom-select__option-text';
		textSpan.textContent = opt.text;
		optEl.appendChild(textSpan);

		if (isSelected) optEl.insertAdjacentHTML('beforeend', CHECK_SVG);
		dropdown.appendChild(optEl);
	});

	wrapper.appendChild(dropdown);
	wireEvents(wrapper, trigger, valueSpan, nativeSelect, dropdown);
}

function closeAll(except) {
	document.querySelectorAll('[data-bfg-select].is-open').forEach(w => {
		if (w === except) return;
		w.classList.remove('is-open');
		w.querySelector('.bfg-custom-select__trigger').setAttribute('aria-expanded', 'false');
	});
}

function wireEvents(wrapper, trigger, valueSpan, nativeSelect, dropdown) {
	trigger.addEventListener('click', e => {
		e.preventDefault();
		e.stopPropagation();
		closeAll(wrapper);
		const opening = !wrapper.classList.contains('is-open');
		wrapper.classList.toggle('is-open', opening);
		trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
	});

	dropdown.addEventListener('click', e => {
		const optEl = e.target.closest('.bfg-custom-select__option');
		if (!optEl) return;
		e.preventDefault();
		e.stopPropagation();

		const value = optEl.dataset.value;

		// Update native select
		nativeSelect.value = value;
		nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));

		// Update display
		valueSpan.textContent = optEl.querySelector('.bfg-custom-select__option-text').textContent;
		valueSpan.classList.remove('is-placeholder');

		// Update selected states
		dropdown.querySelectorAll('.bfg-custom-select__option').forEach(o => {
			const selected = o === optEl;
			o.classList.toggle('is-selected', selected);
			o.setAttribute('aria-selected', selected ? 'true' : 'false');
			o.querySelector('.bfg-custom-select__check')?.remove();
			if (selected) o.insertAdjacentHTML('beforeend', CHECK_SVG);
		});

		wrapper.classList.remove('is-open');
		trigger.setAttribute('aria-expanded', 'false');
	});

	trigger.addEventListener('keydown', e => {
		const optEls = Array.from(dropdown.querySelectorAll('.bfg-custom-select__option'));
		const current = dropdown.querySelector('.bfg-custom-select__option.is-selected');

		if (e.key === 'Enter' || e.key === ' ') {
			e.preventDefault();
			trigger.click();
		} else if (e.key === 'Escape') {
			wrapper.classList.remove('is-open');
			trigger.setAttribute('aria-expanded', 'false');
		} else if (e.key === 'ArrowDown') {
			e.preventDefault();
			if (!wrapper.classList.contains('is-open')) {
				trigger.click();
			} else {
				const idx = optEls.indexOf(current);
				optEls[idx + 1]?.click();
			}
		} else if (e.key === 'ArrowUp') {
			e.preventDefault();
			if (wrapper.classList.contains('is-open')) {
				const idx = optEls.indexOf(current);
				if (idx > 0) optEls[idx - 1]?.click();
			}
		}
	});
}

export function initCustomSelects(root = document) {
	root.querySelectorAll('select.bfg-custom-select').forEach(buildWidget);
}

document.addEventListener('DOMContentLoaded', () => initCustomSelects());
