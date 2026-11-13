<?php

global $wc;

Silver_Option::add_section( 'fl-my-account', array(
	'title'       => __( 'My Account', 'silver' ),
	'description' => 'Note: Register form/link is only visible for non logged in users and the WooCommerce setting, "Allow customers to create an account on the "My account" page" is enabled.',
	'panel'       => 'woocommerce',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'color-alpha',
	'alpha'     => true,
	'settings'  => 'my_account_title_bg_color',
	'label'     => __( 'Title Background Color', 'silver' ),
	'section'   => 'fl-my-account',
	'default'   => '',
	'transport' => silver_customizer_transport(),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'image',
	'settings'     => 'facebook_login_bg',
	'label'       => __( 'Title Background Image', 'silver' ),
	'section'     => 'fl-my-account',
	'transport' => silver_customizer_transport(),
	'default'     => ''
));

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'    => 'my_account_title_align',
	'label'       => __( 'Title Align', 'silver' ),
	'description' => __( 'For logged in users only.', 'silver' ),
	'section'     => 'fl-my-account',
	'default'     => 'left',
	'transport'   => silver_customizer_transport(),
	'choices'     => array(
		'left'   => $image_url . 'align-left.svg',
		'center' => $image_url . 'align-center.svg',
		'right'  => $image_url . 'align-right.svg',
	),
));

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-image',
	'settings'  => 'my_account_title_text_color',
	'label'     => __( 'Text color', 'silver' ),
	'section'   => 'fl-my-account',
	'default'   => 'dark',
	'transport' => silver_customizer_transport(),
	'choices'   => array(
		'light' => $image_url . 'text-light.svg',
		'dark'  => $image_url . 'text-dark.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'text',
	'settings'     => 'facebook_login_text',
	'transport' => silver_customizer_transport(),
	'label'       => __( 'Login Text', 'silver' ),
	'description' => __( '', 'silver' ),
	'section'     => 'fl-my-account',
	'sanitize_callback' => 'silver_custom_sanitize',
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'select',
  'settings'     => 'social_login_pos',
  'label'       => __( 'Social Button', 'silver' ),
  'description' => __( 'Change position of Social Login Buttons in lightbox.', 'silver' ),
  'section'     => 'fl-my-account',
  'default'     => 'top',
  'choices'     => array(
    'top' => __( 'Top', 'silver' ),
    'bottom' => __( 'Bottom', 'silver' ),
  ),
));


Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'wc_account_links',
  'label'       => __( 'Enable default WooCommerce Account links in Dropdown and Account Sidebar. You can create a custom my account menu instead if you want.', 'silver' ),
  'section'     => 'fl-my-account',
  'default'     => 1,
));


Silver_Option::add_field( '', array(
  'type'        => 'custom',
  'settings' => 'custom_html_header_account_shortcut',
  'label'       => __( '', 'silver' ),
  'section'     => 'fl-my-account',
  'default'     => '<button style="margin-top:30px; margin-bottom:15px" class="button button-primary" data-to-section="header_account">Header Element →</button>',
) );
