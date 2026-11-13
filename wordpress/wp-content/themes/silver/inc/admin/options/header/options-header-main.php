<?php

/*************
 * Header Main
 *************/

Silver_Option::add_section( 'main_bar', array(
	'title'       => __( 'Header Main', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'select',
	'settings'    => 'header_main_block',
	'label'       => __( 'Header Main UX Block', 'silver' ),
	'description' => __( 'Select a UX Block to render the whole Header Main (logo included). Leave empty to use the default elements.', 'silver' ),
	'section'     => 'main_bar',
	'default'     => 0,
	'choices'     => silver_get_block_list_by_id( array( 'option_none' => '-- None --' ) ),
) );

Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_header_layout',
    'label'       => __( '', 'silver' ),
    'section'     => 'main_bar',
    'default'     => '<div class="options-title-divider">Layout</div>',
) );


Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'header_width',
	'label'       => __( 'Header Width', 'silver' ),
	'section'     => 'main_bar',
	'default'     => 'container',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'container' => $image_url . 'container.svg',
		'full-width' => $image_url . 'full-width.svg'
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'header_height',
	'label'       => __( 'Height', 'silver' ),
	'section'     => 'main_bar',
	'default'     => 90,
	'choices'     => array(
		'min'  => 30,
		'max'  => 500,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'header_color',
	'label'       => __( 'Text color', 'silver' ),
	'section'     => 'main_bar',
	'default'     => 'light',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'dark' => $image_url . 'text-light.svg',
		'light' => $image_url . 'text-dark.svg'
	),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'color-alpha',
    'alpha' => true,
    'settings'     => 'header_bg',
    'label'       => __( 'Background Color', 'silver' ),
    'section'     => 'main_bar',
	'default'     => 'rgba(255,255,255,0.9)',
	'transport' => silver_customizer_transport(),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'image',
    'settings'     => 'header_bg_img',
    'label'       => __( 'Background Image', 'silver' ),
    'help' => __( 'Image is added to .header container. Try set a header background with opacity if you can not see the background image. (Drag the alpha slider in the background selector)', 'silver' ),
    'section'     => 'main_bar',
	'default'     => "",
	'transport' => silver_customizer_transport(),
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'header_bg_img_repeat',
	'label'       => __( 'Background Repeat', 'silver' ),
	'section'     => 'main_bar',
	'default'     => 'repeat',
	'choices'     => $bg_repeat,
	'transport' => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'header_bg_img',
			'operator' => '!==',
			'value'    => '',
		),
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'box_shadow_header',
	'label'       => __( 'Add Shadow', 'silver' ),
	'section'     => 'main_bar',
	'transport' => silver_customizer_transport(),
	'default'     => 0,
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'header_divider',
	'label'       => __( 'Add Divider', 'silver' ),
	'section'     => 'main_bar',
	'transport' => silver_customizer_transport(),
	'default'     => 1,
));


Silver_Option::add_field( 'option',  array(
	'type'        => 'textarea',
	'settings'     => 'html_after_header',
	'label'       => __( 'HTML after header', 'silver' ),
	'section'     => 'main_bar',
	'default'     => '',
	'sanitize_callback' => 'silver_custom_sanitize',
));


Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_nav',
    'label'       => __( '', 'silver' ),
    'section'     => 'main_bar',
    'default'     => '<div class="options-title-divider">Navigation</div>',
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'nav_style',
	'label'       => __( 'Navigation Style', 'silver' ),
	'section'     => 'main_bar',
	'default'     => '',
	'transport' => silver_customizer_transport(),
	'choices'     => $nav_styles_img
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'nav_size',
	'label'       => __( 'Nav Size', 'silver' ),
	'section'     => 'main_bar',
	'transport' => silver_customizer_transport(),
	'default'     => '',
	'choices'     => $nav_sizes
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-buttonset',
	'settings'     => 'nav_spacing',
	'label'       => __( 'Nav Spacing', 'silver' ),
	'section'     => 'main_bar',
	'transport' => silver_customizer_transport(),
	'default'     => '',
	'choices'     => $nav_sizes
));


Silver_Option::add_field( 'option',  array(
		'type'        => 'checkbox',
		'settings'     => 'nav_uppercase',
		'label'       => __( 'Uppercase', 'silver' ),
		'section'     => 'main_bar',
	    'transport' => silver_customizer_transport(),
		'default'     => 1,
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'nav_body_overlay',
	'label'    => __( 'Add overlay on hover', 'silver' ),
	'section'  => 'main_bar',
	'default'  => 0,
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'nav_height',
	'label'       => __( 'Nav Height', 'silver' ),
	'section'     => 'main_bar',
	'default' => 16,
	'choices'     => array(
		'min'  => 0,
		'max'  => 500,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'nav_push',
	'label'       => __( 'Nav Push', 'silver' ),
	'section'     => 'main_bar',
	'default' => 0,
	'choices'     => array(
		'min'  => -50,
		'max'  => 50,
		'step' => 1
	),
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
    'type'        => 'color',
    'settings'     => 'type_nav_color',
    'label'       => __( 'Nav Color', 'silver' ),
	'section'     => 'main_bar',
    'transport' => silver_customizer_transport()
));

Silver_Option::add_field( 'option',  array(
    'type'        => 'color',
    'settings'     => 'type_nav_color_hover',
    'label'       => __( 'Nav Color :hover', 'silver' ),
	'section'     => 'main_bar',
    'transport' => silver_customizer_transport()
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'color-alpha',
    'alpha' => true,
    'settings'     => 'header_icons_color',
    'label'       => __( 'Icons Color', 'silver' ),
    'section'     => 'main_bar',
	'default'     => '',
	'transport' => silver_customizer_transport()
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'color-alpha',
    'alpha' => true,
    'settings'     => 'header_icons_color_hover',
    'label'       => __( 'Icons Color :hover', 'silver' ),
    'section'     => 'main_bar',
	'default'     => '',
	'transport' => silver_customizer_transport()
));



Silver_Option::add_field( '', array(
    'type'        => 'custom',
    'settings' => 'custom_title_transparent',
    'label'       => __( '', 'silver' ),
    'section'     => 'main_bar',
    'default'     => '<div class="options-title-divider">Transparent Header</div>',
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'header_height_transparent',
	'label'       => __( 'Height - Transparent Header', 'silver' ),
	'section'     => 'main_bar',
	'default'     => 90,
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'min'  => 30,
		'max'  => 500,
		'step' => 1
	),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'color-alpha',
    'settings'     => 'header_bg_transparent',
    'label'       => __( 'Transparent Header Background Color', 'silver' ),
    'section'     => 'main_bar',
	'default'     => '',
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
    'settings'     => 'header_bg_transparent_shade',
	'label'       => __( 'Add Shade', 'silver' ),
	'section'     => 'main_bar',
	'transport' => silver_customizer_transport(),
	'default'     => 0,
));
