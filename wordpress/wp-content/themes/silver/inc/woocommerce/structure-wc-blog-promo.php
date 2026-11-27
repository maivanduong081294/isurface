<?php
/**
 * Related products section under the posts of the blog page and post
 * categories: a section of its own after the section of the posts
 * (template-parts/posts/layout*.php, silver_after_blog), with an H2 ([title]
 * shortcode) and the products ([ux_products]).
 *
 * Products, in this order until the number is reached:
 * 1. the products chosen by hand on the post category;
 * 2. the product category linked to the post category: on sale first, then
 *    best sellers ("View all" links to it);
 * 3. (1 and 2 come from the closest category up the tree that sets them:
 *    subcategories inherit from their parent);
 * 4. the fallback of Customizer > Blog > Blog Related Products (featured,
 *    on sale, best sellers or latest products of the whole shop).
 * Always: published, visible in the catalog, in stock, with a price; no
 * duplicates. The IDs are cached (transient) until a product or a category
 * changes.
 *
 * Heading: the one of the post category (inherited too), else the one of the
 * Customizer; %s = the name of the category / blog page.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_BLOG_PROMO_HEADING  = 'silver_promo_heading';
const SILVER_BLOG_PROMO_CAT      = 'silver_promo_product_cat';
const SILVER_BLOG_PROMO_PRODUCTS = 'silver_promo_products';
const SILVER_BLOG_PROMO_VERSION  = 'silver_blog_promo_version';

/* ------------------------------------------------------------------ Data */

/**
 * The value of a term meta on the term or, when empty, on the closest
 * ancestor that sets it.
 *
 * @param WP_Term|null $term Post category.
 * @param string       $key  Term meta key.
 *
 * @return array array( 'value' => mixed, 'term_id' => int ) (term_id 0: none).
 */
function silver_blog_promo_inherited( $term, $key ) {
	if ( ! $term instanceof WP_Term ) {
		return array( 'value' => '', 'term_id' => 0 );
	}

	$chain = array_merge( array( (int) $term->term_id ), array_map( 'intval', get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) ) );

	foreach ( $chain as $term_id ) {
		$value = get_term_meta( $term_id, $key, true );
		if ( ! empty( $value ) ) {
			return array( 'value' => $value, 'term_id' => $term_id );
		}
	}

	return array( 'value' => '', 'term_id' => 0 );
}

/**
 * Settings of the section for a post category (or the blog page: null).
 *
 * @param WP_Term|null $term Post category.
 *
 * @return array heading (string), product_cat (int), products (int[]).
 */
function silver_blog_promo_config( $term = null ) {
	$heading  = silver_blog_promo_inherited( $term, SILVER_BLOG_PROMO_HEADING );
	$cat      = silver_blog_promo_inherited( $term, SILVER_BLOG_PROMO_CAT );
	$products = silver_blog_promo_inherited( $term, SILVER_BLOG_PROMO_PRODUCTS );

	$product_cat = (int) $cat['value'];
	if ( $product_cat && ! term_exists( $product_cat, 'product_cat' ) ) {
		$product_cat = 0;
	}

	$config = array(
		'heading'     => '' !== (string) $heading['value'] ? (string) $heading['value'] : (string) get_theme_mod( 'blog_promo_title', __( 'Related products', 'silver' ) ),
		'product_cat' => $product_cat,
		'products'    => array_values( array_filter( array_map( 'absint', (array) $products['value'] ) ) ),
	);

	return apply_filters( 'silver_blog_promo_config', $config, $term );
}

/**
 * Number of products: Customizer, 0 = one row (Shop > Product Catalog >
 * Products per row on desktop).
 *
 * @return int
 */
function silver_blog_promo_number() {
	$number  = (int) get_theme_mod( 'blog_promo_number', 0 );
	$columns = silver_blog_promo_columns();

	return max( 1, min( 12, $number ? $number : (int) $columns['columns__lg'] ) );
}

/**
 * Products per row on each screen: Desktop >= 1200px, Tablet landscape
 * 850-1199px, Tablet portrait 550-849px, Mobile < 550px. Customizer > Blog >
 * Blog Related Products; 0 = the setting of Shop > Product Catalog > Products
 * per row (Tablet landscape 0 there = as Tablet portrait).
 *
 * @return array [ux_products] attributes columns (mobile), columns__md,
 *               columns__ml, columns__lg.
 */
