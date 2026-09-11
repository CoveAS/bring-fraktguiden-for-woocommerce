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
 * A step whose button carries data-bfg-dialog opens that dialog.
 */
document.addEventListener('click', (event) => {
	const open = event.target.closest('[data-bfg-dialog]');
	if (open) {
		document.getElementById(open.dataset.bfgDialog)?.showModal();
		return;
	}

	const dialog = event.target.closest('dialog');

	if (event.target.closest('[data-bfg-dialog-close]')) {
		dialog?.close();
		return;
	}

	// A click on the backdrop reports the dialog as the target, so compare the
	// point with the box of the dialog.
	if (dialog && outside(dialog, event)) {
		dialog.close();
	}
});

function outside(element, event) {
	const box = element.getBoundingClientRect();

	return event.clientX < box.left
		|| event.clientX > box.right
		|| event.clientY < box.top
		|| event.clientY > box.bottom;
}

/**
 * The Change button of step 3 opens the panel that holds the Bring credentials.
 */
document.addEventListener('click', (event) => {
	const button = event.target.closest('.bfg-connect__change');
	if (!button) return;

	const panel = document.getElementById(button.getAttribute('aria-controls'));
	if (!panel) return;

	panel.hidden = !panel.hidden;
	button.setAttribute('aria-expanded', String(!panel.hidden));
});
