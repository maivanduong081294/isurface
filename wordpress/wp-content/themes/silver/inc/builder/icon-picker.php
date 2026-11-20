<?php
/**
 * Font Awesome icon picker for UX Builder.
 *
 * Registers the "icon" option type and loads the grid picker popup
 * (search, style tabs, preview) in the UX Builder editor.
 *
 * @package silver
 */

/**
 * Register the "icon" option type for UX Builder.
 */
add_action( 'ux_builder_setup', function () {
	add_ux_builder_option_type( 'icon' );
} );

/**
 * Get the Font Awesome icon list used by the picker.
 *
 * @return array List of array( classes, label, style, search terms ).
 */
function silver_icon_picker_get_icons() {
	static $icons = null;

	if ( null === $icons ) {
		$icons = require __DIR__ . '/shortcodes/values/fontawesome-icons.php';
		$icons = apply_filters( 'silver_icon_picker_icons', $icons );
	}

	return $icons;
}

/**
 * Enqueue the shared picker: Font Awesome (to preview icons), the picker
 * styles and the popup script with the icon list.
 *
 * Used by the UX Builder editor and the Customizer icon control
 * (inc/admin/customizer/customizer-icon-control.php).
 *
 * @param array $style_deps Dependencies of the stylesheets.
 */
function silver_icon_picker_enqueue_popup( $style_deps = array() ) {
	$uri     = get_template_directory_uri();
	$theme   = wp_get_theme( get_template() );
	$version = $theme->get( 'Version' );

	wp_enqueue_style(
		'silver-fontawesome-editor',
		$uri . '/assets/css/icons/fontawesome/css/all.min.css',
		$style_deps,
		'7.3.1'
	);

	wp_enqueue_style(
		'silver-icon-picker',
		$uri . '/assets/css/builder/custom/icon-picker.css',
		$style_deps,
		$version
	);

	wp_enqueue_script(
		'silver-icon-picker-popup',
		$uri . '/assets/js/builder/custom/icon-picker-popup.js',
		array(),
		$version,
		true
	);

	$data = array(
		'icons'  => silver_icon_picker_get_icons(),
		'legacy' => function_exists( 'silver_fontawesome_icon_map' ) ? silver_fontawesome_icon_map() : array(),
	);

	wp_add_inline_script(
		'silver-icon-picker-popup',
		'window.silverIconPicker = ' . wp_json_encode( $data ) . ';',
		'before'
	);
}

/**
 * Enqueue the icon picker assets in the UX Builder editor.
 *
 * @param string $context Context is «editor» or «content».
 */
function silver_icon_picker_enqueue( $context ) {
	if ( 'editor' !== $context ) {
		return;
	}

	$theme = wp_get_theme( get_template() );

	silver_icon_picker_enqueue_popup( array( 'ux-builder-core' ) );

	wp_enqueue_script(
		'silver-icon-picker',
		get_template_directory_uri() . '/assets/js/builder/custom/icon-picker.js',
		array( 'ux-builder-core', 'silver-icon-picker-popup' ),
		$theme->get( 'Version' ),
		true
	);
}

add_action( 'ux_builder_enqueue_scripts', 'silver_icon_picker_enqueue', 20 );