function silver_blog_promo_columns() {
	$pick = static function ( $own, $shop, $shop_default ) {
		$value = (int) get_theme_mod( $own, 0 );

		return $value ? $value : (int) get_theme_mod( $shop, $shop_default );
	};

	$landscape = $pick( 'blog_promo_columns_tablet_landscape', 'category_row_count_tablet_landscape', 0 );

	return array(
		'columns'     => max( 1, $pick( 'blog_promo_columns_mobile', 'category_row_count_mobile', 2 ) ),
		'columns__md' => max( 1, $pick( 'blog_promo_columns_tablet', 'category_row_count_tablet', 3 ) ),
		'columns__ml' => $landscape ? $landscape : '',
		'columns__lg' => max( 1, $pick( 'blog_promo_columns', 'category_row_count', 3 ) ),
	);
}

/**
 * Whether a product can be promoted: published, visible in the catalog, in
 * stock and with a price.
 *
 * @param int $product_id Product ID.
 *
 * @return bool
 */
function silver_blog_promo_is_promotable( $product_id ) {
	$product = wc_get_product( $product_id );

	return $product
		&& 'publish' === $product->get_status()
		&& $product->is_visible()
		&& $product->is_in_stock()
		&& '' !== (string) $product->get_price();
}

/**
 * Product IDs for one source, visible in the catalog and in stock.
 *
 * @param array $source product_cat (int), post__in (int[]), featured (bool),
 *                      best_selling (bool).
 * @param int   $limit  Maximum.
 *
 * @return int[]
 */
function silver_blog_promo_query( $source, $limit ) {
	if ( isset( $source['post__in'] ) && ! $source['post__in'] ) {
		return array();
	}

	$visibility = wc_get_product_visibility_term_ids();
	$tax_query  = array( 'relation' => 'AND' );
	$hidden     = array_filter( array(
		isset( $visibility['exclude-from-catalog'] ) ? (int) $visibility['exclude-from-catalog'] : 0,
		isset( $visibility['outofstock'] ) ? (int) $visibility['outofstock'] : 0,
	) );

	if ( $hidden ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => $hidden,
			'operator' => 'NOT IN',
		);
	}

	if ( ! empty( $source['product_cat'] ) ) {
		$tax_query[] = array(
			'taxonomy'         => 'product_cat',
			'field'            => 'term_id',
			'terms'            => (int) $source['product_cat'],
			'include_children' => true,
		);
	}

	if ( ! empty( $source['featured'] ) ) {
		if ( empty( $visibility['featured'] ) ) {
			return array();
		}
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'term_taxonomy_id',
			'terms'    => array( (int) $visibility['featured'] ),
		);
	}

	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'fields'              => 'ids',
		'posts_per_page'      => $limit,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'tax_query'           => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	if ( ! empty( $source['post__in'] ) ) {
		$args['post__in'] = array_map( 'absint', $source['post__in'] );
	}

	if ( ! empty( $source['best_selling'] ) ) {
		$args['meta_key'] = 'total_sales'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$args['orderby']  = array(
			'meta_value_num' => 'DESC',
			'date'           => 'DESC',
		);
		unset( $args['order'] );
	}

	$query = new WP_Query( apply_filters( 'silver_blog_promo_query_args', $args, $source ) );

	return array_map( 'intval', $query->posts );
}

/**
 * The products of the section, see the rules at the top of the file.
 *
 * @param array $config See silver_blog_promo_config().
 * @param int   $number How many.
 *
 * @return int[]
 */
