/**
 * The customs fields on the product edit screen.
 *
 * The script does two things. It shows and hides the customs fields of a
 * variation behind the override checkbox. It warns when the net weight is
 * above the weight of the product.
 *
 * A warning waits for the field to lose focus, and then for a short delay.
 *
 * PHP passes the field names and the texts in window.bringCustomsFields.
 * See BringFraktguiden\Customs\CustomsFields.
 */
(function () {
	var config = window.bringCustomsFields;

	if (!config) {
		return;
	}

	var NOTE = 'bring-customs-warning';
	var TIMER = 'bringCustomsTimer';

	/**
	 * The wait after a field loses focus, in milliseconds.
	 */
	var DELAY = 400;

	/**
	 * Return whether a field carries one of the customs names.
	 *
	 * A variation field carries an index, so the name is a prefix.
	 */
	function named(field, name) {
		return !!field.name && field.name.indexOf(name) === 0;
	}

	/**
	 * Read a number. A shop may type a comma as the decimal mark.
	 */
	function number(value) {
		return parseFloat(String(value).replace(',', '.'));
	}

	/**
	 * Return the WooCommerce weight field that belongs to a net weight field.
	 *
	 * A variation holds its own. A simple product holds one for the screen.
	 */
	function grossField(field) {
		var panel = field.closest('.woocommerce_variable_attributes');

		return panel
			? panel.querySelector('[name^="variable_weight"]')
			: document.getElementById('_weight');
	}

	/**
	 * Return the warning a field earns, or an empty string.
	 */
	function problem(field) {
		// A variation with the override off never sends its value.
		if (field.closest('.bring-customs-override.hidden')) {
			return '';
		}

		var gross = grossField(field);
		// An empty variation weight inherits the parent weight, and the
		// placeholder holds it.
		var limit = gross ? number(gross.value || gross.placeholder) : NaN;
		var value = number(field.value);

		return value > 0 && limit > 0 && value > limit ? config.tooHeavy : '';
	}

	/**
	 * Write the warning of a field, or remove it when the text is empty.
	 */
	function note(field, text) {
		var wrapper = field.parentNode;
		var element = wrapper.querySelector('.' + NOTE);

		if (!text) {
			if (element) {
				element.remove();
			}

			return;
		}

		if (!element) {
			element = document.createElement('span');
			element.className = NOTE;
			element.style.color = '#b32d2e';
			element.style.display = 'block';
			// The product panel floats every text input, so the note must
			// clear it to sit under the field.
			element.style.clear = 'both';
			element.style.margin = '4px 0 0';
			element.style.lineHeight = '1.4';
			wrapper.appendChild(element);
		}

		element.textContent = text;
	}

	function stopTimer(field) {
		if (field[TIMER]) {
			clearTimeout(field[TIMER]);
			field[TIMER] = null;
		}
	}

	/**
	 * Remove the warning of a field at once.
	 */
	function hide(field) {
		stopTimer(field);
		note(field, '');
	}

	/**
	 * Show the warning of a field at once.
	 */
	function show(field) {
		stopTimer(field);
		note(field, problem(field));
	}

	/**
	 * Show the warning of a field after the delay.
	 */
	function showLater(field) {
		stopTimer(field);
		field[TIMER] = setTimeout(function () {
			field[TIMER] = null;
			note(field, problem(field));
		}, DELAY);
	}

	/**
	 * Return every customs field the script checks.
	 */
	function fields() {
		return document.querySelectorAll('[name^="' + config.netName + '"]');
	}

	/**
	 * Check every field, and leave the one the shop works in alone.
	 */
	function checkAll() {
		fields().forEach(function (field) {
			if (field !== document.activeElement) {
				show(field);
			}
		});
	}

	function isCustomsField(field) {
		return named(field, config.netName);
	}

	document.addEventListener('input', function (event) {
		var field = event.target;

		if (!field.name) {
			return;
		}

		if (isCustomsField(field)) {
			hide(field);

			return;
		}

		if (field.id === '_weight' || named(field, 'variable_weight')) {
			checkAll();
		}
	});

	document.addEventListener('focusout', function (event) {
		if (isCustomsField(event.target)) {
			showLater(event.target);
		}
	});

	document.addEventListener('focusin', function (event) {
		if (isCustomsField(event.target)) {
			hide(event.target);
		}
	});

	document.addEventListener('change', function (event) {
		var box = event.target;

		if (!named(box, config.overrideName)) {
			return;
		}

		var panel = box.closest('.woocommerce_variable_attributes');

		if (!panel) {
			return;
		}

		panel.querySelectorAll('.bring-customs-override').forEach(function (row) {
			row.classList.toggle('hidden', !box.checked);
		});

		checkAll();
	});

	// WooCommerce loads the variation panel over ajax.
	jQuery(document).on('woocommerce_variations_loaded', checkAll);

	checkAll();
})();
