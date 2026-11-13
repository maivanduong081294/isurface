<?php
/**
 * Header contact options.
 *
 * @package Silver
 */

Silver_Option::add_section( 'header_contact', array(
	'title' => esc_html__( 'Contact', 'silver' ),
	'panel' => 'header',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'radio-buttonset',
	'settings'  => 'contact_style',
	'label'     => esc_html__( 'Icon Style', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => 'left',
	'choices'   => array(
		'left'  => esc_html__( 'Icons Left', 'silver' ),
		'icons' => esc_html__( 'Icons Only', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_icon_size',
	'label'     => esc_html__( 'Icon Size', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '16px',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_location',
	'label'     => esc_html__( 'Location', 'silver' ),
	'help'      => esc_html__( 'Type in the location of your place or shop. It will open in a new window on Google Maps', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_location_label',
	'label'     => esc_html__( 'Location label', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_email',
	'label'     => esc_html__( 'E-mail', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => 'youremail@gmail.com',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_email_label',
	'label'     => esc_html__( 'E-mail label', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_hours',
	'label'     => esc_html__( 'Open Hours', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '08:00 - 17:00',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'textarea',
	'settings'  => 'contact_hours_details',
	'label'     => esc_html__( 'Open Hours - Details', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_phone',
	'label'     => esc_html__( 'Phone', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '+47 900 99 000',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_whatsapp',
	'label'     => esc_html__( 'WhatsApp', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'tooltip'   => 'Use a full phone number only in international format. Omit any plus signs, zeroes, brackets, or dashes when adding the phone number.',
	'default'   => '',
) );

Silver_Option::add_field( 'option', array(
	'type'      => 'text',
	'settings'  => 'contact_whatsapp_label',
	'label'     => esc_html__( 'WhatsApp Label', 'silver' ),
	'section'   => 'header_contact',
	'transport' => silver_customizer_transport(),
	'default'   => '',
) );

/**
 * Refresh header contact partials.
 *
 * @param WP_Customize_Manager $wp_customize Customize Manager object.
 *
 * @return void
 */
function silver_refresh_header_contact_partials( WP_Customize_Manager $wp_customize ) {

	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'header-contact', array(
		'selector'            => '.header-contact-wrapper',
		'container_inclusive' => true,
		'settings'            => array(
			'contact_style',
			'contact_icon_size',
			'contact_phone',
			'contact_whatsapp',
			'contact_whatsapp_label',
			'contact_email',
			'contact_email_label',
			'contact_location',
			'contact_location_label',
			'contact_hours',
			'contact_hours_details',
		),
		'render_callback'     => function () {
			get_template_part( 'template-parts/header/partials/element', 'contact' );
		},
	) );
}

add_action( 'customize_register', 'silver_refresh_header_contact_partials' );
