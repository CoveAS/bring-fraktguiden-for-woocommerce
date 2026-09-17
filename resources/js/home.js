import './dialog.js';

/**
 * The Change button of step 3 opens the panel that holds the Bring credentials.
 */
document.addEventListener('click', (event) => {
	const button = event.target.closest('.bfg-step-form__toggle');
	if (!button) return;

	const panel = document.getElementById(button.getAttribute('aria-controls'));
	if (!panel) return;

	panel.hidden = !panel.hidden;
	button.setAttribute('aria-expanded', String(!panel.hidden));
});

/**
 * The price field and the service list of step 3 belong to the fixed price
 * answer. Use of either one picks that answer.
 */
document.addEventListener('focusin', (event) => {
	if (!event.target.matches('#bfg-fallback-price, #bfg-fallback-service')) return;

	const radio = document.getElementById('bfg-fallback-answer-price');
	if (radio) radio.checked = true;
});
