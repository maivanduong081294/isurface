<?php

/**
 * Registers the `ux_stack` shortcode.
 *
 * @package flatsome
 */

/**
 * Renders the `ux_stack` shortcode.
 *
 * @param array  $atts    An array of attributes.
 * @param string $content The shortcode content.
 * @param string $tag     The name of the shortcode, provided for context to enable filtering.
 *
 * @return string
 */
function flatsome_render_ux_stack_shortcode($atts, $content, $tag)
{
	$atts = ux_builder_shortcode_atts(
		array(
			'_id'            => '',
			'visibility'     => '',
			'class'          => '',
			'type'           => 'stack',
			'direction'      => 'row',
			'direction__sm'  => null,
			'direction__md'  => null,
			'distribute'     => 'start',
			'distribute__sm' => null,
			'distribute__md' => null,
			'align'          => 'stretch',
			'align__sm'      => null,
			'align__md'      => null,
			'gap'            => '0',
			'gap__sm'        => null,
			'gap__md'        => null,
			'slider_nav_style'     => 'reveal',
			'slider_nav_color'     => '',
			'slider_nav_position'  => 'inside',
			'slider_style'         => 'normal',
			'slider_bullets'       => '',
			'auto_slide'           => '',
			'infinitive'           => '',
			'thumb_role'           => '',
			'thumb_key'            => '',
		),
		$atts,
		$tag
	);

	$main_classes = ['stack-wrapper'];

	$id      = ! empty($atts['_id']) ? $atts['_id'] : 'stack-' . wp_rand();
	$classes = array();
	$is_slider = 'slider' === $atts['type'];
	if ($is_slider) {
		$classes[] = 'slider';
		$classes[] = 'row-slider';
		if ($atts['slider_nav_style']) {
			$classes[] = 'slider-nav-' . $atts['slider_nav_style'];
		}
		if ($atts['slider_nav_color']) {
			$classes[] = 'slider-nav-' . $atts['slider_nav_color'];
		}
		if ($atts['slider_nav_position'] && 'reveal' !== $atts['slider_nav_style']) {
			$classes[] = 'slider-nav-' . $atts['slider_nav_position'];
		}
		if ($atts['slider_style']) {
			$classes[] = 'slider-style-' . $atts['slider_style'];
		}
		// Main slider + thumbnail slider pair ("Thumbnail Slider" options).
		$thumbnail_class = flatsome_slider_thumbnail_class($atts['thumb_role'], $atts['thumb_key']);
		if ($thumbnail_class) {
			$classes[] = $thumbnail_class;
		}
	} else {
		$classes[] = 'stack';
	}

	if (! empty($atts['class']))      $main_classes[] = $atts['class'];
	if (! empty($atts['visibility'])) $main_classes[] = $atts['visibility'];

	foreach ($is_slider ? array() : array('sm', 'md', 'ml', '') as $value) {
		$class_prefix = $value ? "$value:" : 'lg:';
		$attr_suffix  = $value ? "__$value" : '';

		if ($atts["direction{$attr_suffix}"]) {
			$classes[] = "{$class_prefix}stack-{$atts["direction{$attr_suffix}"]}";
		}

		if ($atts["distribute{$attr_suffix}"]) {
			$classes[] = "{$class_prefix}justify-{$atts["distribute{$attr_suffix}"]}";
		}

		if ($atts["align{$attr_suffix}"]) {
			$classes[] = "{$class_prefix}items-{$atts["align{$attr_suffix}"]}";
		}
	}

	ob_start();
	$slider_options = array(
		'cellAlign'     => 'left',
		'wrapAround'    => 'false' !== $atts['infinitive'],
		'pageDots'      => 'true' === $atts['slider_bullets'],
		'autoPlay'      => $atts['auto_slide'] ? (int) $atts['auto_slide'] : false,
		'groupCells'    => '100%',
		'dragThreshold' => 5,
	);

?>
	<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', $main_classes)); ?>">
		<div class="stack-inner <?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $is_slider ? flatsome_slider_options_attr($slider_options) : ''; ?>>
			<?php echo do_shortcode($content); ?>
		</div>
		<?php
		echo ux_builder_element_style_tag(
			$id,
			array(
				'gap' => array(
					'selector' => '.stack-inner',
					'property' => '--stack-gap',
					'unit'     => 'rem',
				),
			),
			$atts
		);
		?>
	</div>
<?php

	return ob_get_clean();
}
add_shortcode('ux_stack', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_1', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_2', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_4', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_5', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_6', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_7', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_8', 'flatsome_render_ux_stack_shortcode');
add_shortcode('ux_stack_inner_9', 'flatsome_render_ux_stack_shortcode');
