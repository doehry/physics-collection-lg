<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_style', 20 );

function theme_enqueue_style() {
	wp_enqueue_style( 
		'physics-collection-lg-style', 
		get_stylesheet_uri()
	);
}

add_action('admin_enqueue_scripts', 'my_admin_theme_style');

function my_admin_theme_style() {
    wp_enqueue_style(
		'my-admin-style', 
		get_stylesheet_directory_uri() . '/style-admin.css'
	);
}

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