<?php

namespace BringFraktguiden\Customs;

/**
 * The list of HS codes a shop can choose from.
 *
 * Tolletaten publishes the whole Norwegian tariff as open data, under Creative
 * Commons Attribution 4.0. See doc/customs.md.
 *
 * The tariff names goods down to eight digits. Customs and Bring take the
 * first six, so this class keeps one row per six digit code.
 *
 * The text of a code is built from two parts. The position names the group,
 * for example "Hester, esler, muldyr og mulesler, levende". The rest names the
 * goods inside it, for example "hester, til avl". Many codes share one
 * position, so the positions are listed once and a row points at one of them.
 */
class HsCodeIndex
{
	/**
	 * The tariff structure, as JSON.
	 */
	private const URL = 'https://data.toll.no/dataset/6350e783-b989-4c7c-9ec0-de2dcb97363c/resource/68f78255-cbb0-4e75-86b1-3d5928816903/download/tolltariffstruktur.json';

	/**
	 * The option that holds the built index, with the time it was built.
	 */
	private const OPTION = 'bring_fraktguiden_hs_code_index';

	/**
	 * The transient that marks a download in flight.
	 *
	 * A failed download leaves the mark behind, so a source that is down is not
	 * called again on every order screen.
	 */
	private const FETCHING = 'bring_fraktguiden_hs_code_index_fetching';

	/**
	 * How long the mark of a download lives.
	 */
	private const RETRY = 15 * MINUTE_IN_SECONDS;

	/**
	 * When the daily job builds the index again. The tariff changes once a year.
	 */
	private const STALE = MONTH_IN_SECONDS;

	/**
	 * The oldest index a shop worker is served.
	 *
	 * A site whose cron never runs passes this, and then one order screen waits
	 * for the download.
	 */
	private const MAX_AGE = 3 * MONTH_IN_SECONDS;

	/**
	 * The digits of a code in this index. The tariff holds longer numbers, but
	 * only the first six are the international HS code.
	 */
	private const DIGITS = 6;

	/**
	 * Return the index, or an empty index when the tariff cannot be read.
	 *
	 * The stored index answers the reader. Only a missing index, or one past
	 * MAX_AGE, makes the reader wait for the tariff.
	 *
	 * @return array{version: string, positions: string[], codes: array<int, array{0: string, 1: int, 2: string}>}
	 */
	public static function get(): array
	{
		$stored = get_option(self::OPTION);

		if (!is_array($stored)) {
			return self::refresh();
		}

		if (time() - $stored['built'] > self::MAX_AGE) {
			return self::refresh();
		}

		return $stored['index'];
	}

	/**
	 * Build the index again when the stored one is old.
	 *
	 * The daily cron event calls this. See Bring_Fraktguiden::setup().
	 */
	public static function maybe_refresh(): void
	{
		// ponytail: the index lived in a transient before version 2.0. Drop this
		// line once no shop upgrades from an older version.
		delete_transient(self::OPTION);

		$stored = get_option(self::OPTION);

		if (is_array($stored) && time() - $stored['built'] < self::STALE) {
			return;
		}

		self::refresh();
	}

	/**
	 * Download the tariff, build the index and store it.
	 *
	 * Return the stored index when another process already downloads, or when
	 * the download fails.
	 */
	public static function refresh(): array
	{
		$stored = get_option(self::OPTION);

		// ponytail: the mark is read and written in two steps, so two screens
		// opened in the same second can both download. The cost is one extra
		// request. An atomic mark needs add_option and a takeover by age.
		if (get_transient(self::FETCHING)) {
			return is_array($stored) ? $stored['index'] : self::build([]);
		}

		set_transient(self::FETCHING, time(), self::RETRY);

		$index = self::build(self::download());

		if (!$index['codes']) {
			return is_array($stored) ? $stored['index'] : $index;
		}

		update_option(self::OPTION, ['built' => time(), 'index' => $index], false);
		delete_transient(self::FETCHING);

		return $index;
	}

	/**
	 * Drop the stored index, so the next read downloads the tariff again.
	 */
	public static function forget(): void
	{
		delete_option(self::OPTION);
		delete_transient(self::FETCHING);
	}

	/**
	 * Return the tariff as an array, or an empty array.
	 */
	private static function download(): array
	{
		$response = wp_remote_get(self::URL, ['timeout' => 30]);

		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			return [];
		}

		$tariff = json_decode(wp_remote_retrieve_body($response), true);

		return is_array($tariff) ? $tariff : [];
	}

	/**
	 * Turn the tariff into the index.
	 *
	 * @param array $tariff The tariff as Tolletaten publishes it.
	 *
	 * @return array{version: string, positions: string[], codes: array<int, array{0: string, 1: int, 2: string}>}
	 */
	private static function build(array $tariff): array
	{
		$positions = [];
		$codes     = [];
		$seen      = [];

		foreach ($tariff['avsnitt'] ?? [] as $section) {
			self::walk($section, '', '', $positions, $codes, $seen);
		}

		return [
			// The route sends this as the ETag, so a browser that already holds
			// this tariff gets a 304.
			'version'   => substr(md5(serialize($codes)), 0, 12),
			'positions' => array_keys($positions),
			'codes'     => $codes,
		];
	}

	/**
	 * Collect every six digit code below one node of the tariff.
	 *
	 * @param array         $node      The node.
	 * @param string        $position  The text of the position above the node.
	 * @param string        $sub       The text of the subpositions above the node.
	 * @param array<string, int> $positions The positions found so far, keyed by text.
	 * @param array         $codes     The rows found so far.
	 * @param array<string, true> $seen The codes found so far.
	 */
	private static function walk(array $node, string $position, string $sub, array &$positions, array &$codes, array &$seen): void
	{
		$type = $node['type'] ?? '';

		if ('posisjon' === $type) {
			$position = self::text($node['beskrivelse'] ?? '');
			$sub      = '';
		}

		if ('underposisjon' === $type) {
			$sub = trim($sub . ', ' . self::text($node['beskrivelse'] ?? ''), ', ');
		}

		if ('vare' === $type) {
			self::add($node, $position, $sub, $positions, $codes, $seen);
		}

		foreach (['kapitler', 'inndelinger', 'oppdelinger'] as $branch) {
			foreach ($node[$branch] ?? [] as $child) {
				self::walk($child, $position, $sub, $positions, $codes, $seen);
			}
		}
	}

	/**
	 * Add one row for a goods node, unless its code is already in the index.
	 *
	 * The tariff splits a six digit code into several eight digit numbers. The
	 * first one carries the text closest to the code itself.
	 */
	private static function add(array $node, string $position, string $sub, array &$positions, array &$codes, array &$seen): void
	{
		$code = substr((string) ($node['hsNummer'] ?? ''), 0, self::DIGITS);

		if (self::DIGITS !== strlen($code) || isset($seen[$code])) {
			return;
		}

		$seen[$code]      = true;
		$positions[$position] ??= count($positions);

		$codes[] = [
			$code,
			$positions[$position],
			trim($sub . ', ' . self::text($node['vareslag'] ?? ''), ', '),
		];
	}

	/**
	 * Return one line of plain text.
	 *
	 * The tariff writes a description as a fragment of HTML, and ends many of
	 * them with a colon because a list follows.
	 */
	private static function text(string $html): string
	{
		$text = html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

		return rtrim(trim(preg_replace('/\s+/u', ' ', $text)), ':.');
	}
}
