<?php

Silver_Option::add_section( 'type',
	array(
		'title' => __( 'Typography', 'silver' ),
		'panel' => 'style',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'     => 'checkbox',
		'settings' => 'disable_fonts',
		'label'    => __( 'Disable fonts', 'silver' ),
		'section'  => 'type',
		'default'  => 0,
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'checkbox',
		'settings'    => 'google_fonts_cdn',
		'label'       => __( 'Load Google Fonts from CDN', 'silver' ),
		'description' => '<strong>Enabling this is not recommended due to GDPR regulations!</strong> <a href="https://docs.uxthemes.com/article/415-google-fonts" target="_blank" rel="noopener">Learn more</a>',
		'section'     => 'type',
		'default'     => 0,
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option', array(
	'type'            => 'custom',
	'settings'        => 'clear_typography_cache',
	'tooltip'         => 'Clear typography cache and locally downloaded font files.',
	'section'         => 'type',
	'default'         => '<button id="silver-customizer-clear-typography-cache" class="button-primary button" title="Clear typography cache">Clear typography cache</button>',
	'active_callback' => array(
		array(
			'setting'  => 'disable_fonts',
			'operator' => '==',
			'value'    => false,
		),
		array(
			'setting'  => 'google_fonts_cdn',
			'operator' => '==',
			'value'    => false,
		),
	),
) );

Silver_Option::add_field( 'option',
	array(
		'type'        => 'select',
		'settings'    => 'googlefonts_font_display',
		'label'       => __( 'Google Fonts font-display type', 'silver' ),
		'description' => 'Choose how Google Fonts will be loaded.',
		'tooltip'     => '<ul>
								<li><span style="font-weight: bold">auto</span> font display strategy is defined by the user agent</li>
								<li><span style="font-weight: bold">block</span> flash of invisible text until the font loads</li>
								<li><span style="font-weight: bold">swap</span> fallback font until custom font loads (flash of unstyled text)</li>
								<li><span style="font-weight: bold">fallback</span> between block and swap, invisible text for a short time</li>
								<li><span style="font-weight: bold">optional</span> like fallback, but the browser can decide to not use the custom font</li>
							</ul>',
		'default'     => 'swap',
		'section'         => 'type',
		'active_callback' => array(
			array(
				'setting'  => 'google_fonts_cdn',
				'operator' => '==',
				'value'    => true,
			),
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
		'choices'     => array(
			'auto'     => __( 'Auto', 'silver' ),
			'block'    => __( 'Block', 'silver' ),
			'swap'     => __( 'Swap', 'silver' ),
			'fallback' => __( 'Fallback', 'silver' ),
			'optional' => __( 'Optional', 'silver' ),
		),
	)
);

Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_type_headings',
		'label'    => __( '', 'silver' ),
		'section'  => 'type',
		'default'  => '<div class="options-title-divider">Headlines</div>',
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'typography',
		'settings'    => 'type_headings',
		'description' => 'This is the font for all H1, H2, H3, H5, H6 titles.',
		'label'       => esc_attr__( 'Font', 'silver' ),
		'section'     => 'type',
		'default'     => array(
			'font-family' => 'Lato',
			'variant'     => '700',
		),
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);


Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_type_base',
		'label'    => __( '', 'silver' ),
		'section'  => 'type',
		'default'  => '<div class="options-title-divider">Base</div>',
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'     => 'typography',
		'settings' => 'type_texts',
		'label'    => esc_attr__( 'Base Text Font', 'silver' ),
		'section'  => 'type',
		'default'  => array(
			'font-family' => 'Lato',
			'variant'     => 'regular',
		),
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'slider',
		'settings'    => 'type_size',
		'label'       => __( 'Base Font Size', 'silver' ),
		'section'     => 'type',
		'description' => 'Set base font size in %.',
		'default'     => 100,
		'choices'     => array(
			'min'  => 50,
			'max'  => 200,
			'step' => 1,
		),
		'transport'   => 'postMessage',
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'slider',
		'settings'    => 'type_size_mobile',
		'label'       => __( 'Mobile Base Font Size', 'silver' ),
		'section'     => 'type',
		'description' => 'Set mobile base font size in %.',
		'default'     => 100,
		'choices'     => array(
			'min'  => 50,
			'max'  => 200,
			'step' => 1,
		),
		'transport'   => 'postMessage',
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_type_nav',
		'label'    => __( '', 'silver' ),
		'section'  => 'type',
		'default'  => '<div class="options-title-divider">Navigation</div>',
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'typography',
		'settings'  => 'type_nav',
		'label'     => esc_attr__( 'Font', 'silver' ),
		'section'   => 'type',
		'default'   => array(
			'font-family' => 'Lato',
			'variant'     => '700',
		),
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_type_alt',
		'label'    => __( '', 'silver' ),
		'section'  => 'type',
		'default'  => '<div class="options-title-divider">Alt Fonts</div>',
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'        => 'typography',
		'settings'    => 'type_alt',
		'description' => 'Alt font can be selected in the Format dropdown in Text Editor.',
		'label'       => esc_attr__( 'Alt font (.alt-font)', 'silver' ),
		'section'     => 'type',
		'default'     => array(
			'font-family' => 'Dancing Script',
			'variant'     => 'regular',
		),
		'active_callback' => array(
			array(
				'setting'  => 'disable_fonts',
				'operator' => '==',
				'value'    => false,
			),
		),
	)
);

Silver_Option::add_field( '',
	array(
		'type'     => 'custom',
		'settings' => 'custom_title_type_transform',
		'label'    => __( '', 'silver' ),
		'section'  => 'type',
		'default'  => '<div class="options-title-divider">Text Transforms</div>',
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'text_transform_breadcrumbs',
		'label'     => esc_attr__( 'Breadcrumbs', 'silver' ),
		'section'   => 'type',
		'default'   => '',
		'choices'   => array(
			''     => 'UPPERCASE',
			'none' => 'Normal',
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'text_transform_buttons',
		'label'     => esc_attr__( 'Buttons', 'silver' ),
		'section'   => 'type',
		'default'   => '',
		'choices'   => array(
			''     => 'UPPERCASE',
			'none' => 'Normal',
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'text_transform_navigation',
		'label'     => esc_attr__( 'Navigation / Tabs', 'silver' ),
		'section'   => 'type',
		'default'   => '',
		'choices'   => array(
			''     => 'UPPERCASE',
			'none' => 'Normal',
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'text_transform_section_titles',
		'label'     => esc_attr__( 'Section Titles', 'silver' ),
		'section'   => 'type',
		'default'   => '',
		'choices'   => array(
			''     => 'UPPERCASE',
			'none' => 'Normal',
		),
	)
);

Silver_Option::add_field( 'option',
	array(
		'type'      => 'radio-buttonset',
		'settings'  => 'text_transform_widget_titles',
		'label'     => esc_attr__( 'Widget Titles', 'silver' ),
		'section'   => 'type',
		'default'   => '',
		'choices'   => array(
			''     => 'UPPERCASE',
			'none' => 'Normal',
		),
	)
);
