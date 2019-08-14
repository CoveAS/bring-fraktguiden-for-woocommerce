<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

/**
 * Post_Type_Mailbox_Label class
 */
class Post_Type_Mailbox_Label {

	/**
	 * Setup
	 *
	 * @return void
	 */
	public static function setup() {
		add_filter( 'user_has_cap', __CLASS__ . '::disallow_new_post_button', 10, 2 );
		add_filter( 'user_has_cap', __CLASS__ . '::disallow_new_post_page', 10, 2 );
		add_action( 'admin_menu', __CLASS__ . '::admin_menu', 100 );
		add_action( 'init', __CLASS__ . '::label_capabilities', 0 );
		add_action( 'init', __CLASS__ . '::label_post_type', 1 );
	}

	/**
	 * Disallow New Post Page
	 * Prevents users from accessing the "add new label" page
	 *
	 * @param array $allcaps All caps.
	 * @param array $caps    Caps.
	 *
	 * @return array
	 */
	public static function disallow_new_post_page( $allcaps, $caps ) {
		if ( ! did_action( 'admin_init' ) ) {
			return $allcaps;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return $allcaps;
		}

		if ( 'mailbox_label' === $screen->id && 'add' === $screen->action ) {
			return [];
		}

		return $allcaps;
	}

	/**
	 * Disallow New Post Button
	 * Removes the "add new" button on the listing page
	 *
	 * @param array $allcaps All caps.
	 * @param array $caps    Caps.
	 *
	 * @return array
	 */
	public static function disallow_new_post_button( $allcaps, $caps ) {
		if ( ! did_action( 'all_admin_notices' ) ) {
			return $allcaps;
		}

		$screen = get_current_screen();

		if ( 'edit-mailbox_label' !== $screen->id ) {
			return $allcaps;
		}

		$cap = reset( $caps );

		if ( 'edit_mailbox_labels' !== $cap ) {
			return $allcaps;
		}

		if ( isset( $allcaps[ $cap ] ) ) {
			$allcaps[ $cap ] = false;
		}

		// Remove this filter.
		remove_filter( 'user_has_cap', __CLASS__ . '::disallow_new_post_button', 10 );

		return $allcaps;
	}

	/**
	 * Admin menu
	 * removes the "add new label" link from the admin menu
	 */
	public static function admin_menu() {
		global $submenu;

		// if ( ! isset( $submenu['edit.php?post_type=mailbox_label'] ) ) {
		// return;
		// }
		// unset( $submenu[ 'edit.php?post_type=mailbox_label' ][ 10 ] );
		// edit.php?post_type=mailbox_label
		//
		// var_dump( $submenu );die;

		if ( ! isset( $submenu['woocommerce'] ) ) {
			return;
		}

		add_submenu_page(
			'woocommerce',
			__( 'Mailbox labels' ),
			__( 'Mailbox labels' ),
			'read_mailbox_label',
			'edit.php?post_type=mailbox_label'
		);

		add_submenu_page(
			'woocommerce',
			__( 'Mailbox waybills' ),
			__( 'Mailbox waybills' ),
			'read_mailbox_waybill',
			'edit.php?post_type=mailbox_waybill'
		);
	}

	/**
	 * Mailbox Label capabilities
	 * Enables administrators and shop managers to edit labels
	 */
	public static function label_capabilities() {
		$allowed_roles = [ 'administrator', 'shop_manager' ];

		foreach ( $allowed_roles as $role_name ) {
			$role = get_role( $role_name );

			if ( ! $role ) {
				continue;
			}

			$role->add_cap( 'read_mailbox_label' );
			$role->add_cap( 'delete_mailbox_label' );
			$role->add_cap( 'delete_mailbox_labels' );
			$role->remove_cap( 'publish_mailbox_labels' );
			$role->add_cap( 'edit_mailbox_labels' );
			$role->remove_cap( 'edit_mailbox_label' );
			$role->add_cap( 'read_private_mailbox_labels' );
		}
	}

	/**
	 * Mailbox Label post type
	 */
	public static function label_post_type() {
		$labels = [
			'name'                  => _x( 'Mailbox Labels', 'Post Type General Name', 'bring-fraktguiden-for-woocommerce' ),
			'singular_name'         => _x( 'Mailbox Label', 'Post Type Singular Name', 'bring-fraktguiden-for-woocommerce' ),
			'menu_name'             => __( 'Mailbox Labels', 'bring-fraktguiden-for-woocommerce' ),
			'name_admin_bar'        => __( 'Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'archives'              => __( 'Mailbox Label Archives', 'bring-fraktguiden-for-woocommerce' ),
			'attributes'            => __( 'Mailbox Label Attributes', 'bring-fraktguiden-for-woocommerce' ),
			'parent_item_colon'     => __( 'Parent Mailbox Label:', 'bring-fraktguiden-for-woocommerce' ),
			'all_items'             => __( 'All Mailbox Labels', 'bring-fraktguiden-for-woocommerce' ),
			'add_new_item'          => __( 'Add New Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'add_new'               => __( 'Add New', 'bring-fraktguiden-for-woocommerce' ),
			'new_item'              => __( 'New Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'edit_item'             => __( 'Edit Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'update_item'           => __( 'Update Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'view_item'             => __( 'View Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'view_items'            => __( 'View Mailbox Labels', 'bring-fraktguiden-for-woocommerce' ),
			'search_items'          => __( 'Search Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'not_found'             => __( 'Not found', 'bring-fraktguiden-for-woocommerce' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'bring-fraktguiden-for-woocommerce' ),
			'featured_image'        => __( 'Featured Image', 'bring-fraktguiden-for-woocommerce' ),
			'set_featured_image'    => __( 'Set featured image', 'bring-fraktguiden-for-woocommerce' ),
			'remove_featured_image' => __( 'Remove featured image', 'bring-fraktguiden-for-woocommerce' ),
			'use_featured_image'    => __( 'Use as featured image', 'bring-fraktguiden-for-woocommerce' ),
			'insert_into_item'      => __( 'Insert into Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'items_list'            => __( 'Mailbox Labels list', 'bring-fraktguiden-for-woocommerce' ),
			'items_list_navigation' => __( 'Mailbox Labels list navigation', 'bring-fraktguiden-for-woocommerce' ),
			'filter_items_list'     => __( 'Filter Mailbox Labels list', 'bring-fraktguiden-for-woocommerce' ),
		];

		$args = [
			'label'               => __( 'Mailbox Label', 'bring-fraktguiden-for-woocommerce' ),
			'description'         => __( 'Mailbox Label information page.', 'bring-fraktguiden-for-woocommerce' ),
			'labels'              => $labels,
			'supports'            => [],
			'taxonomies'          => [],
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-page',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'mailbox_label',
			'capabilities'        => [
				'edit_post'          => 'edit_mailbox_label',
				'read_post'          => 'read_mailbox_label',
				'delete_post'        => 'delete_mailbox_label',
				'edit_posts'         => 'edit_mailbox_labels',
				'edit_others_posts'  => 'edit_others_mailbox_labels',
				'publish_posts'      => 'publish_mailbox_labels',
				'read_private_posts' => 'read_private_mailbox_labels',
				'delete_posts'       => 'delete_mailbox_labels',
			],
		];

		register_post_type( 'mailbox_label', $args );
		remove_post_type_support( 'mailbox_label', 'title' );
		remove_post_type_support( 'mailbox_label', 'editor' );
	}
}
