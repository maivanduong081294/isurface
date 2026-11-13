<?php


Silver_Option::add_section( 'payment-icons', array(
	'title'       => __( 'Payments Icons', 'silver' ),
	'description' => 'Note: This is not where you select payment methods. These are graphical icons that show which payment methods your shop supports. You can add these anywhere by using the shortcode [ux_payment_icons]',
	'panel'       => 'woocommerce',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'sortable',
	'settings'  => 'payment_icons',
	'label'     => __( 'Payment Icons', 'silver' ),
	'section'   => 'payment-icons',
	'transport' => silver_customizer_transport(),
	'multiple'  => 99,
	'default'   => array( 'visa', 'paypal', 'stripe', 'mastercard', 'cashondelivery' ),
	'choices'   => silver_get_payment_icons_list(),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'image',
	'settings'  => 'payment_icons_custom',
	'label'     => __( 'Custom Icons (Replace)', 'silver' ),
	'section'   => 'payment-icons',
	'default'   => '',
	'transport' => silver_customizer_transport(),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'multicheck',
	'settings'    => 'payment_icons_placement',
	'label'       => __( 'Placement', 'silver' ),
	'description' => __( 'Select where you want to show the payment icons', 'silver' ),
	'section'     => 'payment-icons',
	'default'     => '',
	'choices'     => array(
		'footer' => __( 'Absolute Footer', 'silver' ),
		'cart'   => __( 'Cart Sidebar', 'silver' ),
	),
) );
