<?php

/**
 * Registers the `ux_text` shortcode.
 *
 * @package silver
 */

/**
 * Renders the `ux_text` shortcode.
 *
 * @param array  $atts    An array of attributes.
 * @param string $content The shortcode content.
 * @param string $tag     The name of the shortcode, provided for context to enable filtering.
 *
 * @return string
 */
function silver_render_ux_text_shortcode($atts, $content, $tag)
{
	$atts = ux_builder_shortcode_atts(
		array(
			'_id'             => '',
			'visibility'      => '',
			'class'           => '',
			'font_size'       => '',
			'font_size__sm'   => '',
			'font_size__md'   => '',
			'line_height'     => '',
			'line_height__sm' => '',
			'line_height__md' => '',
			'text_align'      => '',
			'text_align__sm'  => '',
			'text_align__md'  => '',
			'text_color'      => '',
		),
		$atts,
		$tag
	);
	$_id = $atts['_id'] ? $atts['_id'] : 'text-' . wp_rand();
	$classes = array('text');

	if (! empty($atts['class']))      $classes[] = $atts['class'];
	if (! empty($atts['visibility'])) $classes[] = $atts['visibility'];

	ob_start();

?>
	<div id="<?php echo esc_attr($_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
		<?php echo do_shortcode($content); ?>
		<?php
		echo ux_builder_element_style_tag(
			$_id,
			array(
				'font_size'   => array(
					'property' => 'font-size',
					'unit'     => 'rem',
				),
				'line_height' => array(
					'property' => 'line-height',
				),
				'text_align'  => array(
					'property' => 'text-align',
				),
				'text_color'  => array(
					'selector' => ', > *',
					'property' => 'color',
				),
			),
			$atts
		);
		?>
	</div>
<?php


	return ob_get_clean();
}
add_shortcode('ux_text', 'silver_render_ux_text_shortcode');
