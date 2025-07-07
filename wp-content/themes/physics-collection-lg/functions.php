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
?>