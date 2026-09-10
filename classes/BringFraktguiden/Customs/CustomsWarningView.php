<?php

namespace BringFraktguiden\Customs;

/**
 * Prints the customs warning of an order.
 *
 * The markup lives in src/templates/admin/parts/customs-warning.bfg.php, and
 * the compiler writes the file this class loads. A new order screen keeps the
 * template and replaces this class.
 */
class CustomsWarningView
{
	/**
	 * Print the warning. A null warning prints nothing.
	 */
	public static function render(?CustomsWarning $warning): void
	{
		if (!$warning) {
			return;
		}

		require dirname(__DIR__, 3) . '/build/templates/admin/parts/customs-warning.php';
	}
}
