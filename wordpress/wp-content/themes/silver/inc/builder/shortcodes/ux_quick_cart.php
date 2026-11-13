<?php
/**
 * Element "Quick Cart" (WooCommerce): cart icon + mini cart.
 * Render: inc/shortcodes/ux_quick_cart.php. Registered only with WooCommerce.
 */

add_ux_builder_shortcode('ux_quick_cart', array(
	'name'      => __('Quick Cart', 'silver'),
	'category'  => __('Shop'),
	'thumbnail' => silver_ux_builder_thumbnail('woo_add_to_cart'),
	'info'      => '{{ style }}',
	'wrap'      => false,
	'options'   => array(
		'style'      => array(
			'type'    => 'radio-buttons',
			'heading' => __('Style', 'silver'),
			'default' => 'dropdown',
			'options' => array(
				'dropdown'   => array('title' => 'Dropdown'),
				'off-canvas' => array('title' => 'Off-canvas'),
				'link'       => array('title' => 'Link'),
			),
		),
		// Font Awesome picker, same as the Icon box element.
		'icon'       => array(
			'type'    => 'icon',
			'heading' => __('Icon', 'silver'),
			'default' => silver_quick_cart_icon_class(get_theme_mod('cart_icon', 'basket')),
		),
		'icon_size'  => array(
			'type'       => 'slider',
			'heading'    => __('Icon size', 'silver'),
			'responsive' => true,
			'unit'       => 'px',
			'default'    => '',
			'min'        => 10,
			'max'        => 80,
			'step'       => 1,
		),
		'icon_color' => array(
			'type'       => 'colorpicker',
			'heading'    => __('Icon color', 'silver'),
			'responsive' => true,
			'alpha'      => true,
			'format'     => 'rgb',
			'position'   => 'bottom right',
			'helpers'    => require __DIR__ . '/helpers/colors-text.php',
		),
		'show_count' => array(
			'type'    => 'radio-buttons',
			'heading' => __('Count', 'silver'),
			'default' => 'yes',
			'options' => array(
				'yes' => array('title' => 'Show'),
				'no'  => array('title' => 'Hide'),
			),
		),
		'show_title' => array(
			'type'    => 'radio-buttons',
			'heading' => __('Title', 'silver'),
			'default' => 'no',
			'options' => array(
				'yes' => array('title' => 'Show'),
				'no'  => array('title' => 'Hide'),
			),
		),
		'show_total' => array(
			'type'    => 'radio-buttons',
			'heading' => __('Total', 'silver'),
			'default' => 'yes',
			'options' => array(
				'yes' => array('title' => 'Show'),
				'no'  => array('title' => 'Hide'),
			),
		),
		'align'      => array(
			'type'       => 'radio-buttons',
			'heading'    => __('Align', 'silver'),
			'responsive' => true,
			'default'    => 'left',
			'options'    => require __DIR__ . '/values/align-radios.php',
		),
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));
