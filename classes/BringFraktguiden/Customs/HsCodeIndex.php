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
	 * The transient that holds the built index.
	 */
	private const TRANSIENT = 'bring_fraktguiden_hs_code_index';

	/**
	 * How long a built index is kept. The tariff changes once a year.
	 */
	private const LIFETIME = MONTH_IN_SECONDS;

	/**
	 * How long a failed fetch is kept, so a source that is down is not called
	 * again on every order screen.
	 */
	private const RETRY = 15 * MINUTE_IN_SECONDS;

	/**
	 * The digits of a code in this index. The tariff holds longer numbers, but
	 * only the first six are the international HS code.
	 */
	private const DIGITS = 6;

	/**
	 * Return the index, or an empty index when the tariff cannot be read.
	 *
	 * @return array{version: string, positions: string[], codes: array<int, array{0: string, 1: int, 2: string}>}
	 */
	public static function get(): array
	{
		$cached = get_transient(self::TRANSIENT);

		if (is_array($cached)) {
			return $cached;
		}

		$index = self::build(self::download());

		set_transient(self::TRANSIENT, $index, $index['codes'] ? self::LIFETIME : self::RETRY);

		return $index;
	}

	/**
	 * Drop the built index, so the next read fetches the tariff again.
	 */
	public static function forget(): void
	{
		delete_transient(self::TRANSIENT);
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
			// The browser keeps the index, and compares this to know when the
			// tariff it holds is stale.
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
