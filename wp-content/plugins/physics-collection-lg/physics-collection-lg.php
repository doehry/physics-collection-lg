<?php
/**
 * Plugin Name: Physics Collection LG
 * Description: A WordPress Plugin for the Physics Collection at Liechtensteinisches Gymnasium
 * Version: 1.0
 * Requires at least: 6.8
 * Requires Plugins: pods
 */
namespace Physics_Collection_LG;
use Pods_Migrate_Packages;
use WP_Error;

defined( 'ABSPATH' ) || exit();

/**
 * Plugin activation function
 */
function activate_plugin() {
    if ( ! class_exists( 'Pods_Component_I18n' ) ) {
        pods_components()->toggle( 'translate-pods-admin', true );
        pods_components()->load();
    }

    if ( ! class_exists( 'Pods_Migrate_Packages' ) ) {
        pods_components()->toggle( 'migrate-packages', true );
        pods_components()->load();
    }
    Pods_Migrate_Packages::import( file_get_contents( __DIR__ . '/includes/pods.json' ) );

    $teacher_caps = [
        'read' => true,
        'publish_posts' => true,
        'edit_posts' => true,
        'edit_published_posts' => true,
        'delete_posts' => true,
        'delete_published_posts' => true,
        'upload_files' => true,
    ];

    $admin_caps = [
        'read_private_objects' => true,
        'publish_objects' => true,
        'edit_objects' => true,
        'edit_others_objects' => true,
        'edit_published_objects' => true,
        'edit_private_objects' => true,
        'delete_objects' => true,
        'delete_others_objects' => true,
        'delete_published_objects' => true,
        'delete_private_objects' => true,
    ];

    $assistant_caps = $teacher_caps + $admin_caps + [
        'read_private_posts' => true,
        'edit_others_posts' => true,
        'edit_private_posts' => true,
        'delete_others_posts' => true,
        'delete_private_posts' => true,
        'manage_categories' => true,
        'edit_files' => true,
    ];

    $leader_caps = $assistant_caps + [
        'list_users' => true,
        'create_users' => true,
        'edit_users' => true,
        'promote_users' => true,
    ];

    if ( get_role( 'teacher' ) === null ) {
        add_role( 'teacher', 'Teacher', $teacher_caps );
    }
    if ( get_role( 'assistant' ) === null ) {
        add_role( 'assistant', 'Assistant', $assistant_caps );
    }
    if ( get_role( 'leader' ) === null ) {
        add_role( 'leader', 'Leader', $leader_caps );
    }

    $admin = get_role( 'administrator' );
    foreach ( $admin_caps as $cap => $grant ) {
        $admin->add_cap( $cap, $grant );
    }

    update_option( 'default_role', 'teacher' );
}

register_activation_hook( __FILE__, 'Physics_Collection_LG\activate_plugin' );

/**
 * Plugin deactivation function
 */
function deactivate_plugin() {
    update_option( 'default_role', 'subscriber' );
    if ( get_role( 'teacher' ) ) {
        remove_role( 'teacher' );
    }
    if ( get_role( 'assistant' ) ) {
        remove_role( 'assistant' );
    }
    if ( get_role( 'leader' ) ) {
        remove_role( 'leader' );
    }

    $admin_caps = [
        'read_private_objects' => true,
        'publish_objects' => true,
        'edit_objects' => true,
        'edit_others_objects' => true,
        'edit_published_objects' => true,
        'edit_private_objects' => true,
        'delete_objects' => true,
        'delete_others_objects' => true,
        'delete_published_objects' => true,
        'delete_private_objects' => true,
    ];

    $admin = get_role( 'administrator' );
    foreach ( $admin_caps as $cap => $grant ) {
        $admin->remove_cap( $cap, $grant );
    }
}

register_deactivation_hook( __FILE__, 'Physics_Collection_LG\deactivate_plugin' );

/**
 * Insert auto increment inventory number
 */
function insert_inventory_number( $post_id, $post, $update ) {

    if ( ! $update ) {
        $current_base = date( 'y' ) * 1000;
        $pod = pods( 'object' );
        $params = [
            'select' => 'MAX(CAST(inventory_number.meta_value AS UNSIGNED)) AS max_number',
            'where' => 'inventory_number.meta_value > ' . $current_base,
            'limit' => '1',
        ];
        $pod->find( $params )->fetch();
        $max_number = $pod->field( 'max_number' );
        $inventory_number = empty( $max_number ) ? $current_base + 1 : $max_number + 1 ;
        add_post_meta( $post_id, 'inventory_number', $inventory_number );
    }
}

