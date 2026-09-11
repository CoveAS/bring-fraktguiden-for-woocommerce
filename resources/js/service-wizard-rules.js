/**
 * The rules that pick a Bring service from the answers of the service wizard.
 *
 * A service carries its traits in config/services.php. See the docblock of that
 * file for the meaning of each trait.
 */

const WEIGHT_BANDS = {
	'0-5': [0, 5],
	'5-35': [5, 35],
	'35-': [35, Infinity],
};

const matchesRecipient = (service, answers) => answers.recipient.includes(service.recipient);

const matchesDestination = (service, answers) =>
	answers.destination.includes(service.domestic ? 'domestic' : 'international');

const matchesWeight = (service, answers) =>
	answers.weight.some((band) => {
		const [bandMin, bandMax] = WEIGHT_BANDS[band];
		const max = service.maxWeight === null ? Infinity : service.maxWeight;

		return service.minWeight < bandMax && max > bandMin;
	});

/**
 * A mailbox parcel with tracking needs an RFID printer, and the same parcel
 * without tracking needs a regular printer. Every other service ignores the
 * question.
 */
const matchesRfid = (service, answers) => {
	if (service.rfid === null) {
		return true;
	}

	return answers.rfid.includes(service.rfid ? 'yes' : 'no');
};

export const matches = (service, answers) =>
	matchesRecipient(service, answers) &&
	matchesDestination(service, answers) &&
	matchesWeight(service, answers) &&
	matchesRfid(service, answers);
