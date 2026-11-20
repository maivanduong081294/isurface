<?php
/**
 * Flexible Space element. Builder: inc/builder/shortcodes/ux_stack_spacer.php.
 *
 * @package silver
 */

/**
 * [ux_stack_spacer] — empty item that fills the free space of a Stack.
 *
 * @param array $atts Attributes.
 *
 * @return string
 */
function silver_stack_spacer_shortcode($atts)
{
	$atts = ux_builder_shortcode_atts(array(
		'_id'        => '',
		'class'      => '',
		'visibility' => '',
		'min_width'  => 0,
	), $atts, 'ux_stack_spacer');

	if ('hidden' === $atts['visibility']) return '';

	$classes = array('ux-stack-spacer');
	if ($atts['class']) $classes[] = $atts['class'];
	if ($atts['visibility']) $classes[] = $atts['visibility'];

	$min = absint($atts['min_width']);

	return sprintf(
		'<div%s class="%s" aria-hidden="true"%s></div>',
		$atts['_id'] ? ' id="' . esc_attr($atts['_id']) . '"' : '',
		esc_attr(implode(' ', $classes)),
		$min ? ' style="min-width:' . $min . 'px"' : ''
	);
}
add_shortcode('ux_stack_spacer', 'silver_stack_spacer_shortcode');
