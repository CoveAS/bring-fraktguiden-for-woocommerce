<?php

namespace BringFraktguiden\Customs;

/**
 * The list of codes a shop already uses, so every HS code field suggests them.
 */
class HsCodeDatalist
{
	/**
	 * The id an HS code field points its list attribute at.
	 */
	public const ID = 'bring-used-hs-codes';

	/**
	 * Print the list. The product screen prints it once, above the fields.
	 */
	public static function render(): void
	{
		$codes = HsCode::used();

		if (!$codes) {
			return;
		}

		echo '<datalist id="' . esc_attr(self::ID) . '">';

		foreach ($codes as $code) {
			echo '<option value="' . esc_attr($code) . '"></option>';
		}

		echo '</datalist>';
	}
}