function silver_blog_promo_product_ids( $config, $number ) {
	$fallback = get_theme_mod( 'blog_promo_fallback', 'featured' );
	$key      = 'silver_blog_promo_' . md5( wp_json_encode( array(
		$config['products'],
		$config['product_cat'],
		$number,
		$fallback,
		(int) get_option( SILVER_BLOG_PROMO_VERSION, 0 ),
	) ) );

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$ids = array();
	$add = static function ( $candidates ) use ( &$ids, $number ) {
		foreach ( $candidates as $id ) {
			if ( count( $ids ) >= $number ) {
				return;
			}
			$id = (int) $id;
			if ( $id && ! in_array( $id, $ids, true ) && silver_blog_promo_is_promotable( $id ) ) {
				$ids[] = $id;
			}
		}
	};

	// 1. Chosen by hand.
	$add( $config['products'] );

	// 2. The linked product category: on sale, then best sellers.
	if ( count( $ids ) < $number && $config['product_cat'] ) {
		$on_sale = wc_get_product_ids_on_sale();
		$add( silver_blog_promo_query( array( 'product_cat' => $config['product_cat'], 'post__in' => $on_sale ), $number * 2 ) );
		if ( count( $ids ) < $number ) {
			$add( silver_blog_promo_query( array( 'product_cat' => $config['product_cat'], 'best_selling' => true ), $number * 2 ) );
		}
	}

	// 4. The whole shop.
	if ( count( $ids ) < $number ) {
		if ( 'featured' === $fallback ) {
			$add( silver_blog_promo_query( array( 'featured' => true ), $number * 2 ) );
		} elseif ( 'onsale' === $fallback ) {
			$add( silver_blog_promo_query( array( 'post__in' => wc_get_product_ids_on_sale() ), $number * 2 ) );
		} elseif ( 'best_selling' === $fallback ) {
			$add( silver_blog_promo_query( array( 'best_selling' => true ), $number * 2 ) );
		} elseif ( 'latest' === $fallback ) {
			// Newest first, from every product category.
			$add( silver_blog_promo_query( array(), $number * 2 ) );
		}
	}

	$ids = array_values( apply_filters( 'silver_blog_promo_product_ids', $ids, $config, $number ) );
	set_transient( $key, $ids, 6 * HOUR_IN_SECONDS );

	return $ids;
}

/**
 * New products, sales, stock or categories: the cached IDs are out of date.
 */
function silver_blog_promo_flush() {
	update_option( SILVER_BLOG_PROMO_VERSION, time(), false );
}
add_action( 'save_post_product', 'silver_blog_promo_flush' );
add_action( 'woocommerce_update_product', 'silver_blog_promo_flush' );
add_action( 'woocommerce_product_set_stock_status', 'silver_blog_promo_flush' );
add_action( 'woocommerce_variation_set_stock_status', 'silver_blog_promo_flush' );
add_action( 'woocommerce_scheduled_sales', 'silver_blog_promo_flush', 20 );
foreach ( array( 'category', 'product_cat' ) as $silver_blog_promo_tax ) {
	add_action( "created_{$silver_blog_promo_tax}", 'silver_blog_promo_flush' );
	add_action( "edited_{$silver_blog_promo_tax}", 'silver_blog_promo_flush' );
	add_action( "delete_{$silver_blog_promo_tax}", 'silver_blog_promo_flush' );
}
unset( $silver_blog_promo_tax );

/* --------------------------------------------------------------- Output */

/**
 * The section, after the section of the posts (silver_after_blog): blog page
 * and post categories, first page only unless the Customizer says otherwise.
 */
function silver_blog_promo_section() {
	if ( ! get_theme_mod( 'blog_promo', 1 ) || is_singular() ) {
		return;
	}
	if ( ! is_category() && ! ( is_home() && ! is_front_page() ) ) {
		return;
	}
	if ( get_theme_mod( 'blog_promo_first_page', 1 ) && is_paged() ) {
		return;
	}

	$term   = is_category() ? get_queried_object() : null;
	$term   = $term instanceof WP_Term ? $term : null;
	$config = silver_blog_promo_config( $term );
	$ids    = silver_blog_promo_product_ids( $config, silver_blog_promo_number() );

	if ( ! $ids ) {
		return;
	}

	$name    = $term ? $term->name : get_the_title( (int) get_option( 'page_for_posts' ) );
	$heading = trim( str_replace( '%s', $name, $config['heading'] ) );
	$link    = $config['product_cat'] ? get_term_link( $config['product_cat'], 'product_cat' ) : '';
	$link    = is_wp_error( $link ) ? '' : $link;

	$title = '' === $heading ? '' : silver_apply_shortcode( 'title', array(
		'text'      => esc_html( $heading ),
		'tag_name'  => 'h2',
		'style'     => get_theme_mod( 'blog_promo_title_style', 'simple' ),
		'link_text' => $link ? esc_html__( 'View all', 'silver' ) : '',
		'link'      => $link,
		'class'     => 'blog-promo-title',
	) );

	// The image ratio of the cards is the one of every product card
	// (Product Catalog > Product image ratio, silver_product_image_ratio()).
	$products = silver_apply_shortcode( 'ux_products', array_merge( array(
		'ids'      => implode( ',', $ids ),
		'products' => count( $ids ),
		'type'     => 'slider' === get_theme_mod( 'blog_promo_type', 'row' ) ? 'slider' : 'row',
		'class'    => 'blog-promo-products',
		// Round arrows, as the related products of the product page
		// (woocommerce/single-product/related.php), not the "reveal" bars.
		'slider_nav_style' => 'circle',
	), silver_blog_promo_columns() ) );

	// Customizer > Blog > Blog Related Products > background / text color.
	$vars    = array();
	$classes = array( 'blog-promo-section' );
	foreach ( array( 'bg' => 'blog_promo_bg', 'text' => 'blog_promo_text' ) as $var => $mod ) {
		$color = silver_css_var_value( get_theme_mod( $mod, '' ) );
		if ( '' !== $color ) {
			$vars[]    = '--sv-promo-' . $var . ':' . $color;
			$classes[] = 'has-promo-' . $var;
		}
	}
	$style = $vars ? ' style="' . esc_attr( implode( ';', $vars ) ) . '"' : '';

	echo '<section class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $style . '>' . $title . $products . '</section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output, escaped attributes.
}
add_action( 'silver_after_blog', 'silver_blog_promo_section', 5 );

