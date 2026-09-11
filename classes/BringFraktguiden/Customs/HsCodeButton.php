<?php

namespace BringFraktguiden\Customs;

/**
 * The button that opens the HS code picker.
 *
 * A screen prints the code alone. The text of the code lives in the tariff,
 * and the browser holds the tariff, so the script fills the text in. See
 * resources/js/hs-code-picker.js.
 */
class HsCodeButton
{
	/**
	 * Return the button and the hidden input that carries its value.
	 *
	 * @param string                $name  The form field name, or an empty string when nothing posts the value.
	 * @param string                $code  The code the field holds now.
	 * @param array<string, string> $data  Extra data attributes for the hidden input, without the data- prefix.
	 */
	public static function html(string $name, string $code, array $data = []): string
	{
		$attributes = '';

		foreach ($data as $key => $value) {
			$attributes .= sprintf(' data-%s="%s"', esc_attr($key), esc_attr($value));
		}

		return sprintf(
			'<span class="bfg-hs-button-field">'
				. '<input type="hidden"%s value="%s"%s>'
				. '<button type="button" class="bfg-hs-button" data-bfg-hs-pick>'
					. '<span class="bfg-hs-button__code" data-bfg-hs-code>%s</span>'
					. '<span class="bfg-hs-button__text" data-bfg-hs-text></span>'
				. '</button>'
			. '</span>',
			$name ? sprintf(' name="%s"', esc_attr($name)) : '',
			esc_attr($code),
			$attributes,
			$code ? esc_html(self::dotted($code)) : esc_html__('Choose an HS code', 'bring-fraktguiden-for-woocommerce')
		);
	}

	/**
	 * Return the code with dots, as Tolltariffen prints it.
	 *
	 * Tolltariffen breaks the chapter and the position off, and leaves the
	 * rest whole. The code 64031200 reads 64.03.1200.
	 */
	public static function dotted(string $code): string
	{
		$parts = [substr($code, 0, 2), substr($code, 2, 2), substr($code, 4)];

		return implode('.', array_filter($parts));
	}
}
