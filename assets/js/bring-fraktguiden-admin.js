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
});
