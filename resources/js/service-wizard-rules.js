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

/**
 * A service is domestic when it carries a parcel inside the country the shop
 * sends from. It reaches another country when it may cross a border at all.
 */
const matchesDestination = (service, answers, sender) => {
	const domestic = service.domesticFrom.includes(sender);
	const international = service.crossBorder !== false;

	return (
		(domestic && answers.destination.includes('domestic')) ||
		(international && answers.destination.includes('international'))
	);
};

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

export const matches = (service, answers, sender) =>
	service.from.includes(sender) &&
	matchesRecipient(service, answers) &&
	matchesDestination(service, answers, sender) &&
	matchesWeight(service, answers) &&
	matchesRfid(service, answers);
