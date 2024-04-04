<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

use Bring_Fraktguiden\Common\Fraktguiden_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Fraktguiden_Packaging
 *
 * Packs products into 'packages'
 */
class Fraktguiden_Packer {

	/**
	 * Packages to ship
	 *
	 * @var array
	 */
	private $packages_to_ship;

	/**
	 * Popped product boxes
	 *
	 * @var array
	 */
	private $popped_product_boxes;

	/**
	 * LAFF pack
	 *
	 * @var array
	 */
	private $laff_pack;

	/**
	 * @var false|mixed|null
	 */
	private mixed $dim_unit;
	/**
	 * @var false|mixed|null
	 */
	private mixed $weight_unit;

	/**
	 * Construct
	 *
	 * @return void
	 */
	public function __construct() {

		$this->dim_unit    = get_option( 'woocommerce_dimension_unit' );
		$this->weight_unit = get_option( 'woocommerce_weight_unit' );

		$this->packages_to_ship     = [];
		$this->popped_product_boxes = [];

		$this->laff_pack = new \Cloudstek\PhpLaff\Packer();
	}

	/**
	 * Pack product box(es) into container/s
	 *
	 * @recursive
	 *
	 * @param array   $product_boxes Product boxes dimensions. Each box contains an array of { length, width, height, weight }.
	 * @param boolean $multi_pack Multi pack.
	 */
	public function pack( $product_boxes, $multi_pack = false ): array {
		if ( ! $this->laff_pack ) {
			return [];
		}

		// Calculate total weight of boxes.
		$total_weight = 0;

		foreach ( $product_boxes as $box ) {
			$total_weight += floatval( $box['weight'] );
		}

		$package = [
			'weight_in_grams' => $this->get_weight( $total_weight ),
			'length' => Fraktguiden_Helper::get_option( 'minimum_length' ),
			'width' => Fraktguiden_Helper::get_option( 'minimum_width' ),
			'height' => Fraktguiden_Helper::get_option( 'minimum_height' ),
		];
		if (! empty($product_boxes)) {

			// Pack the boxes in a container.
			$this->laff_pack->pack( $product_boxes );
			$package_size = $this->laff_pack->get_container_dimensions();
			$package = [
				...$package,
				'length'          => $this->dimension_in_cm( $package_size['length'] ),
				'width'           => $this->dimension_in_cm( $package_size['width'] ),
				'height'          => $this->dimension_in_cm( $package_size['height'] ),
			];
		}

		// Get the sizes in cm.
		$package = apply_filters(
			'bring_fraktguiden_minimum_dimensions',
			$package
		);

		if ( ! $multi_pack ) {
			$this->packages_to_ship[] = $package;
			return $this->packages_to_ship;
		}

		// Check if the container exceeds max values.
		// Note: This only works for SERVICEPAKKE.
		if ( $this->exceeds_max_package_values( $package ) ) {
			// Move one item to the popped cache and run again.
			$popped = array_pop( $product_boxes );

			if ( ! empty( $product_boxes ) ) {
				// There are still boxes in the package.
				$this->popped_product_boxes[] = $popped;
				$this->pack( $product_boxes, true );

				return $this->packages_to_ship;
			}

			// $popped is too big to ship
			// Pack it without multipack.
			$this->pack( [ $popped ] );
		} else {
			// The package is ok to ship.
			$this->packages_to_ship[] = $package;
		}

		if ( ! empty( $this->popped_product_boxes ) ) {
			// Check the remaining boxes.
			$popped                     = $this->popped_product_boxes;
			$this->popped_product_boxes = [];
			$this->pack( $popped, true );
		}

		return $this->packages_to_ship;
	}

	/**
	 * Creates an array of dimension/s and weight/s for each container.
	 *
	 * @return array
	 */
	public function create_packages_params() {
		$params = [];
		$len    = count( $this->packages_to_ship );
		for ( $i = 0; $i < $len; $i++ ) {
			$params[ 'length' . $i ] = round( $this->packages_to_ship[ $i ]['length'] );
			$params[ 'width' . $i ]  = round( $this->packages_to_ship[ $i ]['width'] );
			$params[ 'height' . $i ] = round( $this->packages_to_ship[ $i ]['height'] );
			$params[ 'weight' . $i ] = round( $this->packages_to_ship[ $i ]['weight_in_grams'] );
		}
		return $params;
	}