/* ---------------------------------------------------- Admin: categories */

/**
 * The fields, on the Add category screen (div) or the Edit screen (tr).
 *
 * @param WP_Term|null $term Category being edited, null on the Add screen.
 */
function silver_blog_promo_term_fields( $term = null ) {
	$heading  = $term ? (string) get_term_meta( $term->term_id, SILVER_BLOG_PROMO_HEADING, true ) : '';
	$cat      = $term ? (int) get_term_meta( $term->term_id, SILVER_BLOG_PROMO_CAT, true ) : 0;
	$products = $term ? array_filter( array_map( 'absint', (array) get_term_meta( $term->term_id, SILVER_BLOG_PROMO_PRODUCTS, true ) ) ) : array();

	// What an empty field takes from the parents.
	$parent = $term && $term->parent ? get_term( $term->parent, 'category' ) : null;
	$parent = $parent instanceof WP_Term ? $parent : null;
	$from   = static function ( $key ) use ( $parent ) {
		$found = silver_blog_promo_inherited( $parent, $key );
		$owner = $found['term_id'] ? get_term( $found['term_id'], 'category' ) : null;

		return array( $found['value'], $owner instanceof WP_Term ? $owner->name : '' );
	};

	list( $parent_heading, $heading_owner ) = $from( SILVER_BLOG_PROMO_HEADING );
	list( $parent_cat, $cat_owner )         = $from( SILVER_BLOG_PROMO_CAT );
	list( $parent_products, $products_owner ) = $from( SILVER_BLOG_PROMO_PRODUCTS );
	$parent_products                          = array_filter( (array) $parent_products );

	$inherit = static function ( $owner, $value ) {
		return $owner
			/* translators: 1: Parent category, 2: Value. */
			? sprintf( __( 'Empty: inherits from "%1$s" (%2$s).', 'silver' ), $owner, $value )
			: __( 'Empty: uses the setting of Customizer > Blog > Blog Related Products.', 'silver' );
	};

	$parent_cat_term = $parent_cat ? get_term( (int) $parent_cat, 'product_cat' ) : null;

	$fields = array(
		array(
			'label' => __( 'Related products heading', 'silver' ),
			'html'  => '<input type="text" name="silver_promo_heading" id="silver-promo-heading" value="' . esc_attr( $heading ) . '" placeholder="' . esc_attr( $parent_heading ? $parent_heading : get_theme_mod( 'blog_promo_title', __( 'Related products', 'silver' ) ) ) . '" />',
			'help'  => __( 'H2 of the related products section under the posts. %s = the name of the category.', 'silver' ) . ' ' . $inherit( $heading_owner, $parent_heading ),
		),
		array(
			'label' => __( 'Related product category', 'silver' ),
			'html'  => wp_dropdown_categories( array(
				'taxonomy'          => 'product_cat',
				'name'              => 'silver_promo_product_cat',
				'id'                => 'silver-promo-product-cat',
				'show_option_none'  => __( '— Inherit —', 'silver' ),
				'option_none_value' => 0,
				'hide_empty'        => 0,
				'hierarchical'      => 1,
				'selected'          => $cat,
				'value_field'       => 'term_id',
				'echo'              => 0,
			) ),
			'help'  => __( 'On sale products first, then best sellers. "View all" links to this category.', 'silver' ) . ' ' . $inherit( $cat_owner, $parent_cat_term instanceof WP_Term ? $parent_cat_term->name : '' ),
		),
		array(
			'label' => __( 'Related products (hand-picked)', 'silver' ),
			'html'  => silver_blog_promo_products_select( $products ),
			'help'  => __( 'Shown first, in this order.', 'silver' ) . ' ' . $inherit( $products_owner, sprintf( /* translators: %d: Number of products. */ _n( '%d product', '%d products', count( $parent_products ), 'silver' ), count( $parent_products ) ) ),
		),
	);

	wp_nonce_field( 'silver_blog_promo_term', 'silver_blog_promo_term_nonce' );

	foreach ( $fields as $field ) {
		if ( $term ) {
			echo '<tr class="form-field"><th scope="row"><label>' . esc_html( $field['label'] ) . '</label></th><td>' . $field['html'] . '<p class="description">' . esc_html( $field['help'] ) . '</p></td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fields built above.
		} else {
			echo '<div class="form-field"><label>' . esc_html( $field['label'] ) . '</label>' . $field['html'] . '<p>' . esc_html( $field['help'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fields built above.
		}
	}
}

/**
 * Product search select (WooCommerce wc-enhanced-select).
 *
 * @param int[] $products Selected product IDs.
 *
 * @return string
 */
function silver_blog_promo_products_select( $products ) {
	$options = '';
	foreach ( $products as $product_id ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			$options .= '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
		}
	}

	return '<select class="wc-product-search" multiple="multiple" style="width:95%;max-width:560px" name="silver_promo_products[]" id="silver-promo-products" data-placeholder="' . esc_attr__( 'Search for a product…', 'silver' ) . '" data-action="woocommerce_json_search_products">' . $options . '</select>';
}

/** Add category screen. */
function silver_blog_promo_add_term_fields() {
	silver_blog_promo_term_fields();
}
add_action( 'category_add_form_fields', 'silver_blog_promo_add_term_fields', 20 );

/**
 * Edit category screen.
 *
 * @param WP_Term $term Category.
 */
function silver_blog_promo_edit_term_fields( $term ) {
	silver_blog_promo_term_fields( $term );
}
add_action( 'category_edit_form_fields', 'silver_blog_promo_edit_term_fields', 20 );

/**
 * Saves the fields.
 *
 * @param int $term_id Term ID.
 */
function silver_blog_promo_save_term( $term_id ) {
	if ( ! isset( $_POST['silver_blog_promo_term_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['silver_blog_promo_term_nonce'] ) ), 'silver_blog_promo_term' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$heading  = isset( $_POST['silver_promo_heading'] ) ? sanitize_text_field( wp_unslash( $_POST['silver_promo_heading'] ) ) : '';
	$cat      = isset( $_POST['silver_promo_product_cat'] ) ? absint( $_POST['silver_promo_product_cat'] ) : 0;
	$products = isset( $_POST['silver_promo_products'] ) ? array_values( array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['silver_promo_products'] ) ) ) ) ) : array();

	$values = array(
		SILVER_BLOG_PROMO_HEADING  => $heading,
		SILVER_BLOG_PROMO_CAT      => $cat && term_exists( $cat, 'product_cat' ) ? $cat : 0,
		SILVER_BLOG_PROMO_PRODUCTS => $products,
	);

	foreach ( $values as $key => $value ) {
		if ( empty( $value ) ) {
			delete_term_meta( $term_id, $key );
		} else {
			update_term_meta( $term_id, $key, $value );
		}
	}
}
add_action( 'created_category', 'silver_blog_promo_save_term' );
add_action( 'edited_category', 'silver_blog_promo_save_term' );

/**
 * The product search of WooCommerce on the category screens.
 */
function silver_blog_promo_admin_scripts() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'edit-category' !== $screen->id ) {
		return;
	}

	wp_enqueue_script( 'wc-enhanced-select' );
	wp_enqueue_style( 'woocommerce_admin_styles' );
}
add_action( 'admin_enqueue_scripts', 'silver_blog_promo_admin_scripts', 20 );
