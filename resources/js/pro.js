import confetti from 'canvas-confetti';

document.addEventListener('DOMContentLoaded', () => {
	if (!new URLSearchParams(window.location.search).has('celebrate')) return;

	confetti({
		particleCount: 150,
		spread: 80,
		origin: { x: 0, y: 1 },
		ticks: 200,
	});
});
