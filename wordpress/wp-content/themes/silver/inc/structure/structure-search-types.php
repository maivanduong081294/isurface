<?php
/**
 * Search results: chips to switch the content type (Products · Posts ·
 * Pages), like the category chips of the shop (.page-header-cats), when at
 * least 2 types are searched (Advanced > Site Search: posts / pages,
 * silver_search_shows(); products when WooCommerce is active).
 *
 * Each chip links to the search of its type, with its number of results; a
 * type without results is disabled. Views:
 * - Products: ?s=…&post_type=product, the search of the header (products
 *             only: the posts and pages found are not listed under them,
 *             silver_pages_in_search_results(), they have their own chip).
 * - Posts:    ?s=…&post_type=post, Pages: ?s=…&post_type=page (blog search).
 *
 * Printed in the page header bar (Breadcrumbs "Below header" with the page
 * title), else above the products / the posts.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Types searched, in chip order.
 *
 * @return string[] product, post, page.
 */
function silver_search_types() {
	$types = array();
	if ( function_exists( 'is_woocommerce_activated' ) && is_woocommerce_activated() ) {
		$types[] = 'product';
	}
	if ( silver_search_shows( 'post' ) ) {
		$types[] = 'post';
	}
	if ( silver_search_shows( 'page' ) ) {
		$types[] = 'page';
	}

	return apply_filters( 'silver_search_types', $types );
}

/**
 * The type being shown.
 *
 * @return string product, post or page; '' for a search of every type.
 */
function silver_search_current_type() {
	$post_type = get_query_var( 'post_type' );
	$post_type = is_array( $post_type ) ? '' : (string) $post_type;

	return in_array( $post_type, array( 'product', 'post', 'page' ), true ) ? $post_type : '';
}

/**
 * ?post_type=page: pages are not publicly queryable, so WordPress ignores
 * the parameter and searches every type. Applied to the main search here,
 * without the shop / cart / checkout pages.
 *
 * @param WP_Query $query Query.
 */
function silver_search_pages_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}
	if ( ! isset( $_GET['post_type'] ) || 'page' !== $_GET['post_type'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$query->set( 'post_type', 'page' );
	$excluded = silver_search_excluded_pages();
	if ( $excluded ) {
		$query->set( 'post__not_in', $excluded );
	}
}
add_action( 'pre_get_posts', 'silver_search_pages_query' );

/**
 * Whether the type chips are shown (2 types or more): each type is then on
 * its own view.
 *
 * @return bool
 */
function silver_search_has_types() {
	return is_search() && count( silver_search_types() ) >= 2 && '' !== trim( get_search_query( false ) );
}

/**
 * Pages never listed in the results (shop, cart, checkout...): same list as
 * silver_pages_in_search_results().
 *
 * @return int[]
 */
function silver_search_excluded_pages() {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return array();
	}

	return array_values( array_filter( array_map( 'wc_get_page_id', array( 'shop', 'cart', 'checkout', 'view_order', 'terms' ) ), function ( $id ) {
		return $id > 0;
	} ) );
}

/**
 * Number of results of a type.
 *
 * @param string $type   product, post or page.
 * @param string $search Search query.
 * @return int
 */
function silver_search_type_count( $type, $search ) {
	$args = array(
		's'                   => $search,
		'post_type'           => $type,
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
		'fields'              => 'ids',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => false,
	);

	if ( 'product' === $type ) {
		// The products of the search page: not the hidden ones (catalog visibility).
		$tax_query = function_exists( 'WC' ) ? WC()->query->get_tax_query( array(), true ) : array();
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}
	} elseif ( 'page' === $type ) {
		$args['post__not_in'] = silver_search_excluded_pages();
	}

	$query = new WP_Query( $args );

	return (int) $query->found_posts;
}

/**
 * URL of a type.
 *
 * @param string $type   product, post or page.
 * @param string $search Search query.
 * @return string
 */
function silver_search_type_url( $type, $search ) {
	return add_query_arg( array(
		's'         => rawurlencode( $search ),
		'post_type' => $type,
	), home_url( '/' ) );
}

