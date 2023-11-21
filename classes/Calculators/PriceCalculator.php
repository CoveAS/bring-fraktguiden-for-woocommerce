<?php

namespace Bring_Fraktguiden\Calculators;

use WC_Tax;

class PriceCalculator {
	public function excl_vat( string|float $line_price ): float{

		$line_price = floatval( $line_price );

		if ( $line_price && wc_prices_include_tax() ) {
			$tax_rates    = WC_Tax::get_shipping_tax_rates();
			$remove_taxes = WC_Tax::calc_tax( $line_price, $tax_rates, true );
			return $line_price - array_sum( $remove_taxes );
		}

		return $line_price;
	}
}
