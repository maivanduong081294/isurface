<?php
/**
 * Lean markup helpers: no empty wrappers, one <style> tag per shortcode tree.
 *
 * @package Silver
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run an action and print its output inside a wrapper. When the action prints
 * nothing, nothing is printed: no empty wrapper is left in the DOM.
 *
 * The action runs exactly as before (same hook, same priority order), so
 * doing_action() checks in callbacks keep working.
 *
 * @param string $hook  Action name.
 * @param string $class Class attribute of the wrapper.
 */
function silver_action_wrapper( $hook, $class ) {
	ob_start();
	do_action( $hook );
	$html = ob_get_clean();

	if ( '' === trim( $html ) ) {
		return;
	}

	echo '<div class="' . esc_attr( trim( $class ) ) . '">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Whether element styles are merged into one <style> tag per shortcode tree.
 *
 * Off in UX Builder: the editor re-renders a single element and needs its
 * styles next to it.
 *
 * @return bool
 */
function silver_merge_inline_styles() {
	static $enabled = null;

	if ( null === $enabled ) {
		$enabled = ! is_admin()
			&& ! ( function_exists( 'ux_builder_is_active' ) && ux_builder_is_active() )
			&& ! ( function_exists( 'ux_builder_is_iframe' ) && ux_builder_is_iframe() );

		/**
		 * Filters whether element <style> tags are merged per shortcode tree.
		 *
		 * @param bool $enabled
		 */
		$enabled = (bool) apply_filters( 'silver_merge_inline_styles', $enabled );
	}

	return $enabled;
}

/**
 * State of the style collector: shortcode nesting depth and collected CSS.
 *
 * @return array
 */
function &silver_inline_style_state() {
	static $state = array(
		'depth' => 0,
		'css'   => array(),
	);

	return $state;
}

/**
 * Wrap element CSS in a <style> tag, or collect it while a shortcode renders.
 *
 * Collected CSS is printed once, in a single <style> tag right before the
 * outermost shortcode (see silver_inline_style_flush()). The rules are scoped
 * to element IDs, so merging them does not change which rule wins.
 *
 * @param string $css CSS without the <style> tag.
 * @return string The <style> tag, or '' when the CSS was collected.
 */
function silver_inline_style_tag( $css ) {
	$css = trim( $css );

	if ( '' === $css ) {
		return '';
	}

	$state = &silver_inline_style_state();

	if ( $state['depth'] > 0 && silver_merge_inline_styles() ) {
		$state['css'][] = $css;
		return '';
	}

	return '<style>' . $css . '</style>';
}

/**
 * Count shortcode nesting before a shortcode runs.
 *
 * @param false|string $return Short-circuit value of other filters.
 * @return false|string
 */
function silver_inline_style_enter( $return ) {
	if ( false === $return ) {
		$state = &silver_inline_style_state();
		$state['depth']++;
	}

	return $return;
}
add_filter( 'pre_do_shortcode_tag', 'silver_inline_style_enter', PHP_INT_MAX );

/**
 * When the outermost shortcode is done, print the CSS collected inside it in
 * one <style> tag before its output.
 *
 * @param string $output Shortcode output.
 * @return string
 */
function silver_inline_style_flush( $output ) {
	$state = &silver_inline_style_state();

	if ( $state['depth'] > 0 ) {
		$state['depth']--;
	}

	if ( 0 === $state['depth'] && $state['css'] ) {
		$output       = '<style>' . implode( '', $state['css'] ) . '</style>' . $output;
		$state['css'] = array();
	}

	return $output;
}
add_filter( 'do_shortcode_tag', 'silver_inline_style_flush', PHP_INT_MAX );

/**
 * Close every open shortcode level and return the collected CSS as a <style>
 * tag. For code that catches an exception thrown while a shortcode renders.
 *
 * @return string
 */
function silver_inline_style_reset() {
	$state          = &silver_inline_style_state();
	$css            = $state['css'];
	$state['depth'] = 0;
	$state['css']   = array();

	return $css ? '<style>' . implode( '', $css ) . '</style>' : '';
}
