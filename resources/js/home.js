import './dialog.js';

document.addEventListener('DOMContentLoaded', () => {
	const page = document.querySelector('.bfg-admin-page__home');
	if (!page) return;

	const setupComplete = page.dataset.setupComplete === 'true';
	if (!setupComplete) return;

	const storageKey = 'bfg_setup_celebrated';
	if (localStorage.getItem(storageKey)) return;

	localStorage.setItem(storageKey, '1');

	if (window.bfgHomeData?.proPageUrl) {
		window.location.href = window.bfgHomeData.proPageUrl + '&celebrate=1';
	}
});

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
