jQuery(function ($) {
	const blockArgs = {
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	};
	let pickUpPoints = window._fraktguiden_data.pick_up_points;
	let selectedPickUpPoint = window._fraktguiden_data.selected_pick_up_point;

	const handleSelectPickUpPoint = function (pickUpPoint) {
		return function (e) {
			e.preventDefault();
			selectPickUpPoint(pickUpPoint);
			if (selectedPickUpPoint.id !== pickUpPoint.id) {
				ajaxSelect(pickUpPoint.id);
			}
			selectedPickUpPoint = pickUpPoint;
			modalEl.hide();
		};
	};

	function getAddress(pickUpPoint) {
		return pickUpPoint.address + ', ' + pickUpPoint.postalCode + ' ' + pickUpPoint.city;
	}

	const mapKey = _fraktguiden_checkout.map_key;
	function selectPickUpPoint(pickUpPoint) {
		$('.bfg-pup__name').text(pickUpPoint.name);
		$('.bfg-pup__address').text(getAddress(pickUpPoint));
		$('.bfg-pup__opening-hours').text(pickUpPoint.openingHours);
		$('.bfg-pup__description').text(pickUpPoint.description);
		if (mapKey) {
			$('.bfg-pup__map').attr('href', pickUpPoint[mapKey]);
		} else {
			$('.bfg-pup__map').hide();
		}
	}

	function ajaxSelect(pickUpPointId) {
		const tr = $('.woocommerce-shipping-totals');
		tr.block( blockArgs );
		$.post(
			_fraktguiden_checkout.ajaxurl,
			{
				action: 'bfg_select_pick_up_point',
				id: pickUpPointId,
			}
		).error(
			function (data) {
				console.error(data)
				$('.bring-fraktguiden-pick-up-point-picker').hide();
				tr.unblock();
			}
		).done( function () { tr.unblock(); } );
	}

	const modalEl = $('.bring-fraktguiden-pick-up-points-modal');
	modalEl.find('.bfg-pupm__close').on('click', function (e) {
		e.preventDefault();
		modalEl.hide();
	}).on('keyup', function (e) {
		if (e.key !== 'Enter' && e.key !== ' ') {
			return;
		}
		e.preventDefault();
		modalEl.hide();
	});
	modalEl.on('click', function (e) {
		e.preventDefault();
		modalEl.hide();
	}).on('keyup', function (e) {
		if (e.key !== 'Escape') {
			return;
		}
		e.preventDefault();
		modalEl.hide();
	});
	modalEl.find('.bfg-pupm__inner').on(
		'click',
		function (e) {
			e.preventDefault();
			e.stopPropagation();
		}
	);

	let previous = $('#shipping_method .shipping_method:checked').val();
	$(document).on(
		'updated_checkout',
		function (event, data) {
			const el = $('.bring-fraktguiden-pick-up-point-picker');
			const current = $('#shipping_method .shipping_method:checked').val();
			let changed = current  !== previous;
			if (changed) {
				previous = current;
			}
			if (!el.length || !modalEl.length) {
				return;
			}
			el.show();
			el.block(blockArgs);

			const listEl = modalEl.find('.bfg-pupm__list');
			const template = modalEl.find('.bfg-pupm__template');

			const focusItem = function () {
				// find selected pick up point and focus it
				let selected = listEl.find('.bfg-pupm__item').filter(function () {
					return $(this).data('id') === selectedPickUpPoint.id;
				});
				if (! selected.length) {
					selected = listEl.find('.bfg-pupm__item').first().focus();
				}
				selected.focus();
				setTimeout(function () { selected.focus(); },100);
				console.log(selected);
			}
			const showModal = function () {
				modalEl.show();
				focusItem();
			};
			if (changed) {
				// Show the picker when selecting method with pickup points
				showModal();
			}

			$('.bfg-pup__change').on('click', showModal)

			// Delete items
			listEl.html('').block(blockArgs);

			$.get(
				_fraktguiden_checkout.ajaxurl,
				{ action: 'bfg_get_pick_up_points' }
			).done(
				function (response) {
					// Update values from response
					window._fraktguiden_data.selected_pick_up_point = response.selected_pick_up_point;
					selectedPickUpPoint = response.selected_pick_up_point;
					window._fraktguiden_data.pick_up_points = response.pick_up_points;
					pickUpPoints = response.pick_up_points;

					selectPickUpPoint(selectedPickUpPoint);
					el.unblock();

					// Create new items
					for (let i = 0; i < pickUpPoints.length; i++) {
						let pickUpPoint = pickUpPoints[i];
						const clone = template.clone();
						clone.attr('class', 'bfg-pupm__item')
						clone.data('id', pickUpPoint.id);
						clone.find('.bfg-pupm__name').text(pickUpPoint.name)
						clone.find('.bfg-pupm__address').text(getAddress(pickUpPoint))
						clone.on('click', handleSelectPickUpPoint(pickUpPoint));
						clone.on('keyup', function (e) {
							if (e.key === 'ArrowUp') {
								clone.prev().focus();
								e.preventDefault();
								return;
							}
							if (e.key === 'ArrowDown') {
								clone.next().focus();
								e.preventDefault();
								return;
							}

							if (e.key !== 'Enter' && e.key !== ' ') {
								return;
							}
							handleSelectPickUpPoint(pickUpPoint)(e);
						});
						listEl.append(clone);

					}
					listEl.unblock();

					if (modalEl.is(':visible')) {
						focusItem();
					}
				}
			).fail( () => listEl.text(_fraktguiden_data.i18n.ERROR_LOADING_PICK_UP_POINTS) );
		}
	);
})
