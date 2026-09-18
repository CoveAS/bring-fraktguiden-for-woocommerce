/**
 * Guards the typography of the admin UI.
 *
 * Every font size, line height, letter spacing and font family in a stylesheet
 * that loads on an admin screen reads a token from
 * resources/css/admin/_tokens.css. A font weight is 400 or 600. The kitchen
 * sink typography page shows the scale.
 *
 * Run: npm run test-js
 */

import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readdirSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const css = fileURLToPath(new URL('../../resources/css/', import.meta.url));

// Every stylesheet that loads on an admin screen, so every one that reads the
// tokens. The checkout stylesheets and the shadow root ones sit outside.
const files = readdirSync(css + 'admin/')
	.filter((name) => name.endsWith('.css'))
	.map((name) => 'admin/' + name)
	.concat('shipping-services.css');

const lines = files.flatMap((name) =>
	readFileSync(css + name, 'utf8')
		.split('\n')
		.map((text, index) => ({ where: `${name}:${index + 1}`, text }))
);

const allowedSize = /font-size:\s*(var\(--bfg-(font|btn)-[a-z0-9-]+\)|inherit)/;
const allowedWeight = /font-weight:\s*(400|600|var\(--bfg-btn-font-weight\)|inherit)/;
// 1.5 is the ratio `.bfg` hands down, which every child then inherits.
const allowedLeading = /line-height:\s*(var\(--bfg-leading-[a-z0-9]+\)|1\.5|inherit)/;
const allowedTracking = /letter-spacing:\s*var\(--bfg-tracking-[a-z]+\)/;
const allowedFamily = /font-family:\s*var\(--bfg-font-(sans|mono)\)/;

const guard = (property, allowed) =>
	lines
		.filter(({ text }) => text.includes(property) && !text.trimStart().startsWith('--'))
		.filter(({ text }) => !allowed.test(text))
		.map(({ where, text }) => `${where}: ${text.trim()}`);

test('every font size comes from the scale', () => {
	assert.deepEqual(guard('font-size:', allowedSize), [], 'Use a --bfg-font-* token.');
});

test('every font weight is 400 or 600', () => {
	assert.deepEqual(guard('font-weight:', allowedWeight), [], 'Use 400 or 600.');
});

test('every line height comes from the scale', () => {
	assert.deepEqual(guard('line-height:', allowedLeading), [], 'Use a --bfg-leading-* token.');
});

test('every letter spacing comes from the scale', () => {
	assert.deepEqual(guard('letter-spacing:', allowedTracking), [], 'Use --bfg-tracking-meta.');
});

test('every font family comes from the scale', () => {
	assert.deepEqual(guard('font-family:', allowedFamily), [], 'Use --bfg-font-sans or --bfg-font-mono.');
});
