<?php

/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Fraktguiden Customs Declaration
 */

class Fraktguiden_Customs_Declaration
{

	/**
	 * Setup
	 * 
	 * @return void
	 */
	public static function setup()
	{
		add_action('woocommerce_product_options_general_product_data', __CLASS__ . '::add_customs_declaration_metaboxes');
		add_action('woocommerce_process_product_meta', __CLASS__ . '::save_customs_declaration_metaboxes');
	}

	/**
	 * Add customs declaration metaboxes
	 * 
	 * @return void
	 */
	public static function add_customs_declaration_metaboxes()
	{
		if (Fraktguiden_Helper::get_option('customs_declaration_fields') === "no") {
			return;
		}

		echo '<div class="product_custom_field">';

		woocommerce_wp_text_input(
			array(
				'id'       => '_customs_declaration_tariff_code',
				'label'	   => __('Customs declaration tariff code', 'bring-fraktguiden-for-woocommerce'),
				'desc_tip' => 'true',
				'type'     => 'text'
			)
		);

		woocommerce_wp_textarea_input(
			array(
				'id'       => '_customs_declaration_description',
				'label'	   => __('Customs declaration description', 'bring-fraktguiden-for-woocommerce'),
				'desc_tip' => 'true',
			)
		);

		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->__get('countries');

		$country_of_origin = array(
			'id' => '_customs_declaration_state_of_origin',
			'class'       => array('form-field'),
			'label'       => __('Country of origin', 'bring-fraktguiden-for-woocommerce'),
			'placeholder' => __('Enter something', 'bring-fraktguiden-for-woocommerce'),
			'options'     => $countries
		);

		woocommerce_wp_select($country_of_origin);

		echo '</div>';
	}

	/**
	 * Save customs declaration metaboxes
	 * 
	 * @param int $post_id Post ID.
	 * 
	 * @return void
	 */
	public static function save_customs_declaration_metaboxes($post_id)
	{
		$woocommerce_customs_declaration_tariff_code = $_POST['_customs_declaration_tariff_code'];
		if ( !empty($woocommerce_customs_declaration_tariff_code) ) {
			update_post_meta($post_id, '_customs_declaration_tariff_code', esc_attr($woocommerce_customs_declaration_tariff_code));
		}

		$woocommerce_customs_declaration_description = $_POST['_customs_declaration_description'];
		if ( !empty($woocommerce_customs_declaration_description) ) {
			update_post_meta($post_id, '_customs_declaration_description', esc_html($woocommerce_customs_declaration_description) );
		}

		$woocommerce_customs_declaration_state_of_origin = $_POST['_customs_declaration_state_of_origin'];
		if ( !empty($woocommerce_customs_declaration_state_of_origin) ) {
			update_post_meta($post_id, '_customs_declaration_state_of_origin', esc_attr($woocommerce_customs_declaration_state_of_origin));
		}
	}
}
