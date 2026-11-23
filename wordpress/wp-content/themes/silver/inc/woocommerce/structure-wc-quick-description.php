<?php
/**
 * Quick description of products: a short plain text (one or two sentences)
 * shown on product cards (shop, categories, Products element) instead of the
 * WooCommerce short description, which stays for the product page.
 *
 * - Edit product screen: "Quick description" box, under the short description.
 * - Product meta "_silver_quick_description".
 * - Product Catalog > Show quick description (setting short_description_in_grid).
 * - Printed by silver_woocommerce_shop_loop_excerpt() (structure-wc-product-box.php).
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

const SILVER_QUICK_DESCRIPTION_META = '_silver_quick_description';

/**
 * The quick description of a product, as plain text.
 *
 * @param WC_Product|int|null $product Product, product ID, or the current one.
 *
 * @return string Empty when not set.
 */
function silver_get_product_quick_description( $product = null ) {
	$product = $product ? wc_get_product( $product ) : wc_get_product( get_the_ID() );

	if ( ! $product ) {
		return '';
	}

	$text = trim( (string) $product->get_meta( SILVER_QUICK_DESCRIPTION_META, true ) );

	return (string) apply_filters( 'silver_product_quick_description', $text, $product );
}

/**
 * Edit product screen: the box, under the short description.
 */
function silver_quick_description_meta_box() {
	add_meta_box(
		'silver-quick-description',
		__( 'Quick description', 'silver' ),
		'silver_quick_description_meta_box_html',
		'product',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'silver_quick_description_meta_box', 40 );

/**
 * Box content. Saved with the product (silver_quick_description_save()), so
 * the WooCommerce nonce and capability checks apply.
 *
 * @param WP_Post $post Product post.
 */
function silver_quick_description_meta_box_html( $post ) {
	$product = wc_get_product( $post->ID );
	$value   = $product ? $product->get_meta( SILVER_QUICK_DESCRIPTION_META, true ) : '';
	?>
	<textarea name="silver_quick_description" id="silver-quick-description" class="widefat" rows="3" maxlength="300"><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description"><?php esc_html_e( 'Shown on product cards (shop, categories, Products element), in place of the short description. One or two sentences; cards show two lines.', 'silver' ); ?></p>
	<?php
}

/**
 * Saves the box with the product.
 *
 * @param WC_Product $product Product being saved.
 */
function silver_quick_description_save( $product ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked by WooCommerce before this hook.
	if ( ! isset( $_POST['silver_quick_description'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$value = sanitize_textarea_field( wp_unslash( $_POST['silver_quick_description'] ) );

	if ( '' === trim( $value ) ) {
		$product->delete_meta_data( SILVER_QUICK_DESCRIPTION_META );
	} else {
		$product->update_meta_data( SILVER_QUICK_DESCRIPTION_META, $value );
	}
}
add_action( 'woocommerce_admin_process_product_object', 'silver_quick_description_save' );
