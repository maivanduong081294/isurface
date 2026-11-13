<?php

Silver_Option::add_section( 'notifications', array(
	'title'    => __( 'Notifications', 'silver' ),
	'priority' => 160,
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'cookie_notice',
	'section'  => 'notifications',
	'label'    => esc_html__( 'Enable cookie notice', 'silver' ),
	'default'  => false,
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'textarea',
	'settings'    => 'cookie_notice_text',
	'section'     => 'notifications',
	'transport'   => silver_customizer_transport(),
	'label'       => esc_html__( 'Custom cookie text', 'silver' ),
	'description' => esc_html__( 'Add any HTML or shortcode here...', 'silver' ),
	'active_callback' => array(
		array(
			'setting'  => 'cookie_notice',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'     => '',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'select',
	'settings'    => 'privacy_policy_page',
	'section'     => 'notifications',
	'label'       => esc_html__( 'Privacy policy page', 'silver' ),
	'description' => esc_html__( 'Show a button linked to the cookie policy page.', 'silver' ),
	'active_callback' => array(
		array(
			'setting'  => 'cookie_notice',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'     => false,
	'choices'     => $list_pages_by_id,
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'select',
	'settings'  => 'cookie_notice_button_style',
	'section'   => 'notifications',
	'transport' => silver_customizer_transport(),
	'label'     => esc_html__( 'Button style', 'silver' ),
	'choices'   => $button_styles,
	'active_callback' => array(
		array(
			'setting'  => 'cookie_notice',
			'operator' => '===',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'cookie_notice_text_color',
	'section'   => 'notifications',
	'transport' => silver_customizer_transport(),
	'label'     => esc_html__( 'Text color', 'silver' ),
	'active_callback' => array(
		array(
			'setting'  => 'cookie_notice',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'   => 'light',
	'choices'   => array(
		'dark'  => $image_url . 'text-light.svg',
		'light' => $image_url . 'text-dark.svg',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'alpha'     => true,
	'settings'  => 'cookie_notice_bg_color',
	'section'   => 'notifications',
	'label'     => esc_html__( 'Background color', 'silver' ),
	'active_callback' => array(
		array(
			'setting'  => 'cookie_notice',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'   => '',
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'text',
	'settings'    => 'cookie_notice_version',
	'section'     => 'notifications',
	'label'       => esc_html__( 'Version', 'silver' ),
	'description' => esc_html__( 'Increase the version to reopen the notice to visitors that have accepted before, after making changes to it.', 'silver' ),
	'active_callback' => array(
		array(
			'setting'  => 'cookie_notice',
			'operator' => '===',
			'value'    => true,
		),
	),
	'default'     => '1',
) );

function silver_refresh_cookies_partials( WP_Customize_Manager $wp_customize ) {

	// Abort if selective refresh is not available.
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'refresh_css_cookies', array(
		'selector'        => 'head > style#custom-css',
		'settings'        => array( 'cookie_notice_bg_color' ),
		'render_callback' => function () {
			silver_custom_css();
		},
	) );

	$wp_customize->selective_refresh->add_partial( 'cookies-text', array(
		'selector'        => '.silver-cookies__text',
		'settings'        => array( 'cookie_notice_text' ),
		'render_callback' => function () {
			return get_theme_mod( 'cookie_notice_text' )
				? do_shortcode( get_theme_mod( 'cookie_notice_text' ) )
				: __( 'This site uses cookies to offer you a better browsing experience. By browsing this website, you agree to our use of cookies.', 'silver' );
		},
	) );
}

add_action( 'customize_register', 'silver_refresh_cookies_partials' );
