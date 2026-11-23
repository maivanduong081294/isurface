<?php
/**
 * Product siblings: versions of one product sold as separate products
 * (Core i5 8GB - 128GB, Core i7 16GB - 512GB...).
 *
 * - Taxonomy "product_sibling" (Products > Product siblings). A product
 *   belongs to one group at most: the Edit product screen has a box with a
 *   dropdown (or a new group name) instead of the tags box, and saving keeps
 *   one term.
 * - Optional "Label in the group" (meta _silver_sibling_label), shown on the
 *   cards instead of the product name.
 * - Product page: the products of the group as cards (label, price), from the
 *   lowest price to the highest, the current one marked. Part of Product
 *   Page > Product info elements ("siblings").
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_SIBLING_TAXONOMY = 'product_sibling';
const SILVER_SIBLING_LABEL    = '_silver_sibling_label';

/** Registers the taxonomy. */
function silver_product_siblings_register() {
	register_taxonomy(
		SILVER_SIBLING_TAXONOMY,
		array( 'product' ),
		array(
			'labels'             => array(
				'name'          => __( 'Product siblings', 'silver' ),
				'singular_name' => __( 'Product sibling group', 'silver' ),
				'menu_name'     => __( 'Product siblings', 'silver' ),
				'all_items'     => __( 'All groups', 'silver' ),
				'edit_item'     => __( 'Edit group', 'silver' ),
				'add_new_item'  => __( 'Add new group', 'silver' ),
				'new_item_name' => __( 'New group name', 'silver' ),
				'search_items'  => __( 'Search groups', 'silver' ),
				'not_found'     => __( 'No groups found.', 'silver' ),
			),
			'description'        => __( 'Versions of one product sold as separate products. A product belongs to one group.', 'silver' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => false,
			'show_admin_column'  => true,
			'show_in_rest'       => false,
			'hierarchical'       => false,
			'rewrite'            => false,
			'query_var'          => false,
			'meta_box_cb'        => 'silver_product_siblings_meta_box',
			'capabilities'       => array(
				'manage_terms' => 'manage_product_terms',
				'edit_terms'   => 'edit_product_terms',
				'delete_terms' => 'delete_product_terms',
				'assign_terms' => 'assign_product_terms',
			),
		)
	);
}
add_action( 'init', 'silver_product_siblings_register', 20 );

/**
 * The group of a product (the first one, if several were set elsewhere).
 *
 * @param int $product_id Product ID.
 *
 * @return WP_Term|null
 */
function silver_product_sibling_group( $product_id ) {
	$terms = get_the_terms( $product_id, SILVER_SIBLING_TAXONOMY );

	return is_array( $terms ) && $terms ? reset( $terms ) : null;
}

/*
 * Admin.
 */

/**
 * Box of the Edit product screen: one group, or a new one, and the label.
 *
 * @param WP_Post $post Product.
 */
function silver_product_siblings_meta_box( $post ) {
	$current = silver_product_sibling_group( $post->ID );
	$terms   = get_terms( array(
		'taxonomy'   => SILVER_SIBLING_TAXONOMY,
		'hide_empty' => false,
		'orderby'    => 'name',
	) );
	$label   = get_post_meta( $post->ID, SILVER_SIBLING_LABEL, true );

	wp_nonce_field( 'silver_product_sibling', 'silver_product_sibling_nonce' );
	?>
	<p>
		<label for="silver-sibling-group" class="screen-reader-text"><?php esc_html_e( 'Group', 'silver' ); ?></label>
		<select name="silver_sibling_group" id="silver-sibling-group" class="widefat">
			<option value=""><?php esc_html_e( '— No group —', 'silver' ); ?></option>
			<?php foreach ( is_array( $terms ) ? $terms : array() as $term ) : ?>
				<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $current ? $current->term_id : 0, $term->term_id ); ?>><?php echo esc_html( sprintf( '%s (%d)', $term->name, $term->count ) ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="silver-sibling-new"><?php esc_html_e( 'Or a new group', 'silver' ); ?></label>
		<input type="text" name="silver_sibling_new" id="silver-sibling-new" class="widefat" value="" placeholder="<?php esc_attr_e( 'E.g. Surface Pro 4', 'silver' ); ?>" />
	</p>
	<p>
		<label for="silver-sibling-label"><?php esc_html_e( 'Label in the group', 'silver' ); ?></label>
		<input type="text" name="silver_sibling_label" id="silver-sibling-label" class="widefat" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'E.g. Core i5 8GB - 128GB (Like New)', 'silver' ); ?>" />
	</p>
	<p class="description"><?php esc_html_e( 'A product belongs to one group. The product page shows the products of its group, from the lowest price, with this label (else the product name).', 'silver' ); ?></p>
	<?php
}

/**
 * Saves the group (one term) and the label.
 *
 * @param int $post_id Product ID.
 */
