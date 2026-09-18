/**
 * Guards the type scale of the admin UI.
 *
 * The admin CSS may set a font size only from the four tokens in
 * resources/css/admin/_tokens.css, and a font weight only to 400 or 600.
 * The kitchen sink typography page shows the scale.
 *
 * Run: npm run test-js
 */

import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readdirSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const dir = fileURLToPath(new URL('../../resources/css/admin/', import.meta.url));

const files = readdirSync(dir).filter((name) => name.endsWith('.css'));

const lines = files.flatMap((name) =>
	readFileSync(dir + name, 'utf8')
		.split('\n')
		.map((text, index) => ({ where: `${name}:${index + 1}`, text }))
);

const allowedSize = /font-size:\s*(var\(--bfg-(font|btn)-[a-z0-9-]+\)|inherit)/;
const allowedWeight = /font-weight:\s*(400|600|var\(--bfg-btn-font-weight\)|inherit)/;

test('every font size comes from the scale', () => {
	const bad = lines
		.filter(({ text }) => text.includes('font-size:') && !text.trimStart().startsWith('--'))
		.filter(({ text }) => !allowedSize.test(text))
		.map(({ where, text }) => `${where}: ${text.trim()}`);

	assert.deepEqual(bad, [], 'Use a --bfg-font-* token. See the kitchen sink typography page.');
});

test('every font weight is 400 or 600', () => {
	const bad = lines
		.filter(({ text }) => text.includes('font-weight:') && !text.trimStart().startsWith('--'))
		.filter(({ text }) => !allowedWeight.test(text))
		.map(({ where, text }) => `${where}: ${text.trim()}`);

	assert.deepEqual(bad, [], 'Use 400 or 600. See the kitchen sink typography page.');
});
