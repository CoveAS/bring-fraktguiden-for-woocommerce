/**
 * Keep the placeholder of a label field in step with the service select.
 *
 * A blank label field shows the name of the chosen service, because the
 * checkout falls back to that name. The field names its select in the
 * data-placeholder-from attribute.
 */
document.querySelectorAll('[data-placeholder-from]').forEach((input) => {
	const select = document.getElementById(input.dataset.placeholderFrom);

	if (!select) {
		return;
	}

	const update = () => {
		const option = select.options[select.selectedIndex];

		// The "No shipping" option has the value 0 and pushes no rate.
		input.placeholder = option && option.value !== '0' ? option.text : '';
	};

	select.addEventListener('change', update);
	update();
});
