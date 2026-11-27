<?php // @codingStandardsIgnoreLine

/**
 * Apply shortcode.
 *
 * @return void
 */
function silver_ajax_apply_shortcode() {
	$tag  = isset( $_GET['tag'] ) ? silver_clean( wp_unslash( $_GET['tag'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$atts = isset( $_GET['atts'] ) ? silver_clean( wp_unslash( (array) $_GET['atts'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	$allowed_tags = array(
		'blog_posts',
		'ux_posts',
		'ux_bestseller_products',
		'ux_featured_products',
		'ux_sale_products',
		'ux_latest_products',
		'ux_custom_products',
		'product_lookbook',
		'products_pinterest_style',
		'ux_products',
	);

	if (
		empty( $tag )
		|| empty( $atts )
		|| ! in_array( $tag, $allowed_tags, true )
	) {
		wp_send_json_error( array(
			'message' => 'Invalid request',
		) );
	}

	// Public endpoint (the relay: "Load more", pages): one page of the element
	// at most. Never a custom ID list: all of it at once, products hidden from
	// the catalog included (the relay is off for those elements anyway, they
	// have a single page).
	unset( $atts['ids'] );
	foreach ( array( 'products', 'posts' ) as $key ) {
		if ( isset( $atts[ $key ] ) ) {
			$atts[ $key ] = (string) max( 1, min( 100, (int) $atts[ $key ] ) );
		}
	}
	if ( isset( $atts['page_number'] ) ) {
		$atts['page_number'] = (string) max( 1, min( 1000, (int) $atts['page_number'] ) );
	}
	if ( isset( $atts['offset'] ) ) {
		$atts['offset'] = (string) min( 10000, absint( $atts['offset'] ) );
	}

	$markup = silver_apply_shortcode( $tag, $atts );

	wp_send_json_success( trim( $markup ) );
}

add_action( 'wp_ajax_silver_ajax_apply_shortcode', 'silver_ajax_apply_shortcode' );
add_action( 'wp_ajax_nopriv_silver_ajax_apply_shortcode', 'silver_ajax_apply_shortcode' );
