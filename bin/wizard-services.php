<?php

/**
 * Prints the service wizard data as JSON.
 *
 * The JavaScript test reads this, so the test runs against the same data the
 * browser gets. WordPress and WooCommerce are not loaded here, so the script
 * stubs the two functions config/services.php and Config need.
 */

if (PHP_SAPI !== 'cli') {
	exit(1);
}

function __($text, $domain = null)
{
	return $text;
}

function WC()
{
	return new class {
		public $countries = null;
	};
}

require __DIR__ . '/../classes/BringFraktguiden/Utility/Config.php';
require __DIR__ . '/../classes/BringFraktguiden/Admin/ServiceWizard.php';

echo json_encode(BringFraktguiden\Admin\ServiceWizard::services(), JSON_PRETTY_PRINT), PHP_EOL;
