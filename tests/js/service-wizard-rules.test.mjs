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

const recommend = (answers) =>
	services.filter((service) => matches(service, answers)).map((service) => service.code);

const answers = (recipient, destination, weight, rfid) => ({ recipient, destination, weight, rfid });

test('every wizard service carries the traits the rules read', () => {
	assert.equal(services.length, 11);

	for (const service of services) {
		assert.ok(['private', 'business'].includes(service.recipient), service.code);
		assert.equal(typeof service.minWeight, 'number', service.code);
		assert.equal(typeof service.domestic, 'boolean', service.code);
	}
});

test('a private domestic parcel under 5 kg needs the printer answer', () => {
	assert.deepEqual(
		recommend(answers(['private'], ['domestic'], ['0-5'], ['no'])).sort(),
		['3584', '5600', '5800']
	);

	assert.deepEqual(
		recommend(answers(['private'], ['domestic'], ['0-5'], ['yes'])).sort(),
		['3570', '5600', '5800']
	);
});

test('a mailbox parcel drops out above 5 kg', () => {
	assert.deepEqual(
		recommend(answers(['private'], ['domestic'], ['5-35'], ['yes'])).sort(),
		['5600', '5800']
	);
});

test('heavy goods reach a business, and no service reaches a private person', () => {
	assert.deepEqual(
		recommend(answers(['business'], ['domestic'], ['35-'], ['no'])).sort(),
		['5100', '5400']
	);

	assert.deepEqual(recommend(answers(['private'], ['domestic'], ['35-'], ['no'])), []);
});

test('a business parcel abroad is offered', () => {
	assert.deepEqual(
		recommend(answers(['business'], ['international'], ['5-35'], ['no'])).sort(),
		['BUSINESS_PARCEL']
	);
});

test('PickUp Parcel stops at 20 kg, Home Delivery Parcel does not', () => {
	assert.deepEqual(
		recommend(answers(['private'], ['international'], ['0-5'], ['no'])).sort(),
		['HOME_DELIVERY_PARCEL', 'PICKUP_PARCEL']
	);

	assert.deepEqual(
		recommend(answers(['private'], ['international'], ['5-35'], ['no'])).sort(),
		['HOME_DELIVERY_PARCEL', 'PICKUP_PARCEL']
	);
});

test('several answers add up', () => {
	const codes = recommend(
		answers(['private', 'business'], ['domestic', 'international'], ['0-5', '5-35', '35-'], ['yes', 'no'])
	);

	assert.equal(codes.length, services.length);
});
