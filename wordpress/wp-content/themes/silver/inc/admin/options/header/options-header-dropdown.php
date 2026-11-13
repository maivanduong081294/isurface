<?php

/*************
 * Header Dropdown Style
 *************/

Silver_Option::add_section( 'header_dropdown', array(
	'title' => __( 'Dropdown Style', 'silver' ),
	'panel' => 'header',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_header_dropdown_all',
	'label'    => '',
	'section'  => 'header_dropdown',
	'default'  => '<div class="options-title-divider">All Dropdowns</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'settings'  => 'dropdown_bg',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Background color', 'silver' ),
	'section'   => 'header_dropdown',
	'default'   => '',
) );


Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'dropdown_border_enabled',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Add border', 'silver' ),
	'section'   => 'header_dropdown',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color-alpha',
	'settings'        => 'dropdown_border',
	'transport'       => silver_customizer_transport(),
	'label'           => __( 'Border Color', 'silver' ),
	'section'         => 'header_dropdown',
	'active_callback' => array(
		array(
			'setting'  => 'dropdown_border_enabled',
			'operator' => '==',
			'value'    => 1,
		),
	),
	'default'         => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'checkbox',
	'settings'  => 'dropdown_shadow',
	'label'     => __( 'Add shadow', 'silver' ),
	'transport' => silver_customizer_transport(),
	'section'   => 'header_dropdown',
	'default'   => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'slider',
	'settings'  => 'dropdown_nav_size',
	'label'     => __( 'Dropdown Content Size (%)', 'silver' ),
	'section'   => 'header_dropdown',
	'default'   => 100,
	'choices'   => array(
		'min'  => 50,
		'max'  => 200,
		'step' => 1,
	),
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-buttonset',
	'settings'  => 'dropdown_radius',
	'label'     => __( 'Dropdown radius', 'silver' ),
	'section'   => 'header_dropdown',
	'transport' => silver_customizer_transport(),
	'default'   => '0',
	'choices'   => array(
		'0'    => __( '0px', 'silver' ),
		'3px'  => __( '3px', 'silver' ),
		'5px'  => __( '5px', 'silver' ),
		'10px' => __( '10px', 'silver' ),
		'15px' => __( '15px', 'silver' ),
	),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_header_dropdown_default',
	'label'    => '',
	'section'  => 'header_dropdown',
	'default'  => '<div class="options-title-divider">Default Dropdowns</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'dropdown_arrow',
	'label'       => __( 'Add arrow', 'silver' ),
	'description' => __( 'Shows a focusing arrow towards the active menu item.', 'silver' ),
	'transport'   => silver_customizer_transport(),
	'section'     => 'header_dropdown',
	'default'     => 1,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'dropdown_style',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Link Style', 'silver' ),
	'section'   => 'header_dropdown',
	'default'   => 'default',
	'choices'   => array(
		'simple'  => $image_url . 'dropdown-style-1.svg',
		'default' => $image_url . 'dropdown-style-2.svg',
		'bold'    => $image_url . 'dropdown-style-3.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-buttonset',
	'settings'  => 'dropdown_text',
	'label'     => __( 'Text Color', 'silver' ),
	'section'   => 'header_dropdown',
	'transport' => silver_customizer_transport(),
	'default'   => 'light',
	'choices'   => array(
		'light' => __( 'Dark', 'silver' ),
		'dark'  => __( 'Light', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-buttonset',
	'settings'  => 'dropdown_text_style',
	'transport' => silver_customizer_transport(),
	'label'     => __( 'Text Style', 'silver' ),
	'section'   => 'header_dropdown',
	'default'   => 'simple',
	'choices'   => array(
		'simple'    => __( 'Simple', 'silver' ),
		'uppercase' => __( 'UPPERCASE', 'silver' ),
	),
) );

function silver_refresh_dropdown_partials( WP_Customize_Manager $wp_customize ) {

	// Abort if selective refresh is not available.
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'refresh_css_dropdown', array(
		'selector'            => 'head > style#custom-css',
		'container_inclusive' => true,
		'settings'            => array(
			'dropdown_border_enabled',
			'dropdown_border',
		),
		'render_callback'     => function () {
			silver_custom_css();
		},
	) );

}

add_action( 'customize_register', 'silver_refresh_dropdown_partials' );
