<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

get_header();

$description = get_the_archive_description();
?>

<?php if ( have_posts() ) : ?>

	<header class="page-header alignwide">
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php if ( $description ) : ?>
			<div class="archive-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>
	</header><!-- .page-header -->

	<?php while ( have_posts() ) :
		the_post();
		
		echo '<div class="entry-content">';
		
		switch ( get_post_type() ) {
			case 'post':
				echo pods( 'post', $post->ID )->template( 'Post List Template' );
				break;
			case 'object':
				echo pods( 'object', $post->ID )->template( 'Object List Template' );
				break;
		}
		echo '</div><!-- .entry-content -->';
	
	endwhile;

	twenty_twenty_one_the_posts_navigation();
	?>

<?php else : ?>
	<?php get_template_part( 'template-parts/content/content-none' ); ?>
<?php endif; ?>

<?php
get_footer();
