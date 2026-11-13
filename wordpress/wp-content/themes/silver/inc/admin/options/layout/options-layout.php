<?php

/*************
 * Layout Panel
 *************/

Silver_Option::add_section( 'layout', array(
	'title'       => __( 'Layout', 'silver' ),
	//'description' => __( 'Change the Layout', 'silver' ),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-buttonset',
	'settings'     => 'body_layout',
	'label'       => __( 'Layout Mode', 'silver' ),
	'description' => __( 'Select Full width, boxed or framed layout', 'silver' ),
	'section'     => 'layout',
	'default'     => 'full-width',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'full-width' => __( 'Full Width', 'silver' ),
		'boxed' => __( 'Boxed', 'silver' ),
		'framed' => __( 'Framed', 'silver' ),
	),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'box_shadow',
	'label'       => __( 'Add Drop Shadow to Content box', 'silver' ),
	'section'     => 'layout',
	'transport' => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'body_layout',
			'operator' => '!==',
			'value'    => 'full-width',
		),
	),
));

Silver_Option::add_field( 'option', array(
	'type'        => 'number',
	'settings'    => 'site_width_boxed',
	'label'       => __( 'Site width (px)', 'silver' ),
	'section'     => 'layout',
	'transport'   => silver_customizer_transport(),
	'default'     => '1170',
	'active_callback' => array(
		array(
			'setting'  => 'body_layout',
			'operator' => '!==',
			'value'    => 'full-width',
		),
	),
	'choices'     => [
		'min'  => 560,
		'max'  => 4000,
		'step' => 10,
	],
));

Silver_Option::add_field( 'option',  array(
 	'type'        => 'color',
  'settings'     => 'body_bg',
  'label'       => __( 'Background Color', 'silver' ),
  'section'     => 'layout',
	'default'     => "",
	'transport' => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'body_layout',
			'operator' => '!==',
			'value'    => 'full-width',
		),
	),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'image',
    'settings'     => 'body_bg_image',
    'label'       => __( 'Background Image', 'silver' ),
    'section'     => 'layout',
	'default'     => "",
	'transport' => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'body_layout',
			'operator' => '!==',
			'value'    => 'full-width',
		),
	),
));


Silver_Option::add_field( 'option', array(
	'type'        => 'radio-buttonset',
	'settings'     => 'body_bg_type',
	'label'       => __( 'Background Repeat', 'silver' ),
	'section'     => 'layout',
	'default'     => 'bg-full-size',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'bg-full-size' => __( 'Full Size', 'silver' ),
		'bg-tiled' => __( 'Tiled', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'body_layout',
			'operator' => '!==',
			'value'    => 'full-width',
		),
		array(
			'setting'  => 'body_bg_image',
			'operator' => '!==',
			'value'    => '',
		),
	),
));

Silver_Option::add_field( 'option', array(
	'type'        => 'number',
	'settings'    => 'site_width',
	'label'       => __( 'Container width (px)', 'silver' ),
	'description' => __( 'Set the default width of content containers. (Header, Rows etc.)', 'silver' ),
	'section'     => 'layout',
	'transport'   => silver_customizer_transport(),
	'default'     => '1080',
	'choices'     => [
		'min'  => 560,
		'max'  => 4000,
		'step' => 10,
	],
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'content_color',
	'label'       => __( 'Content Color', 'silver' ),
	'description' => __( 'Light or Dark content text color', 'silver' ),
	'section'     => 'layout',
	'default'     => 'light',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'light' => $image_url . 'text-dark.svg',
		'dark' => $image_url . 'text-light.svg'
	),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'color',
    'settings'     => 'content_bg',
    'label'       => __( 'Content Background', 'silver' ),
    'section'     => 'layout',
	'default'     => "",
	'transport' => silver_customizer_transport(),
));
