/**
 * Guards the spacing of the admin UI.
 *
 * A padding, margin, gap or inset value on the ladder reads a --bfg-space-*
 * token from resources/css/admin/_tokens.css. The ladder holds 4, 8, 12, 16,
 * 20, 24, 32, 40, 48, 56 and 64px.
 *
 * A value off the ladder, such as a 1px border or a 2px nudge, stays a raw px.
 * A negative value stays a raw px too, because calc() reads worse than the
 * number.
 *
 * Run: npm run test-js
 */

import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readdirSync, readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const css = fileURLToPath(new URL('../../resources/css/', import.meta.url));

const files = readdirSync(css + 'admin/')
	.filter((name) => name.endsWith('.css'))
	.map((name) => 'admin/' + name)
	.concat('shipping-services.css');

const lines = files.flatMap((name) =>
	readFileSync(css + name, 'utf8')
		.split('\n')
		.map((text, index) => ({ where: `${name}:${index + 1}`, text }))
);

const ladder = [4, 8, 12, 16, 20, 24, 32, 40, 48, 56, 64];
const property = /\b((padding|margin)(-[a-z]+)*|gap|row-gap|column-gap|inset):([^;]*)/;
const value = /(?<![-\w.])(\d+)px\b/g;

const offenders = lines
	.filter(({ text }) => !text.trimStart().startsWith('--'))
	.flatMap(({ where, text }) => {
		const match = property.exec(text);
		if (!match) return [];
		const found = [...match[4].matchAll(value)]
			.map((hit) => Number(hit[1]))
			.filter((size) => ladder.includes(size));
		return found.length ? [`${where}: ${text.trim()}`] : [];
	});

test('every spacing value on the ladder comes from a token', () => {
	assert.deepEqual(offenders, [], 'Use a --bfg-space-* token.');
});
