/**
 * Modal dialogs of the admin pages.
 *
 * A button with data-bfg-dialog opens the dialog with that id. A button with
 * data-bfg-dialog-close closes the dialog around it. A click away from the
 * dialog closes it too.
 */
let pressedOutside = false;

function listen() {
	document.addEventListener('mousedown', (event) => {
		const dialog = event.target.closest('dialog');

		pressedOutside = Boolean(dialog) && outside(dialog, event);
	});

	document.addEventListener('click', (event) => {
		const open = event.target.closest('[data-bfg-dialog]');
		if (open) {
			document.getElementById(open.dataset.bfgDialog)?.showModal();
			return;
		}

		const dialog = event.target.closest('dialog');
		if (!dialog) return;

		if (event.target.closest('[data-bfg-dialog-close]')) {
			dialog.close();
			return;
		}

		// A click on the backdrop reports the dialog as the target, so compare
		// the point with the box of the dialog. The press must fall outside as
		// well, so a drag out of the dialog keeps it open.
		if (pressedOutside && outside(dialog, event)) {
			dialog.close();
		}
	});
}

function outside(element, event) {
	const box = element.getBoundingClientRect();

	return event.clientX < box.left
		|| event.clientX > box.right
		|| event.clientY < box.top
		|| event.clientY > box.bottom;
}

// Several entry files import this module, so one page can hold more than one
// copy of it. The flag keeps one pair of handlers for the page.
if (!window.bfgDialogReady) {
	window.bfgDialogReady = true;
	listen();
}
