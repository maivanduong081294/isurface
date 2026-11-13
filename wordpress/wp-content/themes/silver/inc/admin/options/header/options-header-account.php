<?php

/*************
 * - Account
 *************/

Silver_Option::add_section( 'header_account', array(
	'title' => __( 'Account', 'silver' ),
	'panel' => 'header',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'account_icon_style',
	'label'       => __( 'Icon Style', 'silver' ),
	'section'     => 'header_account',
	'transport' => silver_customizer_transport(),
	'default'     => '',
	'choices'     => array(
		'' => $image_url . 'disabled.svg',
		'image' => $image_url . 'account-icon-image.svg',
		'plain' => $image_url . 'account-icon-plain.svg',
		'fill' => $image_url . 'account-icon-fill.svg',
		'fill-round' => $image_url . 'account-icon-fill-round.svg',
		'outline' => $image_url . 'account-icon-outline.svg',
		'outline-round' => $image_url . 'account-icon-outline-round.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'header_account_title',
	'label'       => __( 'Show label', 'silver' ),
	'description' => __( 'Display label for logged in users.', 'silver' ),
	'section'     => 'header_account',
	'transport' => silver_customizer_transport(),
	'default'     => 1,
));

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'header_account_username',
	'label'           => __( 'Use username as label', 'silver' ),
	'description'     => __( 'Display username instead of fixed label for logged in users.', 'silver' ),
	'section'         => 'header_account',
	'transport'       => silver_customizer_transport(),
	'active_callback' => array(
		array(
			'setting'  => 'header_account_title',
			'operator' => '==',
			'value'    => true,
		),
	),
	'default'         => 0,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_account_login',
	'label'    => '',
	'section'  => 'header_account',
	'default'  => '<div class="options-title-divider">Login</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'select',
	'settings'  => 'account_login_style',
	'label'     => __( 'Login Style', 'silver' ),
	'section'   => 'header_account',
	'transport' => silver_customizer_transport(),
	'default'   => 'lightbox',
	'choices'   => array(
		'link'     => __( 'Link', 'silver' ),
		'lightbox' => __( 'Lightbox', 'silver' ),
	),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_title_account_register',
	'label'    => '',
	'section'  => 'header_account',
	'default'  => '<div class="options-title-divider">Register</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'checkbox',
	'settings'    => 'header_account_register',
	'label'       => __( 'Show "Register" label', 'silver' ),
	'description' => __( 'Display "Register" for non logged in users.', 'silver' ),
	'section'     => 'header_account',
	'transport'   => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'radio-image',
	'settings'        => 'account_login_lightbox_layout',
	'label'           => __( 'Lightbox layout (register)', 'silver' ),
	'description'     => 'Register form/link is only visible for non logged in users and if the WooCommerce setting, "Allow customers to create an account on the "My account" page" is enabled.',
	'section'         => 'header_account',
	'active_callback' => array(
		array(
			'setting'  => 'account_login_style',
			'operator' => '===',
			'value'    => 'lightbox',
		),
	),
	'default'         => '',
	'choices'         => array(
		''                     => silver_customizer_images_uri() . '/form-login.svg',
		'lightbox-left-panel'  => silver_customizer_images_uri() . '/form-login-lightbox-left-panel.svg',
		'lightbox-right-panel' => silver_customizer_images_uri() . '/form-login-lightbox-right-panel.svg',
	),
) );

Silver_Option::add_field( 'option',
	array(
		'type'            => 'select',
		'settings'        => 'account_login_lightbox_side_panel_block',
		'label'           => __( 'Side panel block', 'silver' ),
		'description'     => __( 'Replace side panel content with a custom Block that can be edited in UX Builder.', 'silver' ),
		'section'         => 'header_account',
		'active_callback' => array(
			array(
				'setting'  => 'account_login_style',
				'operator' => '===',
				'value'    => 'lightbox',
			),
			array(
				'setting'  => 'account_login_lightbox_layout',
				'operator' => '!=',
				'value'    => '',
			),
		),
		'default'         => false,
		'choices'         => silver_customizer_blocks(),
	)
);

Silver_Option::add_field( 'option', array(
	'type'            => 'color',
	'settings'        => 'account_login_lightbox_side_panel_bg_color',
	'label'           => __( 'Side panel background color', 'silver' ),
	'section'         => 'header_account',
	'active_callback' => array(
		array(
			'setting'  => 'account_login_style',
			'operator' => '===',
			'value'    => 'lightbox',
		),
		array(
			'setting'  => 'account_login_lightbox_layout',
			'operator' => '!=',
			'value'    => '',
		),
		array(
			'setting'  => 'account_login_lightbox_side_panel_block',
			'operator' => '==',
			'value'    => false,
		),
	),
	'default'         => Silver_Default::COLOR_PRIMARY,
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'image',
	'settings'        => 'account_login_lightbox_side_panel_bg_image',
	'label'           => __( 'Side panel background image', 'silver' ),
	'section'         => 'header_account',
	'active_callback' => array(
		array(
			'setting'  => 'account_login_style',
			'operator' => '===',
			'value'    => 'lightbox',
		),
		array(
			'setting'  => 'account_login_lightbox_layout',
			'operator' => '!=',
			'value'    => '',
		),
		array(
			'setting'  => 'account_login_lightbox_side_panel_block',
			'operator' => '==',
			'value'    => false,
		),
	),
	'default'         => '',
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'color-alpha',
	'alpha'           => true,
	'settings'        => 'account_login_lightbox_side_panel_bg_overlay',
	'label'           => __( 'Side panel background overlay', 'silver' ),
	'section'         => 'header_account',
	'active_callback' => array(
		array(
			'setting'  => 'account_login_style',
			'operator' => '===',
			'value'    => 'lightbox',
		),
		array(
			'setting'  => 'account_login_lightbox_layout',
			'operator' => '!=',
			'value'    => '',
		),
		array(
			'setting'  => 'account_login_lightbox_side_panel_block',
			'operator' => '==',
			'value'    => false,
		),
	),
	'default'         => '',
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'custom_html_account_shortcut',
	'label'    => __( '', 'silver' ),
	'section'  => 'header_account',
	'default'  => '<button style="margin-top:30px; margin-bottom:15px" class="button button-primary" data-to-section="fl-my-account">Account Page Layout →</button>',
) );

function silver_refresh_header_account_partials( WP_Customize_Manager $wp_customize ) {

	if ( ! isset( $wp_customize->selective_refresh ) ) {
	      return;
	 }

	// Account
	$wp_customize->selective_refresh->add_partial( 'header-account', array(
	    'selector' => '.header .account-item',
	    'container_inclusive' => true,
	    'settings' => array('header_account_register','account_login_style','account_icon_style','header_account_title','header_account_username'),
	    'render_callback' => function() {
	        get_template_part('template-parts/header/partials/element','account');
	    },
	) );
}
add_action( 'customize_register', 'silver_refresh_header_account_partials' );
