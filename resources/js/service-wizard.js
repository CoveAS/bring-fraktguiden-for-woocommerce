/**
 * The service wizard page.
 *
 * The page shows one question at a time. After the last question it shows the
 * Bring services that match every answer.
 */

import { matches } from './service-wizard-rules.js';

document.addEventListener('DOMContentLoaded', () => {
	const form = document.querySelector('.bfg-wizard');
	if (!form) return;

	const services = window.bfgServiceWizard?.services ?? [];
	const sender = window.bfgServiceWizard?.sender ?? '';
	const steps = [...form.querySelectorAll('.bfg-wizard__step')];
	const result = form.querySelector('.bfg-wizard__result');
	const empty = form.querySelector('.bfg-wizard__empty');
	const questions = steps.filter((step) => step !== result);

	const answersOf = (step) =>
		[...step.querySelectorAll('input[type="checkbox"]:checked')].map((input) => input.value);

	const update = () => {
		const answers = {};
		let answered = true;

		questions.forEach((step) => {
			step.hidden = !answered;
			const given = answersOf(step);
			answers[step.dataset.step] = given;
			step.querySelector('.bfg-wizard__hint').hidden = given.length > 0;
			answered = answered && given.length > 0;
		});

		result.hidden = !answered;
		if (!answered) return;

		let found = 0;

		services.forEach((service) => {
			const row = result.querySelector(`[data-code="${service.code}"]`);
			if (!row) return;

			row.hidden = !matches(service, answers, sender);
			row.querySelector('input').checked = !row.hidden;
			if (!row.hidden) found += 1;
		});

		empty.hidden = found > 0;
		form.querySelector('button[type="submit"]').disabled = answersOf(result).length === 0;
	};

	form.addEventListener('change', update);

	form.addEventListener('submit', (event) => {
		const confirmText = form.querySelector('[data-confirm]')?.dataset.confirm;

		if (confirmText && !window.confirm(confirmText)) {
			event.preventDefault();
		}
	});

	update();
});
