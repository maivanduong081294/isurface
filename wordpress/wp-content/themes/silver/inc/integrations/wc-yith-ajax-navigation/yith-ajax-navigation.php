<?php
/**
 * YITH woocommerce ajax navigation.
 *
 * @author      UX Themes
 * @package     Silver/Integrations
 */

/**
 * Enqueues integrations scripts
 */
function silver_yith_woocommerce_ajax_navigation_integrations_scripts() {
	global $integrations_uri;

	wp_enqueue_script(
		'silver-yith-woocommerce-ajax-navigation',
		$integrations_uri . '/wc-yith-ajax-navigation/yith-ajax-navigation.js',
		array( 'jquery', 'silver-js' ),
		wp_get_theme( get_template() )->get( 'Version' ),
		true
	);
}

add_action( 'wp_enqueue_scripts', 'silver_yith_woocommerce_ajax_navigation_integrations_scripts' );
