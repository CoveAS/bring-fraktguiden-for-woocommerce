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
		[...step.querySelectorAll('input:checked')].map((input) => input.value);

	/**
	 * A step may depend on an answer to an earlier step, written as
	 * data-needs="<step>:<value>". A step without the attribute always applies.
	 */
	const applies = (step, answers) => {
		if (!step.dataset.needs) return true;

		const [question, value] = step.dataset.needs.split(':');

		return (answers[question] ?? []).includes(value);
	};

	const update = () => {
		const answers = {};
		let answered = true;

		questions.forEach((step) => {
			const skipped = !applies(step, answers);
			const given = skipped ? [] : answersOf(step);

			step.hidden = !answered || skipped;
			answers[step.dataset.step] = given;
			step.querySelector('.bfg-wizard__hint').hidden = given.length > 0;
			answered = answered && (skipped || given.length > 0);
		});

		result.hidden = !answered;
		if (!answered) return;

		let found = 0;

		services.forEach((service) => {
			const row = result.querySelector(`[data-code="${service.code}"]`);
			if (!row) return;

			const show = matches(service, answers, sender);

			// Tick a service only when it appears, so a box the user clears stays clear.
			if (show === row.hidden) {
				row.querySelector('input').checked = show;
			}

			row.hidden = !show;
			if (show) found += 1;
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
