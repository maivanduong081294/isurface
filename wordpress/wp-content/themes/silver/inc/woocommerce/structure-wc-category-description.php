<?php
/**
 * Description of product categories, tags and other product taxonomies.
 *
 * - Product Catalog > Category description position: under the products
 *   (default) or above them.
 * - Product Catalog > Table of contents in the category description: the
 *   description is laid out with [ux_toc] (inc/shortcodes/ux_toc.php), the
 *   content and the list of its headings next to it, a bar on phones.
 *
 * Same rules as woocommerce_taxonomy_archive_description(), which it
 * replaces: first page only, woocommerce_taxonomy_archive_description_raw.
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Where the description goes: "bottom" (under the products and the
 * pagination) or "top" (above the products).
 *
 * @return string
 */
function silver_category_description_position() {
	$position = get_theme_mod( 'category_description_position', 'bottom' );

	return 'top' === apply_filters( 'silver_category_description_position', $position ) ? 'top' : 'bottom';
}

/**
 * Prints the description of the product taxonomy being shown.
 */
function silver_category_description() {
	if ( ! is_product_taxonomy() || 0 !== absint( get_query_var( 'paged' ) ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	/** This filter is documented in woocommerce/includes/wc-template-functions.php */
	$description = apply_filters( 'woocommerce_taxonomy_archive_description_raw', $term->description, $term );
	if ( empty( $description ) ) {
		return;
	}

	$html     = wc_format_content( wp_kses_post( $description ) );
	$position = silver_category_description_position();

	if ( get_theme_mod( 'category_description_toc', 1 ) && function_exists( 'silver_toc_shortcode' ) ) {
		$atts = apply_filters( 'silver_category_description_toc_atts', array(
			'_id'      => 'term-description-toc',
			'levels'   => 'h2,h3',
			'position' => 'right',
			// Product Catalog > Table of contents width (--ux-toc-width).
			'width'    => silver_sanitize_css_length( get_theme_mod( 'category_description_toc_width', '40%' ) ),
			'mobile'   => 'bar',
		), $term );

		$html = silver_toc_shortcode( $atts, $html, 'ux_toc' );
	}

	// Product Catalog > Read more in the category description: the whole
	// description (the content and the table of contents next to it) is cut
	// to a height.
	if ( get_theme_mod( 'category_description_readmore', 1 ) && function_exists( 'silver_readmore_shortcode' ) ) {
		$readmore = apply_filters( 'silver_category_description_readmore_atts', array(
			'_id'    => 'term-description-readmore',
			'height' => absint( get_theme_mod( 'category_description_readmore_height', 480 ) ),
		), $term );

		$html = silver_readmore_shortcode( $readmore, $html, 'ux_readmore' );
	}

	$html = '<div class="term-description term-description--' . esc_attr( $position ) . '">' . $html . '</div>';

	// Below the products: a full-width section of its own, outside the row of
	// the category layout (Product Catalog > Category description background).
	if ( 'bottom' === $position ) {
		$html = '<section class="term-description-section"><div class="container">' . $html . '</div></section>';
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- formatted like WooCommerce.
}

/**
 * Takes the place of the WooCommerce description, above or under the products.
 */
function silver_category_description_setup() {
	if ( ! function_exists( 'is_product_taxonomy' ) || ! is_product_taxonomy() ) {
		return;
	}

	remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

	if ( 'top' === silver_category_description_position() ) {
		add_action( 'woocommerce_archive_description', 'silver_category_description', 10 );
	} else {
		// woocommerce/archive-product.php, after the category layout.
		add_action( 'silver_after_category_layout', 'silver_category_description', 10 );
	}
}
add_action( 'wp', 'silver_category_description_setup' );
