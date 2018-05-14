<?php


class Post_Type_Mailbox_Label {

  static function setup() {
    add_filter( 'user_has_cap', __CLASS__ .'::disallow_new_post_button', 10, 2 );
    add_filter( 'user_has_cap', __CLASS__ .'::disallow_new_post_page', 10, 2 );
    add_action( 'admin_menu', __CLASS__ .'::admin_menu', 100 );
    add_action( 'init', __CLASS__ .'::label_capabilities', 0 );
    add_action( 'init', __CLASS__ .'::label_post_type', 1 );
  }

  /**
   * Disallow New Post Page
   * Prevents users from accessing the "add new label" page
   * @return array
   */
  static function disallow_new_post_page( $allcaps, $caps ) {
    if ( ! did_action( 'admin_init' ) ) {
      return $allcaps;
    }
    $screen = get_current_screen();
    if ( ! $screen ) {
      return $allcaps;
    }
    if ( $screen->id == 'mailbox_label' && $screen->action == 'add' ) {
      return [];
    }
    return $allcaps;
  }

  /**
   * Disallow New Post Button
   * Removes the "add new" button on the listing page
   * @return array
   */
  static function disallow_new_post_button( $allcaps, $caps ) {
    if ( ! did_action( 'all_admin_notices' ) ) {
      return $allcaps;
    }
    $screen = get_current_screen();
    if ( 'edit-mailbox_label' != $screen->id ) {
      return $allcaps;
    }
    $cap = reset( $caps );
    if ( 'edit_mailbox_labels' !== $cap ) {
      return $allcaps;
    }
    if ( isset( $allcaps[ $cap ] ) ) {
      $allcaps[ $cap ] = false;
    }
    // Remove this filter
    remove_filter( 'user_has_cap', __CLASS__ .'::disallow_new_post_button', 10 );
    return $allcaps;
  }

  /**
   * Admin menu
   * removes the "add new label" link from the admin menu
   */
  static function admin_menu() {
    global $submenu;
    // if ( ! isset( $submenu['edit.php?post_type=mailbox_label'] ) ) {
    //   return;
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
      'administrator',
      'edit.php?post_type=mailbox_label'
    );
    add_submenu_page(
      'woocommerce',
      __( 'Mailbox waybills' ),
      __( 'Mailbox waybills' ),
      'administrator',
      'edit.php?post_type=mailbox_waybill'
    );
    // var_dump( $submenu['woocommerce'] );die;
  }
  /**
   * Mailbox Label capabilities
   * Enables administrators and shop managers to edit labels
   */
  static function label_capabilities() {
    $allowed_roles = ['administrator', 'shop_manager'];
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
  static function label_post_type() {
    $labels = array(
      'name'                  => _x( 'Mailbox Labels', 'Post Type General Name', 'bring-fraktguiden' ),
      'singular_name'         => _x( 'Mailbox Label', 'Post Type Singular Name', 'bring-fraktguiden' ),
      'menu_name'             => __( 'Mailbox Labels', 'bring-fraktguiden' ),
      'name_admin_bar'        => __( 'Mailbox Label', 'bring-fraktguiden' ),
      'archives'              => __( 'Item Archives', 'bring-fraktguiden' ),
      'attributes'            => __( 'Item Attributes', 'bring-fraktguiden' ),
      'parent_item_colon'     => __( 'Parent Item:', 'bring-fraktguiden' ),
      'all_items'             => __( 'All Items', 'bring-fraktguiden' ),
      'add_new_item'          => __( 'Add New Item', 'bring-fraktguiden' ),
      'add_new'               => __( 'Add New', 'bring-fraktguiden' ),
      'new_item'              => __( 'New Item', 'bring-fraktguiden' ),
      'edit_item'             => __( 'Edit Item', 'bring-fraktguiden' ),
      'update_item'           => __( 'Update Item', 'bring-fraktguiden' ),
      'view_item'             => __( 'View Item', 'bring-fraktguiden' ),
      'view_items'            => __( 'View Items', 'bring-fraktguiden' ),
      'search_items'          => __( 'Search Item', 'bring-fraktguiden' ),
      'not_found'             => __( 'Not found', 'bring-fraktguiden' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'bring-fraktguiden' ),
      'featured_image'        => __( 'Featured Image', 'bring-fraktguiden' ),
      'set_featured_image'    => __( 'Set featured image', 'bring-fraktguiden' ),
      'remove_featured_image' => __( 'Remove featured image', 'bring-fraktguiden' ),
      'use_featured_image'    => __( 'Use as featured image', 'bring-fraktguiden' ),
      'insert_into_item'      => __( 'Insert into item', 'bring-fraktguiden' ),
      'uploaded_to_this_item' => __( 'Uploaded to this item', 'bring-fraktguiden' ),
      'items_list'            => __( 'Items list', 'bring-fraktguiden' ),
      'items_list_navigation' => __( 'Items list navigation', 'bring-fraktguiden' ),
      'filter_items_list'     => __( 'Filter items list', 'bring-fraktguiden' ),
    );
    $args = array(
      'label'                 => __( 'Mailbox Label', 'bring-fraktguiden' ),
      'description'           => __( 'Mailbox Label information page.', 'bring-fraktguiden' ),
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
      'capability_type'       => 'mailbox_label',
      'capabilities'          => [
        'edit_post'          => 'edit_mailbox_label',
        'read_post'          => 'read_mailbox_label',
        'delete_post'        => 'delete_mailbox_label',
        'edit_posts'         => 'edit_mailbox_labels',
        'edit_others_posts'  => 'edit_others_mailbox_labels',
        'publish_posts'      => 'publish_mailbox_labels',
        'read_private_posts' => 'read_private_mailbox_labels',
        'delete_posts'       => 'delete_mailbox_labels',
      ],
    );
    register_post_type( 'mailbox_label', $args );
    remove_post_type_support('mailbox_label', 'title');
    remove_post_type_support('mailbox_label', 'editor');
  }
}