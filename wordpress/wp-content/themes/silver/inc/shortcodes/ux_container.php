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
function silver_ux_container_spacing_value($value)
{
	if ($value === null || $value === '') {
		return $value;
	}

	return is_numeric($value) ? floatval($value) . 'px' : $value;
}

function silver_render_ux_container_shortcode($atts, $content = null, $tag = 'ux_container')
{
	$raw_atts = (array) $atts;
	$atts = ux_builder_shortcode_atts(array(
		'_id' => '',
		'class' => '',
		'visibility' => '',
		'width' => 'container',
		// Must match the builder defaults (default values are not saved).
		'v_align' => 'equal',
		'h_align' => 'left',
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
		'bg_visible' => 'show',
		'bg_size' => 'large',
		'bg_pos' => '',
		'bg_color' => '',
		'bg_overlay' => '',
		'bg_overlay__lg' => null,
		'bg_overlay__md' => null,
		'bg_overlay__ml' => null,
		'effect' => '',
		// Link (commons/links.php): "link" is resolved by ux_builder_shortcode_atts().
		'link' => '',
		'link_label' => '',
		'target' => '',
		'rel' => '',
	), $atts, $tag);

	foreach (array('padding', 'padding__md', 'padding__ml', 'padding__lg', 'margin', 'margin__md', 'margin__ml', 'margin__lg') as $key) {
		$atts[$key] = silver_ux_container_spacing_value($atts[$key]);
	}

	$id = sanitize_html_class($atts['_id']);
	if (! $id) {
		$id = 'ux-container-' . wp_rand();
	}
	$classes = array('ux-container', 'full-width' === $atts['width'] ? 'ux-container-full' : 'container');
	$classes[] = $atts['class'];
	$classes[] = $atts['visibility'];
	// Alignment: flex layout when any screen is aligned, the mobile value as
	// classes, larger screens in silver_ux_container_responsive_style().
	if (silver_ux_container_is_aligned($atts)) {
		$classes[] = 'ux-container-aligned';
	}
	if (in_array($atts['v_align'], array('equal', 'middle', 'bottom'), true)) {
		$classes[] = 'ux-container-v-' . $atts['v_align'];
	}
	if (in_array($atts['h_align'], array('center', 'right'), true)) {
		$classes[] = 'ux-container-h-' . $atts['h_align'];
	}
	$effect = in_array($atts['effect'], array('rain', 'sparkle', 'sliding-glass', 'confetti', 'snow'), true) ? $atts['effect'] : '';
	if ($effect) {
		wp_enqueue_style('silver-effects');
	}

	if (! $atts['bg_size']) {
		$atts['bg_size'] = 'large';
	}

	// Background set on any breakpoint.
	$has_bg = silver_responsive_values($atts, 'bg') || silver_responsive_values($atts, 'bg_color') || silver_responsive_values($atts, 'bg_overlay');
	if ($has_bg) {
		$classes[] = 'has-bg';
	}

	$link = trim((string) $atts['link']);
	$rel  = trim((string) $atts['rel']);
	if ($link && '_blank' === $atts['target'] && false === strpos($rel, 'noopener')) {
		$rel = trim($rel . ' noopener');
	}
	if ($link) {
		$classes[] = 'ux-container-has-link';
	}

	// Accessible name of the link: the Label option, else derived from the
	// Link Type: post title, term name, or post type archive name.
	// Custom links have no known target name, so the label stays empty.
	$link_label = trim(wp_strip_all_tags((string) $atts['link_label']));
	if ($link && '' === $link_label) {
		$link_data = silver_get_link_data($raw_atts);

		switch ($link_data['object_type']) {
			case 'post_type':
				$link_label = get_the_title($link_data['object_id']);
				break;
			case 'taxonomy':
				$term       = get_term($link_data['object_id'], $link_data['object_name']);
				$link_label = ($term && ! is_wp_error($term)) ? $term->name : '';
				break;
			case 'post_type_archive':
				$post_type  = get_post_type_object($link_data['object_name']);
				$link_label = $post_type ? $post_type->labels->name : '';
				break;
		}

		$link_label = trim(wp_strip_all_tags((string) $link_label));
	}

	ob_start();
?>
	<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"<?php echo $link ? ' style="position:relative"' : ''; ?>>
		<?php if ($link) : ?>
			<?php // Stretched link: phủ toàn bộ container, tránh lồng thẻ <a> trong <a>. ?>
			<a class="ux-container-link" href="<?php echo esc_url($link); ?>"<?php echo $atts['target'] ? ' target="' . esc_attr($atts['target']) . '"' : ''; ?><?php echo $rel ? ' rel="' . esc_attr($rel) . '"' : ''; ?><?php echo $link_label ? ' aria-label="' . esc_attr($link_label) . '"' : ''; ?> style="position:absolute;top:0;right:0;bottom:0;left:0;z-index:2"></a>
		<?php endif; ?>
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
		<?php echo silver_ux_container_responsive_style($id, $atts); ?>
	</div>
<?php
	return ob_get_clean();
}

