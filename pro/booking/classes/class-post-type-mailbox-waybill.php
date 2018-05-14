<?php


class Post_Type_Mailbox_Waybill {

  static function setup() {
    add_action( 'init', __CLASS__ .'::waybill_capabilities', 0 );
    add_action( 'init', __CLASS__ .'::waybill_post_type', 1 );
  }

  /**
   * Waybill capabilities
   * Enables administrators and shop managers to edit waybills
   */
  static function waybill_capabilities() {
    $allowed_roles = ['administrator', 'shop_manager'];
    foreach ( $allowed_roles as $role_name ) {
      $role = get_role( $role_name );
      if ( ! $role ) {
        continue;
      }
      $role->add_cap( 'edit_mailbox_waybill' );
      $role->add_cap( 'read_mailbox_waybill' );
      $role->add_cap( 'edit_mailbox_waybills' );
      $role->add_cap( 'delete_mailbox_waybill' );
      $role->add_cap( 'delete_mailbox_waybills' );
      $role->add_cap( 'publish_mailbox_waybills' );
      $role->add_cap( 'read_private_mailbox_waybills' );
    }
  }

  /**
   * Waybill post type
   */
  static function waybill_post_type() {
    $labels = array(
      'name'                  => _x( 'Mailbox Waybills', 'Post Type General Name', 'bring-fraktgiden' ),
      'singular_name'         => _x( 'Mailbox Waybill', 'Post Type Singular Name', 'bring-fraktgiden' ),
      'menu_name'             => __( 'Mailbox Waybills', 'bring-fraktgiden' ),
      'name_admin_bar'        => __( 'Mailbox Waybill', 'bring-fraktgiden' ),
      'archives'              => __( 'Item Archives', 'bring-fraktgiden' ),
      'attributes'            => __( 'Item Attributes', 'bring-fraktgiden' ),
      'parent_item_colon'     => __( 'Parent Item:', 'bring-fraktgiden' ),
      'all_items'             => __( 'All Items', 'bring-fraktgiden' ),
      'add_new_item'          => __( 'Add New Item', 'bring-fraktgiden' ),
      'add_new'               => __( 'Add New', 'bring-fraktgiden' ),
      'new_item'              => __( 'New Item', 'bring-fraktgiden' ),
      'edit_item'             => __( 'Edit Waybill', 'bring-fraktgiden' ),
      'update_item'           => __( 'Update Item', 'bring-fraktgiden' ),
      'view_item'             => __( 'View Item', 'bring-fraktgiden' ),
      'view_items'            => __( 'View Items', 'bring-fraktgiden' ),
      'search_items'          => __( 'Search Item', 'bring-fraktgiden' ),
      'not_found'             => __( 'Not found', 'bring-fraktgiden' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'bring-fraktgiden' ),
      'featured_image'        => __( 'Featured Image', 'bring-fraktgiden' ),
      'set_featured_image'    => __( 'Set featured image', 'bring-fraktgiden' ),
      'remove_featured_image' => __( 'Remove featured image', 'bring-fraktgiden' ),
      'use_featured_image'    => __( 'Use as featured image', 'bring-fraktgiden' ),
      'insert_into_item'      => __( 'Insert into item', 'bring-fraktgiden' ),
      'uploaded_to_this_item' => __( 'Uploaded to this item', 'bring-fraktgiden' ),
      'items_list'            => __( 'Items list', 'bring-fraktgiden' ),
      'items_list_navigation' => __( 'Items list navigation', 'bring-fraktgiden' ),
      'filter_items_list'     => __( 'Filter items list', 'bring-fraktgiden' ),
    );
    $args = array(
      'label'                 => __( 'Mailbox Waybill', 'bring-fraktgiden' ),
      'description'           => __( 'Mailbox Waybill information page.', 'bring-fraktgiden' ),
      'labels'                => $labels,
      'supports'              => array(),
      'taxonomies'            => array(),
      'hierarchical'          => false,
      'public'                => false,
      'show_ui'               => true,
      'show_in_menu'          => false,
      'menu_position'         => 5,
      'menu_icon'             => 'dashicons-admin-page',
      'show_in_admin_bar'     => false,
      'show_in_nav_menus'     => false,
      'can_export'            => false,
      'has_archive'           => false,
      'exclude_from_search'   => true,
      'publicly_queryable'    => false,
      'capability_type'       => 'mailbox_waybill',
      'capabilities'          => [
        'edit_post'          => 'edit_mailbox_waybill',
        'read_post'          => 'read_mailbox_waybill',
        'delete_post'        => 'delete_mailbox_waybill',
        'edit_posts'         => 'edit_mailbox_waybills',
        'edit_others_posts'  => 'edit_others_mailbox_waybills',
        'publish_posts'      => 'publish_mailbox_waybills',
        'read_private_posts' => 'read_private_mailbox_waybills',
        'delete_posts'       => 'delete_mailbox_waybills',
      ],
    );
    register_post_type( 'mailbox_waybill', $args );
    remove_post_type_support('mailbox_waybill', 'title');
    remove_post_type_support('mailbox_waybill', 'editor');
  }
}