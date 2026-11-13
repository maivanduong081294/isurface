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


Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'button_radius',
	'label'       => __( 'Default Button Radius', 'silver' ),
	'section'     => 'global-styles',
	'transport'   => silver_customizer_transport(),
	'default'     => '',
));
