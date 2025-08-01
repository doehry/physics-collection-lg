<?php
/**
 * Displays header site branding
 *
 * @package WordPress
 * @since physics-collection-lg 1.0
 */

$blog_info    = get_bloginfo( 'name' );
$description  = get_bloginfo( 'description', 'display' );
$show_title   = ( true === get_theme_mod( 'display_title_and_tagline', true ) );
$header_class = $show_title ? 'site-title' : 'screen-reader-text';

?>

<div class="site-branding">
	<div class="site-logo">
		<a href="<?php echo home_url(); ?>">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Logo_LG_Physiksammlung.svg" alt="Physiksammlung" class="custom-logo">
		</a>
	</div>
</div><!-- .site-branding -->