function silver_product_siblings_save( $post_id ) {
	if ( ! isset( $_POST['silver_product_sibling_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_product_sibling_nonce'] ) ), 'silver_product_sibling' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$term_id = isset( $_POST['silver_sibling_group'] ) ? absint( $_POST['silver_sibling_group'] ) : 0;
	$new     = isset( $_POST['silver_sibling_new'] ) ? sanitize_text_field( wp_unslash( $_POST['silver_sibling_new'] ) ) : '';

	// A new group name wins over the dropdown (an existing name is reused).
	if ( '' !== $new && current_user_can( 'manage_product_terms' ) ) {
		$existing = term_exists( $new, SILVER_SIBLING_TAXONOMY );
		if ( $existing ) {
			$term_id = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
		} else {
			$created = wp_insert_term( $new, SILVER_SIBLING_TAXONOMY );
			if ( ! is_wp_error( $created ) ) {
				$term_id = (int) $created['term_id'];
			}
		}
	}

	if ( $term_id && ! get_term( $term_id, SILVER_SIBLING_TAXONOMY ) instanceof WP_Term ) {
		$term_id = 0;
	}

	// One group at most: replaces the terms of the product.
	wp_set_object_terms( $post_id, $term_id ? array( $term_id ) : array(), SILVER_SIBLING_TAXONOMY, false );

	$label = isset( $_POST['silver_sibling_label'] ) ? sanitize_text_field( wp_unslash( $_POST['silver_sibling_label'] ) ) : '';
	if ( '' === $label ) {
		delete_post_meta( $post_id, SILVER_SIBLING_LABEL );
	} else {
		update_post_meta( $post_id, SILVER_SIBLING_LABEL, $label );
	}
}
add_action( 'save_post_product', 'silver_product_siblings_save' );

/*
 * Front end.
 */

/**
 * Products of the group of a product, from the lowest price to the highest
 * (lowest price of variable products), published and visible in the catalog.
 *
 * @param WC_Product $product Product.
 *
 * @return WC_Product[] Empty when the product has no group or is alone in it.
 */
function silver_product_siblings( $product ) {
	$group = $product instanceof WC_Product ? silver_product_sibling_group( $product->get_id() ) : null;
	if ( ! $group ) {
		return array();
	}

	$ids = get_posts( array(
		'post_type'        => 'product',
		'post_status'      => 'publish',
		'posts_per_page'   => (int) apply_filters( 'silver_product_siblings_limit', 24 ),
		'fields'           => 'ids',
		'no_found_rows'    => true,
		'suppress_filters' => false,
		'tax_query'        => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => SILVER_SIBLING_TAXONOMY,
				'field'    => 'term_id',
				'terms'    => $group->term_id,
			),
		),
	) );

	$products = array();
	foreach ( $ids as $id ) {
		$sibling = wc_get_product( $id );
		if ( $sibling && ( $sibling->get_id() === $product->get_id() || $sibling->is_visible() ) ) {
			$products[] = $sibling;
		}
	}

	if ( count( $products ) < 2 ) {
		return array();
	}

	// Lowest price first; products without a price last.
	$price = function ( $item ) {
		return '' === (string) $item->get_price() ? INF : (float) $item->get_price();
	};
	usort( $products, function ( $a, $b ) use ( $price ) {
		$pa = $price( $a );
		$pb = $price( $b );
		return $pa === $pb ? strcmp( $a->get_name(), $b->get_name() ) : ( $pa < $pb ? -1 : 1 );
	} );

	return (array) apply_filters( 'silver_product_siblings', $products, $product, $group );
}

/**
 * Prints the cards of the group (woocommerce_single_product_summary).
 */
function silver_product_siblings_html() {
	if ( ! is_product() ) {
		return;
	}

	$product  = wc_get_product( get_the_ID() );
	$siblings = silver_product_siblings( $product );
	if ( ! $siblings ) {
		return;
	}

	$group = silver_product_sibling_group( $product->get_id() );
	$cards = '';

	foreach ( $siblings as $sibling ) {
		$current = $sibling->get_id() === $product->get_id();
		$label   = get_post_meta( $sibling->get_id(), SILVER_SIBLING_LABEL, true );
		$label   = '' !== $label ? $label : $sibling->get_name();
		$price   = function_exists( 'silver_product_buy_box_price_html' ) ? silver_product_buy_box_price_html( $sibling ) : $sibling->get_price_html();
		$classes = array( 'product-sibling' );

		if ( $current ) {
			$classes[] = 'is-current';
		}
		if ( ! $sibling->is_in_stock() ) {
			$classes[] = 'is-out-of-stock';
		}

		$cards .= sprintf(
			'<a class="%1$s" href="%2$s"%3$s><span class="product-sibling__label">%4$s</span><span class="price product-sibling__price">%5$s</span>%6$s</a>',
			esc_attr( implode( ' ', $classes ) ),
			esc_url( $sibling->get_permalink() ),
			$current ? ' aria-current="page"' : '',
			esc_html( $label ),
			$price, // WooCommerce price HTML.
			$current ? '<span class="product-sibling__check" aria-hidden="true"></span>' : ''
		);
	}

	printf(
		'<nav class="product-siblings" aria-label="%1$s"><div class="product-siblings__list">%2$s</div></nav>',
		esc_attr( $group ? $group->name : __( 'Product siblings', 'silver' ) ),
		$cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	);
}
// Between the short description (20) and the add to cart (30).
add_action( 'woocommerce_single_product_summary', 'silver_product_siblings_html', 22 );
