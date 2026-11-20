<?php

/**
 * Display placeholder with tooltip message on header elements when they miss a resource.
 *
 * @param string $resource Name of the resource.
 */
function fl_header_element_error( $resource ) {
	$title = '';
	switch ( $resource ) {
		case 'woocommerce':
			$title = 'WooCommerce needed';
	}
	echo '<li><a class="element-error tooltip" title="' . esc_attr( $title ) . '">-</a></li>';
}

/**
 * Get silver_breadcrumb hooked content.
 *
 * @param string|array $class   One or more classes to add to the class list.
 * @param bool         $display Whether to display the breadcrumb (true) or return it (false).
 */
function silver_breadcrumb( $class = '', $display = true ) {
	do_action( 'silver_breadcrumb', $class, $display );
}

/**
 * The divider between breadcrumb links, as HTML.
 *
 * Customizer > Breadcrumbs > Divider icon: the chosen Font Awesome icon, or a
 * slash when none is chosen. Used by the WooCommerce, blog and portfolio
 * breadcrumbs, and by Yoast SEO / AIOSEO breadcrumbs when an icon is chosen.
 *
 * @param string $fallback Divider when no icon is chosen (escaped HTML).
 *
 * @return string
 */
function silver_breadcrumb_divider( $fallback = '&#47;' ) {
	$icon    = silver_sanitize_icon_class( get_theme_mod( 'breadcrumb_divider_icon', '' ) );
	$divider = $icon ? get_silver_icon( $icon ) : $fallback;

	return apply_filters( 'silver_breadcrumb_divider', $divider, $icon );
}

/**
 * Where the breadcrumbs go on the current view.
 *
 * Customizer > Breadcrumbs > Breadcrumbs position:
 * - "title":  their usual place (above the blog title, in the shop toolbar,
 *             above the product title);
 * - "header": in a bar right under the header (silver_breadcrumbs_bar()).
 * A transparent header covers the top of the page, so there they keep their
 * usual place.
 *
 * @return string "title" or "header".
 */
function silver_breadcrumbs_position() {
	// blog_breadcrumbs_position: the first, blog-only version of this option.
	$position = get_theme_mod( 'breadcrumbs_position', get_theme_mod( 'blog_breadcrumbs_position', 'title' ) );
	$position = 'header' === $position ? 'header' : 'title';

	if ( 'header' === $position ) {
		$header_classes = explode( ' ', implode( ' ', (array) apply_filters( 'silver_header_class', array() ) ) );
		if ( in_array( 'transparent', $header_classes, true ) ) {
			$position = 'title';
		}
	}

	return apply_filters( 'silver_breadcrumbs_position', $position );
}

/**
 * The shop view whose breadcrumbs can move to the bar under the header.
 *
 * @return string "category" (shop page, product categories, tags and other
 *                product taxonomies), "product" (single product, except UX
 *                Builder "Custom" layouts, which place their own breadcrumbs)
 *                or "" for anything else.
 */
function silver_shop_breadcrumbs_view() {
	if ( ! function_exists( 'is_woocommerce_activated' ) || ! is_woocommerce_activated() ) {
		return '';
	}

	if ( is_product() ) {
		return 'custom' === get_theme_mod( 'product_layout' ) ? '' : 'product';
	}

	if ( is_shop() || is_product_taxonomy() ) {
		return 'category';
	}

	return '';
}

/**
 * Whether the bar under the header printed the breadcrumbs of this page.
 * The usual places check it so the breadcrumbs never show twice.
 *
 * @param bool|null $printed Set by silver_breadcrumbs_bar().
 *
 * @return bool
 */
function silver_breadcrumbs_bar_printed( $printed = null ) {
	static $done = false;

	if ( null !== $printed ) {
		$done = (bool) $printed;
	}

	return $done;
}

/**
 * Outputs the breadcrumbs in a bar under the header when Breadcrumbs position
 * is "Below header", on blog, shop, product category and product pages, then
 * takes them out of their usual place.
 *
 * Runs after the custom "HTML after header" (priority 1) and before the blog
 * header, the "Top" post title, the shop category header and the product
 * header (priority 10).
 */
function silver_breadcrumbs_bar() {
	if ( 'header' !== silver_breadcrumbs_position() ) {
		return;
	}

	$blog = function_exists( 'silver_blog_breadcrumbs_enabled' ) && silver_blog_breadcrumbs_enabled();
	$shop = $blog ? '' : silver_shop_breadcrumbs_view();

	ob_start();
	if ( $blog ) {
		silver_blog_breadcrumbs( 'header' );
	} elseif ( $shop ) {
		silver_breadcrumb();
	}
	$breadcrumbs = trim( ob_get_clean() );

	if ( '' === $breadcrumbs ) {
		return; // Nothing to move: the usual place keeps them.
	}

	silver_breadcrumbs_bar_printed( true );

	if ( 'category' === $shop ) {
		// Shop toolbar (loop/breadcrumbs.php): title, result count and ordering stay.
		remove_action( 'silver_category_title', 'silver_shop_loop_tools_breadcrumbs', 10 );
	} elseif ( 'product' === $shop ) {
		// Above the product title, and in the featured product header.
		// The "Gallery wide" layout checks silver_breadcrumbs_bar_printed().
		remove_action( 'woocommerce_single_product_summary', 'silver_woocommerce_product_breadcrumb', 0 );
		remove_action( 'silver_product_title', 'silver_product_page_breadcrumbs', 20 );
	}

	echo '<div class="breadcrumbs-bar"><div class="container">' . $breadcrumbs . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the breadcrumb source.
}
add_action( 'silver_after_header', 'silver_breadcrumbs_bar', 5 );

/**
 * Outputs the beginning markup of a sticky column.
 *
 * Outputs the markup directly if no theme modification name has been
 * given. Else based on the return value of the mod.
 *
 * If a theme mod was given and will output, sticky mode gets set based on $name . '_mode' theme mod.
 *
 * @param string $name  Theme modification name.
 * @param string $mode  Sticky mode (css or javascript).
 */
function silver_sticky_column_open( $name = '', $mode = '' ) {
	if ( empty( $name ) || get_theme_mod( $name ) ) {
		if ( ! empty( $name ) && empty( $mode ) ) {
			$mode = get_theme_mod( $name . '_mode' );
		}

		echo sprintf( '<div class="is-sticky-column"%s>',
			! empty( $mode ) ? ' data-sticky-mode="' . esc_attr( $mode ) . '"' : ''
		);
		echo '<div class="is-sticky-column__inner">';
	}
}

/**
 * Outputs the end markup of a sticky column.
 *
 * Outputs the markup directly if no theme modification name has been
 * given. Else based on the return value of the mod.
 *
 * @param string $name   Theme modification name.
 */
function silver_sticky_column_close( $name = '' ) {
	if ( empty( $name ) || get_theme_mod( $name ) ) {
		echo '</div></div>';
	}
}

/**
 * @deprecated 3.7
 */
function get_silver_breadcrumbs() {
	_deprecated_function( __FUNCTION__, '3.7', 'silver_breadcrumb' );
	silver_breadcrumb();
}
