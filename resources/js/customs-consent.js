/**
 * The customs consent callout of the booking screens.
 *
 * An export order and an NVIT order need the signature of the shop before
 * Bring accepts the declaration. The callout carries the text and a Sign
 * button, and it renders wherever the customs warning renders.
 *
 * The consent is a shop setting, so one signature covers every booking. The
 * module therefore keeps one state for the whole page.
 *
 * The send is optimistic. The callout goes at the press, and it comes back
 * with the reason when the server refuses.
 */

// Every button that must not book while the signature is missing.
const BLOCKED = '[data-bfg-book], #bfg-bulk-book-send';

// The request in flight, or null. A booking waits for it.
let signing = null;

/** Return the callout of the page, or null. */
function callout() {
	return document.querySelector('[data-bfg-consent]');
}

/**
 * Turn the booking buttons off while the callout shows.
 *
 * A button off for another reason keeps its own block, so only a block this
 * module put on comes off again.
 */
export function applyConsent() {
	const blocked = callout() && !callout().hidden;

	document.querySelectorAll(BLOCKED).forEach((button) => {
		if (blocked && !button.disabled) {
			button.disabled = true;
			button.dataset.bfgConsentBlocked = '';
		} else if (!blocked && 'bfgConsentBlocked' in button.dataset) {
			button.disabled = false;
			delete button.dataset.bfgConsentBlocked;
		}
	});
}

/**
 * Wait for the signature in flight.
 *
 * A booking pressed right after the Sign button must not reach Bring before
 * the server records the consent. The promise rejects when the server refuses,
 * and the booking then stops.
 */
export function consentReady() {
	return signing || Promise.resolve();
}

// booking-admin.js is a jQuery file outside the module bundle, and the bulk
// booking button lives there.
window.bfgConsentReady = consentReady;

/** Show why the signature failed, and let the shop worker try again. */
function showError(box, message) {
	const line = box.querySelector('[data-bfg-consent-error]');

	if (line) {
		line.textContent = message;
		line.hidden = false;
	}
}

/** Sign the customs declaration, and show the callout again when that fails. */
function sign(box) {
	const line = box.querySelector('[data-bfg-consent-error]');

	if (line) {
		line.hidden = true;
	}

	box.hidden = true;
	applyConsent();

	signing = fetch(box.dataset.url, {
		method: 'POST',
		credentials: 'same-origin',
		headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': box.dataset.nonce },
		body: '{}',
	}).then((answer) => {
		if (!answer.ok) {
			throw new Error(box.dataset.error);
		}

		signing = null;
		box.remove();
	}, () => {
		// A refused request and a lost network read the same to the shop
		// worker, so both show the one message the server side wrote.
		throw new Error(box.dataset.error);
	}).catch((error) => {
		signing = null;
		box.hidden = false;
		applyConsent();
		showError(box, error.message);

		throw error;
	});

	// A booking reads the answer through consentReady, so nothing is lost here.
	signing.catch(() => {});
}

document.addEventListener('click', (event) => {
	const button = event.target.closest('[data-bfg-consent-sign]');
	const box = button && button.closest('[data-bfg-consent]');

	// A second press while the first request runs would sign twice.
	if (box && !signing) {
		sign(box);
	}
});

applyConsent();
