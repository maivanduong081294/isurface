<?php
/**
 * No success notices (.woocommerce-message) when products are added to or
 * removed from the cart, on the product, cart and checkout pages:
 * - "“X” has been added to your cart." (also after "Buy now", on checkout).
 * - "“X” removed. Undo?" (cart page).
 * - "Cart updated." (quantities changed on the cart page).
 * Errors and other notices stay (out of stock, required fields...).
 *
 * The mini cart still opens after a product is added without AJAX (Header >
 * Cart > reveal): the theme hung that on the "added" notice
 * (structure-wc-global.php), silver_cart_notices_added_flag() prints the same
 * trigger on its own.
 *
 * Filter "silver_hide_cart_notices" (false keeps the notices).
 *
 * @package Silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the notices are hidden.
 *
 * @return bool
 */
function silver_hide_cart_notices() {
	return (bool) apply_filters( 'silver_hide_cart_notices', true );
}

/**
 * Whether a product was added to the cart in this request (static flag).
 *
 * @param bool|null $set True to mark it.
 *
 * @return bool
 */
function silver_cart_notices_added( $set = null ) {
	static $added = false;

	if ( null !== $set ) {
		$added = (bool) $set;
	}

	return $added;
}

/**
 * "Added to your cart": no notice (an empty message is not stored by
 * wc_add_notice()).
 *
 * @param string $message Notice HTML.
 *
 * @return string
 */
function silver_cart_notices_added_message( $message ) {
	if ( ! silver_hide_cart_notices() ) {
		return $message;
	}

	silver_cart_notices_added( true );

	return '';
}
add_filter( 'wc_add_to_cart_message_html', 'silver_cart_notices_added_message', 99 );

/**
 * "Removed" and "Cart updated": WooCommerce asks for the type of these two
 * notices just before adding them, so the next success notice is skipped.
 *
 * @param string $type Notice type.
 *
 * @return string
 */
function silver_cart_notices_skip_next( $type ) {
	if ( silver_hide_cart_notices() && 'success' === $type ) {
		$GLOBALS['silver_skip_cart_notice'] = true;
	}

	return $type;
}
add_filter( 'woocommerce_cart_item_removed_notice_type', 'silver_cart_notices_skip_next', 99 );
add_filter( 'woocommerce_cart_updated_notice_type', 'silver_cart_notices_skip_next', 99 );

/**
 * Drops the success notice marked by silver_cart_notices_skip_next().
 *
 * @param string $message Notice HTML.
 *
 * @return string
 */
function silver_cart_notices_skip( $message ) {
	if ( ! empty( $GLOBALS['silver_skip_cart_notice'] ) ) {
		$GLOBALS['silver_skip_cart_notice'] = false;
		return '';
	}

	return $message;
}
add_filter( 'woocommerce_add_success', 'silver_cart_notices_skip', 99 );

/**
 * Mini cart reveal after a product is added without AJAX: the trigger the
 * theme's script looks for (span.added-to-cart, woocommerce.js), without the
 * notice around it.
 */
function silver_cart_notices_added_flag() {
	if ( silver_cart_notices_added() && function_exists( 'silver_is_mini_cart_reveal' ) && silver_is_mini_cart_reveal() ) {
		echo '<span class="added-to-cart" data-timer="" hidden></span>';
	}
}
add_action( 'wp_footer', 'silver_cart_notices_added_flag', 5 );

/*
 * "Added to cart" toast (assets/js/src/silver-cart-toast.js): a small dark
 * message at the bottom of the screen, "Đã thêm Engu Mascot Tee — Glacier
 * White / M vào giỏ hàng", after AJAX adds (single product, archives) and
 * after a product page reloads with a product added.
 */

/**
 * "Name — Value / Value" of what was added (static, this request).
 *
 * @param string|null $set Label to keep.
 *
 * @return string
 */
function silver_cart_toast_label( $set = null ) {
	static $label = '';

	if ( null !== $set ) {
		$label = (string) $set;
	}

	return $label;
}

/**
 * Keeps the label of the product added in this request (woocommerce_add_to_cart).
 *
 * @param string $cart_item_key Cart item key.
 * @param int    $product_id    Product ID.
 * @param int    $quantity      Quantity.
 * @param int    $variation_id  Variation ID.
 * @param array  $variation     Attribute values of the variation.
 */
function silver_cart_toast_remember( $cart_item_key, $product_id, $quantity, $variation_id, $variation ) {
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return;
	}

	$label  = $product->get_name();
	$values = array();

	foreach ( (array) $variation as $key => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}
		$taxonomy = substr( (string) $key, 0, 10 ) === 'attribute_' ? substr( (string) $key, 10 ) : (string) $key;
		$term     = taxonomy_exists( $taxonomy ) ? get_term_by( 'slug', $value, $taxonomy ) : false;
		$values[] = $term ? $term->name : $value;
	}

	if ( $values ) {
		$label .= ' — ' . implode( ' / ', $values );
	}

	silver_cart_toast_label( apply_filters( 'silver_cart_toast_label', $label, $product_id, $variation_id, $variation ) );
}
add_action( 'woocommerce_add_to_cart', 'silver_cart_toast_remember', 10, 5 );

/**
 * The toast (a live region, always in the page); with the label when the
 * page was loaded after adding a product without AJAX.
 */
function silver_cart_toast() {
	if ( ! function_exists( 'is_woocommerce' ) || ! apply_filters( 'silver_cart_toast', true ) ) {
		return;
	}

	$message = silver_cart_notices_added() ? silver_cart_toast_label() : '';

	printf(
		'<div class="sv-toast" role="status" aria-live="polite" aria-atomic="true" data-template="%1$s"%2$s><span class="sv-toast__icon" aria-hidden="true"></span><span class="sv-toast__text"></span></div>',
		/* translators: %s: product name and the chosen options, e.g. "Engu Mascot Tee — Glacier White / M" */
		esc_attr__( 'Added %s to cart', 'silver' ),
		'' !== $message ? ' data-message="' . esc_attr( $message ) . '"' : ''
	);
}
add_action( 'wp_footer', 'silver_cart_toast', 6 );
