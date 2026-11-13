<?php

/*************
 * Header Top
 *************/

Silver_Option::add_section( 'top_bar', array(
	'title'       => __( 'Top Bar', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'select',
	'settings'    => 'header_top_block',
	'label'       => __( 'Top Bar UX Block', 'silver' ),
	'description' => __( 'Select a UX Block to render the whole Top Bar. Leave empty to use the default elements.', 'silver' ),
	'section'     => 'top_bar',
	'default'     => 0,
	'choices'     => silver_get_block_list_by_id( array( 'option_none' => '-- None --' ) ),
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'topbar_show',
	//'transport' => silver_customizer_transport(),
	'label'       => __( 'Enable Top Bar', 'silver' ),
	'section'     => 'top_bar',
	'default'     => 1,
));

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_header_top_layout',
	'section'  => 'top_bar',
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'  => '<div class="options-title-divider">Layout</div>',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'header_top_height',
	'label'       => __( 'Height', 'silver' ),
	'section'     => 'top_bar',
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'     => 30,
	'choices'     => array(
		'min'  => 20,
		'max'  => 100,
		'step' => 1
	),
	'transport' => silver_customizer_transport()
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'topbar_color',
	'label'       => __( 'Text Color', 'silver' ),
	'section'     => 'top_bar',
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'     => 'dark',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'dark' => $image_url . 'text-light.svg',
		'light' => $image_url . 'text-dark.svg'
	),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'color-alpha',
    'settings'     => 'topbar_bg',
    'label'       => __( 'Background Color', 'silver' ),
    //'description' => __( 'This is the control description', 'silver' ),
    //'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
    'section'     => 'top_bar',
    'active_callback' => array(
	    array(
		    'setting'  => 'topbar_show',
		    'operator' => '===',
		    'value'    => true,
	    ),
    ),
    'default' => '',
    'transport' => silver_customizer_transport(),
	'js_vars'   => array(
		array(
			'element'  => '.header-top',
			'function' => 'css',
			'property' => 'background-color'
		),
	)
));

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_header_top_nav',
	'section'  => 'top_bar',
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'  => '<div class="options-title-divider">Navigation</div>',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'nav_style_top',
	'label'       => __( 'Navigation Style', 'silver' ),
	'section'     => 'top_bar',
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'     => 'divided',
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_styles_img
));

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'nav_top_uppercase',
	'label'     => __( 'Uppercase', 'silver' ),
	'section'   => 'top_bar',
	'transport' => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'   => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'nav_top_body_overlay',
	'label'    => __( 'Add overlay on hover', 'silver' ),
	'section'  => 'top_bar',
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'  => 0,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'nav_height_top',
	'label'     => __( 'Nav Height', 'silver' ),
	'section'   => 'top_bar',
	'transport' => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'   => 16,
	'choices'   => array(
		'min'  => 0,
		'max'  => 100,
		'step' => 1,
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'type_nav_top_color',
	'label'       => __( 'Nav Color', 'silver' ),
	'section'     => 'top_bar',
	'transport'   => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'color',
	'settings'    => 'type_nav_top_color_hover',
	'label'       => __( 'Nav Color :hover', 'silver' ),
	'section'     => 'top_bar',
	'transport'   => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'topbar_show',
			'operator' => '===',
			'value'    => true,
		),
	),
) );
