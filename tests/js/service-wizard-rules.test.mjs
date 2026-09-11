/**
 * Runs the wizard rules against the real service data.
 *
 * The data comes from config/services.php through bin/wizard-services.php, so a
 * change to the service traits changes this test.
 *
 * Run: npm run test-js
 */

import { test } from 'node:test';
import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';
import { matches } from '../../resources/js/service-wizard-rules.js';

const root = fileURLToPath(new URL('../..', import.meta.url));
const services = JSON.parse(execFileSync('php', [root + 'bin/wizard-services.php']));

const recommend = (sender, answers) =>
	services.filter((service) => matches(service, answers, sender)).map((service) => service.code).sort();

const answers = (recipient, destination, weight, rfid) => ({ recipient, destination, weight, rfid });

test('every wizard service carries the traits the rules read', () => {
	assert.equal(services.length, 11);

	for (const service of services) {
		assert.ok(['private', 'business'].includes(service.recipient), service.code);
		assert.equal(typeof service.minWeight, 'number', service.code);
		assert.ok(Array.isArray(service.from), service.code);
		assert.ok(Array.isArray(service.domesticFrom), service.code);
	}
});

test('a private domestic parcel under 5 kg needs the printer answer', () => {
	assert.deepEqual(
		recommend('NO', answers(['private'], ['domestic'], ['0-5'], ['no'])),
		['3584', '5600', '5800']
	);

	assert.deepEqual(
		recommend('NO', answers(['private'], ['domestic'], ['0-5'], ['yes'])),
		['3570', '5600', '5800']
	);
});

test('a mailbox parcel drops out above 5 kg', () => {
	assert.deepEqual(
		recommend('NO', answers(['private'], ['domestic'], ['5-35'], ['yes'])),
		['5600', '5800']
	);
});

test('heavy goods reach a business, and no service reaches a private person', () => {
	assert.deepEqual(
		recommend('NO', answers(['business'], ['domestic'], ['35-'], ['no'])),
		['5100', '5400']
	);

	assert.deepEqual(recommend('NO', answers(['private'], ['domestic'], ['35-'], ['no'])), []);
});

test('a business parcel abroad is offered', () => {
	assert.deepEqual(
		recommend('NO', answers(['business'], ['international'], ['5-35'], ['no'])),
		['BUSINESS_PARCEL']
	);
});

test('PickUp Parcel and Home Delivery Parcel serve a private recipient abroad', () => {
	assert.deepEqual(
		recommend('NO', answers(['private'], ['international'], ['0-5'], ['no'])),
		['HOME_DELIVERY_PARCEL', 'PICKUP_PARCEL']
	);
});

test('a Swedish shop gets no Posten Norway service', () => {
	const codes = recommend(
		'SE',
		answers(['private', 'business'], ['domestic', 'international'], ['0-5', '5-35', '35-'], ['yes', 'no'])
	);

	assert.deepEqual(codes, [
		'BUSINESS_PALLET',
		'BUSINESS_PARCEL',
		'HOME_DELIVERY_PARCEL',
		'PICKUP_PARCEL',
	]);
});

test('a Swedish shop ships inside Sweden with the Bring services', () => {
	assert.deepEqual(
		recommend('SE', answers(['private'], ['domestic'], ['5-35'], ['no'])),
		['HOME_DELIVERY_PARCEL', 'PICKUP_PARCEL']
	);
});

test('a Norwegian shop ships inside Norway with the Posten services only', () => {
	const codes = recommend(
		'NO',
		answers(['private', 'business'], ['domestic'], ['0-5', '5-35', '35-'], ['yes', 'no'])
	);

	assert.deepEqual(codes, ['3570', '3584', '5000', '5100', '5400', '5600', '5800']);
});

test('an Icelandic shop gets nothing, because Bring sells no service from Iceland', () => {
	assert.deepEqual(
		recommend(
			'IS',
			answers(['private', 'business'], ['domestic', 'international'], ['0-5', '5-35', '35-'], ['yes', 'no'])
		),
		[]
	);
});

test('several answers add up', () => {
	const codes = recommend(
		'NO',
		answers(['private', 'business'], ['domestic', 'international'], ['0-5', '5-35', '35-'], ['yes', 'no'])
	);

	assert.equal(codes.length, services.length);
});
