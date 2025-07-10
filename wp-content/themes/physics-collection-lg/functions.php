<?php
// load style.css
function theme_enqueue_style() {
	wp_enqueue_style( 
		'physics-collection-lg-style', 
		get_stylesheet_uri()
	);
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_style', 20 );

// load style-admin.css
function my_admin_theme_style() {
    wp_enqueue_style(
		'my-admin-style', 
		get_stylesheet_directory_uri() . '/style-admin.css'
	);
}
add_action('admin_enqueue_scripts', 'my_admin_theme_style');

// Add custom columns to the object post type
function set_custom_edit_object_columns( $columns ) {
    $pod = pods( 'object' );
    unset( $columns['date'] );
    $reordered = array();
    foreach ( $columns as $key => $value ) {
        if ($key == 'title') {
            $reordered[ $key ] = $value;
            $reordered['inventory_number'] = $pod->fields( 'inventory_number', 'label' );
            $reordered['location'] = $pod->fields( 'related_location', 'label' );
        } else {
            $reordered[ $key ] = $value;
        }
    }
    return $reordered;
}
add_filter( 'manage_object_posts_columns', 'set_custom_edit_object_columns' );

// Add data to the custom columns for the objekt post type:
function custom_object_column( $column, $post_id ) {
    $objects = pods( 'object', $post_id );
    switch ( $column ) {
        case 'inventory_number' :
            echo $objects->field( 'inventory_number' );
            break;
        case 'location' :
            echo $objects->field( 'related_location.name' );
            break;
    }
}
add_action( 'manage_object_posts_custom_column' , 'custom_object_column', 10, 2 );

// Make it sortable
function object_sortable_columns( $columns ) {
	$columns[ 'inventory_number' ] = 'inventory_number';
    $columns[ 'location' ] = 'location';
	return $columns;
}
add_filter( 'manage_edit-object_sortable_columns', 'object_sortable_columns' );

function object_slice_orderby( $query ) {
    $orderby = $query->get( 'orderby' );
    switch ( $orderby ) {
        case 'inventory_number' :
            $query->set( 'meta_key','inventory_number' );
            $query->set( 'orderby','meta_value_num' );
        case 'location' :
            $query->set( 'meta_key','related_location' );
            $query->set( 'orderby','meta_value' );
    }
}
add_action( 'pre_get_posts', 'object_slice_orderby' );

// Add auto increment inventory number
function get_next_inventory_number() {
    $post_type = pods_v( 'post_type' );
    if ( isset( $post_type ) && $post_type == 'object' ) {
        $current_inventory_base = date('y')*1000;
        $pod = pods( 'object' );
        $params = array(
            'select' => 'MAX(cast(inventory_number.meta_value as unsigned)) as max_inventory_number',
            'where' => 'inventory_number.meta_value > ' . $current_inventory_base,
            'limit' => '1',
        );
        $pod->find( $params )->fetch();
        $inventory_number = $pod->field( 'max_inventory_number' );
        $_POST['inventory_number'] = empty( $inventory_number ) ? $current_inventory_base + 1 : $inventory_number + 1 ;
    }
}
add_action( 'wp_insert_post', 'get_next_inventory_number' );

// date format function for use in pods magic tags
function my_date($input_date) {
	return date( "d.m.Y", strtotime( $input_date ) );  
}

// return select tag from taxonomy or options array
function get_select_form( $name, $taxonomy_or_options, $selected='', $submit_on_change=true ) {
	if ( is_a( $taxonomy_or_options, 'WP_Taxonomy' ) ) {
		$options = [];
		$taxonomy = $taxonomy_or_options->name;
		$label = $taxonomy_or_options->label;
		$taxonomy_list = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
		$options[] = [ "value" => "", "option" => "-- " . $label . " --" ];

		foreach ( $taxonomy_list as $taxonomy_item ) {
			$options[] = [ "value" => $taxonomy_item->slug, "option" => $taxonomy_item->name ];
		}
	} else {
		$options = $taxonomy_or_options;
	}
	$form_output = '<select name="' . $name . '" id="' . $name . '" ' 
		. ( $submit_on_change ? 'onchange="this.form.submit()"' : '') . ' >';
	foreach ( $options as $option ) {
		$form_output .= '<option value="' . $option[ 'value' ]  . '" ' 
			. ( $option[ 'value' ] == $selected  ? 'selected' : '' ) . ' ">';
		$form_output .= $option[ 'option' ] . '</option>';
	}
	$form_output .= '</select>';
	return $form_output;
}

?>