/**
 * BFG Field Validation
 *
 * Generic utility for showing/clearing inline field errors.
 * Works on any .bfg-field wrapper — no per-field setup required.
 *
 * Usage:
 *   bfgField.showError(inputEl, 'Error message');
 *   bfgField.clearError(inputEl);
 */
window.bfgField = {
	showError(input, message) {
		const field = input.closest('.bfg-field');
		if (!field) return;

		field.classList.add('bfg-field--has-error');
		input.setAttribute('aria-invalid', 'true');

		let errorEl = field.querySelector('.bfg-field__error');
		if (!errorEl) {
			errorEl = document.createElement('span');
			errorEl.className = 'bfg-field__error';
			errorEl.setAttribute('role', 'alert');
			errorEl.setAttribute('aria-live', 'polite');
			const desc = field.querySelector('.bfg-description');
			if (desc) desc.before(errorEl);
			else field.appendChild(errorEl);
		}
		errorEl.textContent = message;
		input.focus();
	},

	clearError(input) {
		const field = input.closest('.bfg-field');
		if (!field) return;

		field.classList.remove('bfg-field--has-error');
		input.removeAttribute('aria-invalid');

		const errorEl = field.querySelector('.bfg-field__error');
		if (errorEl) errorEl.textContent = '';
	},
};
