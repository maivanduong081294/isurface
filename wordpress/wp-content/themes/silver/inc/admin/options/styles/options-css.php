<?php

Silver_Option::add_panel( 'style', array(
  'title'       => __( 'Style', 'silver' ),
) );

Silver_Option::add_section( 'custom-css', array(
	'title'       => __( 'Custom CSS', 'silver' ),
	'panel'       => 'style',
) );

Silver_Option::add_field( 'option',  array(
	'type'        => 'code',
	'settings'     => 'html_custom_css',
	'label'       => __( 'Custom CSS', 'silver' ),
	'section'     => 'custom-css',
	'transport'   => silver_customizer_transport(),
	'placeholder' => '.add-css-here{}',
  'choices'     => array(
    'language' => 'css',
  ),
));

Silver_Option::add_field( 'option',  array(
  'type'        => 'code',
	'settings'     => 'html_custom_css_tablet',
	'label'       => __( 'Custom Tablet CSS (all tablets and mobile, up to 1199px)', 'silver' ),
	'section'     => 'custom-css',
	'default'     => '',
  'placeholder' => '.add-css-here{}',
	'transport'   => silver_customizer_transport(),
  'choices'     => array(
    'language' => 'css',
  ),
));

// One tier only each (no live preview script: the preview reloads).
Silver_Option::add_field( 'option',  array(
	'type'        => 'code',
	'settings'    => 'html_custom_css_tablet_landscape',
	'label'       => __( 'Custom Tablet Landscape CSS (850-1199px)', 'silver' ),
	'section'     => 'custom-css',
	'default'     => '',
	'placeholder' => '.add-css-here{}',
	'transport'   => 'refresh',
	'choices'     => array(
		'language' => 'css',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'code',
	'settings'    => 'html_custom_css_tablet_portrait',
	'label'       => __( 'Custom Tablet Portrait CSS (550-849px)', 'silver' ),
	'section'     => 'custom-css',
	'default'     => '',
	'placeholder' => '.add-css-here{}',
	'transport'   => 'refresh',
	'choices'     => array(
		'language' => 'css',
	),
));

Silver_Option::add_field( 'option',  array(
	'type'        => 'code',
	'settings'     => 'html_custom_css_mobile',
	'label'       => __( 'Custom Mobile CSS', 'silver' ),
	'section'     => 'custom-css',
	'default'     => '',
  'placeholder' => '.add-css-here{}',
	'transport'   => silver_customizer_transport(),
  'choices'     => array(
    'language' => 'css',
  ),
));