/**
 * Whether the container is aligned (vertically or horizontally) on any screen.
 *
 * @param array $atts
 * @return bool
 */
function silver_ux_container_is_aligned($atts)
{
	foreach (silver_responsive_filled($atts, 'v_align', 'equal') as $value) {
		if (in_array($value, array('equal', 'middle', 'bottom'), true)) return true;
	}
	foreach (silver_responsive_filled($atts, 'h_align', 'left') as $value) {
		if (in_array($value, array('center', 'right'), true)) return true;
	}
	return false;
}

/**
 * Responsive width and alignment (values for larger screens).
 *
 * Mirrors the classes used for the base value: container / ux-container-full
 * and ux-container-v-* / ux-container-h-* (assets/css/silver.css). The
 * alignment only changes the --ux-v / --ux-h / --ux-height variables, so
 * `display` (and the visibility classes) are never touched.
 *
 * @param string $id
 * @param array  $atts
 * @return string
 */
function silver_ux_container_responsive_style($id, $atts)
{
	$rules = array(
		'width' => array(
			'selector'       => '',
			'only_overrides' => true,
			'default'        => 'container',
			'map'            => array(
				'container'  => 'max-width: ' . silver_responsive_container_width() . '; padding-left: var(--fs-container-padding); padding-right: var(--fs-container-padding)',
				'full-width' => 'max-width: none; padding-left: 0; padding-right: 0',
			),
		),
	);

	// No !important: the ID already beats the ux-container-v-*/h-* classes.
	$rules['v_align'] = array(
		'selector'       => '',
		'only_overrides' => true,
		'default'        => 'equal',
		'important'      => false,
		'map'            => array(
			'top'    => '--ux-height: auto; --ux-v: flex-start',
			'equal'  => '--ux-height: 100%; --ux-v: flex-start',
			'middle' => '--ux-height: 100%; --ux-v: center',
			'bottom' => '--ux-height: 100%; --ux-v: flex-end',
		),
	);
	$rules['h_align'] = array(
		'selector'       => '',
		'only_overrides' => true,
		'default'        => 'left',
		'important'      => false,
		'map'            => array(
			'left'   => '--ux-h: stretch',
			'center' => '--ux-h: center',
			'right'  => '--ux-h: flex-end',
		),
	);

	return silver_responsive_style_tag($id, $atts, $rules);
}

add_shortcode('ux_container', 'silver_render_ux_container_shortcode');
add_shortcode('ux_container_inner', 'silver_render_ux_container_shortcode');
for ($ux_container_level = 1; $ux_container_level <= 9; $ux_container_level++) {
	add_shortcode('ux_container_inner_' . $ux_container_level, 'silver_render_ux_container_shortcode');
}
unset($ux_container_level);
