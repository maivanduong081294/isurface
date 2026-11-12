<?php
/**
 * Font Awesome icon picker for UX Builder.
 *
 * Registers the "icon" option type and loads the grid picker popup
 * (search, style tabs, preview) in the UX Builder editor.
 *
 * @package flatsome
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
 * Enqueue the icon picker assets in the UX Builder editor.
 *
 * @param string $context Context is «editor» or «content».
 */
function silver_icon_picker_enqueue( $context ) {
	if ( 'editor' !== $context ) {
		return;
	}

	$uri     = get_template_directory_uri();
	$theme   = wp_get_theme( get_template() );
	$version = $theme->get( 'Version' );

	// Font Awesome must be available in the editor sidebar to preview icons.
	wp_enqueue_style(
		'silver-fontawesome-editor',
		$uri . '/assets/css/icons/fontawesome/css/all.min.css',
		array( 'ux-builder-core' ),
		'7.3.1'
	);

	wp_enqueue_style(
		'silver-icon-picker',
		$uri . '/assets/css/builder/custom/icon-picker.css',
		array( 'ux-builder-core' ),
		$version
	);

	wp_enqueue_script(
		'silver-icon-picker',
		$uri . '/assets/js/builder/custom/icon-picker.js',
		array( 'ux-builder-core' ),
		$version,
		true
	);

	$data = array(
		'icons'  => silver_icon_picker_get_icons(),
		'legacy' => function_exists( 'silver_fontawesome_icon_map' ) ? silver_fontawesome_icon_map() : array(),
	);

	wp_add_inline_script(
		'silver-icon-picker',
		'window.silverIconPicker = ' . wp_json_encode( $data ) . ';',
		'before'
	);
}

add_action( 'ux_builder_enqueue_scripts', 'silver_icon_picker_enqueue', 20 );
