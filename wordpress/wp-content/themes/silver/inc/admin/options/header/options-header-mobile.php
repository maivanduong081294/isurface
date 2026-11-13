<?php

/*************
 * Header Mobile
 *************/

Silver_Option::add_section( 'header_mobile', array(
	'title'       => __( 'Header Mobile Menu / Overlay', 'silver' ),
	'panel'       => 'header',
	//'description' => __( 'This is the section description', 'silver' ),
) );


Silver_Option::add_field( 'option',  array(
	'type'        => 'slider',
	'settings'     => 'header_height_mobile',
	'label'       => __( 'Mobile Header Height', 'silver' ),
	//'description' => __( 'This is the control description', 'silver' ),
	//'help'        => __( 'This is some extra help. You can use this to add some additional instructions for users. The main description should go in the "description" of the field, this is only to be used for help tips.', 'silver' ),
	'section'     => 'header_mobile',
	'default'     => '70',
	'choices'     => array(
		'min'  => 30,
		'max'  => 500,
		'step' => 1
	),
	'transport' => silver_customizer_transport()
));

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'logo_position_mobile',
	'label'       => __( 'Logo position', 'silver' ),
	'section'     => 'header_mobile',
	'transport' => silver_customizer_transport(),
	'default'     => 'center',
	'choices'     => array(
		'left' => $image_url . 'logo-left.svg',
		'center' => $image_url . 'logo-right.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'radio-image',
	'settings'     => 'menu_icon_style',
	'label'       => __( 'Menu Icon Style', 'silver' ),
	'section'     => 'header_mobile',
	'default'     => '',
	'transport' => silver_customizer_transport(),
	'choices'     => array(
		'' => $image_url . 'nav-icon-plain.svg',
		'outline' => $image_url . 'nav-icon-outline.svg',
		'fill' => $image_url . 'nav-icon-fill.svg',
		'fill-round' => $image_url . 'nav-icon-fill-round.svg',
		'outline-round' => $image_url . 'nav-icon-outline-round.svg',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'checkbox',
	'settings'     => 'menu_icon_title',
	'label'       => __( 'Show Menu title', 'silver' ),
	'section'     => 'header_mobile',
	'transport' => silver_customizer_transport(),
	'default'     => 0,
));

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'mobile_overlay',
	'label'       => __( 'Menu Overlay', 'silver' ),
	'section'     => 'header_mobile',
	'transport'	  => silver_customizer_transport(),
	'default'     => 'left',
	'choices'     => array(
		'left' => $image_url . 'overlay-left.svg',
		'right' => $image_url . 'overlay-right.svg',
		'center' => $image_url . 'overlay-center.svg'
	),
));

Silver_Option::add_field( '', array(
	'type'            => 'custom',
	'settings'        => 'mobile_drawer_link',
	'label'           => '',
	'section'         => 'header_mobile',
	'default'         => '<p><a href="#" data-to-section="lightbox">Customize Drawer &rarr;</a></p>',
	'active_callback' => array(
		array(
			'setting'  => 'mobile_overlay',
			'operator' => '!=',
			'value'    => 'center',
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'radio',
	'settings'        => 'mobile_submenu_parent_behavior',
	'label'           => __( 'Menu item behavior', 'silver' ),
	'description'     => __( 'Click behavior for menu items with a submenu', 'silver' ),
	'section'         => 'header_mobile',
	'transport'       => 'refresh',
	'default'         => '',
	'choices'         => array(
		''       => __( 'Open link', 'silver' ),
		'toggle' => __( 'Toggle submenu', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'radio',
	'settings'        => 'mobile_submenu_effect',
	'label'           => __( 'Submenu effect', 'silver' ),
	'section'         => 'header_mobile',
	'transport'       => 'refresh',
	'default'         => 'accordion',
	'choices'         => array(
		'accordion' => __( 'Accordion', 'silver' ),
		'slide'     => __( 'Slide', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'mobile_overlay',
			'operator' => '!=',
			'value'    => 'center',
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'select',
	'settings'        => 'mobile_submenu_levels',
	'label'           => __( 'Submenu levels', 'silver' ),
	'section'         => 'header_mobile',
	'transport'       => 'refresh',
	'default'         => '1',
	'choices'         => array(
		'1' => __( '1 level', 'silver' ),
		'2' => __( '2 levels', 'silver' ),
	),
	'active_callback' => array(
		array(
			'setting'  => 'mobile_overlay',
			'operator' => '!=',
			'value'    => 'center',
		),
		array(
			'setting'  => 'mobile_submenu_effect',
			'operator' => '===',
			'value'    => 'slide',
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'              => 'textarea',
	'settings'          => 'mobile_sidebar_top_content',
	'label'             => __( 'Top content', 'silver' ),
	'section'           => 'header_mobile',
	'sanitize_callback' => 'silver_custom_sanitize',
	'default'           => '',
) );

Silver_Option::add_field( 'option', array(
	'type'     => 'radio-buttonset',
	'settings' => 'mobile_sidebar_tabs',
	'label'    => __( 'Tabs', 'silver' ),
	'section'  => 'header_mobile',
	'default'  => '0',
	'choices'  => array(
		'0' => __( 'None', 'silver' ),
		'2' => __( '2 Tabs', 'silver' ),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'text',
	'settings'        => 'mobile_sidebar_tab_text',
	'label'           => __( 'Tab 1 text', 'silver' ),
	'section'         => 'header_mobile',
	'default'         => '',
	'active_callback' => array(
		array(
			'setting'  => 'mobile_sidebar_tabs',
			'operator' => '!=',
			'value'    => false,
		),
	),
) );

Silver_Option::add_field( 'option',  array(
  'type'        => 'sortable',
  'settings'     => 'mobile_sidebar',
  'label'       => __( 'Menu elements', 'silver' ),
  'section'     => 'header_mobile',
  'transport'   => silver_customizer_transport(),
  'multiple' => 10,
  'default'     => silver_header_mobile_sidebar(),
  'choices'     => $nav_elements
));

Silver_Option::add_field( 'option', array(
	'type'            => 'text',
	'settings'        => 'mobile_sidebar_tab_2_text',
	'label'           => __( 'Tab 2 text', 'silver' ),
	'section'         => 'header_mobile',
	'default'         => '',
	'active_callback' => array(
		array(
			'setting'  => 'mobile_sidebar_tabs',
			'operator' => '!=',
			'value'    => false,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'            => 'sortable',
	'settings'        => 'mobile_sidebar_tab_2',
	'label'           => __( 'Menu elements tab 2', 'silver' ),
	'section'         => 'header_mobile',
	'transport'       => silver_customizer_transport(),
	'multiple'        => 10,
	'default'         => '',
	'choices'         => $nav_elements,
	'active_callback' => array(
		array(
			'setting'  => 'mobile_sidebar_tabs',
			'operator' => '!=',
			'value'    => false,
		),
	),
) );

Silver_Option::add_field( 'option', array(
	'type'        => 'radio-image',
	'settings'     => 'mobile_overlay_color',
	'label'       => __( 'Overlay Color', 'silver' ),
	'section'     => 'header_mobile',
	'transport'	  => silver_customizer_transport(),
	'default'     => '',
	'choices'     => array(
		'' => $image_url . 'text-dark.svg',
		'dark' => $image_url . 'text-light.svg',
	),
));


Silver_Option::add_field( 'option',  array(
    'type'        => 'color-alpha',
    'settings'     => 'mobile_overlay_bg',
    'label'       => __( 'Background Color', 'silver' ),
	'section'     => 'header_mobile',
	'default'     => '',
	'transport' => silver_customizer_transport()
));
