<?php
/**
 * Active price from the price fields.
 *
 * WooCommerce stores the active price of a product in its own field (_price)
 * and only updates it when the product is saved. Imported or migrated
 * products can keep a stale value there: a product with Regular price 29 and
 * Sale price 15 then shows "29 → 29" and is charged 29.
 *
 * These filters derive the active price from the Regular / Sale price fields
 * (and the sale schedule), as WooCommerce does when a product is saved, so
 * the shop, the cart and the checkout all use the price entered in the
 * product. Products whose stored price is already right are not changed.
 *
 * @package Silver\WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Active price of a simple product or a variation from its price fields.
 *
 * @param WC_Product $product Product.
 * @return string Price, or '' when the fields do not give one.
 */
function silver_wc_price_from_fields( $product ) {
	if ( ! $product instanceof WC_Product || $product->is_type( array( 'variable', 'grouped' ) ) ) {
		return '';
	}

	$regular = $product->get_regular_price( 'edit' );
	if ( '' === $regular ) {
		return '';
	}

	// is_on_sale() checks the sale price against the regular price and the
	// sale dates; the "edit" context reads the raw fields (no filters).
	return $product->is_on_sale( 'edit' ) ? $product->get_sale_price( 'edit' ) : $regular;
}

/**
 * Replace a stale stored price with the one from the price fields.
 *
 * Runs early and only replaces the value when nothing has changed it yet
 * (it still equals the stored price): prices set by other plugins (dynamic
 * pricing, currency switchers...) are left alone.
 *
 * @param string     $price   Price.
 * @param WC_Product $product Product.
 * @return string
 */
function silver_wc_fix_stale_price( $price, $product ) {
	if ( ! $product instanceof WC_Product ) {
		return $price;
	}

	$stored = $product->get_price( 'edit' );
	if ( (string) $price !== (string) $stored ) {
		return $price;
	}

	$active = silver_wc_price_from_fields( $product );

	return ( '' !== $active && (string) $active !== (string) $stored ) ? $active : $price;
}
add_filter( 'woocommerce_product_get_price', 'silver_wc_fix_stale_price', 1, 2 );
add_filter( 'woocommerce_product_variation_get_price', 'silver_wc_fix_stale_price', 1, 2 );

/**
 * The same for the prices a variable product shows ("15 ₫ – 29 ₫"), which
 * are read from the variations' stored prices.
 *
 * @param string               $price     Price of the variation.
 * @param WC_Product_Variation $variation Variation.
 * @return string
 */
function silver_wc_fix_stale_variation_price( $price, $variation ) {
	return silver_wc_fix_stale_price( $price, $variation );
}
add_filter( 'woocommerce_variation_prices_price', 'silver_wc_fix_stale_variation_price', 1, 2 );

/**
 * Variable product prices are cached: a new hash makes WooCommerce rebuild
 * the cache with the prices above.
 *
 * @param array $hash Data the cache key is built from.
 * @return array
 */
function silver_wc_variation_prices_hash( $hash ) {
	$hash[] = 'silver-price-from-fields-1';
	return $hash;
}
add_filter( 'woocommerce_get_variation_prices_hash', 'silver_wc_variation_prices_hash' );
