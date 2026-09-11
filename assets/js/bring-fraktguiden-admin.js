jQuery(function ($) {
	// Track keyboard vs mouse focus for focus ring styling
	// Only show focus ring when using keyboard navigation (like step rows)
	(function() {
		document.body.addEventListener('mousedown', function() {
			document.body.classList.add('using-mouse');
		});
		document.body.addEventListener('keydown', function(e) {
			if (e.key === 'Tab') {
				document.body.classList.remove('using-mouse');
			}
		});
	})();

	$('.bring-notice.is-dismissible').each(
		function () {
			var notice_id = $(this).data('notice_id');
			$(this).on(
				'click',
				'.notice-dismiss',
				function (e) {
					e.preventDefault();
					$.post(
						bring_fraktguiden.ajaxurl,
						{
							action: 'bring_dismiss_notice',
							notice_id: notice_id
						}
					);
				}
			);
		}
	);

	const toggleDisabled = function (el, truthy) {
		return function () {
			const depEl = $(this);
			el.prop(
				'disabled',
				truthy ? ! depEl.prop('checked') : depEl.prop('checked')
			);
		};
	};
	$('.bfg-input [data-dependencies]').each( function() {
		const el = $(this);
		const dependencies = (el.data('dependencies'));
		if (! dependencies || Array.isArray(dependencies) && dependencies.length === 0) {
			return;
		}
		console.log(dependencies);
		for (const dep in dependencies) {
			const depEl = $('[name="'+dep+'"]');
			if (depEl.length === 0) {
				return;
			}
			const truthy = dependencies[dep];
			depEl.on('change', toggleDisabled(el, truthy));
			toggleDisabled(el, truthy).call(depEl);
		}
	});

	// Checkbox card toggle - add/remove is-checked class for styling
	$('.bfg-field--checkbox-box').each(function() {
		const $card = $(this);
		const $checkbox = $card.find('input[type="checkbox"]');

		// Set initial state
		if ($checkbox.prop('checked')) {
			$card.addClass('is-checked');
		}

		// Toggle on change
		$checkbox.on('change', function() {
			$card.toggleClass('is-checked', this.checked);
		});
	});

	// Make time inputs open picker when clicking anywhere on the field
	$('.bfg input[type="time"]').each(function() {
		const input = this;
		const $input = $(this);
		const $wrapper = $input.closest('.bfg-input--time');

		// Make clicking anywhere on the input open the picker
		$input.on('click', function(e) {
			try {
				this.showPicker();
			} catch (err) {
				// Browser doesn't support showPicker
			}
		});

		// Also handle wrapper clicks
		if ($wrapper.length) {
			$wrapper.css('cursor', 'pointer');
			$wrapper.on('click', function(e) {
				if (e.target !== input) {
					try {
						input.showPicker();
					} catch (err) {
						input.focus();
					}
				}
			});
		}
	});

	// Conditional field groups - show/hide based on checkbox state
	$('.bfg-conditional-group').each(function() {
		const $group = $(this);
		const triggerName = $group.data('trigger');
		const $trigger = $('input[name="' + triggerName + '"]');

		if (!$trigger.length) {
			return;
		}

		function updateState() {
			const isEnabled = $trigger.prop('checked');
			const $inputs = $group.find('input, select, textarea');

			$group.css({
				'opacity': isEnabled ? '1' : '0.5',
				'pointer-events': isEnabled ? 'auto' : 'none'
			});
			$inputs.prop('disabled', !isEnabled);
		}

		updateState();
		$trigger.on('change', updateState);
	});
});
