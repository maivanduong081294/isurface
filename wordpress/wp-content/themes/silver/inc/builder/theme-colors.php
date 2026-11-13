<?php
/**
 * Theme colors in the UX Builder color options.
 *
 * The color swatches under a color option (shortcodes/helpers/colors.php) save
 * a CSS variable such as var(--fs-color-primary). The frontend and the builder
 * preview already define those variables (inc/functions/function-custom-css.php).
 * The editor sidebar is a separate document, so this file defines them there
 * too and loads the script that teaches the color picker to show a variable.
 *
 * @package silver
 */

defined( 'ABSPATH' ) || exit;

/**
 * Red, green and blue of a hex color as "r, g, b", or null for any other value.
 * Same result as the color channels printed by the custom CSS.
 *
 * @param string $color Hex color, with or without #, three or six digits.
 *
 * @return string|null
 */
function silver_ux_builder_color_channels( $color ) {
	$hex = ltrim( trim( (string) $color ), '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = preg_replace( '/(.)/', '$1$1', $hex );
	}

	if ( ! preg_match( '/^[0-9a-f]{6}$/i', $hex ) ) {
		return null;
	}

	return sprintf( '%d,%d,%d', hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
}

/**
 * Enqueues the theme color support of the editor.
 *
 * @param string $context editor or content.
 */
function silver_ux_builder_theme_colors_enqueue( $context ) {
	if ( 'editor' !== $context ) {
		return;
	}

	$file = get_template_directory() . '/assets/js/builder/custom/theme-colors.js';

	wp_enqueue_script(
		'silver-ux-builder-theme-colors',
		get_template_directory_uri() . '/assets/js/builder/custom/theme-colors.js',
		array( 'ux-builder-core' ),
		file_exists( $file ) ? filemtime( $file ) : wp_get_theme( get_template() )->get( 'Version' ),
		true
	);

	// Success is no longer offered as a swatch, but elements saved with it keep
	// resolving in the editor.
	$colors = array(
		'primary'   => get_theme_mod( 'color_primary', Silver_Default::COLOR_PRIMARY ),
		'secondary' => get_theme_mod( 'color_secondary', Silver_Default::COLOR_SECONDARY ),
		'base'      => get_theme_mod( 'color_texts', Silver_Default::COLOR_BASE ),
		'success'   => get_theme_mod( 'color_success', Silver_Default::COLOR_SUCCESS ),
	);

	$css = '';
	foreach ( $colors as $name => $color ) {
		$css .= '--fs-color-' . $name . ':' . esc_attr( $color ) . ';';

		// The 10%..90% series of the swatches, as printed on the frontend.
		$rgb = silver_ux_builder_color_channels( $color );
		if ( 'success' === $name || ! $rgb ) {
			continue;
		}
		for ( $i = 10; $i <= 90; $i += 10 ) {
			$css .= '--fs-color-' . $name . '-' . $i . ':rgba(' . $rgb . ',0.' . ( $i / 10 ) . ');';
		}
	}

	// Lay the swatches out as a grid of 9 columns: the colors on the first row,
	// then one row per 10%..90% series. A series starts at the swatch titled
	// "... 10%" (inc/builder/shortcodes/helpers/colors.php), so the layout does
	// not depend on how many colors there are. The popup is raised so that the
	// options below it (text align, ...) do not paint over it.
	$layout = '.option-colorpicker .option-helpers-colors{display:grid;grid-template-columns:repeat(9,auto);gap:3px 0;white-space:normal;z-index:100}'
		. '.option-colorpicker .option-helpers-colors a[title$=" 10%"]{grid-column-start:1}'
		. '.dragging .option-colorpicker .option-helpers-colors{display:none}'
		// "None" (transparent) and "Default" (inherit) swatches: a crossed swatch.
		. '.option-colorpicker .option-helpers-colors a[title="None"],.option-colorpicker .option-helpers-colors a[title="Default"]{background:#fff linear-gradient(to top right,transparent calc(50% - 1px),#e33 calc(50% - 1px),#e33 calc(50% + 1px),transparent calc(50% + 1px))!important}';

	wp_add_inline_style( 'ux-builder-core', ':root{' . $css . '}' . $layout );
}
add_action( 'ux_builder_enqueue_scripts', 'silver_ux_builder_theme_colors_enqueue', 20 );
