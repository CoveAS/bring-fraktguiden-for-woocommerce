jQuery(function ($) {
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
		$('.bfg-custom-select').each(function() {
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

	// Initialize Flatpickr time pickers
	function initTimePickers() {
		if (typeof flatpickr === 'undefined') {
			return;
		}

		$('.bfg-admin-page input[type="time"]').each(function() {
			const $input = $(this);

			// Skip if already initialized
			if ($input.data('flatpickr-initialized')) {
				return;
			}

			// Mark as initialized
			$input.data('flatpickr-initialized', true);

			// Initialize flatpickr
			flatpickr(this, {
				enableTime: true,
				noCalendar: true,
				dateFormat: "H:i",
				time_24hr: true,
				clickOpens: true,
				allowInput: true
			});
		});
	}

	// Initialize time pickers
	initTimePickers();
});
