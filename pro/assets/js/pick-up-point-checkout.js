jQuery(function ($) {
	let pickUpPoints = window._fraktguiden_data.pick_up_points;
	let selectedPickUpPoint = window._fraktguiden_data.selected_pick_up_point;

	console.log(pickUpPoints);

	function getAddress(pickUpPoint) {
		return pickUpPoint.address + ', ' + pickUpPoint.postalCode + ' ' + pickUpPoint.city;
	}

	function selectPickUpPoint(pickUpPoint) {
		$('.bfg-pup__name').text(pickUpPoint.name);
		$('.bfg-pup__address').text(getAddress(pickUpPoint));
		$('.bfg-pup__opening-hours').text(pickUpPoint.openingHours);
		$('.bfg-pup__description').text(pickUpPoint.description);
	}

	$(document).on(
		'updated_checkout',
		function (data) {
			const el = $('.bring-fraktguiden-pick-up-point-picker');
			const modalEl = $('.bring-fraktguiden-pick-up-points-modal');
			if (!el.length || !modalEl.length) {
				return;
			}
			el.show();

			selectPickUpPoint(selectedPickUpPoint);

			const listEl = modalEl.find('.bfg-pupm__list');
			const template = modalEl.find('.bfg-pupm__template');
			$('.bfg-pup__change').on('click', function () {modalEl.show();});

			listEl.html('');
			for (let i = 0; i < pickUpPoints.length; i++) {
				let pickUpPoint = pickUpPoints[i];
				const clone = template.clone();
				clone.attr('class', 'bfg-pupm__item')
				clone.find('.bfg-pupm__name').text(pickUpPoint.name)
				clone.find('.bfg-pupm__address').text(getAddress(pickUpPoint))
				clone.on('click', function () {
					selectPickUpPoint(pickUpPoint);
					selectedPickUpPoint = pickUpPoint;
					modalEl.hide();
				})
				listEl.append(clone);
			}

		}
	);
})
