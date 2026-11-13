<?php
/**
 * Newsletter Element
 */

// Add section immediately to keep sort order.
Silver_Option::add_section( 'header_newsletter',
	array(
		'title' => __( 'Newsletter', 'silver' ),
		'panel' => 'header',
	)
);

function silver_customizer_header_newsletter_options() {
	Silver_Option::add_field( '',
		array(
			'type'     => 'custom',
			'settings' => 'custom_title_header_newsletter_layout',
			'label'    => __( '', 'silver' ),
			'section'  => 'header_newsletter',
			'default'  => '<div class="options-title-divider">Layout</div>',
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'      => 'radio-image',
			'settings'  => 'newsletter_icon_style',
			'label'     => __( 'Icon Style', 'silver' ),
			'section'   => 'header_newsletter',
			'transport' => silver_customizer_transport(),
			'default'   => 'plain',
			'choices'   => array(
				''              => silver_customizer_images_uri() . '/disabled.svg',
				'plain'         => silver_customizer_images_uri() . '/account-icon-plain.svg',
				'fill'          => silver_customizer_images_uri() . '/account-icon-fill.svg',
				'fill-round'    => silver_customizer_images_uri() . '/account-icon-fill-round.svg',
				'outline'       => silver_customizer_images_uri() . '/account-icon-outline.svg',
				'outline-round' => silver_customizer_images_uri() . '/account-icon-outline-round.svg',
			),
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'      => 'text',
			'settings'  => 'header_newsletter_label',
			'label'     => __( 'Label', 'silver' ),
			'section'   => 'header_newsletter',
			'transport' => silver_customizer_transport(),
			'default'   => 'Newsletter',
		)
	);

	Silver_Option::add_field( 'option', array(
		'type'      => 'text',
		'settings'  => 'header_newsletter_title',
		'label'     => __( 'Title', 'silver' ),
		'section'   => 'header_newsletter',
		'transport' => silver_customizer_transport(),
		'default'   => 'Sign up for Newsletter',
	) );

	Silver_Option::add_field( 'option',
		array(
			'type'        => 'select',
			'settings'    => 'header_newsletter_block',
			'label'       => __( 'Newsletter Block', 'silver' ),
			'description' => __( 'Replace newsletter pop-up content with a custom Block that can be edited in UX Builder.' ),
			'section'     => 'header_newsletter',
			'default'     => false,
			'choices'     => silver_customizer_blocks(),
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'              => 'text',
			'settings'          => 'header_newsletter_sub_title',
			'active_callback'   => array(
				array(
					'setting'  => 'header_newsletter_block',
					'operator' => '==',
					'value'    => false,
				),
			),
			'label'             => __( 'Sub Title', 'silver' ),
			'section'           => 'header_newsletter',
			'transport'         => silver_customizer_transport(),
			'sanitize_callback' => 'silver_custom_sanitize',
			'default'           => 'Signup for our newsletter to get notified about sales and new products. Add any text here or remove it.',
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'              => 'text',
			'settings'          => 'header_newsletter_shortcode',
			'active_callback'   => array(
				array(
					'setting'  => 'header_newsletter_block',
					'operator' => '==',
					'value'    => false,
				),
			),
			'label'             => __( 'Form Shortcode', 'silver' ),
			'description'       => __( 'Insert any form shortcode here.', 'silver' ),
			'section'           => 'header_newsletter',
			'sanitize_callback' => 'silver_custom_sanitize',
			'transport'         => silver_customizer_transport(),
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'            => 'image',
			'settings'        => 'header_newsletter_bg',
			'active_callback' => array(
				array(
					'setting'  => 'header_newsletter_block',
					'operator' => '==',
					'value'    => false,
				),
			),
			'label'           => __( 'Background Image', 'silver' ),
			'section'         => 'header_newsletter',
			'transport'       => silver_customizer_transport(),
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'            => 'text',
			'settings'        => 'header_newsletter_height',
			'active_callback' => array(
				array(
					'setting'  => 'header_newsletter_block',
					'operator' => '==',
					'value'    => false,
				),
			),
			'label'           => __( 'Height', 'silver' ),
			'section'         => 'header_newsletter',
			'default'         => '500px',
			'transport'       => silver_customizer_transport(),
		)
	);

	Silver_Option::add_field( '',
		array(
			'type'     => 'custom',
			'settings' => 'custom_title_header_newsletter_behavior',
			'label'    => __( '', 'silver' ),
			'section'  => 'header_newsletter',
			'default'  => '<div class="options-title-divider">Behavior</div>',
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'      => 'checkbox',
			'settings'  => 'header_newsletter_auto_open',
			'label'     => __( 'Auto Open', 'silver' ),
			'section'   => 'header_newsletter',
			'transport' => silver_customizer_transport(),
			'default'   => false,
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'            => 'slider',
			'settings'        => 'header_newsletter_auto_timer',
			'active_callback' => array(
				array(
					'setting'  => 'header_newsletter_auto_open',
					'operator' => '==',
					'value'    => true,
				),
			),
			'label'           => __( 'Auto Timer', 'silver' ),
			'description'     => __( 'In milliseconds (1000ms = 1sec).', 'silver' ),
			'section'         => 'header_newsletter',
			'transport'       => silver_customizer_transport(),
			'default'         => 3000,
			'choices'         => array(
				'min'  => 1000,
				'max'  => 300000,
				'step' => 500,
			),
		)
	);

	Silver_Option::add_field( 'option',
		array(
			'type'            => 'select',
			'settings'        => 'header_newsletter_auto_show',
			'active_callback' => array(
				array(
					'setting'  => 'header_newsletter_auto_open',
					'operator' => '==',
					'value'    => true,
				),
			),
			'label'           => __( 'Auto Show', 'silver' ),
			'section'         => 'header_newsletter',
			'transport'       => silver_customizer_transport(),
			'default'         => 'always',
			'multiple'        => 0,
			'choices'         => array(
				'always' => __( 'Always', 'silver' ),
				'once'   => __( 'Once', 'silver' ),
			),
		)
	);

	Silver_Option::add_field( 'option', array(
		'type'            => 'text',
		'settings'        => 'header_newsletter_version',
		'active_callback' => array(
			array(
				'setting'  => 'header_newsletter_auto_open',
				'operator' => '==',
				'value'    => true,
			),
		),
		'label'           => __( 'Version', 'silver' ),
		'description'     => __( 'Increase the version to reopen a "show once" configured newsletter popup to visitors after making changes to it.', 'silver' ),
		'section'         => 'header_newsletter',
		'transport'       => silver_customizer_transport(),
		'default'         => '1',
	) );
}
add_action( 'init', 'silver_customizer_header_newsletter_options' );

function silver_refresh_header_newsletter_partials( WP_Customize_Manager $wp_customize ) {

	// Abort if selective refresh is not available.
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'header-newsletter',
		array(
			'selector'            => '.header-newsletter-item',
			'container_inclusive' => true,
			'settings'            => array(
				'header_newsletter_height',
				'header_newsletter_bg',
				'header_newsletter_sub_title',
				'header_newsletter_label',
				'header_newsletter_shortcode',
				'newsletter_icon_style',
				'header_newsletter_title',
			),
			'render_callback'     => function () {
				get_template_part( 'template-parts/header/partials/element', 'newsletter' );
			},
		)
	);
}

add_action( 'customize_register', 'silver_refresh_header_newsletter_partials' );
