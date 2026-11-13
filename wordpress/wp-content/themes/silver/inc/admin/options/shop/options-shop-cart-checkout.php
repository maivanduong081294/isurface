<?php

// CART

Silver_Option::add_section( 'cart-checkout', array(
	'title'       => __( 'Cart', 'silver' ),
	'panel' => 'woocommerce',
  'priority'    => 1000
) );


Silver_Option::add_field( 'option', array(
  'type'        => 'radio-buttonset',
  'settings'     => 'cart_layout',
  'label'       => __( 'Cart layout', 'silver' ),
  'section'     => 'cart-checkout',
  'default'     => '',
  'choices'     => array(
    '' => __( 'Default', 'silver' ),
    'simple' => __( 'Simple', 'silver' ),
    'focused' => __( 'Focused', 'silver' ),
  ),
));


Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'cart_sticky_sidebar',
  'label'       => __( 'Sticky sidebar', 'silver' ),
  'section'     => 'cart-checkout',
  'default' => 0
));

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'cart_sticky_sidebar_mode',
	'label'           => __( 'Sticky mode', 'silver' ),
	'section'         => 'cart-checkout',
	'default'         => '',
	'choices'         => array(
		''           => __( 'CSS (native)', 'silver' ),
		'javascript' => __( 'JavaScript (enhanced)', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'cart_sticky_sidebar',
			'operator' => '==',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'cart_auto_refresh',
	'label'    => __( 'Auto update on quantity change', 'silver' ),
	'section'  => 'cart-checkout',
	'default'  => 0,
) );


Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'cart_boxed_shipping_labels',
  'label'       => __( 'Boxed Shipping labels', 'silver' ),
  'section'     => 'cart-checkout',
  'default' => 0
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'cart_estimate_text',
  'label'       => __( 'Show shipping estimate destination', 'silver' ),
  'section'     => 'cart-checkout',
  'default' => 1
));

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'cart_shipping',
	'label'    => esc_html__( 'Show free shipping', 'silver' ),
	'section'  => 'cart-checkout',
	'default'  => 0,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'cart_steps_title',
	'label'    => '',
	'section'  => 'cart-checkout',
	'default'  => '<div class="options-title-divider">Steps</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'cart_steps_size',
	'label'    => __( 'Steps size', 'silver' ),
	'section'  => 'cart-checkout',
	'default'  => 'h2',
	'choices'  => array(
		'h2' => __( 'Default', 'silver' ),
		'h3' => __( 'Small', 'silver' ),
		'h4' => __( 'Smaller', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'cart_steps_case',
	'label'    => esc_attr__( 'Steps letter case', 'silver' ),
	'section'  => 'cart-checkout',
	'default'  => 'uppercase',
	'choices'  => array(
		'uppercase' => 'UPPERCASE',
		'none'      => 'Normal',
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'cart_steps_numbers',
	'label'    => __( 'Step numbers', 'silver' ),
	'section'  => 'cart-checkout',
	'default'  => 0,
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'cart_coupon_title',
	'label'    => '',
	'section'  => 'cart-checkout',
	'default'  => '<div class="options-title-divider">Coupon</div>',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'select',
	'settings' => 'cart_coupon_position',
	'label'    => esc_html__( 'Position', 'silver' ),
	'section'  => 'cart-checkout',
	'default'  => 'sidebar',
	'choices'  => array(
		'sidebar' => esc_html__( 'In Sidebar', 'silver' ),
		'bottom'  => esc_html__( 'Below products', 'silver' ),
	),
) );

Silver_Option::add_field( '', array(
	'type'     => 'custom',
	'settings' => 'html_cart_title',
	'label'    => '',
	'section'  => 'cart-checkout',
	'default'  => '<div class="options-title-divider">Custom Content</div>',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'textarea',
	'settings'     => 'html_cart_sidebar',
	'transport' => silver_customizer_transport(),
	'label'       => __( 'Cart Sidebar content', 'silver' ),
	'help'        => __( 'Enter HTML that will show on bottom of cart sidebar' ),
	'section'     => 'cart-checkout',
	'default'     => '',
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'textarea',
	'settings'     => 'html_cart_footer',
	'transport' => silver_customizer_transport(),
	'label'       => __( 'After Cart content', 'silver' ),
	'help'        => __( 'Enter HTML or Shortcodes that will show after cart here.' ),
	'section'     => 'cart-checkout',
	'default'     => '',
));


// CHECKOUT

Silver_Option::add_field( 'option', array(
  'type'        => 'radio-buttonset',
  'settings'     => 'checkout_layout',
  'priority' => 1,
  'label'       => __( 'Checkout layout', 'silver' ),
  'section'     => 'woocommerce_checkout',
  'default'     => '',
  'choices'     => array(
    '' => __( 'Default', 'silver' ),
    'simple' => __( 'Simple', 'silver' ),
    'focused' => __( 'Focused', 'silver' ),
  ),
));


if ( is_nextend_facebook_login() || is_nextend_google_login() ) {
	Silver_Option::add_field( 'option',  array(
		'type'        => 'checkbox',
		'settings'     => 'facebook_login_checkout',
		'label'       => __( 'Social Login Buttons', 'silver' ),
		'section'     => 'woocommerce_checkout',
		'default' => 0
	));
}

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'checkout_terms_and_conditions',
	'label'    => __( 'Terms and conditions link style', 'silver' ),
	'section'  => 'woocommerce_checkout',
	'default'  => '',
	'choices'  => array(
		''         => __( 'Default', 'silver' ),
		'tab'      => __( 'New Tab', 'silver' ),
		'lightbox' => __( 'Lightbox', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'checkbox',
	'settings'        => 'terms_and_conditions_lightbox_buttons',
	'transport'       => silver_customizer_transport(),
	'label'           => __( 'Terms and conditions "Agree" button', 'silver' ),
	'section'         => 'woocommerce_checkout',
	'default'         => 1,
	'active_callback' => array(
		array(
			'setting'  => 'checkout_terms_and_conditions',
			'operator' => '==',
			'value'    => 'lightbox',
		),
	),
) );

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'checkout_floating_labels',
  'label'       => __( 'Floating field labels', 'silver' ),
  'section'     => 'woocommerce_checkout',
  'default' => 0
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'checkout_fields_email_first',
  'label'       => __( 'Move E-mail field to first position', 'silver' ),
  'section'     => 'woocommerce_checkout',
  'default' => 0
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'checkbox',
  'settings'     => 'checkout_sticky_sidebar',
  'label'       => __( 'Sticky sidebar', 'silver' ),
  'section'     => 'woocommerce_checkout',
  'default' => 0
));

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'checkout_sticky_sidebar_mode',
	'label'           => __( 'Sticky mode', 'silver' ),
	'section'         => 'woocommerce_checkout',
	'default'         => '',
	'choices'         => array(
		''           => __( 'CSS (native)', 'silver' ),
		'javascript' => __( 'JavaScript (enhanced)', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'checkout_sticky_sidebar',
			'operator' => '==',
			'value'    => true,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'checkbox',
	'settings' => 'checkout_shipping',
	'label'    => esc_html__( 'Show free shipping', 'silver' ),
	'section'  => 'woocommerce_checkout',
	'default'  => 0,
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'textarea',
	'settings'     => 'html_checkout_sidebar',
	'transport' => silver_customizer_transport(),
	'label'       => __( 'Checkout Sidebar content', 'silver' ),
	'help'        => __( 'Enter HTML that will show on bottom of checkout sidebar' ),
	'section'     => 'woocommerce_checkout',
	'default'     => '',
));