	/**
	 * Checks if the given package size qualifies for package splitting.
	 *
	 * @param array       $container_size Array with container width/height/length/weight.
	 * @param object|null $product WP Product.
	 *
	 * @return boolean
	 */
	public function exceeds_max_package_values( $container_size, $product = null ) {

		$weight = $container_size['weight_in_grams'];

		if ( $weight >= 35000 ) {
			if ( $product ) {
				Fraktguiden_Helper::add_admin_message( 'Product with SKU %s exceeds the max weight of 35 Kg', $product->get_sku() );
			}

			return true;
		}

		// Create L x W x H array by removing the weight elements.
		$dimensions = $container_size;
		unset( $dimensions['weight_in_grams'] );
		unset( $dimensions['weight'] );

		// Reverse sort the dimensions/L x W x H array.
		arsort( $dimensions );

		// The longest side should now be on the first element.
		$longest_side = current( $dimensions );

		if ( $longest_side > 240 ) {
			if ( $product ) {
				Fraktguiden_Helper::add_admin_message( 'Product with SKU %s exceeds the max length of 240 cm', $product->get_sku() );
			}

			return true;
		}

		// Store the other sides.
		$side2 = next( $dimensions );
		$side3 = next( $dimensions );

		// Add the longest side and add the other sides multiplied by 2.
		$longest_plus_circumference = $longest_side + ( $side2 * 2 ) + ( $side3 * 2 );

		if ( $longest_plus_circumference > 360 ) {
			if ( $product ) {
				Fraktguiden_Helper::add_admin_message( 'Product with SKU %s exceeds the max circumference of 360 cm', $product->get_sku() );
			}
			return true;
		}

		return false;
	}

	/**
	 * Return weight in grams
	 *
	 * @param float $weight Weight.
	 *
	 * @return float
	 */
	public function get_weight( $weight ) {
		$weight = floatval( $weight );

		switch ( $this->weight_unit ) {

			case 'g':
				break;

			case 'kg':
				$weight = $weight / 0.0010000;
				break;

			case 'lbs':
				$weight = $weight / 0.0022046;
				break;

			case 'oz':
				$weight = $weight / 0.035274;
				break;

			/* Unknown weight unit */
			default:
				$weight = 0;
		}

		return $weight;
	}

	/**
	 * Return dimension in centimeters
	 *
	 * @param float $dimension Dimension.
	 *
	 * @return float
	 */
	public function dimension_in_cm($dimension ) {

		switch ( $this->dim_unit ) {
			case 'mm':
				$dimension = $dimension / 10.000;
				break;
			case 'in':
				$dimension = $dimension / 0.39370;
				break;
			case 'yd':
				$dimension = $dimension / 0.010936;
				break;
			case 'cm':
				break;
			case 'm':
				$dimension = $dimension / 0.010000;
				break;
			// Unknown dimension unit.
			default:
				return false;
		}

		if ( 1 > $dimension ) {
			// Minimum 1 cm.
			$dimension = 1;
		}

		return $dimension;
	}

	/**
	 * Return volume in dm
	 *
	 * @param int $dimension Dimension.
	 *
	 * @return float
	 */
	public function get_volume( $dimension ) {
		switch ( $this->dim_unit ) {

			case 'mm':
				return $dimension / 100;

			case 'in':
				return $dimension * 0.254;

			case 'yd':
				return $dimension * 9.144;

			case 'cm':
				return $dimension / 1000;

			case 'm':
				return $dimension / 10;

			/* Unknown dimension unit */
			default:
				return false;
		}
	}

	/**
	 * Create boxes
	 *
	 * @param object $cart WC_Cart.
	 *
	 * @return array
	 */
	public function create_boxes( $cart ) {
		// Create an array of 'product boxes' (l,w,h,weight).
		$product_boxes     = [];
		$ignore_dimensions = 'yes' === Fraktguiden_Helper::get_option( 'calculate_by_weight' );

		foreach ( $cart as $values ) {
			$product = $values['data']; // WC_Product.

			if ( empty($product) || ! $product->needs_shipping() ) {
				continue;
			}

			$quantity = $values['quantity'];

			for ( $i = 0; $i < $quantity; $i++ ) {
				// Assign product the lowest unit dimensions (1x1x1cm) by default.
				$dims = [ 0, 0, 0 ];

				if ( $product->has_dimensions() && ! $ignore_dimensions ) {
					$dims = [
						$product->get_length(),
						$product->get_width(),
						$product->get_height(),
					];
				}

				// Workaround weird Cloudstek\PhpLaff\Packer issue where the dimensions are expected in reverse order.
				rsort( $dims );

				$box = [
					'length'          => floatval($dims[0]),
					'width'           => floatval($dims[1]),
					'height'          => floatval($dims[2]),
					'weight'          => floatval($product->get_weight()),
					'weight_in_grams' => floatval($this->get_weight( $product->get_weight() )),
				];

				$product_boxes[] = $box;
			}
		}

		return $product_boxes;
	}
}
