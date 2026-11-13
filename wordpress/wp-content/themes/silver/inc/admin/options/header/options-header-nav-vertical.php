<?php
/**
 * Options for nav vertical element.
 *
 * @package silver
 */

Silver_Option::add_section( 'header_nav_vertical', array(
	'title' => __( 'Vertical Menu', 'silver' ),
	'panel' => 'header',
) );

/**
 * Add options.
 */
function silver_customizer_header_nav_vertical_options() {
	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_header_nav_vertical_layout',
		'label'    => '',
		'section'  => 'header_nav_vertical',
		'default'  => '<div class="options-title-divider">Opener</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'radio-image',
		'settings'  => 'header_nav_vertical_icon_style',
		'label'     => __( 'Icon', 'silver' ),
		'section'   => 'header_nav_vertical',
		'default'   => 'plain',
		'transport' => silver_customizer_transport(),
		'choices'   => array(
			''      => silver_customizer_images_uri() . '/disabled.svg',
			'plain' => silver_customizer_images_uri() . '/nav-icon-plain.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'slider',
		'settings'  => 'header_nav_vertical_height',
		'label'     => __( 'Height', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '50',
		'choices'   => array(
			'min'  => '10',
			'max'  => '500',
			'step' => '1',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'slider',
		'settings'  => 'header_nav_vertical_width',
		'label'     => __( 'Width', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '250',
		'choices'   => array(
			'min'  => '10',
			'max'  => '500',
			'step' => '1',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'text',
		'settings'  => 'header_nav_vertical_tagline',
		'label'     => __( 'Tag line', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'text',
		'settings'  => 'header_nav_vertical_text',
		'label'     => __( 'Text', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'radio-image',
		'settings'  => 'header_nav_vertical_text_color',
		'label'     => __( 'Text base color', 'silver' ),
		'section'   => 'header_nav_vertical',
		'default'   => 'dark',
		'transport' => silver_customizer_transport(),
		'choices'   => array(
			'dark'  => silver_customizer_images_uri() . '/text-light.svg',
			'light' => silver_customizer_images_uri() . '/text-dark.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'header_nav_vertical_color',
		'label'     => __( 'Color', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'header_nav_vertical_bg_color',
		'label'     => __( 'Background color', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_header_nav_vertical_fly_out',
		'label'    => '',
		'section'  => 'header_nav_vertical',
		'default'  => '<div class="options-title-divider">Fly out</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'checkbox',
		'settings'  => 'header_nav_vertical_fly_out_frontpage',
		'label'     => __( 'Keep open on front page', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'checkbox',
		'settings'  => 'header_nav_vertical_fly_out_shadow',
		'label'     => __( 'Add shadow', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'slider',
		'settings'  => 'header_nav_vertical_fly_out_width',
		'label'     => __( 'Width', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '250',
		'choices'   => array(
			'min'  => '10',
			'max'  => '500',
			'step' => '1',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'header_nav_vertical_fly_out_bg_color',
		'label'     => __( 'Background color', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( '', array(
		'type'     => 'custom',
		'settings' => 'custom_title_header_nav_vertical_fly_out_navigation',
		'label'    => '',
		'section'  => 'header_nav_vertical',
		'default'  => '<div class="options-title-divider">Fly out navigation</div>',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'checkbox',
		'settings'  => 'header_nav_vertical_fly_out_nav_divider',
		'label'     => __( 'Divider', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => 1,
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'slider',
		'settings'  => 'header_nav_vertical_fly_out_nav_height',
		'label'     => __( 'Nav height', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => 0,
		'choices'   => array(
			'min'  => 0,
			'max'  => 200,
			'step' => 1,
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'radio-image',
		'settings'  => 'header_nav_vertical_fly_out_text_color',
		'label'     => __( 'Text base color', 'silver' ),
		'section'   => 'header_nav_vertical',
		'default'   => 'light',
		'transport' => silver_customizer_transport(),
		'choices'   => array(
			'dark'  => silver_customizer_images_uri() . '/text-light.svg',
			'light' => silver_customizer_images_uri() . '/text-dark.svg',
		),
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'header_nav_vertical_fly_out_nav_color',
		'label'     => __( 'Nav color', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'header_nav_vertical_fly_out_nav_color_hover',
		'label'     => __( 'Nav color :hover', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );

	Silver_Option::add_field( 'option', array(
		'type'      => 'color-alpha',
		'alpha'     => true,
		'settings'  => 'header_nav_vertical_fly_out_nav_bg_color_hover',
		'label'     => __( 'Nav background color :hover', 'silver' ),
		'section'   => 'header_nav_vertical',
		'transport' => silver_customizer_transport(),
		'default'   => '',
	) );
}

add_action( 'init', 'silver_customizer_header_nav_vertical_options' );

/**
 * Refresh partials.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function silver_refresh_header_nav_vertical_partials( WP_Customize_Manager $wp_customize ) {
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'header-vertical-menu', array(
		'selector'            => '.header-vertical-menu',
		'container_inclusive' => true,
		'settings'            => array(
			'header_nav_vertical_icon_style',
			'header_nav_vertical_tagline',
			'header_nav_vertical_text',
		),
		'render_callback'     => function () {
			get_template_part( 'template-parts/header/partials/element', 'nav-vertical' );
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'header-vertical-menu-refresh-css', array(
		'selector'        => 'head > style#custom-css',
		'settings'        => array(
			'header_nav_vertical_color',
			'header_nav_vertical_bg_color',
			'header_nav_vertical_fly_out_bg_color',
			'header_nav_vertical_fly_out_nav_divider',
			'header_nav_vertical_fly_out_nav_height',
			'header_nav_vertical_fly_out_nav_color',
			'header_nav_vertical_fly_out_nav_color_hover',
			'header_nav_vertical_fly_out_nav_bg_color_hover',
		),
		'render_callback' => function () {
			silver_custom_css();
		},
	) );
}

add_action( 'customize_register', 'silver_refresh_header_nav_vertical_partials' );
