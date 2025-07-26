<?php
/**
 * The template for displaying all single objects
 *
 * @package WordPress
 * @subpackage physics-collection-lg
 * @since physics-collection-lg 0.9
 */

get_header();

the_post();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >

	<header class="entry-header alignwide">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<?php twenty_twenty_one_post_thumbnail(); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer default-max-width">
		<div class="posted-by"></div>
		<?php if ( has_category() ) { ?>

			<div class="post-taxonomies">
			<?php
				$categories_list = get_the_category_list( wp_get_list_item_separator() );
				if ( $categories_list ) {
					printf(
						/* translators: %s: List of categories. */
						'<span class="cat-links">' . esc_html__( 'Categorized as %s', 'twentytwentyone' ) . ' </span>',
						$categories_list // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}
			?>
			</div>
		<?php } ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->

<?php
get_footer();
