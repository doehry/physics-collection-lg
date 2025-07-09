<?php
/**
 * The template for displaying object archive pages
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
	<h1 class="page-title">Objekte</h1>
</header><!-- .page-header -->
<div class="entry-content">
<?php
$pods = pods( 'object' );

$categories = get_terms(array('taxonomy' => 'category', 'hide_empty' => false));
$locations = get_terms(array('taxonomy' => 'location', 'hide_empty' => false));
$manufacturers = get_terms(array('taxonomy' => 'manufacturer', 'hide_empty' => false));

$filter_cat = sanitize_text_field( pods_v('filter_cat') );
$filter_loc = sanitize_text_field( pods_v('filter_loc') );
$filter_man = sanitize_text_field( pods_v('filter_man') );
$filter_search = sanitize_text_field( pods_v('filter_s') );
$order_by = sanitize_text_field( pods_v('order_by') );


$taxonomy = reset($manufacturers)->taxonomy;
print_r(get_taxonomy($taxonomy));
$out = get_taxonomy($taxonomy);
echo $out->label;
?>
<form class="filter_form">
	<select name="filter_cat" id="filter_cat" onchange="this.form.submit()">
		<option value="">-- Kategorien --</option>
		<?php foreach( $categories as $category ) { ?>
			<option value="<?php echo $category->slug; ?>" <?php echo ( $category->slug == $filter_cat ) ? "selected" : ""; ?> ><?php echo $category->name; ?></option>
		<?php } ?>
	</select>
	<select name="filter_loc" id="filter_loc" onchange="this.form.submit()">
		<option value="">-- Lagerort --</option>
		<?php foreach( $locations as $location ){ ?>
			<option value="<?php echo $location->slug; ?>" <?php echo ( $location->slug == $filter_loc ) ? "selected" : ""; ?> ><?php echo $location->name; ?></option>
		<?php } ?>
	</select>
	<select name="filter_man" id="filter_man" onchange="this.form.submit()">
		<option value="">-- Hersteller --</option>
		<?php foreach( $manufacturers as $manufacturer ){ ?>
			<option value="<?php echo $manufacturer->slug; ?>" <?php echo ( $manufacturer->slug == $filter_man ) ? "selected" : ""; ?> ><?php echo $manufacturer->name; ?></option>
		<?php } ?>
	</select>
	<input type="text" value="<?php echo $filter_search; ?>" name="filter_s" id="filter_s" placeholder="Bezeichnung / Nummer">
	<input type="submit" value="Filtern">
</form>

<form class="order_by_form">
	<label for="order_by">Sortieren nach</label>
    <select name="order_by" id="order_by" onchange="this.form.submit()">
		<option value="post_title" <?php echo ( 'post_title'== $order_by ) ? "selected" : ""; ?> >Bezeichnung</option>
		<option value="inventory_number" <?php echo ( 'inventory_number' == $order_by ) ? "selected" : ""; ?> >Inventarnummer</option>
		<option value="related_location" <?php echo ('related_location' == $order_by ) ? "selected" : ""; ?> >Lagerort</option>	
    </select>
	<noscript><input type="submit" value="Sortieren"/></noscript>
</form>
<?php

$params['limit'] = get_option( 'posts_per_page' );
$params['orderby'] = ( empty( $order_by ) ? 'post_title' : $order_by ) . ' ASC';

if ( !empty( $filter_cat ) ) {
	$params['where'] = "category.slug = '" . $filter_cat . "'";
}
if ( !empty( $filter_loc ) ) {
	$params['where'] = "location.slug = '" . $filter_loc . "'";
}
if ( !empty( $filter_man ) ) {
	$params['where'] = "manufacturer.slug = '" . $filter_man . "'";
}
if (!empty( $filter_search ) ) {
	$params['where'] = "post_title LIKE '%" . $filter_search . "%' OR inventory_number.meta_value LIKE '%" . $filter_search . "%' OR manufacturer_number.meta_value LIKE '%" . $filter_search . "%'";
}

$pods->find( $params );

if ( $pods->total() > 0 ) :
	while ( $pods->fetch() ) :
		echo $pods->template( 'Object Archive List Template' );
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
