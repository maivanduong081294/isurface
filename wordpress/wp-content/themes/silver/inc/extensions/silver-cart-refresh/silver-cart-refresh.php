<?php
/**
 * Silver Cart refresh extension
 *
 * @author     UX Themes
 * @category   Extension
 * @package    Silver/Extensions
 * @since      3.6.0
 */

/**
 * To be enqueued refresh script.
 */
function silver_cart_refresh_script() {
	global $extensions_uri;
	$theme   = wp_get_theme( get_template() );
	$version = $theme->get( 'Version' );
	wp_enqueue_script( 'silver-cart-refresh', $extensions_uri . '/silver-cart-refresh/silver-cart-refresh.js', array( 'jquery', 'silver-js' ), $version, true );
}

/**
 * Add extension script if on cart page.
 */
function silver_add_cart_refresh_script() {
	if ( is_cart() ) {
		add_action( 'wp_enqueue_scripts', 'silver_cart_refresh_script' );
	}
}

add_action( 'wp', 'silver_add_cart_refresh_script' );

