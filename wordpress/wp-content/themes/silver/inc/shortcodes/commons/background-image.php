<?php // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
/**
 * Background image.
 *
 * The image (bg), its size (bg_size) and its visibility (bg_visible) can
 * change per breakpoint (bg__md, bg_size__lg, bg_visible__ml...),
 * see silver_responsive_background_images().
 *
 * @package Silver
 */

if ( silver_responsive_background_is_responsive( $atts ) ) {
	echo silver_responsive_background_images( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput
} elseif ( ! empty( $atts['bg'] ) ) {
	echo silver_responsive_background_image( $atts['bg'], $atts['bg_size'] ); // phpcs:ignore WordPress.Security.EscapeOutput
} // phpcs:enable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