add_action( 'save_post_object', 'Physics_Collection_LG\insert_inventory_number', 10, 3 );

/**
 * Add custom columns to the object post type
 */
function set_custom_edit_object_columns( $columns ) {
    $pod = pods( 'object' );
    unset( $columns['date'] );
    $reordered = [];
    foreach ( $columns as $key => $value ) {
        if ( $key == 'title' ) {
            $reordered[ $key ] = $value;
            $reordered['inventory_number'] = $pod->fields( 'inventory_number', 'label' );
            $reordered['location'] = $pod->fields( 'related_location', 'label' );
        } else {
            $reordered[ $key ] = $value;
        }
    }
    return $reordered;
}

add_filter( 'manage_object_posts_columns', 'Physics_Collection_LG\set_custom_edit_object_columns' );

/**
 * Add data to the custom columns for the object post type:
 */
function custom_object_column( $column, $post_id ) {
    $objects = pods( 'object', $post_id );
    switch ( $column ) {
        case 'inventory_number':
            echo $objects->field( 'inventory_number' );
            break;
        case 'location':
            echo $objects->field( 'related_location.name' );
            break;
    }
}

add_action( 'manage_object_posts_custom_column' , 'Physics_Collection_LG\custom_object_column', 10, 2 );

/**
 * Make custom columns sortable
 */
function object_sortable_columns( $columns ) {
	$columns['inventory_number'] = 'inventory_number';
    $columns['location'] = 'location';
	return $columns;
}

add_filter( 'manage_edit-object_sortable_columns', 'Physics_Collection_LG\object_sortable_columns' );

function object_slice_orderby( $query ) {
    $orderby = $query->get( 'orderby' );
    switch ( $orderby ) {
        case 'inventory_number':
            $query->set( 'meta_key','inventory_number' );
            $query->set( 'orderby','meta_value' );
            break;
        case 'location':
            $query->set( 'meta_key','related_location' );
            $query->set( 'orderby','meta_value' );
            break;
    }
}

add_action( 'pre_get_posts', 'Physics_Collection_LG\object_slice_orderby' );

/**
 * Load style-admin.css
 */
add_action('admin_enqueue_scripts',
    function () {
        wp_enqueue_style( 'my-admin-style', plugin_dir_url( __FILE__ ) . '/css/style-admin.css' );
    }
);

/**
 * Do not allow changes to admins if the current user is not an admin
 */
function my_map_meta_cap( $caps, $cap, $user_id, $args ) {
    $check_caps = [
        'edit_user',
        'promote_user',
        'delete_user',
    ];
    if ( ! in_array( $cap, $check_caps ) || current_user_can( 'administrator' ) ) {
        return $caps;
    }
    $other = get_user_by( 'id', $args[0] ?? false );
    if ( $other && $other->has_cap( 'administrator' ) ) {
        echo $cap ." ";
        $caps[] = 'do_not_allow';
    }
return $caps;
}

add_filter( 'map_meta_cap', 'Physics_Collection_LG\my_map_meta_cap', 10, 4 );

/**
 * Remove 'Administrator' from the list of roles if the current user is not an admin
 */
function editable_roles( $roles ){
    if ( isset( $roles['administrator'] ) && !current_user_can( 'administrator' ) ) {
        unset( $roles['administrator'] );
    }
    return $roles;
}

add_filter( 'editable_roles', 'Physics_Collection_LG\editable_roles' );

/**
 *  Make the frontend private
 *  from https://github.com/reimersjan/wp-logged-in-only
 */
function logged_in_only_frontend() {
	if ( ! is_user_logged_in() ) {
		auth_redirect();
	}
}
add_action( 'template_redirect', 'Physics_Collection_LG\logged_in_only_frontend' );

/**
 * Make the REST API private
 * from https://github.com/reimersjan/wp-logged-in-only
 */
function logged_in_only_rest_api( $result ) {

	if ( ! empty( $result ) ) {
		return $result;
	}

	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'rest_not_logged_in', 'API Requests are only supported for authenticated requests.', array( 'status' => 401, ) );
	}

	return $result;
}
add_filter( 'rest_authentication_errors', 'Physics_Collection_LG\logged_in_only_rest_api' );

?>