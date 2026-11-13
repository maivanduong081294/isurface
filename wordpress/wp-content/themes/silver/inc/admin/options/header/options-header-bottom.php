<?php

/*************
 * Header Main
 *************/

Silver_Option::add_section( 'bottom_bar', array(
	'title'       => __( 'Header Bottom', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'select',
	'settings'    => 'header_bottom_block',
	'label'       => __( 'Header Bottom UX Block', 'silver' ),
	'description' => __( 'Select a UX Block to render the whole Header Bottom. Leave empty to use the default elements.', 'silver' ),
	'section'     => 'bottom_bar',
	'default'     => 0,
	'choices'     => silver_get_block_list_by_id( array( 'option_none' => '-- None --' ) ),
) );


Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_header_bottom_layout',
    'label'       => __( '', 'silver' ),
	'section'     => 'bottom_bar',
    'default'     => '<div class="options-title-divider">Layout</div>',
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'header_bottom_height',
	'label'       => __( 'Height', 'silver' ),
	'section'     => 'bottom_bar',
	'default' => '',
	'choices'     => array(
		'min'  => 10,
		'max'  => 100,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'color-alpha',
    'settings'     => 'nav_position_bg',
    'label'       => __( 'Background color', 'silver' ),
    'section'     => 'bottom_bar',
	'default'     => "#f1f1f1",
	'transport' => silver_customizer_transport(),
));



Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_header_bottom_nav',
    'label'       => __( '', 'silver' ),
	'section'     => 'bottom_bar',
    'default'     => '<div class="options-title-divider">Navigation</div>',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'nav_style_bottom',
	'label'       => __( 'Nav Style', 'silver' ),
	'section'     => 'bottom_bar',
	'default'     => '',
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_styles_img
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'nav_height_bottom',
	'label'       => __( 'Nav Height', 'silver' ),
	'section'     => 'bottom_bar',
	'default' => 16,
	'choices'     => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'nav_size_bottom',
	'label'       => __( 'Nav Size', 'silver' ),
	'section'     => 'bottom_bar',
	'transport' => silver_customizer_transport(),
	'default'     => '',
	'choices'     => $nav_sizes
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'nav_spacing_bottom',
	'label'       => __( 'Nav Spacing', 'silver' ),
	'section'     => 'bottom_bar',
	'transport' => silver_customizer_transport(),
	'default'     => '',
	'choices'     => $nav_sizes
));

Silver_Option::add_field( 'option',  array(
		'type'        => 'checkbox',
		'settings'     => 'nav_uppercase_bottom',
		'label'       => __( 'Uppercase', 'silver' ),
		'section'     => 'bottom_bar',
	    'transport' => silver_customizer_transport(),
		'default'     => 1,
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'nav_bottom_body_overlay',
	'label'    => __( 'Add overlay on hover', 'silver' ),
	'section'  => 'bottom_bar',
	'default'  => 0,
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'nav_position_color',
	'label'       => __( 'Nav Base Color', 'silver' ),
	'section'     => 'bottom_bar',
	'default'     => 'light',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'dark' => $image_url . 'text-light.svg',
		'light' => $image_url . 'text-dark.svg'
	),
));

Silver_Option::add_field( 'option',  array(
    'type'        => 'color',
    'settings'     => 'type_nav_bottom_color',
    'label'       => __( 'Nav Color', 'silver' ),
	'section'     => 'bottom_bar',
    'transport' => silver_customizer_transport()
));

Silver_Option::add_field( 'option',  array(
    'type'        => 'color',
    'settings'     => 'type_nav_bottom_color_hover',
    'label'       => __( 'Nav Color :hover', 'silver' ),
	'section'     => 'bottom_bar',
    'transport' => silver_customizer_transport()
));


