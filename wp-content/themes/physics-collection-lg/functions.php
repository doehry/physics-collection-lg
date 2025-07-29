<?php
/** 
 * load style.css
 */
add_action( 'wp_enqueue_scripts', 
	function () {
		wp_enqueue_style( 
			'physics-collection-lg-style', 
			get_stylesheet_uri()
		);
	}, 20 
);

/** 
 * date format function for use in pods magic tags
 */
function my_date($input_date) {
	return date( "d.m.Y", strtotime( $input_date ) );  
}

/**
 * return select from taxonomy or options array
 */
function get_select_form( $name, $taxonomy_or_options, $selected='', $submit_on_change=true ) {
	if ( is_a( $taxonomy_or_options, 'WP_Taxonomy' ) ) {
		$options = [];
		$taxonomy = $taxonomy_or_options->name;
		$label = $taxonomy_or_options->label;
		$taxonomy_list = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
		$options[] = [ "option" => "-- " . $label . " --" ];

		foreach ( $taxonomy_list as $taxonomy_item ) {
			$options[ $taxonomy_item->slug ] = [ "option" => $taxonomy_item->name ];
		}
	} else {
		$options = $taxonomy_or_options;
	}
	$form_output = '<select name="' . $name . '" id="' . $name . '" ' 
		. ( $submit_on_change ? 'onchange="this.form.submit()"' : '') . ' >';
	foreach ( $options as $value=>$option ) {
		$form_output .= '<option value="' . $value  . '" ' 
			. ( $value == $selected  ? 'selected' : '' ) . ' ">';
		$form_output .= $option[ 'option' ] . '</option>';
	}
	$form_output .= '</select>';
	return $form_output;
}

?>