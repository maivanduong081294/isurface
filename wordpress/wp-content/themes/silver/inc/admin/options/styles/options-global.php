<?php


Silver_Option::add_section( 'global-styles', array(
	'title'       => __( 'Global Styles', 'silver' ),
	'panel'       => 'style',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'silver_modern_ui',
	'label'       => __( 'Modern UI', 'silver' ),
	'description' => __( 'Rounded corners, soft shadows and smooth hover effects for all elements. Turn off to use the classic style.', 'silver' ),
	'section'     => 'global-styles',
	'default'     => 1,
) );

// Colour accents on top of Modern UI (assets/css/src/silver-vivid.css).
Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'silver_vivid_ui',
	'label'           => __( 'Vivid UI', 'silver' ),
	'description'     => __( 'Adds color accents from the primary and secondary colors to the elements: gradients, tinted cards, colored shadows and icons. Needs Modern UI.', 'silver' ),
	'section'         => 'global-styles',
	'default'         => 0,
	'active_callback' => array(
		array(
			'setting'  => 'silver_modern_ui',
			'operator' => '==',
			'value'    => true,
		),
	),
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'button_radius',
	'label'       => __( 'Default Button Radius', 'silver' ),
	'section'     => 'global-styles',
	'transport'   => silver_customizer_transport(),
	'default'     => '',
));