/**
 * The chips, as HTML ('' when fewer than 2 types).
 *
 * @return string
 */
function silver_search_types_html() {
	static $html = null;
	if ( null !== $html ) {
		return $html;
	}
	$html = '';

	if ( ! silver_search_has_types() ) {
		return $html;
	}
	$types  = silver_search_types();
	$search = get_search_query( false );

	$labels = array(
		'product' => __( 'Products', 'silver' ),
		'post'    => __( 'Posts' ), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- WordPress core string.
		'page'    => __( 'Pages' ), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- WordPress core string.
	);

	$counts = array();
	foreach ( $types as $type ) {
		$counts[ $type ] = silver_search_type_count( $type, $search );
	}
	$active = silver_search_current_type();

	$items = '';
	foreach ( $counts as $type => $count ) {
		$label = '<span>' . esc_html( $labels[ $type ] ) . '</span><span class="page-header-cat__count">' . esc_html( number_format_i18n( $count ) ) . '</span>';

		if ( $type === $active ) {
			$items .= '<a class="page-header-cat is-active" href="' . esc_url( silver_search_type_url( $type, $search ) ) . '" aria-current="page">' . $label . '</a>';
		} elseif ( 0 === $count ) {
			$items .= '<span class="page-header-cat is-disabled" aria-disabled="true">' . $label . '</span>';
		} else {
			$items .= '<a class="page-header-cat" href="' . esc_url( silver_search_type_url( $type, $search ) ) . '">' . $label . '</a>';
		}
	}

	$html = '<nav class="page-header-cats search-type-chips" aria-label="' . esc_attr__( 'Search results by type', 'silver' ) . '">' . $items . '</nav>';

	return $html;
}

/**
 * Whether the chips were already printed (page header bar).
 *
 * @param bool|null $set True to mark them printed.
 * @return bool
 */
function silver_search_types_printed( $set = null ) {
	static $printed = false;
	if ( null !== $set ) {
		$printed = (bool) $set;
	}

	return $printed;
}

/**
 * Page header bar: the chips under the title "Search Results for: …".
 *
 * @param array $data Title data (silver_page_header_title_data()).
 * @return array
 */
function silver_search_types_header_data( $data ) {
	// Posts / pages search: the number of results under the title, like the
	// "N products available" of the products search.
	if ( is_search() && ! empty( $data['title'] ) && '' === trim( (string) ( $data['desc'] ?? '' ) ) ) {
		$type  = silver_search_current_type();
		$count = (int) $GLOBALS['wp_query']->found_posts;
		if ( 'post' === $type ) {
			/* translators: %s: Number of posts. */
			$data['desc'] = esc_html( sprintf( _n( '%s post found', '%s posts found', $count, 'silver' ), number_format_i18n( $count ) ) );
		} elseif ( 'page' === $type ) {
			/* translators: %s: Number of pages. */
			$data['desc'] = esc_html( sprintf( _n( '%s page found', '%s pages found', $count, 'silver' ), number_format_i18n( $count ) ) );
		}
	}

	if ( is_search() && ! empty( $data['title'] ) ) {
		$html = silver_search_types_html();
		if ( '' !== $html ) {
			$data['after_html'] = $html;
			silver_search_types_printed( true );
		}
	}

	return $data;
}
add_filter( 'silver_page_header_title_data', 'silver_search_types_header_data' );

/** No page header bar: above the products / the posts. */
function silver_search_types_fallback() {
	if ( ! is_search() || silver_search_types_printed() ) {
		return;
	}
	$html = silver_search_types_html();
	if ( '' === $html ) {
		return;
	}
	silver_search_types_printed( true );
	echo '<div class="search-type-chips-row">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in silver_search_types_html().
}
add_action( 'woocommerce_before_shop_loop', 'silver_search_types_fallback', 5 );
add_action( 'woocommerce_no_products_found', 'silver_search_types_fallback', 5 );
add_action( 'silver_before_blog', 'silver_search_types_fallback', 25 );

