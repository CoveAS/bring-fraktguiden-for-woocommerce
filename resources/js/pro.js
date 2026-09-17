document.addEventListener('DOMContentLoaded', () => {
	// Next steps — mark as done when visited, restore on page load
	const STORAGE_KEY = 'bfg_next_steps_done';
	const stepsList = document.querySelector('.bfg-steps-list');
	if (!stepsList) return;

	let doneSteps = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

	function markStepDone(stepEl) {
		stepEl.classList.add('bfg-access-link--done');
	}

	// Restore previously completed steps
	stepsList.querySelectorAll('.bfg-access-link[data-step-key]').forEach(stepEl => {
		if (doneSteps.includes(stepEl.dataset.stepKey)) {
			markStepDone(stepEl);
		}
	});

	// Track clicks — mark step done when the user navigates away
	stepsList.querySelectorAll('.bfg-access-link[data-step-key] a').forEach(link => {
		link.addEventListener('click', () => {
			const stepEl = link.closest('.bfg-access-link[data-step-key]');
			const key = stepEl.dataset.stepKey;
			if (!doneSteps.includes(key)) {
				doneSteps.push(key);
				localStorage.setItem(STORAGE_KEY, JSON.stringify(doneSteps));
			}
		});
	});
});
