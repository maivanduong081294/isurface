<?php
/**
 * Element "Flexible Space": empty item that takes the free space of a Stack,
 * pushing the items after it to the end (e.g. a mobile header: menu + logo
 * on the left, search + cart on the right). Stacks cannot be nested, so this
 * is how items are grouped. Render: inc/shortcodes/ux_stack_spacer.php.
 */

add_ux_builder_shortcode('ux_stack_spacer', array(
	'name'      => __('Flexible Space', 'silver'),
	'category'  => __('Layout', 'silver'),
	'thumbnail' => silver_ux_builder_thumbnail('gap'),
	'wrap'      => false,
	'presets'   => array(
		array(
			'name'    => __('Mobile only', 'silver'),
			'content' => '[ux_stack_spacer visibility="show-for-medium"]',
		),
	),
	'options'   => array(
		'min_width' => array(
			'type'    => 'slider',
			'heading' => __('Min width', 'silver'),
			'unit'    => 'px',
			'default' => 0,
			'min'     => 0,
			'max'     => 200,
			'step'    => 4,
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));
