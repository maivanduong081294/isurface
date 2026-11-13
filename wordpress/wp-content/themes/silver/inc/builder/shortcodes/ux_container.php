<?php

/** Registers the Container element in UX Builder. */

// Link group + a label used as the accessible name (aria-label) of the link.
$container_link_options            = require __DIR__ . '/commons/links.php';
$container_link_options['options'] = array_merge(
	array(
		'link_label' => array(
			'type'        => 'textfield',
			'heading'     => __('Label', 'silver'),
			'description' => __('Mô tả đích đến của link (aria-label), giúp SEO và trình đọc màn hình. Ví dụ: Xem chi tiết bài viết ABC.', 'silver'),
			'default'     => '',
		),
	),
	$container_link_options['options']
);

add_ux_builder_shortcode('ux_container', array(
	'type'      => 'container',
	'name'      => __('Container', 'silver'),
	'category'  => __('Layout', 'silver'),
	'thumbnail' => silver_ux_builder_thumbnail('ux_container'),
	// Admin label shown next to the name in the builder tree.
	'info'      => '{{ label }}',
	// Use server rendering so preview and frontend share responsive inheritance.
	'wrap'      => false,
	'nested'    => true,
	'presets'   => array(
		array(
			'name' => __('Default', 'silver'),
			'content' => '[ux_container][/ux_container]',
		),
	),
	'options'   => array(
		'label' => array(
			'full_width'  => true,
			'type'        => 'textfield',
			'heading'     => 'Label',
			'placeholder' => 'Enter admin label here..',
		),
		'width' => array(
			'type' => 'select',
			'heading' => __('Width', 'silver'),
			'responsive' => true,
			'default' => 'full-width',
			'options' => array('container' => 'Container', 'full-width' => 'Full width'),
		),
		// Every choice has an explicit value: an empty value at a larger
		// breakpoint means "inherit" in UX Builder.
		'v_align' => array(
			'type' => 'radio-buttons',
			'heading' => __('Align Vertical', 'silver'),
			'full_width' => true,
			'responsive' => true,
			'default' => 'equal',
			'options' => array(
				'top' => array('title' => 'Top'),
				'equal' => array('title' => 'Equal'),
				'middle' => array('title' => 'Middle'),
				'bottom' => array('title' => 'Bottom'),
			),
		),
		'h_align' => array(
			'type' => 'radio-buttons',
			'heading' => __('Align Horizontal', 'silver'),
			'full_width' => true,
			'responsive' => true,
			'default' => 'left',
			'options' => array(
				'left' => array('title' => 'Left'),
				'center' => array('title' => 'Center'),
				'right' => array('title' => 'Right'),
			),
		),
		'padding' => array(
			'type' => 'margins',
			'heading' => __('Padding', 'silver'),
			'full_width' => true,
			'responsive' => true,
			'min' => 0,
			'max' => 200,
			'step' => 1,
		),
		'margin' => array(
			'type' => 'margins',
			'heading' => __('Margin', 'silver'),
			'full_width' => true,
			'responsive' => true,
			'min' => -500,
			'max' => 500,
			'step' => 1,
		),
		'background_options' => array(
			'type' => 'group',
			'heading' => __('Background', 'silver'),
			'options' => array(
				'bg' => array(
					'type' => 'image',
					'heading' => __('Image', 'silver'),
					'responsive' => true,
					'thumb_size' => 'bg_size',
					'bg_position' => 'bg_pos',
				),
				// Hide the image on a screen: an empty image there means "inherit".
				'bg_visible' => array(
					'type' => 'radio-buttons',
					'heading' => __( 'Image visibility' ),
					'responsive' => true,
					'default' => 'show',
					'conditions' => 'bg',
					'options' => array(
						'show' => array( 'title' => 'Show' ),
						'hide' => array( 'title' => 'Hide' ),
					),
				),
				'bg_size' => array(
					'type' => 'select',
					'heading' => __('Size', 'silver'),
					'responsive' => true,
					'default' => 'large',
					'conditions' => 'bg',
					'options' => silver_ux_builder_image_sizes(),
				),
				'bg_pos' => array(
					'conditions' => 'bg',
					'type' => 'textfield',
					'heading' => __('Position', 'silver'),
					'responsive' => true,
				),
				'bg_color' => array(
					'type' => 'colorpicker',
					'heading' => __('Color', 'silver'),
					'responsive' => true,
					'format' => 'rgb',
					'position' => 'bottom right',
					'helpers' => require __DIR__ . '/helpers/colors.php',
				),
				'bg_overlay' => array(
					'type' => 'colorpicker',
					'heading' => __('Overlay', 'silver'),
					'responsive' => true,
					'alpha' => true,
					'format' => 'rgb',
					'position' => 'bottom right',
					'helpers' => require __DIR__ . '/helpers/colors-overlay.php',
				),
				'effect' => array(
					'type' => 'select',
					'heading' => __('Background effect', 'silver'),
					'default' => '',
					'options' => array(
						'' => 'None',
						'rain' => 'Rain',
						'sparkle' => 'Sparkle',
						'sliding-glass' => 'Sliding glass',
						'confetti' => 'Confetti',
						'snow' => 'Snow',
					),
				),
			),
		),
		'link_options' => $container_link_options,
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
));
