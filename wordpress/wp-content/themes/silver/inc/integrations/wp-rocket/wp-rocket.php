<?php

function silver_wp_rocket_integration() {
	global $integrations_uri;

	if ( get_rocket_option( 'lazyload' ) ) {
		wp_enqueue_script(
			'silver-wp-rocket',
			$integrations_uri . '/wp-rocket/silver-wp-rocket.js',
			array( 'silver-js' ),
			wp_get_theme( get_template() )->get( 'Version' ),
			true
		);
	}
}

add_action( 'wp_enqueue_scripts', 'silver_wp_rocket_integration' );

/**
 * JS files to be included in the footer during the minification process.
 */
function silver_wp_rocket_minify_js_in_footer( $scripts ) {
  $uri = get_template_directory_uri();
  if ( wp_script_is( 'silver-countdown-theme-js' ) ) {
    $scripts[] = $uri . '/inc/shortcodes/ux_countdown/ux-countdown.js';
  }
  if ( wp_script_is( 'silver-lazy' ) ) {
    $scripts[] = $uri . '/inc/extensions/silver-lazy-load/silver-lazy-load.js';
  }
  if ( wp_script_is( 'silver-wp-rocket' ) ) {
    $scripts[] = $uri . '/inc/integrations/wp-rocket/silver-wp-rocket.js';
  }
  if ( wp_script_is( 'silver-isotope-js' ) ) {
    $scripts[] = $uri . '/assets/libs/isotope.pkgd.min.js';
  }
  return $scripts;
}
add_filter( 'rocket_minify_js_in_footer', 'silver_wp_rocket_minify_js_in_footer' );
