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
	wp_add_inline_style(
		'silver-customizer-admin',
		'#accordion-section-floating_contact > h3:before { content: "\f525"; }'
		// Breadcrumbs section (options-breadcrumbs.php): dashicons-networking.
		. '#accordion-section-breadcrumbs > h3:before { content: "\f325"; }'
		// Tablet landscape preview (silver_customizer_previewable_devices):
		// 1024 x 768, inside the 850-1199px tier. The core tablet is 720px
		// wide (Tablet portrait tier) and the core phone 320px (Mobile).
		. '.wp-full-overlay.preview-tablet-landscape .wp-full-overlay-main { left: 50%; width: 1024px; height: 768px; max-width: 100%; max-height: 100%; margin: auto 0 auto -512px; }'
		. '.wp-full-overlay-footer .devices .preview-tablet-landscape:before { content: "\f471"; display: inline-block; transform: rotate(90deg); }'
	);
}

add_action( 'customize_controls_print_styles', 'silver_enqueue_customizer_stylesheet' );

/**
 * Customizer preview devices: Desktop, Tablet landscape, Tablet portrait and
 * Mobile, the four screens of UX Builder and of the theme CSS.
 *
 * @param array $devices Core devices (desktop, tablet, mobile).
 * @return array
 */
function silver_customizer_previewable_devices( $devices ) {
	$ordered = array();

	foreach ( $devices as $name => $device ) {
		if ( 'tablet' === $name ) {
			// The core tablet (720px) keeps its translated label.
			$ordered['tablet-landscape'] = array(
				'label' => __( 'Tablet landscape', 'silver' ),
			);
		}
		$ordered[ $name ] = $device;
	}

	return $ordered;
}

add_filter( 'customize_previewable_devices', 'silver_customizer_previewable_devices' );

/**
 * Enqueue custom stylesheets and scripts for the Customizer live preview.
 */
function silver_customizer_live_preview() {
	silver_enqueue_asset( 'silver-customizer-frontend-js', 'admin/customizer-frontend' );
	wp_enqueue_style( 'silver-customizer-preview', get_template_directory_uri() . '/assets/css/admin/admin-frontend.css', [], silver()->version() );
}

add_action( 'customize_preview_init', 'silver_customizer_live_preview' );
