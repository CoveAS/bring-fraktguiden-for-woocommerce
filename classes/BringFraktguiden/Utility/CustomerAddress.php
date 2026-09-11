<?php

namespace BringFraktguiden\Utility;

class CustomerAddress
{

	private ?string $country;
	private ?string $postcode;

	public function __construct()
	{
	}

	public function withCountry(?string $country): static
	{
		$this->country = $country;
		return $this;
	}

	public function withPostcode(?string $postcode): static
	{
		$this->postcode = $postcode;
		return $this;
	}

	public function getCountry()
	{
		return esc_html(apply_filters('bring_pickup_point_country', $this->country ?? WC()->customer->get_shipping_country()));
	}

	public function getPostcode()
	{
		return esc_html(apply_filters('bring_pickup_point_postcode', $this->postcode ?? WC()->customer->get_shipping_postcode()));
	}
}
