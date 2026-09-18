<?php

namespace BringFraktguiden\Utility;

class CustomerAddress
{

	private ?string $country;
	private ?string $postcode;
	private ?string $street;

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

	public function withStreet(?string $street): static
	{
		$this->street = $street;
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

	/**
	 * The street line. It goes to the Bring API as a query parameter, so it
	 * stays raw.
	 */
	public function getStreet(): string
	{
		return (string) apply_filters('bring_pickup_point_street', $this->street ?? WC()->customer?->get_shipping_address());
	}

	/**
	 * Every part the pick up point lookup depends on. The checkout compares
	 * this key to see whether it must load the points again.
	 */
	public function getKey(): string
	{
		return $this->getCountry() . $this->getPostcode() . $this->getStreet();
	}
}
