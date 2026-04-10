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

	// Custom Select Dropdown
	function initCustomSelects() {
		$('.bfg-custom-select:not([data-bfg-select])').each(function() {
			const $select = $(this);
			const $trigger = $select.find('.bfg-custom-select__trigger');
			const $native = $select.find('.bfg-custom-select__native');
			const $valueDisplay = $select.find('.bfg-custom-select__value');
			const $options = $select.find('.bfg-custom-select__option');

			// Toggle dropdown on trigger click
			$trigger.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				// Close all other dropdowns first
				$('.bfg-custom-select').not($select).removeClass('is-open');
				$('.bfg-custom-select__trigger').not($trigger).attr('aria-expanded', 'false');

				// Toggle this dropdown
				const isOpen = $select.hasClass('is-open');
				$select.toggleClass('is-open');
				$trigger.attr('aria-expanded', !isOpen);
			});

			// Select an option
			$options.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				const $option = $(this);
				const value = $option.data('value');
				const text = $option.find('.bfg-custom-select__option-text').text();

				// Update native select
				$native.val(value).trigger('change');

				// Update display value and remove placeholder styling
				$valueDisplay.text(text).removeClass('is-placeholder');

				// Update selected state and checkmarks
				$options.removeClass('is-selected').attr('aria-selected', 'false');
				$options.find('.bfg-custom-select__check').remove();

				$option.addClass('is-selected').attr('aria-selected', 'true');
				$option.append('<svg class="bfg-custom-select__check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');

				// Close dropdown
				$select.removeClass('is-open');
				$trigger.attr('aria-expanded', 'false');
			});

			// Keyboard navigation
			$trigger.on('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					$trigger.trigger('click');
				} else if (e.key === 'Escape') {
					$select.removeClass('is-open');
					$trigger.attr('aria-expanded', 'false');
				} else if (e.key === 'ArrowDown') {
					e.preventDefault();
					if (!$select.hasClass('is-open')) {
						$trigger.trigger('click');
					} else {
						const $current = $options.filter('.is-selected');
						const $next = $current.next('.bfg-custom-select__option');
						if ($next.length) {
							$next.trigger('click');
						}
					}
				} else if (e.key === 'ArrowUp') {
					e.preventDefault();
					if ($select.hasClass('is-open')) {
						const $current = $options.filter('.is-selected');
						const $prev = $current.prev('.bfg-custom-select__option');
						if ($prev.length) {
							$prev.trigger('click');
						}
					}
				}
			});
		});

		// Close dropdown when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.bfg-custom-select').length) {
				$('.bfg-custom-select').removeClass('is-open');
				$('.bfg-custom-select__trigger').attr('aria-expanded', 'false');
			}
		});
	}

	// Initialize custom selects
	initCustomSelects();

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
	$('.bfg-admin-page input[type="time"]').each(function() {
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
