<?php

Silver_Option::add_section( 'header_buttons',
	array(
		'title' => __( 'Buttons', 'silver' ),
		'panel' => 'header',
	)
);

Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_button_1',
		'label'    => __( '', 'silver' ),
		'section'  => 'header_buttons',
		'default'  => '<div class="options-title-divider">Button 1</div>',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'text',
		'settings'  => 'header_button_1',
		'transport' => silver_customizer_transport(),
		'default'   => 'Button 1',
		'label'     => __( 'Text', 'silver' ),
		'section'   => 'header_buttons',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_1_letter_case',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Letter case', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => '',
		'choices'   => array(
			''          => __( 'ABC', 'silver' ),
			'lowercase' => __( 'Abc', 'silver' ),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'text',
		'settings'    => 'header_button_1_link',
		'transport'   => silver_customizer_transport(),
		'default'     => '',
		'label'       => __( 'Link', 'silver' ),
		'section'     => 'header_buttons',
		'description' => $smart_links,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'select',
		'settings'  => 'header_button_1_link_target',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Target', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => '_self',
		'choices'   => array(
			'_self'  => __( 'Same window', 'silver' ),
			'_blank' => __( 'New window', 'silver' ),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'text',
		'settings'    => 'header_button_1_link_rel',
		'transport'   => silver_customizer_transport(),
		'default'     => '',
		'label'       => __( 'Rel', 'silver' ),
		'section'     => 'header_buttons',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'text',
		'settings'  => 'header_button_1_radius',
		'transport' => silver_customizer_transport(),
		'default'   => '99px',
		'label'     => __( 'Radius', 'silver' ),
		'section'   => 'header_buttons',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'select',
		'settings'  => 'header_button_1_icon',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Icon', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => '',
		'choices'   => require get_template_directory() . '/inc/builder/shortcodes/values/icons.php',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'            => 'radio-buttonset',
		'settings'        => 'header_button_1_icon_position',
		'transport'       => silver_customizer_transport(),
		'label'           => __( 'Icon position', 'silver' ),
		'section'         => 'header_buttons',
		'default'         => '',
		'choices'         => array(
			'left' => __( 'Left', 'silver' ),
			''     => __( 'Right', 'silver' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'header_button_1_icon',
				'operator' => '!=',
				'value'    => '',
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'            => 'radio-buttonset',
		'settings'        => 'header_button_1_icon_visibility',
		'transport'       => silver_customizer_transport(),
		'label'           => __( 'Icon visibility', 'silver' ),
		'section'         => 'header_buttons',
		'default'         => '',
		'choices'         => array(
			''     => __( 'Always visible', 'silver' ),
			'true' => __( 'Visible on hover', 'silver' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'header_button_1_icon',
				'operator' => '!=',
				'value'    => '',
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_1_color',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Color', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 'primary',
		'choices'   => array(
			'plain'     => __( 'Plain', 'silver' ),
			'primary'   => __( 'Primary', 'silver' ),
			'secondary' => __( 'Secondary', 'silver' ),
			'success'   => __( 'Success', 'silver' ),
			'alert'     => __( 'Alert', 'silver' ),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_1_style',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Style', 'silver' ),
		'section'   => 'header_buttons',
		'choices'   => $button_styles,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_1_size',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Size', 'silver' ),
		'section'   => 'header_buttons',
		'choices'   => $nav_sizes,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'slider',
		'settings'  => 'header_button_1_depth',
		'label'     => __( 'Depth', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 0,
		'choices'   => array(
			'min'  => 0,
			'max'  => 5,
			'step' => 1,
		),
		'transport' => silver_customizer_transport(),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'slider',
		'settings'  => 'header_button_1_depth_hover',
		'label'     => __( 'Depth :hover', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 0,
		'choices'   => array(
			'min'  => 0,
			'max'  => 5,
			'step' => 1,
		),
		'transport' => silver_customizer_transport(),
	)
);

Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_button_2',
		'label'    => __( '', 'silver' ),
		'section'  => 'header_buttons',
		'default'  => '<div class="options-title-divider">Button 2</div>',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'text',
		'settings'  => 'header_button_2',
		'transport' => silver_customizer_transport(),
		'default'   => 'Button 2',
		'label'     => __( 'Text', 'silver' ),
		'section'   => 'header_buttons',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_2_letter_case',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Letter case', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => '',
		'choices'   => array(
			''          => __( 'ABC', 'silver' ),
			'lowercase' => __( 'Abc', 'silver' ),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'text',
		'settings'    => 'header_button_2_link',
		'transport'   => silver_customizer_transport(),
		'default'     => '',
		'label'       => __( 'Link', 'silver' ),
		'section'     => 'header_buttons',
		'description' => $smart_links,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'select',
		'settings'  => 'header_button_2_link_target',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Target', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => '_self',
		'choices'   => array(
			'_self'  => __( 'Same window', 'silver' ),
			'_blank' => __( 'New window', 'silver' ),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'text',
		'settings'    => 'header_button_2_link_rel',
		'transport'   => silver_customizer_transport(),
		'default'     => '',
		'label'       => __( 'Rel', 'silver' ),
		'section'     => 'header_buttons',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'text',
		'settings'  => 'header_button_2_radius',
		'transport' => silver_customizer_transport(),
		'default'   => '99px',
		'label'     => __( 'Radius', 'silver' ),
		'section'   => 'header_buttons',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'select',
		'settings'  => 'header_button_2_icon',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Icon', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => '',
		'choices'   => require get_template_directory() . '/inc/builder/shortcodes/values/icons.php',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'            => 'radio-buttonset',
		'settings'        => 'header_button_2_icon_position',
		'transport'       => silver_customizer_transport(),
		'label'           => __( 'Icon position', 'silver' ),
		'section'         => 'header_buttons',
		'default'         => '',
		'choices'         => array(
			'left' => __( 'Left', 'silver' ),
			''     => __( 'Right', 'silver' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'header_button_2_icon',
				'operator' => '!=',
				'value'    => '',
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'            => 'radio-buttonset',
		'settings'        => 'header_button_2_icon_visibility',
		'transport'       => silver_customizer_transport(),
		'label'           => __( 'Icon visibility', 'silver' ),
		'section'         => 'header_buttons',
		'default'         => '',
		'choices'         => array(
			''     => __( 'Always visible', 'silver' ),
			'true' => __( 'Visible on hover', 'silver' ),
		),
		'active_callback' => array(
			array(
				'setting'  => 'header_button_2_icon',
				'operator' => '!=',
				'value'    => '',
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_2_color',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Color', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 'secondary',
		'choices'   => array(
			'plain'     => __( 'Plain', 'silver' ),
			'primary'   => __( 'Primary', 'silver' ),
			'secondary' => __( 'Secondary', 'silver' ),
			'success'   => __( 'Success', 'silver' ),
			'alert'     => __( 'Alert', 'silver' ),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_2_style',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Style', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 'outline',
		'choices'   => $button_styles,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'header_button_2_size',
		'transport' => silver_customizer_transport(),
		'label'     => __( 'Size', 'silver' ),
		'section'   => 'header_buttons',
		'choices'   => $nav_sizes,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'slider',
		'settings'  => 'header_button_2_depth',
		'label'     => __( 'Depth', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 0,
		'choices'   => array(
			'min'  => 0,
			'max'  => 5,
			'step' => 1,
		),
		'transport' => silver_customizer_transport(),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'slider',
		'settings'  => 'header_button_2_depth_hover',
		'label'     => __( 'Depth :hover', 'silver' ),
		'section'   => 'header_buttons',
		'default'   => 0,
		'choices'   => array(
			'min'  => 0,
			'max'  => 5,
			'step' => 1,
		),
		'transport' => silver_customizer_transport(),
	)
);

function silver_refresh_header_buttons_partials( WP_Customize_Manager $wp_customize ) {
	// Abort if selective refresh is not available.
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial( 'header-button-2',
		array(
			'selector'            => '.header-button-2',
			'container_inclusive' => true,
			'settings'            => array(
				'header_button_2',
				'header_button_2_radius',
				'header_button_2_link',
				'header_button_2_style',
				'header_button_2_color',
				'header_button_2_size',
				'header_button_2_depth',
				'header_button_2_depth_hover',
				'header_button_2_icon',
				'header_button_2_icon_position',
				'header_button_2_icon_visibility',
				'header_button_2_letter_case',
			),
			'render_callback'     => function () {
				get_template_part( 'template-parts/header/partials/element', 'button-2' );
			},
		)
	);

	$wp_customize->selective_refresh->add_partial( 'header-button-1',
		array(
			'selector'            => '.header-button-1',
			'container_inclusive' => true,
			'settings'            => array(
				'header_button_1',
				'header_button_1_radius',
				'header_button_1_link',
				'header_button_1_style',
				'header_button_1_color',
				'header_button_1_size',
				'header_button_1_depth',
				'header_button_1_depth_hover',
				'header_button_1_icon',
				'header_button_1_icon_position',
				'header_button_1_icon_visibility',
				'header_button_1_letter_case',
			),
			'render_callback'     => function () {
				get_template_part( 'template-parts/header/partials/element', 'button-1' );
			},
		)
	);

}

add_action( 'customize_register', 'silver_refresh_header_buttons_partials' );
