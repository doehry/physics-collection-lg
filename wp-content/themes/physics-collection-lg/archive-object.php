<?php
/**
 * The template for displaying object archive pages
 *
 * @package WordPress
 * @subpackage physics-collection-lg
 * @since physics-collection-lg 0.1
 */

get_header();
?>

<header class="page-header alignwide">
	<h1 class="page-title">Objekte</h1>
</header><!-- .page-header -->
<div class="entry-content">
<?php
$pods = pods( 'object' );

$categories = get_taxonomy( 'category' );
$locations = get_taxonomy( 'location' );
$manufacturers = get_taxonomy( 'manufacturer' );

$filter_cat = sanitize_text_field( pods_v( 'filter_cat' ) );
$filter_loc = sanitize_text_field( pods_v( 'filter_loc' ) );
$filter_man = sanitize_text_field( pods_v( 'filter_man' ) );
$filter_search = sanitize_text_field( pods_v( 'filter_s' ) );
$order_by = sanitize_text_field( pods_v( 'order_by' ) );
?>
<form class="filter_form">
	<?php echo get_select_form( 'filter_cat', $categories, $filter_cat );  ?>
	<?php echo get_select_form( 'filter_loc', $locations, $filter_loc ); ?>
	<?php echo get_select_form( 'filter_man', $manufacturers, $filter_man ); ?>
	<input type="text" value="<?php echo $filter_search; ?>" name="filter_s" id="filter_s" placeholder="Bezeichnung/Nummer">
	<input type="hidden" value="<?php echo $order_by; ?> " name="order_by">
	<input type="submit" value="Filtern">
</form>

<?php
$options_order_by = [
	'title' => [ "item" => "Bezeichnung", "sql" => "post_title ASC" ],
	'newest' => [ "item" => "neueste zuerst", "sql" => "inventory_number DESC" ],
	'oldest' => [ "item" => "älteste zuerst", "sql" => "inventory_number ASC" ],
	'location' => [ "item" => "Lagerort", "sql" => "location ASC" ],
];
?>

<form class="order_by_form">
	<label for="order_by">Sortieren nach</label>
	<?php echo get_select_form( 'order_by', $options_order_by, $order_by ); ?>
	<input type="hidden" value="<?php echo $filter_cat; ?> " name="filter_cat">
	<input type="hidden" value="<?php echo $filter_loc; ?> " name="filter_loc">
	<input type="hidden" value="<?php echo $filter_man; ?> " name="filter_man">
	<input type="hidden" value="<?php echo $filter_search; ?> " name="filter_s">
	<noscript><input type="submit" value="Sortieren"/></noscript>
</form>

<?php
$params['limit'] = get_option( 'posts_per_page' );
$params['orderby'] = ( empty( $order_by ) ? 'post_title ASC' : $options_order_by[ $order_by ]['sql'] );

if ( ! empty( $filter_cat ) ) {
	$params['where'] = "category.slug = '" . $filter_cat . "'";
}
if ( ! empty( $filter_loc ) ) {
	$params['where'] = "location.slug = '" . $filter_loc . "'";
}
if ( ! empty( $filter_man ) ) {
	$params['where'] = "manufacturer.slug = '" . $filter_man . "'";
}
if ( ! empty( $filter_search ) ) {
	$params['where'] = "post_title LIKE '%" . $filter_search ."%' "
		. "OR inventory_number.meta_value LIKE '%" . $filter_search ."%' "
		. "OR manufacturer_number.meta_value LIKE '%" . $filter_search . "%'";
}

$pods->find( $params );

if ( $pods->total() > 0 ) :
	while ( $pods->fetch() ) :
		echo $pods->template( 'Object List Template' );
	endwhile;

	echo $pods->pagination( [ 'type' => 'paginate' ] );
else :
	echo '<p>Es wurden keine Objekte gefunden.</p>';
endif;
?>
</div><!-- .entry-content -->

<?php
get_footer();
?>
