<?php

/** Renders a container and its child shortcodes. */

/**
 * Normalize a spacing value: a bare number from the old slider option
 * becomes "Npx", while a "margins" option value (e.g. "10px 0px 20px 0px")
 * is used as is.
 *
 * @param  mixed $value
 * @return mixed
 */
function flatsome_ux_container_spacing_value($value)
{
	if ($value === null || $value === '') {
		return $value;
	}

	return is_numeric($value) ? floatval($value) . 'px' : $value;
}

function flatsome_render_ux_container_shortcode($atts, $content = null, $tag = 'ux_container')
{
	$atts = ux_builder_shortcode_atts(array(
		'_id' => '',
		'class' => '',
		'visibility' => '',
		'width' => 'container',
		'padding' => null,
		'padding__lg' => null,
		'padding__md' => null,
		'padding__ml' => null,
		'margin' => null,
		'margin__lg' => null,
		'margin__md' => null,
		'margin__ml' => null,
		// Background.
		'bg' => '',
		'bg_size' => 'large',
		'bg_pos' => '',
		'bg_color' => '',
		'bg_overlay' => '',
		'bg_overlay__lg' => null,
		'bg_overlay__md' => null,
		'bg_overlay__ml' => null,
		'effect' => '',
	), $atts, $tag);

	foreach (array('padding', 'padding__md', 'padding__ml', 'padding__lg', 'margin', 'margin__md', 'margin__ml', 'margin__lg') as $key) {
		$atts[$key] = flatsome_ux_container_spacing_value($atts[$key]);
	}

	$id = sanitize_html_class($atts['_id']);
	if (! $id) {
		$id = 'ux-container-' . wp_rand();
	}
	$classes = array('ux-container', 'full-width' === $atts['width'] ? 'ux-container-full' : 'container');
	$classes[] = $atts['class'];
	$classes[] = $atts['visibility'];
	$effect = in_array($atts['effect'], array('rain', 'sparkle', 'sliding-glass', 'confetti', 'snow'), true) ? $atts['effect'] : '';
	if ($effect) {
		wp_enqueue_style('flatsome-effects');
	}

	if (! $atts['bg_size']) {
		$atts['bg_size'] = 'large';
	}

	$has_bg = $atts['bg'] || $atts['bg_color'] || $atts['bg_overlay'] || $atts['bg_overlay__md'] || $atts['bg_overlay__ml'] || $atts['bg_overlay__lg'];
	if ($has_bg) {
		$classes[] = 'has-bg';
	}

	ob_start();
?>
	<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
		<?php if ($has_bg) : ?>
			<div class="ux-container-bg" aria-hidden="true">
				<?php require __DIR__ . '/commons/background-image.php'; ?>
				<?php if ($atts['bg_overlay'] || $atts['bg_overlay__md'] || $atts['bg_overlay__ml'] || $atts['bg_overlay__lg']) : ?>
					<div class="ux-container-bg-overlay"></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ($effect) : ?>
			<div class="ux-container-effect effect-<?php echo esc_attr($effect); ?>" aria-hidden="true"></div>
		<?php endif; ?>
		<div class="ux-container-content"><?php echo do_shortcode($content ?? ''); ?></div>
		<?php echo ux_builder_element_style_tag($id, array(
			'padding' => array(
				'selector' => '> .ux-container-content',
				'property' => 'padding',
			),
			'margin' => array(
				'selector' => '',
				'property' => 'margin',
			),
			'bg_color' => array(
				'selector' => '> .ux-container-bg',
				'property' => 'background-color',
			),
			'bg_overlay' => array(
				'selector' => '> .ux-container-bg > .ux-container-bg-overlay',
				'property' => 'background-color',
			),
			'bg_pos' => array(
				'selector' => '> .ux-container-bg img',
				'property' => 'object-position',
			),
		), $atts); ?>
	</div>
<?php
	return ob_get_clean();
}

add_shortcode('ux_container', 'flatsome_render_ux_container_shortcode');
add_shortcode('ux_container_inner', 'flatsome_render_ux_container_shortcode');
for ($ux_container_level = 1; $ux_container_level <= 9; $ux_container_level++) {
	add_shortcode('ux_container_inner_' . $ux_container_level, 'flatsome_render_ux_container_shortcode');
}
unset($ux_container_level);
