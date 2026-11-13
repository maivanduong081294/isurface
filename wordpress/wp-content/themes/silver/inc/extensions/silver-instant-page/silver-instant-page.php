<?php
/**
 * Silver Instant Page extension
 *
 * @author     UX Themes
 * @category   Extension
 * @package    Silver/Extensions
 * @since      3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_action( 'wp_enqueue_scripts', 'silver_instant_page' );

if ( ! function_exists( 'silver_instant_page' ) ) :

function silver_instant_page() {
  global $extensions_uri;

  wp_enqueue_script(
    'silver-instant-page',
    $extensions_uri . '/silver-instant-page/silver-instant-page.js',
    array(),
    '1.2.1',
    true
  );
}

endif;
