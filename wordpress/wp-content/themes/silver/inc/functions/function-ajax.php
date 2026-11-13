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

	$markup = silver_apply_shortcode( $tag, $atts );

	wp_send_json_success( trim( $markup ) );
}

add_action( 'wp_ajax_silver_ajax_apply_shortcode', 'silver_ajax_apply_shortcode' );
add_action( 'wp_ajax_nopriv_silver_ajax_apply_shortcode', 'silver_ajax_apply_shortcode' );
