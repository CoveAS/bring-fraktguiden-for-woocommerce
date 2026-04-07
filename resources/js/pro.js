import confetti from 'canvas-confetti';

document.addEventListener('DOMContentLoaded', () => {
	if (!new URLSearchParams(window.location.search).has('celebrate')) return;

	confetti({
		particleCount: 150,
		spread: 80,
		origin: { y: 0.6 },
		ticks: 200,
	});
});
