document.addEventListener('DOMContentLoaded', () => {
	const page = document.querySelector('.bfg-admin-page__home');
	if (!page) return;

	const setupComplete = page.dataset.setupComplete === 'true';
	if (!setupComplete) return;

	const storageKey = 'bfg_setup_celebrated';
	if (localStorage.getItem(storageKey)) return;

	localStorage.setItem(storageKey, '1');

	if (window.bfgHomeData?.proPageUrl) {
		window.location.href = window.bfgHomeData.proPageUrl + '&celebrate=1';
	}
});
