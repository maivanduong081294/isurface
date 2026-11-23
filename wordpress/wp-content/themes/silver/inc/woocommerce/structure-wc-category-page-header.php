<?php

/* Remove default Hooks */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/* Add Ordering to Silver tools */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/** $GLOBALS['woocommerce_loop'] will not be set in time otherwise. */
remove_action( 'woocommerce_before_shop_loop', 'wc_setup_loop' );
add_action( 'silver_category_title_alt', 'wc_setup_loop' );

/**
 * Displays the WooCommerce result count.
 */
function silver_woocommerce_result_count() {
	if ( get_theme_mod( 'category_show_result_count', 1 ) ) {
		woocommerce_result_count();
	}
}

add_action( 'silver_category_title_alt', 'silver_woocommerce_result_count', 20 );

/**
 * Displays the WooCommerce catalog ordering (dropdown style).
 */
function silver_woocommerce_catalog_ordering() {
	if ( get_theme_mod( 'category_show_catalog_ordering', 1 ) && 'pills' !== get_theme_mod( 'category_catalog_ordering_style', 'pills' ) ) {
		woocommerce_catalog_ordering();
	}
}

add_action( 'silver_category_title_alt', 'silver_woocommerce_catalog_ordering', 30 );

/**
 * Sort options of the "Buttons" catalog ordering: orderby value => label.
 * "" is the default sorting of the store (no orderby in the URL).
 *
 * @return array
 */
function silver_catalog_ordering_pill_options() {
	return apply_filters( 'silver_catalog_ordering_pill_options', array(
		''           => __( 'Default', 'silver' ),
		'price'      => __( 'Price: low to high', 'silver' ),
		'price-desc' => __( 'Price: high to low', 'silver' ),
	) );
}

/**
 * Displays the catalog ordering as a row of buttons (links), on the left of
 * the shop toolbar. Product Catalog > Catalog ordering style > Buttons.
 */
function silver_catalog_ordering_pills() {
	if ( ! get_theme_mod( 'category_show_catalog_ordering', 1 ) || 'pills' !== get_theme_mod( 'category_catalog_ordering_style', 'pills' ) ) {
		return;
	}
	if ( ! woocommerce_products_will_display() || ! $GLOBALS['wp_query']->found_posts ) {
		return;
	}

	$options = silver_catalog_ordering_pill_options();
	if ( empty( $options ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only sorting.
	$current = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : '';
	$default = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
	if ( $current === $default ) {
		$current = '';
	}

	// Page 1 of the current view, with its other query args (filters).
	$base  = remove_query_arg( array( 'orderby', 'paged', 'product-page' ), get_pagenum_link( 1, false ) );
	$items = '';

	foreach ( $options as $orderby => $label ) {
		$active = (string) $orderby === (string) $current;
		$url    = '' === (string) $orderby ? $base : add_query_arg( 'orderby', $orderby, $base );

		$items .= sprintf(
			'<a class="catalog-sort__item%1$s" href="%2$s" rel="nofollow"%3$s>%4$s</a>',
			$active ? ' is-active' : '',
			esc_url( $url ),
			$active ? ' aria-current="true"' : '',
			esc_html( $label )
		);
	}

	// Visible label (hidden on phones, silver-modern.css); the nav is named by aria-label.
	$label = '<span class="catalog-sort__label" aria-hidden="true">' . esc_html__( 'Sort by:', 'silver' ) . '</span>';

	echo '<nav class="catalog-sort" aria-label="' . esc_attr__( 'Sort products', 'silver' ) . '">' . $label . $items . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
}

add_action( 'silver_category_title', 'silver_catalog_ordering_pills', 5 );

if ( ! function_exists( 'silver_category_header' ) ) {
	/**
	 * Main category header function
	 */
	function silver_category_header() {
		global $wp_query;

		// Set Custom Shop Header.
		if ( get_theme_mod( 'html_shop_page' ) && is_shop() && ! $wp_query->is_search() && $wp_query->query_vars['paged'] < 1 ) {
			echo do_shortcode( '<div class="custom-page-title">' . get_theme_mod( 'html_shop_page' ) . '</div>' );
			wc_get_template_part( 'layouts/headers/category-title' );
		} // Set Category headers.
		elseif ( is_product_category() || is_shop() || is_product_tag() || is_product_taxonomy() ) {
			// Get Custom Header Content.
			$cat_header_style = get_theme_mod( 'category_title_style' );

			// Fix Transparent header.
			if ( get_theme_mod( 'category_header_transparent', 0 ) && ! $cat_header_style ) {
				$cat_header_style = 'featured';
			}

			$queried_object = get_queried_object();
			if ( ! is_shop() && get_term_meta( $queried_object->term_id, 'cat_meta' ) ) {
				$content = get_term_meta( $queried_object->term_id, 'cat_meta' );
				if ( ! empty( $content[0]['cat_header'] ) ) {
					if ( ! $cat_header_style ) {
						echo do_shortcode( $content[0]['cat_header'] );
						wc_get_template_part( 'layouts/headers/category-title' );
					} else {
						wc_get_template_part( 'layouts/headers/category-title', $cat_header_style );
						echo '<div class="custom-category-header">' . do_shortcode( $content[0]['cat_header'] ) . '</div>';
					}
				} else {
					// Get default header title.
					wc_get_template_part( 'layouts/headers/category-title', $cat_header_style );
				}
			} else {
				// Get default header title.
				wc_get_template_part( 'layouts/headers/category-title', $cat_header_style );
			}
		}
	}
}
add_action( 'silver_after_header', 'silver_category_header' );


if ( ! function_exists( 'silver_category_header_classes' ) ) {
	/**
	 * Add Transparent Header To Category if Set
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	function silver_category_header_classes( $classes ) {
		$transparent = get_theme_mod( 'category_header_transparent', 0 );
		if ( $transparent && is_shop() || $transparent && is_product_category() || $transparent && is_product_tag() ) {
			$classes[] = 'transparent has-transparent nav-dark toggle-nav-dark';
		}

		return $classes;
	}
}
add_filter( 'silver_header_class', 'silver_category_header_classes', 10 );


if ( ! function_exists( 'silver_add_category_filter_button' ) ) {
	/**
	 * Add Category Filter button for Mobile and Off Canvas
	 */
	function silver_add_category_filter_button() {
		wc_get_template_part( 'loop/filter-button' );
	}
}
add_action( 'silver_category_title', 'silver_add_category_filter_button', 20 );


if ( ! function_exists( 'silver_category_title' ) ) {
	/**
	 * Add Category Title if set
	 */
	function silver_category_title() {
		if ( ! get_theme_mod( 'category_show_title', 0 ) ) {
			return;
		} ?>
		<h1 class="shop-page-title is-xlarge"><?php woocommerce_page_title(); ?></h1>
		<?php
	}
}
add_action( 'silver_category_title', 'silver_category_title', 1 );


if ( ! function_exists( 'silver_shop_loop_tools_breadcrumbs' ) ) {
	/**
	 * Add Breadcrumbs
	 */
	function silver_shop_loop_tools_breadcrumbs() {
		wc_get_template_part( 'loop/breadcrumbs' );
	}
}
add_action( 'silver_category_title', 'silver_shop_loop_tools_breadcrumbs', 10 );
