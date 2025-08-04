<?php
/** 
 * Load style.css
 */
add_action( 'wp_enqueue_scripts', 
	function () {
		wp_enqueue_style( 'physics-collection-lg-style', get_stylesheet_uri() );
	}, 20 
);

/** 
 * Add favicon 
 * created with https://realfavicongenerator.net/
 */
add_action( 'wp_head' , 
	function() { 
		echo '<link rel="icon" type="image/png" href="' . get_stylesheet_directory_uri() . '/assets/favicon/favicon-96x96.png" sizes="96x96" >';
		echo '<link rel="icon" type="image/svg+xml" href="' . get_stylesheet_directory_uri() . '/assets/favicon/favicon.svg" >';
		echo '<link rel="shortcut icon" href="' . get_stylesheet_directory_uri() . '/assets/favicon/favicon.ico" >';
		echo '<link rel="apple-touch-icon" sizes="180x180" href="' . get_stylesheet_directory_uri() . '/assets/favicon/apple-touch-icon.png" >';
		echo '<meta name="apple-mobile-web-app-title" content="Physik" >';
		echo '<link rel="manifest" href="' . get_stylesheet_directory_uri() . '/assets/favicon/site.webmanifest" >';
	}
);

/** 
 * Replace logo on login page
 */
add_action( 'login_head', 
	function () {
		$logo_url = get_stylesheet_directory_uri() .
			'/assets/images/Logo_LG_Physiksammlung.svg';
		echo '<style type="text/css">
			#login h1 a {
				background-image: url(' .$logo_url .');
				height: 100px;
				width: 300px;
				background-size: 300px 100px;
				background-repeat: no-repeat;
			}
		</style>';
	}
);

/** 
 * Date format function for use in pods magic tags
 */
function my_date( $input_date ) {
	return date( get_option( 'date_format' ), strtotime( $input_date ) );
}

/**
 * Return select from taxonomy or options array
 */
function get_select_form( $name, $taxonomy_or_options, $selected='', $submit_on_change=true ) {
	if ( is_a( $taxonomy_or_options, 'WP_Taxonomy' ) ) {
		$options = [];
		$taxonomy = $taxonomy_or_options->name;
		$label = $taxonomy_or_options->label;
		$taxonomy_list = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
		$options[ '' ] = [ 'item' => '-- ' . $label . ' --' ];

		foreach ( $taxonomy_list as $taxonomy_item ) {
			$options[ $taxonomy_item->slug ] = [ 'item' => $taxonomy_item->name ];
		}
	} else {
		$options = $taxonomy_or_options;
	}

	$form_output = '<select name="' . $name . '" id="' . $name . '" ' .
		( $submit_on_change ? 'onchange="this.form.submit()"' : '') . ' >';

	foreach ( $options as $value=>$option ) {
		$form_output .= '<option value="' . $value  . '" ' .
			( $value == $selected  ? 'selected' : '' ) . ' ">' .
			$option[ 'item' ] . '</option>';
	}

	$form_output .= '</select>';

	return $form_output;
}

?>