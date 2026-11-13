<?php
/**
 * Add assets to Customizer
 *
 * @package Silver
 */

/**
 * Enqueue custom stylesheets for the Customizer controls.
 */
function silver_enqueue_customizer_stylesheet() {
	silver_enqueue_asset( 'silver-customizer-admin-js', 'admin/customizer-admin' );
	wp_enqueue_style( 'silver-header-builder-css', get_template_directory_uri() . '/assets/css/admin/admin-header-builder.css', [], silver()->version() );
	wp_enqueue_style( 'silver-customizer-admin', get_template_directory_uri() . '/assets/css/admin/admin-customizer.css', [], silver()->version() );
}

add_action( 'customize_controls_print_styles', 'silver_enqueue_customizer_stylesheet' );

/**
 * Enqueue custom stylesheets and scripts for the Customizer live preview.
 */
function silver_customizer_live_preview() {
	silver_enqueue_asset( 'silver-customizer-frontend-js', 'admin/customizer-frontend' );
	wp_enqueue_style( 'silver-customizer-preview', get_template_directory_uri() . '/assets/css/admin/admin-frontend.css', [], silver()->version() );
}

add_action( 'customize_preview_init', 'silver_customizer_live_preview' );
