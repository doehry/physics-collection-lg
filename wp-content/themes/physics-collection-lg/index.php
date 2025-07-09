<?php
/**
 * The template for displaying and filtering posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage physics-collection-lg
 * @since physics-collection-lg 0.1
 */

get_header();
?>
<header class="page-header alignwide">
	<h1 class="page-title">Beiträge</h1>
</header><!-- .page-header -->
<div class="entry-content">
<?php
$pods = pods( 'post' );

$categories = get_taxonomy( 'category' );
$tags = get_taxonomy( 'post_tag' );

$filter_cat = sanitize_text_field( pods_v('filter_cat') );
$filter_tag = sanitize_text_field( pods_v('filter_tag') );
$filter_search = sanitize_text_field( pods_v('filter_s') );
$order_by = sanitize_text_field( pods_v('order_by') );

?>
<form class="filter_form">
	<?php echo get_select_form( 'filter_cat', $categories, $filter_cat );  ?>
	<?php echo get_select_form( 'filter_tag', $tags, $filter_tag );  ?>
	<input type="text" value="<?php echo $filter_search; ?>" name="filter_s" id="filter_s" placeholder="Suchen">
	<input type="submit" value="Filtern">
</form>

<?php
$options_order_by[ 'title' ] = [ "value" => "title", "option" => "Bezeichnung", "sql" => "post_title ASC" ];
$options_order_by[ 'newest' ] = [ "value" => "newest", "option" => "neueste zuerst", "sql" => "post_date DESC" ];
$options_order_by[ 'oldest' ] = [ "value" => "oldest", "option" => "älteste zuerst", "sql" => "post_date ASC" ];
$options_order_by[ 'category' ] = [ "value" => "category", "option" => "Kategorie", "sql" => "category ASC" ];
?>

<form class="order_by_form">
	<label for="order_by">Sortieren nach</label>
	<?php echo get_select_form( "order_by", $options_order_by, $order_by ); ?>
	<noscript><input type="submit" value="Sortieren"/></noscript>
</form>
<?php

$params['limit'] = get_option( 'posts_per_page' );
$params['orderby'] = ( empty( $order_by ) ? 'post_title ASC' : $options_order_by[ $order_by][ 'sql' ] );

if ( !empty( $filter_cat ) ) {
	$params['where'] = "category.slug = '" . $filter_cat . "'";
}
if (!empty( $filter_search ) ) {
	$params['where'] = "post_title LIKE '%" . $filter_search ."%' "
		. "OR post_content LIKE '%" . $filter_search ."%' "
		. "OR manufacturer_number.meta_value LIKE '%" . $filter_search . "%'";
}

$pods->find( $params );

if ( $pods->total() > 0 ) :
	while ( $pods->fetch() ) :
		echo $pods->template( 'Post List Template' );
	endwhile;

	echo $pods->pagination( array( 'type' => 'paginate' ) );
else :
	echo "<p>keine Objekte gefunden</p>";
endif;
?>
</div><!-- .entry-content -->

<?php
get_footer();
?>
