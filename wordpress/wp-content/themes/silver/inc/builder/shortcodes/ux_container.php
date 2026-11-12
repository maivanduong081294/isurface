<?php
/** Registers the Container element in UX Builder. */

add_ux_builder_shortcode( 'ux_container', array(
	'type'      => 'container',
	'name'      => __( 'Container', 'flatsome' ),
	'category'  => __( 'Layout', 'flatsome' ),
	'thumbnail' => flatsome_ux_builder_thumbnail( 'ux_container' ),
	// Use server rendering so preview and frontend share responsive inheritance.
	'wrap'      => false,
	'nested'    => true,
	'presets'   => array(
		array(
			'name' => __( 'Default', 'flatsome' ),
			'content' => '[ux_container][/ux_container]',
		),
	),
	'options'   => array(
		'width' => array(
			'type' => 'select',
			'heading' => __( 'Width', 'flatsome' ),
			'default' => 'container',
			'options' => array( 'container' => 'Container', 'full-width' => 'Full width' ),
		),
		'padding' => array(
			'type' => 'margins',
			'heading' => __( 'Padding', 'flatsome' ),
			'full_width' => true,
			'responsive' => true,
			'min' => 0,
			'max' => 200,
			'step' => 1,
		),
		'margin' => array(
			'type' => 'margins',
			'heading' => __( 'Margin', 'flatsome' ),
			'full_width' => true,
			'responsive' => true,
			'min' => -500,
			'max' => 500,
			'step' => 1,
		),
		'background_options' => array(
			'type' => 'group',
			'heading' => __( 'Background', 'flatsome' ),
			'options' => array(
				'bg' => array(
					'type' => 'image',
					'heading' => __( 'Image', 'flatsome' ),
					'thumb_size' => 'bg_size',
					'bg_position' => 'bg_pos',
				),
				'bg_size' => array(
					'type' => 'select',
					'heading' => __( 'Size', 'flatsome' ),
					'default' => 'large',
					'conditions' => 'bg',
					'options' => flatsome_ux_builder_image_sizes(),
				),
				'bg_pos' => array(
					'conditions' => 'bg',
					'type' => 'textfield',
					'heading' => __( 'Position', 'flatsome' ),
				),
				'bg_color' => array(
					'type' => 'colorpicker',
					'heading' => __( 'Color', 'flatsome' ),
					'format' => 'rgb',
					'position' => 'bottom right',
					'helpers' => require __DIR__ . '/helpers/colors.php',
				),
				'bg_overlay' => array(
					'type' => 'colorpicker',
					'heading' => __( 'Overlay', 'flatsome' ),
					'responsive' => true,
					'alpha' => true,
					'format' => 'rgb',
					'position' => 'bottom right',
					'helpers' => require __DIR__ . '/helpers/colors-overlay.php',
				),
				'effect' => array(
					'type' => 'select',
					'heading' => __( 'Background effect', 'flatsome' ),
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
		'advanced_options' => require __DIR__ . '/commons/advanced.php',
	),
) );
